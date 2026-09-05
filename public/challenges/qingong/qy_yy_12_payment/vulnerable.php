<?php
/**
 * QY-YY-12 vulnerable.php - 漏洞演示
 * 分类：payment_tamper
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】前端价格可篡改
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item'];
    $price = (float) $_POST['price'];  // 客户端可篡改
    echo "已购买 $item，价格 $price";
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('logic');
