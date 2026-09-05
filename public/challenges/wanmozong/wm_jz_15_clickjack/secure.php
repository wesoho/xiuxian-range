<?php
/**
 * WM-JZ-15 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：禁止 iframe 嵌入
header('X-Frame-Options: DENY');
header("Content-Security-Policy: frame-ancestors 'none'");
?>
<button>确认</button>