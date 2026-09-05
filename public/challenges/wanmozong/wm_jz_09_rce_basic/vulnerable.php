<?php
/**
 * WM-JZ-09 vulnerable.php - 漏洞演示
 * 分类：rce_basic
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】直接拼接命令
$ip = $_GET['ip'] ?? '127.0.0.1';
system("ping -c 1 $ip");  // 命令注入
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('rce');
