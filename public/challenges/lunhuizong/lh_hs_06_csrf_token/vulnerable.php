<?php
/**
 * LH-HS-06 vulnerable.php - 漏洞演示
 * 分类：csrf_token_bypass
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// LH-HS-06 通用漏洞演示（csrf_token_bypass）
echo '待实现具体漏洞逻辑';
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('csrf');
