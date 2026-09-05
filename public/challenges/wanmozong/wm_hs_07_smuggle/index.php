<?php
/**
 * WM-HS-07 【万魔宗·化神】魔影分流
 * 修真叙事：万魔宗在多服务器间传递请求时可被走私。
 * 漏洞类型：http_smuggle
 * 难度：L5
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·化神】魔影分流 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·化神】魔影分流</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗在多服务器间传递请求时可被走私。
        </div>
        <p>HTTP 请求走私（CL-TE / TE-CL）。</p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> HTTP 请求走私（CL-TE）
            <hr>
            Flag 提交位置：<a href="/challenge/WM-HS-07" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-HS-07" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>