<?php
// 漏洞：高级缓存投毒
$path = $_GET['page'] ?? 'index.html';
// Varnish 缓存键包含 page 但不包含 User-Agent
// 攻击者通过 X-Forwarded-Host 投毒
header("X-Cache: MISS");
echo file_get_contents("cache/$path");
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('poison');
