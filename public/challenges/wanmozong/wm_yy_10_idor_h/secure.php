<?php
/**
 * WM-YY-10 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：添加 user_id 校验
session_start();
$orderId = $_GET['id'] ?? 1;
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT * FROM demo_orders WHERE id = ? AND user_id = ?');
$stmt->execute([$orderId, $userId]);
if (!$stmt->fetch()) {
    http_response_code(403);
    exit('Forbidden');
}