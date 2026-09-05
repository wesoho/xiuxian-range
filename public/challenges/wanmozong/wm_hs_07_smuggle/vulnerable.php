<?php
/**
 * WM-HS-07 vulnerable.php - 漏洞演示
 * 分类：http_smuggle
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 教学演示：HTTP 请求走私
// 实际需要中间人代理，此处仅展示原理
echo "HTTP/1.1 200 OK\r\nContent-Length: 0\r\n\r\n";
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('smuggle');
