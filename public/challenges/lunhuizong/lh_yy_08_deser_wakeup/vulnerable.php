<?php
// 反序列化 __wakeup 绕过
class User {
    public $username;
    public $role = 'guest';

    public function __wakeup() {
        // 【漏洞】__wakeup 可以绕过
        if ($this->role !== 'admin') {
            $this->role = 'guest';
        }
    }
}

$data = $_POST['data'] ?? '';
$user = @unserialize($data);

if ($user instanceof User) {
    echo "用户：{$user->username} | 角色：{$user->role}";
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('deser');
