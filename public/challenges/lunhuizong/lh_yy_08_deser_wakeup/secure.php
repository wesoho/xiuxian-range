<?php
// 修复：使用 allowed_classes + JSON 替代
class User {
    public $username;
    public $role = 'guest';
}

$data = $_POST['data'] ?? '';
// PHP 7+ allowed_classes 限制可反序列化类
$user = @unserialize($data, ['allowed_classes' => ['User']]);

if ($user instanceof User && $user->role === 'admin') {
    echo "Admin 用户：{$user->username}";
}

// 或更安全：使用 JSON
// $user = json_decode(file_get_contents('php://input'), true);