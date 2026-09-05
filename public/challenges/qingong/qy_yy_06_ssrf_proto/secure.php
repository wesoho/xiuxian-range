<?php
/**
 * QY-YY-06 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：禁用危险协议
$blocked = ['gopher', 'dict', 'ldap', 'file'];
$scheme = parse_url($url, PHP_URL_SCHEME);
if (in_array($scheme, $blocked)) exit('Scheme not allowed');