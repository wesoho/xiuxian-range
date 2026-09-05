<?php
/**
 * QY-JZ-12 【青云宗·筑基】留言板的诅咒
 * 修真叙事：留言板的咒语会被永久记住，伤害所有访问者。
 * 漏洞类型：xss_stored
 * 难度：L2
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·筑基】留言板的诅咒 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·筑基】留言板的诅咒</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 留言板的咒语会被永久记住，伤害所有访问者。
        </div>
        <form method="POST" class="mb-4">
            <textarea name="content" class="form-control" rows="3" placeholder="留言..."></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交留言</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 存储型 XSS。留言会被永久保存
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JZ-12" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JZ-12" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>