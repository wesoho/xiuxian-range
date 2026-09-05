<?php
/**
 * QY-JZ-01 【青云宗·筑基】练功房的咒语
 * 修真叙事：练功房的墙上刻着前辈的咒语，你输入的话会被原封不动地回显出来。
 * 漏洞类型：xss_reflected
 * 难度：L2
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·筑基】练功房的咒语 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·筑基】练功房的咒语</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 练功房的墙上刻着前辈的咒语，你输入的话会被原封不动地回显出来。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">输入：</span>
                <input type="text" name="msg" class="form-control" autofocus>
                <button class="xxr-btn xxr-btn-primary">提交</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 反射型 XSS。在 URL 参数注入 <code>&lt;script&gt;alert(1)&lt;/script&gt;</code>
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JZ-01" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JZ-01" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>