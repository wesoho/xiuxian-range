<?php
// 修复：使用 prepare（不支持多语句）
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
$pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);