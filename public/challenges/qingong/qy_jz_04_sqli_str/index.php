<?php
/**
 * QY-JZ-04 【青云宗·筑基】丹方的字符咒语
 * 修真叙事：这次丹方名称是字符串，需要闭合引号才能注入。
 * 漏洞类型：sqli_string
 * 难度：L2
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·筑基】丹方的字符咒语 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·筑基】丹方的字符咒语</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 这次丹方名称是字符串，需要闭合引号才能注入。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">弟子名：</span>
                <input type="text" name="name" class="form-control" placeholder="试试: ' OR '1'='1" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 字符型 SQL 注入。Payload: <code>xxx&#39; OR &#39;1&#39;=&#39;1</code>
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JZ-04" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JZ-04" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>