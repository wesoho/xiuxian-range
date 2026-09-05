<?php
/**
 * QY-JD-09 【青云宗·金丹】空间的缝隙
 * 修真叙事：青云宗过滤了空格，你可以利用其他字符代替。
 * 漏洞类型：rce_space
 * 难度：L3
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·金丹】空间的缝隙 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·金丹】空间的缝隙</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 青云宗过滤了空格，你可以利用其他字符代替。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">IP：</span>
                <input type="text" name="ip" class="form-control" placeholder="空格过滤绕过">
                <button class="xxr-btn xxr-btn-primary">测灵</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 命令注入 - 空格过滤绕过（<code>$IFS</code>、<code>%09</code>）
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JD-09" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JD-09" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>