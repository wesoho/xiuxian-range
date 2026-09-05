<?php
/**
 * QY-JD-02 【青云宗·金丹】咒语变形
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·金丹】咒语变形 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【青云宗·金丹】咒语变形</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 金丹真人会过滤 script 等关键字，你需要变形绕过。
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">XSS关键字过滤绕过</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>使用DOMPurify；CSP头限制脚本；输出转义</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JD-02" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>