<?php
// 修复：白名单 URL
$allowed = ['/home', '/about', '/contact', '/challenges'];
$url = $_GET['url'] ?? '/';
if (!in_array($url, $allowed, true)) {
    http_response_code(400);
    exit('URL not allowed');
}
header("Location: $url");