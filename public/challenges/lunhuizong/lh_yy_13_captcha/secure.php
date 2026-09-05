<?php
/**
 * LH-YY-13 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：验证码一次性使用 + 过期时间
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['captcha'] ?? '') !== ($_SESSION['captcha'] ?? '')) {
        echo '验证码错误';
    } else {
        echo '提交成功';
        unset($_SESSION['captcha']);  // 一次性
    }
}
// 加上过期（5分钟）
if (isset($_SESSION['captcha_time']) && time() - $_SESSION['captcha_time'] > 300) {
    unset($_SESSION['captcha']);
}