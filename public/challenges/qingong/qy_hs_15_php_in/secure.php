<?php
/**
 * QY-HS-15 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：第三个参数传 true（严格模式）
if (in_array($role, ['admin', 'user'], true)) {
    echo '允许访问';
}