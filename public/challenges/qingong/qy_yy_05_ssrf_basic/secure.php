<?php
// 修复：白名单
$allowed = ['https://xiuxian-range.local'];
$url = $_GET['url'] ?? '';
$host = parse_url($url, PHP_URL_HOST);
if (!in_array($host, $allowed, true)) exit('URL not allowed');

$content = @file_get_contents($url);
echo htmlspecialchars($content);