<?php
/**
 * QY-HS-10 vulnerable.php - 漏洞演示
 * 分类：crypto_hash_ext
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】未使用 HMAC
$secret = 'secret_key';
$data = $_POST['data'] ?? '';
$signature = $_POST['sig'] ?? '';
$expected = md5($secret . $data);  // 易受长度扩展
if ($signature === $expected) {
    echo '签名验证通过';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('crypto');
