<?php
/**
 * QY-JD-09 【青云宗·金丹】空间的缝隙
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·金丹】空间的缝隙 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【青云宗·金丹】空间的缝隙</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 青云宗过滤了空格，你可以利用其他字符代替。
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">命令注入空格过滤绕过</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>白名单命令参数；PHP禁用危险函数（escapeshellarg + escapeshellcmd）</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JD-09" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>