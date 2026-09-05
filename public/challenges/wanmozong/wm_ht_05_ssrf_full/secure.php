<?php
// 【修复/安全】综合 SSRF 防御：白名单 + 协议限制
$url = $_GET['url'] ?? '';
$parsed = parse_url($url);

// 1. 白名单域名
$allowed = ['xiuxian-range.local'];
if (!in_array($parsed['host'] ?? '', $allowed, true)) {
    exit('Domain not allowed');
}

// 2. 禁用危险协议
$blocked = ['gopher', 'dict', 'ldap', 'file', 'ftp'];
if (in_array($parsed['scheme'] ?? '', $blocked, true)) {
    exit('Protocol not allowed');
}

// 3. 解析后再次检查 IP（防 DNS rebinding）
$ip = gethostbyname($parsed['host']);
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
    exit('Private IP blocked');
}