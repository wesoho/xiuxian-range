<?php
/**
 * QY-HS-09 【青云宗·化神】古典加密
 * 修真叙事：青云宗使用 ECB 模式加密，加密等同于明文块重排。
 * 漏洞类型：crypto_ecb
 * 难度：L5
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·化神】古典加密 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·化神】古典加密</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 青云宗使用 ECB 模式加密，加密等同于明文块重排。
        </div>
        <p>AES-ECB 模式加密（块重排攻击）。</p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> AES-ECB 模式利用
            <hr>
            Flag 提交位置：<a href="/challenge/QY-HS-09" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-HS-09" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>