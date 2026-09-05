<?php
// 修复：使用 firebase/php-jwt 库
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;

$secret = 'super-secret-key-32-chars-minimum-12345678';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? '';

    try {
        // 强制使用 HS256，禁用 alg=none
        $decoded = JWT::decode($token, new \Firebase\JWT\Key($secret, 'HS256'));
        echo "用户：{$decoded->user}, 角色：{$decoded->role}";
    } catch (Exception $e) {
        http_response_code(401);
        echo 'Invalid token';
    }
}