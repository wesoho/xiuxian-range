<?php
/**
 * QY-HS-09 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：使用 CBC/GCM 模式
$key = random_bytes(32);
$iv = random_bytes(16);
$encrypted = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);