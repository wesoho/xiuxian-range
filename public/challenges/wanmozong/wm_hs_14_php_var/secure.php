<?php
/**
 * WM-HS-14 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：不要使用 extract()，或者限制 extract 范围
$role = 'guest';
// 不要使用 extract($_GET);
// 而是从 $_GET 显式读取
$role = $_GET['role'] ?? 'guest';
$role = preg_replace('/[^a-zA-Z]/', '', $role);  // 白名单