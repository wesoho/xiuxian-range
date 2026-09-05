<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-YY-15 vulnerable.php - 漏洞演示
 * 分类：brute_force
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】无失败锁定
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
$pdo = new PDO($dsn, $__xxr_u, $__xxr_p);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM demo_users WHERE username = ? AND password = ?');
    $stmt->execute([$_POST['username'], $_POST['password']]);
    if ($stmt->fetch()) echo '登录成功';
    else echo '登录失败';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('logic');
