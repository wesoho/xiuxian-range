<?php
/**
 * QY-JD-03 vulnerable.php - 漏洞演示
 * 分类：csrf_token
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】CSRF Token 可预测
session_start();
$token = $_SESSION['csrf_token'] ?? md5(time());  // 基于时间，可预测
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['csrf'] ?? '') === $token) {
        echo '转账成功';
    } else {
        echo 'Token 错误：' . $token;  // 泄露 token
    }
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('csrf');
