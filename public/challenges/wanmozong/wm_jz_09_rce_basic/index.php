<?php
/**
 * WM-JZ-09 【万魔宗·筑基】Ping 测灵根
 * 修真叙事：魔窟的测灵阵会根据你输入的 IP 来 ping 你，但可不止 ping 那么简单。
 * 漏洞类型：rce_basic
 * 难度：L2
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·筑基】Ping 测灵根 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·筑基】Ping 测灵根</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 魔窟的测灵阵会根据你输入的 IP 来 ping 你，但可不止 ping 那么简单。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">IP：</span>
                <input type="text" name="ip" class="form-control" placeholder="试试: 127.0.0.1; ls /" autofocus>
                <button class="xxr-btn xxr-btn-primary">测灵</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 命令注入基础。Payload: <code>;ls /</code>
            <hr>
            Flag 提交位置：<a href="/challenge/WM-JZ-09" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JZ-09" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>