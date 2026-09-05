<?php
/**
 * WM-JD-08 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：参数化（不要用黑名单）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);