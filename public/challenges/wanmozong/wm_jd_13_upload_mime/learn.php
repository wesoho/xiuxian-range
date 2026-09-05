<?php
/**
 * WM-JD-13 【万魔宗·金丹】灵识伪装
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·金丹】灵识伪装 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【万魔宗·金丹】灵识伪装</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 万魔宗灵识伪装术：上传时只检查 MIME 类型。
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">文件上传MIME绕过</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>使用mime_content_type()而非客户端Content-Type；白名单；重命名文件</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JD-13" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>