<?php
/**
 * 修真靶场 - 后台管理入口
 *
 * 通过 /admin/ 路径访问，仅管理员（role=admin）可访问
 */

// 1. 启动框架（避免与 public/index.php 重复加载）
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'XiuXian\\';
        if (!str_starts_with($class, $prefix)) return;
        $relative = substr($class, strlen($prefix));
        $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) require $file;
    });

    require dirname(__DIR__) . '/app/Helpers/functions.php';
    require dirname(__DIR__) . '/app/Helpers/security.php';
    require dirname(__DIR__) . '/app/Helpers/response.php';
}

session()->start();

// 2. 鉴权：必须为管理员
if (!auth()->isAdmin()) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <title>无权访问 · 长老殿</title>
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/xiuxian.css" rel="stylesheet">
    </head>
    <body class="bg-dark text-light d-flex align-items-center justify-content-center" style="height:100vh;">
        <div class="text-center">
            <h1 class="text-warning">🚫 道友无权访问长老禁地</h1>
            <p class="text-muted">仅长老（管理员）可入</p>
            <a href="/" class="xxr-btn xxr-btn-primary mt-3">返回山门</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 3. 简单路由
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = '/' . trim(preg_replace('#^/admin/?#', '', $path), '/');
$method = $_SERVER['REQUEST_METHOD'];

// 路径安全：拒绝相对段与非法字符，防止 require 路径穿越
if (str_contains($path, '..') || !preg_match('#^/[a-zA-Z0-9_/-]*$#', $path)) {
    not_found('长老页面不存在');
}

// API 路由（POST）
if ($method === 'POST') {
    $actionFile = __DIR__ . "/actions/{$path}.php";
    if (is_file($actionFile)) {
        require $actionFile;
        exit;
    }
    not_found('API 端点不存在');
}

// 页面路由（GET）
$pageMap = [
    '/'            => 'dashboard.php',
    '/challenges'  => 'challenges.php',
    '/users'       => 'users.php',
    '/statistics'  => 'statistics.php',
    '/settings'    => 'settings.php',
];

$file = $pageMap[$path] ?? null;
if ($file && is_file(__DIR__ . '/actions/' . $file)) {
    require __DIR__ . '/actions/' . $file;
} else {
    not_found('长老页面不存在');
}