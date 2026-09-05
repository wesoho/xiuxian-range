<?php
/**
 * LH-YY-01 【轮回宗·元婴】DOM幻象
 * 修真叙事：轮回宗在客户端 DOM 中动态渲染，存在 DOM XSS。
 * 漏洞类型：xss_dom
 * 难度：L4
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·元婴】DOM幻象 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·元婴】DOM幻象</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗在客户端 DOM 中动态渲染，存在 DOM XSS。
        </div>
        <p>DOM XSS 通过修改 URL hash 触发：<code>#&lt;img src=x onerror=alert(1)&gt;</code></p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> DOM XSS。通过 URL fragment 触发
            <hr>
            Flag 提交位置：<a href="/challenge/LH-YY-01" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-YY-01" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>