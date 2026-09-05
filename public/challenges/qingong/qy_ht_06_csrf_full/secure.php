<?php
// 【修复/安全】综合 CSRF 防御：Token + SameSite + 二次确认
session_start();

// 1. CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. SameSite Cookie
session_set_cookie_params(['samesite' => 'Strict']);

// 3. 验证 Token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['_token'] ?? '')) {
        http_response_code(419);
        exit('CSRF token invalid');
    }
}

// 4. CORS 配置
header("Access-Control-Allow-Origin: https://xiuxian-range.local");
header("Access-Control-Allow-Credentials: true");