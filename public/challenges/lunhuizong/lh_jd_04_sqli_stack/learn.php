<?php
/**
 * LH-JD-04 【轮回宗·金丹】轮回双咒
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·金丹】轮回双咒 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【轮回宗·金丹】轮回双咒</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 轮回宗允许同时执行多个咒语（堆叠查询）。
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">SQL堆叠注入</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>PDO禁用multi_query；最小权限数据库账户；WAF拦截分号</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JD-04" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>