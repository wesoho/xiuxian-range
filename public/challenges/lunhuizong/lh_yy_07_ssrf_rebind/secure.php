<?php
/**
 * LH-YY-07 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：解析 URL 后重新检查 host
$host = parse_url($url, PHP_URL_HOST);
$ip = gethostbyname($host);
if (isPrivateIP($ip)) exit('Private IP blocked');
echo file_get_contents($url);

function isPrivateIP($ip) {
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
}