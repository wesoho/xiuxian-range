<?php
/**
 * LH-YY-07 vulnerable.php - 漏洞演示
 * 分类：ssrf_rebind
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】DNS rebinding 绕过
$url = $_GET['url'] ?? '';
if ($url !== '') {
    // 保持漏洞语义：直接请求用户提供的地址（演示环境对不可达目标回显错误）
    try {
        echo file_get_contents($url);
    } catch (\Throwable $e) {
        echo '请求失败：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
} else {
    echo '<p class="text-muted">以 ?url= 参数提交目标地址进行试炼。</p>';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('ssrf');
