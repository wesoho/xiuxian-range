<?php
// 修复：白名单命令
$allowed = ['status', 'ping', 'uptime'];
$cmd = $_GET['cmd'] ?? '';
if (!in_array($cmd, $allowed, true)) exit('Command not allowed');

// 安全执行
echo shell_exec($cmd . ' localhost');