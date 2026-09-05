<?php
// 修复：严格比较 ===
$password = $_POST['password'] ?? '';
if ($password === '0') {  // 严格类型
    echo '登录成功';
}

// 或使用 password_verify
$hash = password_hash('0', PASSWORD_BCRYPT);
if (password_verify($password, $hash)) {
    echo '登录成功';
}