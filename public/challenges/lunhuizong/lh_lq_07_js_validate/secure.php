<?php
// LH-LQ-07 secure.php - 服务端必须做校验
/**
 * 安全实践：
 * 1. 服务端**永远**要重新校验所有输入
 * 2. 前端校验仅作 UX 提示
 * 3. 验证失败时返回相同的错误信息（防用户名枚举）
 * 4. 使用 prepared statement 防 SQL 注入
 */
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// 服务端校验
if (empty($username) || empty($password) || strlen($username) > 50) {
    http_response_code(400);
    exit('参数错误');
}

// 哈希比对（即使后端数据库泄露也无影响）
$stmt = db()->pdo()->prepare('SELECT password_hash FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    echo 'OK';
} else {
    echo '认证失败';
}