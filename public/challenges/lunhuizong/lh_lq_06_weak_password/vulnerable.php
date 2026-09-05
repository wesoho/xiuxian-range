<?php
// LH-LQ-06 vulnerable.php - 弱口令
/**
 * 漏洞：使用明文存储的用户名/密码列表，未做任何防护。
 * 真实案例：Mirai 僵尸网络就是利用 IoT 设备的弱口令。
 */
$weakAccounts = [
    'admin' => 'admin',
    'root'  => 'root',
    'user'  => 'user',
    'test'  => 'test123',
];

if (isset($weakAccounts[$_POST['username']]) && $weakAccounts[$_POST['username']] === $_POST['password']) {
    echo '登录成功';
}