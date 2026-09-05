<?php
/**
 * WM-JZ-08 【万魔宗·筑基】时光咒
 * 修真叙事：万魔宗有时会用时光咒让一切停摆。利用这种停顿来推断秘密。
 * 漏洞类型：sqli_time
 * 难度：L2
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·筑基】时光咒 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·筑基】时光咒</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗有时会用时光咒让一切停摆。利用这种停顿来推断秘密。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">用户名：</span>
                <input type="text" name="name" class="form-control" placeholder="试试: admin' AND SLEEP(5)-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 时间盲注。利用 <code>SLEEP()</code> 触发响应延迟
            <hr>
            Flag 提交位置：<a href="/challenge/WM-JZ-08" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JZ-08" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>