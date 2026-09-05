<?php
/**
 * QY-HS-04 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：严格匹配 redirect_uri（完整 URL 或前缀）
$allowed = ['https://xiuxian-range.local/callback'];
$redirect = $_GET['redirect_uri'] ?? '';
if (!in_array($redirect, $allowed)) {
    http_response_code(400);
    exit('redirect_uri not allowed');
}
header("Location: $redirect");