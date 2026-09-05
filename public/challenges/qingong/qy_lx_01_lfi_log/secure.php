<?php
// 修复：白名单文件包含
$allowed = ['home', 'about', 'contact'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed, true)) {
    http_response_code(403);
    exit('File not allowed');
}

// 防止日志投毒：过滤 User-Agent 中的特殊字符
if (preg_match('/<\?|<script/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
    http_response_code(400);
    exit('Invalid User-Agent');
}

include __DIR__ . '/pages/' . $file . '.php';