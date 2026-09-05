<?php
/**
 * 修真靶场 - PHPUnit 测试引导
 *
 * 必须在 phpunit.xml 中通过 bootstrap 加载此文件。
 */

// 1. 加载 Composer 自动加载（如果存在）
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
} else {
    // 简易 PSR-4 加载
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

// 2. 测试环境配置
$_ENV['APP_ENV']   = 'testing';
$_ENV['APP_DEBUG'] = 'true';

// 测试数据库（与生产分离，避免污染）
putenv('APP_ENV=testing');
putenv('APP_DEBUG=true');

// 3. 全局辅助：静默 PHP 警告
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);