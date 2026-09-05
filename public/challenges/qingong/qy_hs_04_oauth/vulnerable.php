<?php
/**
 * QY-HS-04 vulnerable.php - 漏洞演示
 * 分类：oauth_redirect
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】redirect_uri 未严格校验
$redirect = $_GET['redirect_uri'] ?? '';
header("Location: $redirect");
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('redirect');
