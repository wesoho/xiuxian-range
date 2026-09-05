<?php
/**
 * LH-YY-02 【轮回宗·元婴】盗取灵识
 * 修真叙事：通过 XSS 偷取其他弟子的灵识 Cookie。
 * 漏洞类型：xss_cookie
 * 难度：L4
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·元婴】盗取灵识 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·元婴】盗取灵识</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 通过 XSS 偷取其他弟子的灵识 Cookie。
        </div>
        <p>利用 XSS 窃取其他弟子的 Cookie（教学演示）。</p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> XSS 窃取 Cookie（教学演示）
            <hr>
            Flag 提交位置：<a href="/challenge/LH-YY-02" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-YY-02" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>