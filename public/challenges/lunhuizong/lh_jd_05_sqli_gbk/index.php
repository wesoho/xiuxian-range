<?php
/**
 * LH-JD-05 【轮回宗·金丹】宽字节迷阵
 * 修真叙事：轮回宗使用 GBK 编码，引号会被吞掉。
 * 漏洞类型：sqli_gbk
 * 难度：L3
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·金丹】宽字节迷阵 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·金丹】宽字节迷阵</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗使用 GBK 编码，引号会被吞掉。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="宽字节: %bf%27">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 宽字节注入。Payload: <code>1%bf%27 OR 1=1-- -</code>
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JD-05" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JD-05" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>