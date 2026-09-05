<?php
declare(strict_types=1);

namespace XiuXian\Core;

use XiuXian\Models\User;

/**
 * 用户认证管理（基于 Session）
 */
class Auth
{
    private static ?Auth $instance = null;
    private ?array $user = null;

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct() {}

    /**
     * 通过用户名/邮箱 + 密码登录
     */
    public function attempt(string $username, string $password): bool
    {
        $user = User::findByUsernameOrEmail($username);
        if (!$user) {
            // 防时序攻击：仍然做一次假校验
            password_verify($password, '$argon2id$v=19$m=65536,t=4,p=1$xxx$xxx');
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        $this->loginById((int) $user['id']);
        return true;
    }

    /**
     * 通过用户 ID 登录
     */
    public function loginById(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }
        // 重新生成 Session ID 防止会话固定
        session()->regenerate();

        session()->set('user_id', $user['id']);
        session()->set('user', $user);

        // 更新最后登录信息
        User::updateLastLogin($userId, client_ip());

        logger()->info('用户登录', ['user_id' => $userId, 'username' => $user['username']]);
    }

    /**
     * 注销
     */
    public function logout(): void
    {
        $userId = $this->id();
        session()->forget('user_id');
        session()->forget('user');
        session()->regenerate();
        if ($userId) {
            logger()->info('用户注销', ['user_id' => $userId]);
        }
    }

    /**
     * 当前登录用户 ID
     */
    public function id(): ?int
    {
        return session()->get('user_id') ?: null;
    }

    /**
     * 当前登录用户信息
     */
    public function user(): ?array
    {
        if ($this->user === null) {
            $id = $this->id();
            if ($id) {
                $this->user = User::find($id);
            }
        }
        return $this->user;
    }

    /**
     * 是否已登录
     */
    public function check(): bool
    {
        return $this->id() !== null;
    }

    /**
     * 是否未登录
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * 是否为管理员
     */
    public function isAdmin(): bool
    {
        $u = $this->user();
        return $u && ($u['role'] ?? 'user') === 'admin';
    }

    /**
     * 校验密码强度
     */
    public static function passwordStrength(string $password): array
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = '密码至少 8 位';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = '需包含大写字母';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = '需包含小写字母';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = '需包含数字';
        }
        return $errors;
    }
}