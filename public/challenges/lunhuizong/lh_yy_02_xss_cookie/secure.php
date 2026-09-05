<?php
/**
 * LH-YY-02 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：HttpOnly Cookie + SameSite
session_start();
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => true,
]);