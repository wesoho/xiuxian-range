<?php
/**
 * LH-JD-06 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：入库时过滤恶意字符 + 转义
session_start();
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['username']);  // 白名单
    $stmt = $pdo->prepare('INSERT INTO demo_users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, 'pass']);
}
// 查询时同样使用参数化
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE username = ?');
$stmt->execute([$_GET['login'] ?? '']);