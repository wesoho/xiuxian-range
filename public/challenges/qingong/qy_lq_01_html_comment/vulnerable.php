<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// QY-LQ-01 vulnerable.php - HTML 注释泄露
/**
 * 漏洞：在 HTML 注释中保留开发笔记、调试信息、TODO 等。
 * 真实案例：Stripe 曾因 HTML 注释泄露内部文档链接。
 */
?>
<!DOCTYPE html>
<html>
<body>
<h1>藏经阁</h1>
<!--
TODO: 移除这些调试注释
开发备注：API密钥藏在注释里
Flag: <?= xxr_challenge_flag() ?>
-->
</body>
</html>