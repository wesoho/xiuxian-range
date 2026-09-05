<?php
declare(strict_types=1);

namespace XiuXian\Core;

/**
 * Session 封装（基于 PHP 原生 Session）
 */
class Session
{
    private array $config;
    private bool $started = false;

    public function __construct(array $config)
    {
        $this->config = $config;

        // 配置 Session
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.use_strict_mode', '1');

            if (!empty($config['path']) && is_dir($config['path'])) {
                ini_set('session.save_path', $config['path']);
            }

            if (!empty($config['lifetime'])) {
                ini_set('session.gc_maxlifetime', (string) $config['lifetime']);
                ini_set('cookie_lifetime', (string) $config['lifetime']);
            }
        }
    }

    /**
     * 启动 Session
     */
    public function start(): void
    {
        if (!$this->started) {
            session_start();
            $this->started = true;
            $this->regenerateIfNeeded();
        }
    }

    /**
     * 定期重新生成 Session ID（防会话固定）
     */
    private function regenerateIfNeeded(): void
    {
        $lastRegen = $_SESSION['_last_regen'] ?? 0;
        if (time() - $lastRegen > 600) { // 每 10 分钟
            session_regenerate_id(true);
            $_SESSION['_last_regen'] = time();
        }
    }

    /**
     * 获取 session 值
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * 设置 session 值
     */
    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    /**
     * 删除 session 值
     */
    public function forget(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /**
     * 检查 session 值是否存在
     */
    public function has(string $key): bool
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    /**
     * 设置 Flash 数据（下一次请求生效后清除）
     */
    public function setFlash(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * 读取并清除 Flash 数据
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        $this->start();
        $val = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    /**
     * 销毁整个 Session
     */
    public function destroy(): void
    {
        $this->start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        $this->started = false;
    }

    /**
     * 重新生成 Session ID
     */
    public function regenerate(): void
    {
        $this->start();
        session_regenerate_id(true);
        $_SESSION['_last_regen'] = time();
    }

    /**
     * 获取当前 Session ID
     */
    public function id(): string
    {
        $this->start();
        return session_id();
    }

    /**
     * 设置错误 Flash（用于重定向后的错误提示）
     */
    public function error(string $message): void
    {
        $this->setFlash('error', $message);
    }

    /**
     * 设置成功 Flash
     */
    public function success(string $message): void
    {
        $this->setFlash('success', $message);
    }
}