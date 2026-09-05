<?php
/**
 * QY-YY-06 vulnerable.php - 漏洞演示
 * 分类：ssrf_protocol
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】gopher:// 攻击内网 Redis
$url = $_GET['url'] ?? '';
if ($url !== '') {
    $urlParsed = parse_url($url);
    $scheme = is_array($urlParsed) ? ($urlParsed['scheme'] ?? '') : '';
    try {
        echo file_get_contents($url);
    } catch (\Throwable $e) {
        echo '请求失败：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
} else {
    echo '<p class="text-muted">以 ?url= 参数提交 gopher:// 等协议地址进行试炼。</p>';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('ssrf');
