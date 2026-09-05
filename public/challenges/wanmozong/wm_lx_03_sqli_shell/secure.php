<?php
// 修复：参数化 + 数据库用户最小权限（无 FILE）
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'xiuxian_readonly', 'xxx',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) { die('fail'); }

$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);