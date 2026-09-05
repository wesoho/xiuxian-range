<?php
/**
 * QY-HS-10 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：使用 HMAC
$secret = 'secret-key';
$signature = hash_hmac('sha256', $data, $secret);