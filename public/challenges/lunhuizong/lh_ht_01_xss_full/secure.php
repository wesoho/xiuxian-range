<?php
// 【修复/安全】综合 XSS 防御：CSP + 输出转义
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'");

$msg = $_GET['msg'] ?? '';
echo '回显：' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');