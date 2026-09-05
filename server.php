<?php
/**
 * 修真靶场 - PHP 内置开发服务器路由脚本
 *
 * 用途：本机无 Docker/MySQL 时，用 `php -S 127.0.0.1:8080 server.php` 拉起靶场。
 * 规则：真实存在的静态文件/目录（assets、关卡页、robots.txt 等）原样返回，
 *      其余路径交给 public/index.php 走框架路由。
 */

declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$file = __DIR__ . '/public' . $uri;

// 后台管理（docroot 之外的独立应用，自带鉴权、子路由与路径守卫）
if ($uri === '/admin' || str_starts_with($uri, '/admin/')) {
    require __DIR__ . '/admin/index.php';
    exit;
}

// 真实文件 → 内置服务器直接返回（图片/CSS/JS/robots.txt/www.zip 等）
if ($uri !== '/' && is_file($file)) {
    return false;
}

// 真实目录（如 /challenges/qingong/qy_lq_01_html_comment/）→ 让内置服务器找其 index.php；
// 无 index 的目录（如 /challenges 本身）→ 交给框架路由
if ($uri !== '/' && is_dir($file)) {
    if (is_file($file . '/index.php') || is_file($file . '/index.html')) {
        return false;
    }
}

// 防目录穿越
$real = realpath($file);
if ($real !== false && !str_starts_with($real, realpath(__DIR__ . '/public'))) {
    http_response_code(403);
    exit('Forbidden');
}

require __DIR__ . '/public/index.php';
