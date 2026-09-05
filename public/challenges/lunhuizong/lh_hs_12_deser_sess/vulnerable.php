<?php
/**
 * LH-HS-12 vulnerable.php - 漏洞演示
 * 分类：deserialize_session
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// LH-HS-12 通用漏洞演示（deserialize_session）
echo '待实现具体漏洞逻辑';
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('deser');
