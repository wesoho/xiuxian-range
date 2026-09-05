<?php
/**
 * LH-JZ-06 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复 1：关闭 display_errors
// 修复 2：使用日志记录错误
// 修复 3：参数化查询
ini_set('display_errors', '0');
error_reporting(0);

$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$id]);
foreach ($stmt as $row) {
    // ...
}