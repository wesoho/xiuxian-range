<?php
/**
 * 修真网络安全靶场 - 通用辅助函数
 * XiuXian Range Common Helpers
 */

if (!function_exists('env')) {
    /**
     * 读取环境变量（兼容 .env 文件）
     */
    function env(string $key, mixed $default = null): mixed
    {
        static $env = null;
        if ($env === null) {
            $env = [];
            $envFile = dirname(__DIR__, 2) . '/.env';
            if (is_readable($envFile)) {
                foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    if (str_starts_with(trim($line), '#')) continue;
                    [$k, $v] = explode('=', $line, 2) + [null, null];
                    if ($k !== null) {
                        $env[trim($k)] = trim($v ?? '', " \t\"'");
                    }
                }
            }
            $env = array_merge($env, $_ENV ?? [], $_SERVER ?? []);
        }
        return $env[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('config')) {
    /**
     * 获取配置项
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $config = null;
        if ($config === null) {
            $config = [
                'app' => [
                    'name'    => env('APP_NAME', '修真网络安全靶场'),
                    'env'     => env('APP_ENV', 'production'),
                    'debug'   => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
                    'key'     => env('APP_KEY', 'change-me'),
                    'url'     => env('APP_URL', 'http://localhost'),
                    'timezone'=> env('APP_TIMEZONE', 'Asia/Shanghai'),
                ],
                'db' => [
                    'driver'   => env('DB_CONNECTION', 'mysql'),
                    'host'     => env('DB_HOST', 'localhost'),
                    'port'     => env('DB_PORT', '3306'),
                    'database' => env('DB_DATABASE', 'xiuxian_range'),
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset'  => 'utf8mb4',
                ],
                'redis' => [
                    'host' => env('REDIS_HOST', 'localhost'),
                    'port' => env('REDIS_PORT', '6379'),
                ],
                'session' => [
                    'lifetime' => (int) env('SESSION_LIFETIME', 7200),
                    'path'     => dirname(__DIR__, 2) . '/storage/sessions',
                ],
                'paths' => [
                    'base'      => dirname(__DIR__, 2),
                    'app'       => dirname(__DIR__),
                    'public'    => dirname(__DIR__, 2) . '/public',
                    'storage'   => dirname(__DIR__, 2) . '/storage',
                    'logs'      => dirname(__DIR__, 2) . '/storage/logs',
                    'cache'     => dirname(__DIR__, 2) . '/storage/cache',
                    'challenges'=> dirname(__DIR__, 2) . '/challenges',
                    'views'     => dirname(__DIR__, 2) . '/app/Views',
                ],
            ];
        }
        $parts = explode('.', $key);
        $value = $config;
        foreach ($parts as $p) {
            if (!is_array($value) || !array_key_exists($p, $value)) {
                return $default;
            }
            $value = $value[$p];
        }
        return $value;
    }
}

if (!function_exists('e')) {
    /**
     * HTML 转义（防 XSS，平台自身代码使用）
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('view_path')) {
    /**
     * 视图路径解析
     */
    function view_path(string $name): string
    {
        return config('paths.views') . '/' . str_replace('.', '/', $name) . '.php';
    }
}

if (!function_exists('now')) {
    /**
     * 当前时间
     */
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('uuid')) {
    /**
     * 生成简易 UUID
     */
    function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('logger')) {
    /**
     * 获取 Logger 实例（懒加载）
     */
    function logger(): \XiuXian\Core\Logger
    {
        static $logger = null;
        return $logger ??= new \XiuXian\Core\Logger(config('paths.logs'));
    }
}

if (!function_exists('db')) {
    /**
     * 获取数据库实例
     */
    function db(): \XiuXian\Core\Database
    {
        static $db = null;
        return $db ??= new \XiuXian\Core\Database(config('db'));
    }
}

if (!function_exists('session')) {
    /**
     * 获取 Session 实例
     */
    function session(): \XiuXian\Core\Session
    {
        static $session = null;
        return $session ??= new \XiuXian\Core\Session(config('session'));
    }
}

if (!function_exists('auth')) {
    /**
     * 获取 Auth 实例
     */
    function auth(): \XiuXian\Core\Auth
    {
        return \XiuXian\Core\Auth::getInstance();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * 生成 CSRF Token
     */
    function csrf_token(): string
    {
        return \XiuXian\Core\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * 生成 CSRF 隐藏字段
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('url')) {
    /**
     * 生成 URL
     */
    function url(string $path = ''): string
    {
        $base = rtrim(config('app.url') ?: '', '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    /**
     * 302 重定向
     */
    function redirect(string $url, int $status = 302): void
    {
        header('Location: ' . $url, true, $status);
        exit;
    }
}

if (!function_exists('flash')) {
    /**
     * Flash 消息（一次性提示）
     */
    function flash(string $key, mixed $value = null): mixed
    {
        $s = session();
        if ($value === null) {
            $val = $s->getFlash($key);
            return $val;
        }
        $s->setFlash($key, $value);
        return null;
    }
}

if (!function_exists('render_realm')) {
    /**
     * 境界名称显示
     */
    function render_realm(string $realm): string
    {
        $map = [
            'liqi'    => '🥉 炼气期',
            'zhuji'   => '🥉 筑基期',
            'jindan'  => '🥈 金丹期',
            'yuanying'=> '🥈 元婴期',
            'huashen' => '🥇 化神期',
            'lianxu'  => '🥇 炼虚期',
            'heti'    => '💎 合体期',
            'dacheng' => '💎 大乘期',
        ];
        return $map[$realm] ?? $realm;
    }
}

if (!function_exists('render_sect')) {
    /**
     * 宗门名称显示
     */
    function render_sect(string $sect): string
    {
        $map = [
            'qiingong'   => '🏯 青云宗',
            'wanmozong'  => '🔥 万魔宗',
            'lunhuizong' => '🔮 轮回宗',
            'wanderer'   => '🌿 散修',
        ];
        return $map[$sect] ?? $sect;
    }
}

if (!function_exists('render_difficulty')) {
    /**
     * 难度星标
     */
    function render_difficulty(int $level): string
    {
        $stars = str_repeat('⭐', $level) . str_repeat('☆', 5 - $level);
        return $stars;
    }
}