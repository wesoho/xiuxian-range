<?php
// 修复：白名单 + 路径规范化
$allowedDir = realpath(__DIR__ . '/data');
$file = $_GET['file'] ?? '';
$realPath = realpath($allowedDir . '/' . $file);

if (!$realPath || !str_starts_with($realPath, $allowedDir)) {
    http_response_code(403);
    exit('Forbidden');
}

echo htmlspecialchars(file_get_contents($realPath));