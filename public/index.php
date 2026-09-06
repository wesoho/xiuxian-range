<?php
/**
 * 修真网络安全靶场 - 入口文件
 *
 * 修真网安靶场 - XiuXian Range v1.0
 * Powered by Native PHP 8.2 + MySQL 8.0
 */

declare(strict_types=1);

// 1. 错误显示（开发环境）
if (getenv('APP_DEBUG') === 'true' || ($_ENV['APP_DEBUG'] ?? '') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// 2. 加载 Composer 自动加载
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
} else {
    // 没有 Composer 时使用简易 PSR-4 加载
    spl_autoload_register(function (string $class): void {
        $prefix = 'XiuXian\\';
        if (!str_starts_with($class, $prefix)) return;
        $relative = substr($class, strlen($prefix));
        $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) require $file;
    });

    // 加载辅助函数
    require dirname(__DIR__) . '/app/Helpers/functions.php';
    require dirname(__DIR__) . '/app/Helpers/security.php';
    require dirname(__DIR__) . '/app/Helpers/response.php';
}

// 3. 启动 Session
session()->start();

// 5. 全局异常处理
set_exception_handler(function (\Throwable $e): void {
    http_response_code(500);
    $isDebug = config('app.debug');
    if ($isDebug) {
        echo '<pre style="background:#fff5f5;color:#900;padding:20px;border:1px solid #900;border-radius:8px;margin:20px;">';
        echo '<strong>Exception:</strong> ' . get_class($e) . "\n";
        echo '<strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . "\n";
        echo '<strong>File:</strong> ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
        echo $e->getTraceAsString();
        echo '</pre>';
    } else {
        echo '<h1>修真靶场发生意外</h1><p>请稍后再试或联系长老</p>';
    }
    logger()->error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
});

// 6. 路由定义
$router = new \XiuXian\Core\Router();

// ---- 主页 / 关于 ----
$router->get('/',           [\XiuXian\Controllers\HomeController::class, 'index']);
$router->get('/about',      [\XiuXian\Controllers\HomeController::class, 'about']);

// ---- CSRF 令牌刷新（供前端在 419 后自动恢复旧页面） ----
$router->get('/csrf-token', function (): void {
    json_ok(['token' => csrf_token()]);
});

// ---- 用户认证 ----
$router->get('/login',      [\XiuXian\Controllers\AuthController::class, 'showLogin']);
$router->post('/login',     [\XiuXian\Controllers\AuthController::class, 'login']);
$router->get('/register',   [\XiuXian\Controllers\AuthController::class, 'showRegister']);
$router->post('/register',  [\XiuXian\Controllers\AuthController::class, 'register']);
$router->any('/logout',     [\XiuXian\Controllers\AuthController::class, 'logout']);
$router->get('/profile',    [\XiuXian\Controllers\AuthController::class, 'profile']);

// ---- 关卡 ----
$router->get('/challenges',                 [\XiuXian\Controllers\ChallengeController::class, 'map']);
$router->get('/challenges/realm/{realm}',   [\XiuXian\Controllers\ChallengeController::class, 'map']);
$router->get('/challenge/{id}',             [\XiuXian\Controllers\ChallengeController::class, 'show']);
$router->get('/challenge/{id}/learn',       [\XiuXian\Controllers\ChallengeController::class, 'learn']);
$router->get('/challenge/{id}/fight',       [\XiuXian\Controllers\ChallengeController::class, 'fight']);
$router->get('/challenge/{id}/review',      [\XiuXian\Controllers\ChallengeController::class, 'review']);
$router->get('/challenge/{id}/source',      [\XiuXian\Controllers\ChallengeController::class, 'viewSource']);
$router->post('/challenge/submit-flag',     [\XiuXian\Controllers\ChallengeController::class, 'submitFlag']);
$router->post('/challenge/save-writeup',    [\XiuXian\Controllers\ChallengeController::class, 'saveWriteup']);
$router->post('/challenge/get-hint',        [\XiuXian\Controllers\ChallengeController::class, 'getHint']);

// ---- 排行榜 ----
$router->get('/leaderboard',     [\XiuXian\Controllers\LeaderboardController::class, 'index']);
$router->get('/leaderboard/sect',[\XiuXian\Controllers\LeaderboardController::class, 'bySect']);

// ---- 趣味玩法：天机阁 / 万宝楼 / 斗法台 / 悬赏令 ----
$router->get('/tianji',           [\XiuXian\Controllers\FunController::class, 'tianji']);
$router->post('/tianji/draw',     [\XiuXian\Controllers\FunController::class, 'drawFortune']);
$router->get('/wanbaolou',        [\XiuXian\Controllers\FunController::class, 'shop']);
$router->post('/wanbaolou/buy',   [\XiuXian\Controllers\FunController::class, 'buy']);
$router->post('/wanbaolou/equip', [\XiuXian\Controllers\FunController::class, 'equip']);
$router->get('/doufatai',         [\XiuXian\Controllers\FunController::class, 'quiz']);
$router->post('/doufatai/submit', [\XiuXian\Controllers\FunController::class, 'quizSubmit']);
$router->get('/xuanshang',        [\XiuXian\Controllers\FunController::class, 'bounty']);
$router->post('/xuanshang/claim', [\XiuXian\Controllers\FunController::class, 'bountyClaim']);

// ---- 彩蛋系统：兑换 / 行为上报 / 秘境 / 飞升 ----
$router->post('/egg/claim',   [\XiuXian\Controllers\FunController::class, 'claimEgg']);
$router->post('/egg/konami',  [\XiuXian\Controllers\FunController::class, 'claimKonami']);
$router->post('/egg/crane',   [\XiuXian\Controllers\FunController::class, 'claimCrane']);
$router->post('/egg/whistle', [\XiuXian\Controllers\FunController::class, 'whistle']);
$router->get('/mijing',       [\XiuXian\Controllers\FunController::class, 'mijing']);
$router->get('/ascend',       [\XiuXian\Controllers\FunController::class, 'ascend']);
$router->get('/dacheng-final',[\XiuXian\Controllers\FunController::class, 'finalCredits']);

// ---- 健康检查 ----
$router->get('/healthz', function (): void {
    header('Content-Type: text/plain');
    try {
        db()->pdo()->query('SELECT 1');
        echo 'OK';
    } catch (\Throwable $e) {
        http_response_code(503);
        echo 'FAIL';
    }
    exit;
});

// ---- robots.txt（动态渲染：QY-LQ-02 关卡 Flag 与天机残页口令均随机化后由数据库注入） ----
$router->get('/robots.txt', function (): void {
    header('Content-Type: text/plain; charset=utf-8');
    try {
        $flag = db()->fetchScalar("SELECT flag FROM challenges WHERE id = 'QY-LQ-02' AND enabled = 1") ?: '[FLAG_UNAVAILABLE]';
        $slip1 = db()->fetchScalar("SELECT secret FROM easter_eggs WHERE code = 'egg_slip_1' AND is_active = 1") ?: '[SECRET_UNAVAILABLE]';
    } catch (\Throwable $e) {
        $flag = '[FLAG_UNAVAILABLE]';
        $slip1 = '[SECRET_UNAVAILABLE]';
    }
    echo <<<ROBOTS
User-agent: *
Disallow: /admin/
Disallow: /private/
Disallow: /backup/
Disallow: /storage/
Disallow: /.git/

# ============================================================
# 【本关答案】QY-LQ-02 守山神兽
#
# Flag: {$flag}
#
# ↑ 复制这一行 Flag，回到关卡详情页提交，即可通过本关。
#   （实际环境不会在 robots.txt 中放 Flag，这只是靶场演示）
# ============================================================

# ------------------------------------------------------------
# 【支线彩蛋 · 与通关无关，纯粹是前人留下的字条】
#
# 　　　　天机残页·壹
#
# 　残页口令：{$slip1}
# 　（口令请到 /tianji 天机阁兑换，集齐五张残页是另一条支线任务）
#
# 　下一环线索（具体操作）：打开山门首页，在网址后面加上 ?dao=1
# ------------------------------------------------------------
ROBOTS;
    exit;
});

// ---- 7. 分发请求 ----
$router->dispatch();