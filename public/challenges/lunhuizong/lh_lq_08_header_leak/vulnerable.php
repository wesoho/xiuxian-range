<?php
// LH-LQ-08 vulnerable.php - HTTP 响应头泄露
/**
 * 漏洞：暴露了过多信息的响应头
 */
header('X-Powered-By: PHP/8.2.0-DebugBuild');
header('X-Debug-Token: ' . md5($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_TIME']));
header('X-Backend-Server: internal-api-host.local');
// ... 真实环境中可能泄露：版本号、API密钥、内部地址、调试信息等