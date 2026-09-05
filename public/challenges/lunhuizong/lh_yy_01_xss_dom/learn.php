<?php
/**
 * LH-YY-01 【轮回宗·元婴】DOM幻象
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·元婴】DOM幻象 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【轮回宗·元婴】DOM幻象</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 修真靶场剧情
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">XSS DOM型</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>使用textContent而非innerHTML；CSP禁止内联脚本</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-YY-01" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>