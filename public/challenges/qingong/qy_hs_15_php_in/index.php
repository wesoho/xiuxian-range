<?php
/**
 * QY-HS-15 【青云宗·化神】in_array 陷阱
 * 修真叙事：in_array 第三个参数默认为 false，导致类型转换绕过。
 * 漏洞类型：php_in_array
 * 难度：L5
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·化神】in_array 陷阱 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·化神】in_array 陷阱</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> in_array 第三个参数默认为 false，导致类型转换绕过。
        </div>
        <p>in_array 弱比较绕过。</p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> PHP in_array 弱比较
            <hr>
            Flag 提交位置：<a href="/challenge/QY-HS-15" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-HS-15" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>