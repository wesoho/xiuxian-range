<?php
/**
 * LH-JZ-07 【轮回宗·筑基】真言之试
 * 修真叙事：轮回殿只回应真假两种答复，你需要用真假来推断秘密。
 * 漏洞类型：sqli_bool
 * 难度：L2
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·筑基】真言之试 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·筑基】真言之试</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回殿只回应真假两种答复，你需要用真假来推断秘密。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">用户名：</span>
                <input type="text" name="name" class="form-control" placeholder="试试: admin' AND 1=1-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 布尔盲注。根据页面真假判断条件
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JZ-07" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JZ-07" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>