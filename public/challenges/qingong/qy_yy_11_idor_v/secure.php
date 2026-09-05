<?php
/**
 * QY-YY-11 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：检查角色
session_start();
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Admin only');
}