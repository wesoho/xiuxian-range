<?php
/**
 * LH-JD-11 【轮回宗·金丹】轮回之眼
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·金丹】轮回之眼 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【轮回宗·金丹】轮回之眼</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 轮回宗的眼睛能看到任何文件路径。
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">LFI目录穿越</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>白名单文件包含；realpath规范化；open_basedir限制</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JD-11" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>