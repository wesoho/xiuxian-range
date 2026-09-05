<?php
/**
 * LH-YY-14 【轮回宗·元婴】强行改命
 * 修真叙事：轮回宗可以强行修改他人的命运（密码）。
 * 漏洞类型：password_reset
 * 难度：L4
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·元婴】强行改命 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·元婴】强行改命</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗可以强行修改他人的命运（密码）。
        </div>
        <form method="POST">
            <input type="email" name="email" class="form-control" placeholder="输入邮箱">
            <button class="xxr-btn xxr-btn-primary mt-2">重置密码</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 任意密码重置
            <hr>
            Flag 提交位置：<a href="/challenge/LH-YY-14" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-YY-14" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>