<?php
/**
 * QY-HS-03 vulnerable.php - 漏洞演示
 * 分类：jwt_kid
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】kid 字段路径穿越
$jwt = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$header = json_decode(base64_decode(explode('.', $jwt)[0] ?? ''), true);
$kid = $header['kid'] ?? '';  // kid=../../../etc/passwd 可读取文件作为密钥
echo "kid: $kid";
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('jwt');
