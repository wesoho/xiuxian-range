<?php
declare(strict_types=1);

namespace XiuXian\Controllers;

use XiuXian\Models\User;
use XiuXian\Core\Auth;

/**
 * 用户认证
 */
class AuthController
{
    /**
     * 登录页
     */
    public function showLogin(): void
    {
        if (auth()->check()) {
            redirect('/');
        }
        view('auth.login', [
            'redirect' => $_GET['redirect'] ?? '/',
        ]);
    }

    /**
     * 登录提交
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        if (!validate_csrf()) {
            flash('error', '安全令牌已过期，请重新提交');
            redirect('/login');
        }

        if (!rate_limit('login', 10, 60)) {
            flash('error', '登录尝试过于频繁，请稍后再试');
            redirect('/login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$username || !$password) {
            flash('error', '请输入用户名和密码');
            redirect('/login');
        }

        if (auth()->attempt($username, $password)) {
            session()->success('欢迎道友回到修真靶场！');
            self::checkAnniversary((int) auth()->id());
            $redirect = $_POST['redirect'] ?? '/';
            // 安全检查：redirect 必须为本应用
            if (!str_starts_with($redirect, '/')) {
                $redirect = '/';
            }
            redirect($redirect);
        }

        flash('error', '用户名或密码错误');
        redirect('/login');
    }

    /**
     * 周年彩蛋：入山满整年且当日恰逢纪念日时，点亮长明灯
     */
    private static function checkAnniversary(int $userId): void
    {
        try {
            $user = auth()->user();
            if (!$user || empty($user['created_at'])) {
                return;
            }
            $created = strtotime((string) $user['created_at']);
            $years = (int) floor((time() - $created) / (365.25 * 86400));
            if ($years < 1) {
                return;
            }
            if (date('m-d') !== date('m-d', $created)) {
                return;
            }
            if (\XiuXian\Services\EggService::award($userId, 'egg_anniversary')) {
                flash('success', "🎂 入山 {$years} 周年之喜！宗门为你点亮长明灯，彩蛋【守岁修士】已入册。");
            }
        } catch (\Throwable $e) {
            // 旧库未安装彩蛋表时静默跳过，不影响登录
        }
    }

    /**
     * 注册页
     */
    public function showRegister(): void
    {
        if (auth()->check()) {
            redirect('/');
        }
        view('auth.register');
    }

    /**
     * 注册提交
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }

        if (!validate_csrf()) {
            flash('error', '安全令牌已过期');
            redirect('/register');
        }

        if (!rate_limit('register', 5, 600)) {
            flash('error', '注册过于频繁，请稍后再试');
            redirect('/register');
        }

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $sect     = $_POST['sect'] ?? 'wanderer';

        // 校验
        if (!User::isValidUsername($username)) {
            flash('error', '用户名须为 3-20 位字母/数字/下划线');
            redirect('/register');
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', '邮箱格式不正确');
            redirect('/register');
        }
        if ($password !== $confirm) {
            flash('error', '两次密码不一致');
            redirect('/register');
        }

        $errors = Auth::passwordStrength($password);
        if ($errors) {
            flash('error', '密码强度不足：' . implode('；', $errors));
            redirect('/register');
        }

        if (User::findByUsername($username)) {
            flash('error', '用户名已被占用');
            redirect('/register');
        }
        if ($email && User::findByEmail($email)) {
            flash('error', '邮箱已被注册');
            redirect('/register');
        }

        // 合法宗门
        $validSects = ['qiingong', 'wanmozong', 'lunhuizong', 'wanderer'];
        if (!in_array($sect, $validSects, true)) {
            $sect = 'wanderer';
        }

        $hash = password_hash($password, PASSWORD_ARGON2ID);
        $userId = User::create([
            'username'      => $username,
            'email'         => $email ?: null,
            'password_hash' => $hash,
            'sect'          => $sect,
            'realm_level'   => 'liqi',
            'title'         => '炼气小修',
            'bio'           => '新入门弟子',
        ]);

        auth()->loginById((int) $userId);
        session()->success('欢迎加入修真靶场，你的修真之路从此开始！');
        redirect('/');
    }

    /**
     * 注销
     */
    public function logout(): void
    {
        auth()->logout();
        session()->success('道友一路走好，期待下次相见！');
        redirect('/');
    }

    /**
     * 个人中心
     */
    public function profile(): void
    {
        $user = auth()->user();
        if (!$user) {
            redirect('/login');
        }
        $progress = \XiuXian\Models\Progress::listByUser((int) $user['id']);
        $completedCount = \XiuXian\Models\Progress::completedCount((int) $user['id']);

        // 徽章墙（境界徽章 + 彩蛋徽章；旧库未装彩蛋表时静默降级）
        $badges = [];
        try {
            $badges = db()->fetchAll(
                'SELECT b.code, b.name, b.description, b.icon, b.tier, ub.earned_at
                 FROM user_badges ub JOIN badges b ON b.id = ub.badge_id
                 WHERE ub.user_id = ?
                 ORDER BY ub.earned_at DESC',
                [(int) $user['id']]
            );
        } catch (\Throwable $e) {
            $badges = [];
        }

        view('auth.profile', [
            'user'           => $user,
            'progress'       => $progress,
            'completedCount' => $completedCount,
            'badges'         => $badges,
        ]);
    }
}