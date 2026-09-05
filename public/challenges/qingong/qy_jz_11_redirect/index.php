<?php
/**
 * QY-JZ-11 【青云宗·筑基】传送门的诡计
 * 修真叙事：青云宗有一个传送门会跳转到任意地方。
 * 漏洞类型：open_redirect
 * 难度：L2
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·筑基】传送门的诡计 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·筑基】传送门的诡计</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 青云宗有一个传送门会跳转到任意地方。
        </div>
        <p>点击下方按钮跳转：</p>
        <a href="?url=https://example.com" class="xxr-btn xxr-btn-primary">跳转</a>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> URL 重定向
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JZ-11" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JZ-11" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>