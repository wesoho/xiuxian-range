<?php
/**
 * QY-HS-15 vulnerable.php - 漏洞演示
 * 分类：php_in_array
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】in_array 弱比较
$role = $_GET['role'] ?? 'guest';
if (in_array($role, ['admin', 'user'])) {  // 第三个参数默认为 false（弱比较）
    // 'admin1' == 'admin' 为真
    echo '允许访问';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('phpweak');
