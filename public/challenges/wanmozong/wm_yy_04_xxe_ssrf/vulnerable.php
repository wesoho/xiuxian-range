<?php
/**
 * WM-YY-04 vulnerable.php - 漏洞演示
 * 分类：xxe_ssrf
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】XXE SSRF
$xml = $_POST['xml'] ?? '';
if ($xml !== '') {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadXML($xml, LIBXML_NOENT);  // 启用实体
    // 实体 SYSTEM "http://169.254.169.254/" 可访问内网
    echo $dom->saveXML();
} else {
    echo '<p class="text-muted">以 POST 提交 XML 报文进行试炼（例如：<code>&lt;?xml version="1.0"?&gt;&lt;r&gt;&amp;xxe;&lt;/r&gt;</code>）</p>';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('xxe');
