<?php
// 修复：POST + CSRF Token
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['_token'] ?? '')) {
        http_response_code(419);
        exit('CSRF token invalid');
    }
    // 转账逻辑
    $amount = (float) ($_POST['amount'] ?? 0);
    $_SESSION['balance'] -= $amount;
}