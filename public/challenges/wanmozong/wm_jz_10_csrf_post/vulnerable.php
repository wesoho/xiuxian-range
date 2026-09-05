<?php
/**
 * WM-JZ-10 vulnerable.php - 漏洞演示
 * 分类：csrf_post
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】POST 操作无 CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer'])) {
    $to = $_POST['to'] ?? '';
    $amount = (float) ($_POST['amount'] ?? 0);
    echo "<p>已向 $to 转账 $amount 灵石</p>";
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('csrf');
