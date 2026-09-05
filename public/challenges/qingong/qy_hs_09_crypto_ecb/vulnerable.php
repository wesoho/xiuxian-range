<?php
/**
 * QY-HS-09 vulnerable.php - 漏洞演示
 * 分类：crypto_ecb
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】ECB 模式块重排
$key = '1234567890123456';  // 固定密钥
$plaintext = $_POST['data'] ?? '';
$encrypted = openssl_encrypt($plaintext, 'aes-128-ecb', $key, OPENSSL_RAW_DATA, '');
echo bin2hex($encrypted);
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('crypto');
