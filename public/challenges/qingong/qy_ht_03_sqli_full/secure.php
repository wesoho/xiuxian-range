<?php
// 【修复/安全】综合 SQL 注入防御：参数化查询（终极方案）
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
$pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$id]);