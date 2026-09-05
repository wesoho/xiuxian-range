<?php
/**
 * WM-HS-08 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：缓存键包含用户身份、严格过滤缓存内容
// Vary: Cookie / Authorization
header('Vary: Cookie, Authorization');
header("Cache-Control: no-cache, no-store, must-revalidate");