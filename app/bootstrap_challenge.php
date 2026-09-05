<?php
/**
 * 修真靶场 - 关卡页面引导文件
 *
 * 用途：public/challenges/ 下的关卡页需要连接平台数据库时，
 *      引入本文件即可获得与主应用一致的 db() 连接（MySQL / SQLite 均可）。
 * 用法：require_once __DIR__ . '/bootstrap_challenge.php';
 *
 * ⚠️ 本文件只提供连接，不改变关卡自身的漏洞演示逻辑。
 */

declare(strict_types=1);

if (!defined('XXR_CHALLENGE_BOOTSTRAP')) {
    define('XXR_CHALLENGE_BOOTSTRAP', 1);

    $appRoot = dirname(__DIR__);

    // 自动加载（与 public/index.php 一致）
    if (is_file($appRoot . '/vendor/autoload.php')) {
        require $appRoot . '/vendor/autoload.php';
    } else {
        spl_autoload_register(function (string $class) use ($appRoot): void {
            $prefix = 'XiuXian\\';
            if (!str_starts_with($class, $prefix)) return;
            $relative = substr($class, strlen($prefix));
            $file = $appRoot . '/app/' . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) require $file;
        });
        require $appRoot . '/app/Helpers/functions.php';
        require $appRoot . '/app/Helpers/security.php';
        require $appRoot . '/app/Helpers/response.php';
    }
}

if (!function_exists('xxr_flag_lookup')) {
    /**
     * 按目录名前缀查找关卡 Flag（内部共享实现）
     */
    function xxr_flag_lookup(string $dir): string
    {
        static $flags = null;
        if ($flags === null) {
            $flags = [];
            try {
                foreach (db()->fetchAll('SELECT id, flag FROM challenges WHERE enabled = 1') as $row) {
                    $flags[strtolower(str_replace('-', '_', $row['id']))] = $row['flag'];
                }
            } catch (\Throwable $e) {
                $flags = []; // 数据库不可用时降级为占位符
            }
        }
        foreach ($flags as $prefix => $flag) {
            if ($dir === $prefix || str_starts_with($dir, $prefix . '_')) {
                return $flag;
            }
        }
        return '[FLAG_UNAVAILABLE]';
    }
}

if (!function_exists('xxr_challenge_flag')) {
    /**
     * 获取当前关卡的 Flag（从数据库动态读取）
     *
     * 关卡 Flag 在每次初始化数据库时随机生成（防猜测、防仓库泄露），
     * 关卡页面必须通过本函数动态渲染，禁止在源码中硬编码 Flag。
     *
     * 关卡 ID 由调用方文件所在目录名推导：
     *   目录名以「关卡 ID 的下划线形式」为前缀，如
     *   qy_lq_01_html_comment -> QY-LQ-01，dc_01_cross -> DC-01。
     *
     * @return string 当前关卡 Flag；无法定位时返回占位符（页面仍可渲染）
     */
    function xxr_challenge_flag(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $dir = isset($trace[0]['file']) ? basename(dirname($trace[0]['file'])) : '';
        return xxr_flag_lookup($dir);
    }
}

if (!function_exists('xxr_pdo_args')) {
    /**
     * 关卡演示代码的数据库连接参数（环境自适应）
     *
     * 关卡页面历史上硬编码 Docker MySQL DSN（mysql:host=db），本地 SQLite 开发环境
     * 无法运行。本函数按平台配置返回正确的连接三参数，页面代码保持漏洞演示逻辑不变。
     *
     * @return array{0:string, 1:string|null, 2:string|null} [dsn, user, pass]
     */
    function xxr_pdo_args(): array
    {
        try {
            $driver = (string) config('db.driver');
            $database = (string) config('db.database');
        } catch (\Throwable $e) {
            $driver = 'mysql';
            $database = '';
        }
        if ($driver === 'sqlite') {
            return ['sqlite:' . $database, null, null];
        }
        return ['mysql:host=db;dbname=xiuxian_range;charset=utf8mb4', 'xiuxian', 'xiuxian_pass'];
    }
}

if (!function_exists('xxr_flag_reveal')) {
    /**
     * 试炼印记：按攻击特征解锁 Flag 展示
     *
     * 大量关卡页面只演示漏洞、从未展示 Flag（先天无法通关）。
     * 本函数在请求中检测到「本关所授攻击行为」的特征时，输出当前随机 Flag。
     * 玩家按 learn 页所学发起攻击即可获得 Flag，未发起攻击的普通浏览不泄露。
     */
    function xxr_flag_reveal(string $sig): void
    {
        static $revealed = false;
        if ($revealed) {
            return;
        }
        // 注意：此处调用方是关卡文件本身，需取 backtrace[0]（而非经 xxr_challenge_flag 的两层栈）
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $dir = isset($trace[0]['file']) ? basename(dirname($trace[0]['file'])) : '';
        $flag = xxr_flag_lookup($dir);
        if ($flag === '[FLAG_UNAVAILABLE]' || !str_starts_with($flag, 'flag{')) {
            return;
        }

        $probe = strtolower(rawurldecode($_SERVER['REQUEST_URI'] ?? '') . '|' . (string) @file_get_contents('php://input')
            . '|' . ($_SERVER['HTTP_ORIGIN'] ?? '')
            . '|' . ($_SERVER['HTTP_REFERER'] ?? '')
            . '|' . ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? '')
            . '|' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')
            . '|' . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $ct = strtolower($_SERVER['CONTENT_TYPE'] ?? '');

        $special = [
            '__post__'     => $method === 'POST',
            '__multipart__' => str_contains($ct, 'multipart/form-data') || !empty($_FILES),
            '__anyparam__' => ($_SERVER['QUERY_STRING'] ?? '') !== '' || $method === 'POST',
            '__iframe__'   => strtolower($_SERVER['HTTP_SEC_FETCH_DEST'] ?? '') === 'iframe',
        ];

        $signatures = [
            'sqli'     => ['union select', 'union all', 'select+', 'select%20', 'select ', 'sleep(', 'order by', 'group by', '1=1', "' or ", ' or 1', 'information_schema', 'information_schema', '%27%20or', 'admin\'--', '--'],
            'xss'      => ['<script', 'onerror', 'onload', 'alert(', '<img', '<svg', '<iframe', 'javascript:', 'document.cookie', '%3cscript'],
            'csrf'     => ['__post__'],
            'upload'   => ['__multipart__'],
            'rce'      => [';', '|', '&&', 'cat ', 'ls ', ' whoami', 'whoami', 'pwd', 'id;', '`', '$(', 'ping ', 'nc ', '%0a'],
            'lfi'      => ['../', '..%2f', '%2e%2e', 'php://', 'data://', 'file://', '/etc/passwd', 'php_input', 'log'],
            'ssrf'     => ['127.0.0.1', 'localhost', '169.254', 'file:', 'gopher', 'dict:', '0.0.0.0', '%31%32%37'],
            'xxe'      => ['<!entity', '<!doctype', '__post__'],
            'deser'    => ['o:', 'a:2', 's:4', 'phar://', 'serialize', 'object'],
            'jwt'      => ['alg', 'kid', 'eyJ', '../', 'header'],
            'redirect' => ['redirect=', 'url=', 'next=', 'target=', 'callback=', 'return_to=', 'http://evil', 'evil.com', 'weixin://'],
            'cors'     => ['origin', 'evil', '__anyparam__'],
            'crypto'   => ['0e', 'md5', 'sha', '==', 'ecb', 'aes', 'decrypt'],
            'phpweak'  => ['0e', '==', '===', 'strcmp', 'md5', 'json', 'preg_match', '[]'],
            'logic'    => ['__post__'],
            'smuggle'  => ['__post__'],
            'poison'   => ['x-forwarded', 'host:', 'poison', '__anyparam__'],
            'escape'   => ['__anyparam__'],
            'clickjack' => ['__iframe__', '__anyparam__'],
        ];

        $tests = $signatures[$sig] ?? ['__anyparam__'];
        $hit = false;
        foreach ($tests as $needle) {
            if (isset($special[$needle])) {
                if ($special[$needle]) {
                    $hit = true;
                    break;
                }
                continue;
            }
            if ($needle !== '' && str_contains($probe, $needle)) {
                $hit = true;
                break;
            }
        }
        if (!$hit) {
            return;
        }
        $revealed = true;
        echo '<div style="margin:18px auto;max-width:760px;padding:14px 18px;border:1px solid rgba(212,175,55,.6);'
            . 'border-radius:8px;background:rgba(212,175,55,.08);color:#f0d879;font-size:15px;">'
            . '🎁 <strong>攻击奏效，试炼印记显现！</strong> 本关 Flag：<code style="color:#fff;background:rgba(0,0,0,.45);padding:2px 10px;border-radius:4px;">'
            . e($flag) . '</code>'
            . '<span style="color:#8a97a8;font-size:12px;margin-left:10px;">（复制到关卡详情页提交即可通关）</span></div>';
    }
}
