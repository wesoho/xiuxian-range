<?php
// 综合认证防御：多因素认证 + 安全密码哈希
session_start();
session_regenerate_id(true);

// 1. 密码哈希
$hash = password_hash($password, PASSWORD_ARGON2ID);

// 2. 多因素
// 3. 失败锁定
$_SESSION['attempts'] ??= 0;
if ($_SESSION['attempts'] > 5) {
    http_response_code(429);
    exit('Too many attempts');
}