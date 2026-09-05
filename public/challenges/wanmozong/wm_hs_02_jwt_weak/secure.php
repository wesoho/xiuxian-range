<?php
// 修复：使用 32+ 字节的强密钥
$secret = bin2hex(random_bytes(32));  // 64 字符 hex
// 或使用环境变量管理密钥
$secret = getenv('JWT_SECRET');