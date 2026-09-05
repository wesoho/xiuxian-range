<?php
/**
 * WM-JZ-15 【万魔宗·筑基】无形之框
 * 修真叙事：万魔宗用一个看不见的框罩住点击按钮，劫持用户操作。
 * 漏洞类型：clickjacking
 * 难度：L2
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·筑基】无形之框 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·筑基】无形之框</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗用一个看不见的框罩住点击按钮，劫持用户操作。
        </div>
        <p>透明 iframe + 诱饵按钮劫持点击。</p>
        <button class="xxr-btn xxr-btn-primary">确认</button>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 点击劫持
            <hr>
            Flag 提交位置：<a href="/challenge/WM-JZ-15" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JZ-15" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>