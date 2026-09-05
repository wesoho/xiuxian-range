<?php
/**
 * LH-JZ-05 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：参数化查询（UNION 注入因参数化失效）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare('SELECT id, username, email FROM demo_users WHERE id = ?');
$stmt->execute([$id]);
foreach ($stmt as $row) {
    echo "ID={$row['id']} 用户={$row['username']}";
}