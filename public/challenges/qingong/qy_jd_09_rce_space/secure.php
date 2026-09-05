<?php
// 修复：白名单 IP
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) exit('Invalid IP');
echo shell_exec('ping -c 1 ' . escapeshellarg($ip));