<?php
/**
 * QY-YY-11 vulnerable.php - 漏洞演示
 * 分类：idor_vertical
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】未做角色校验
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '请先登录';
} else {
    echo "长老禁地（应只有 admin 可访问）";
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('logic');
