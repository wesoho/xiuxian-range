<?php
/**
 * WM-HS-14 vulnerable.php - 漏洞演示
 * 分类：php_variable
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】extract() 变量覆盖
$role = 'guest';
extract($_GET);  // 攻击者 ?role=admin 可覆盖
if ($role === 'admin') {
    echo '长老专属';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('phpweak');
