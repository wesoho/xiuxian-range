<?php
/**
 * LH-JD-05 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：使用 UTF-8 字符集
$mysqli = new mysqli('db', 'xiuxian', 'xiuxian_pass', 'xiuxian_range');
$mysqli->set_charset('utf8mb4');  // 不用 GBK
$stmt = $mysqli->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->bind_param('i', $_GET['id'] ?? 1);
$stmt->execute();