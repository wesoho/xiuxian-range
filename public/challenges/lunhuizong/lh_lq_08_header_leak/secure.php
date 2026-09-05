<?php
// LH-LQ-08 secure.php - 安全实践
/**
 * 安全实践：
 * 1. 生产环境移除/替换 Server、X-Powered-By 头
 * 2. 不要在响应头中放置 token、密钥、调试信息
 * 3. Apache: ServerTokens Prod / Nginx: server_tokens off
 * 4. 使用 mod_security 移除敏感头
 */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
// 不暴露 X-Powered-By
header_remove('X-Powered-By');