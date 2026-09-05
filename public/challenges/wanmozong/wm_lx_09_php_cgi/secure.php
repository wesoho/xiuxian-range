<?php
// 修复：
// 1. 升级 PHP 8.x（已修复 CVE-2024-4577）
// 2. 使用 PHP-FPM 替代 PHP-CGI
// 3. 配置 cgi.fix_pathinfo=0
// 4. NginX/Apache 拒绝畸形请求
header('X-Frame-Options: DENY');