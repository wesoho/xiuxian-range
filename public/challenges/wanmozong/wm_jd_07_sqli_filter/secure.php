<?php
/**
 * WM-JD-07 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：使用参数化（不要靠过滤）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
foreach ($stmt as $row) print_r($row);