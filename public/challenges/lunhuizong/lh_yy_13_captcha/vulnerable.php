<?php
/**
 * LH-YY-13 vulnerable.php - 漏洞演示
 * 分类：captcha_reuse
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】验证码不失效
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['captcha'] === ($_SESSION['captcha'] ?? '')) {
        echo '提交成功';
        // 未清空 $_SESSION['captcha']
    } else {
        echo '验证码错误';
    }
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('logic');
