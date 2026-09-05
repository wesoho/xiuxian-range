<?php
/**
 * WM-YY-15 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：失败次数限制 + 锁定 + 验证码
session_start();
$_SESSION['login_attempts'] ??= 0;
if ($_SESSION['login_attempts'] > 5) {
    exit('Too many attempts, try again in 15 minutes');
}

$stmt = $pdo->prepare('SELECT password_hash FROM demo_users WHERE username = ?');
$stmt->execute([$_POST['username'] ?? '']);
$user = $stmt->fetch();
if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
    $_SESSION['login_attempts'] = 0;
    echo '登录成功';
} else {
    $_SESSION['login_attempts']++;
    usleep(random_int(100000, 500000));
    echo '登录失败';
}