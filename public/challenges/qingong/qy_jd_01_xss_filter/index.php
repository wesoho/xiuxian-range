<?php
/**
 * QY-JD-01 【青云宗·金丹】金光的过滤
 * 修真叙事：金丹期的咒语会过滤一些关键字，但你可以用编码绕过。
 * 漏洞类型：xss_filter
 * 难度：L3
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·金丹】金光的过滤 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·金丹】金光的过滤</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 金丹期的咒语会过滤一些关键字，但你可以用编码绕过。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">输入：</span>
                <input type="text" name="msg" class="form-control" autofocus>
                <button class="xxr-btn xxr-btn-primary">提交</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> XSS 过滤绕过（HTML 实体、URL 编码）
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JD-01" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JD-01" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>