<?php
/**
 * WM-JZ-09 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：白名单 IP 校验
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    exit('Invalid IP');
}
system("ping -c 1 " . escapeshellarg($ip));