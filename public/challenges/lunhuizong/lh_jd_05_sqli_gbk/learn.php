<?php
/**
 * LH-JD-05 【轮回宗·金丹】宽字节迷阵
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·金丹】宽字节迷阵 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【轮回宗·金丹】宽字节迷阵</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 轮回宗使用 GBK 编码，引号会被吞掉。
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">SQL注入宽字节绕过</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>使用UTF-8字符集；用addslashes前先检查字符集；推荐参数化</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JD-05" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>