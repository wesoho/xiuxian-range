<?php
// 修复：htmlspecialchars + CSP
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
$msg = $_GET['msg'] ?? '';
echo '石壁回响：' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');