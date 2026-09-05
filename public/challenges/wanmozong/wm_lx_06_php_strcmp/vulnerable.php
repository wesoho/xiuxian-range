<?php
// 漏洞：strcmp 数组绕过
$password = $_POST['password'] ?? '';
if (strcmp($password, 'secret') == 0) {  // 传入数组返回 NULL == 0 为真
    echo '登录成功';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('phpweak');
