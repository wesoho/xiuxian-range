<?php
/**
 * QY-JD-03 【青云宗·金丹】令牌之谜
 * 修真叙事：CSRF Token 可以被预测或泄露。
 * 漏洞类型：csrf_token
 * 难度：L3
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·金丹】令牌之谜 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·金丹】令牌之谜</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> CSRF Token 可以被预测或泄露。
        </div>
        <form method="POST">
            <input type="hidden" name="transfer" value="1">
            <button class="xxr-btn xxr-btn-primary">转账</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> CSRF Token 可预测/泄露
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JD-03" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JD-03" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>