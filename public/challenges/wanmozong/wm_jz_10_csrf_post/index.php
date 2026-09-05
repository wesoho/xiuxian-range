<?php
/**
 * WM-JZ-10 【万魔宗·筑基】魔影传书
 * 修真叙事：万魔宗的弟子可以不知不觉地替别人提交表单。
 * 漏洞类型：csrf_post
 * 难度：L2
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·筑基】魔影传书 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·筑基】魔影传书</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗的弟子可以不知不觉地替别人提交表单。
        </div>
        <form method="POST">
            <input type="hidden" name="amount" value="999">
            <button class="xxr-btn xxr-btn-primary">提交转账</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> POST 型 CSRF。构造自动提交表单
            <hr>
            Flag 提交位置：<a href="/challenge/WM-JZ-10" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JZ-10" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>