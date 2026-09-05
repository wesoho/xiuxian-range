<?php
/**
 * WM-HS-01 【万魔宗·化神】无相法印
 * 修真叙事：化神期可用无相法印（alg=none）伪造身份。
 * 漏洞类型：jwt_none
 * 难度：L5
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·化神】无相法印 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·化神】无相法印</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 化神期可用无相法印（alg=none）伪造身份。
        </div>
        <p>伪造 JWT Token（alg=none）。</p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> JWT alg:none 攻击
            <hr>
            Flag 提交位置：<a href="/challenge/WM-HS-01" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-HS-01" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>