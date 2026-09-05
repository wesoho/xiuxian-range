<?php
/**
 * LH-JD-06 【轮回宗·金丹】二次重生
 * 修真叙事：轮回宗会让你重生于第二次注册时。
 * 漏洞类型：sqli_second
 * 难度：L3
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·金丹】二次重生 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·金丹】二次重生</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗会让你重生于第二次注册时。
        </div>
        <p class="text-muted">先去注册一个用户名含 SQL 语句的账号，再触发查询。</p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 二次注入。先注册恶意用户名，再触发查询
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JD-06" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JD-06" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>