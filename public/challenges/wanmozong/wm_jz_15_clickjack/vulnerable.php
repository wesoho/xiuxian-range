<?php
/**
 * WM-JZ-15 vulnerable.php - 漏洞演示
 * 分类：clickjacking
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】未设置 X-Frame-Options
?>
<button>确认</button>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('clickjack'); ?>
