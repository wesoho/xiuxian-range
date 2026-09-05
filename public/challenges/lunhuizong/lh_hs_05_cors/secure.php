<?php
// 修复：精确 CORS 白名单
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['https://xiuxian-range.local'];

if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}