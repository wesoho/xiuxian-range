<?php
/**
 * WM-JD-07 【万魔宗·金丹】禁咒过滤
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·金丹】禁咒过滤 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【万魔宗·金丹】禁咒过滤</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 万魔宗过滤了 union/select 等关键字。
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">SQL关键字过滤绕过</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>不依赖黑名单；使用参数化查询；WAF深度检测</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JD-07" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>