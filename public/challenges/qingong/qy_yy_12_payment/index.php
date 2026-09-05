<?php
/**
 * QY-YY-12 【青云宗·元婴】灵石篡改
 * 修真叙事：灵石交易可被篡改金额。
 * 漏洞类型：payment_tamper
 * 难度：L4
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·元婴】灵石篡改 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·元婴】灵石篡改</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 灵石交易可被篡改金额。
        </div>
        <form method="POST">
            <input type="hidden" name="item" value="sword">
            <input type="hidden" name="price" value="100">
            <button class="xxr-btn xxr-btn-primary">购买（100 灵石）</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 支付漏洞 - 金额篡改
            <hr>
            Flag 提交位置：<a href="/challenge/QY-YY-12" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-YY-12" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>