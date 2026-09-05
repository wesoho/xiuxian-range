<?php
/**
 * WM-YY-15 【万魔宗·元婴】魔锤试炼
 * 修真叙事：万魔宗大门无任何限制，可以无限尝试。
 * 漏洞类型：brute_force
 * 难度：L4
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·元婴】魔锤试炼 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·元婴】魔锤试炼</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗大门无任何限制，可以无限尝试。
        </div>
        <form method="POST">
            <input type="text" name="username" class="form-control" placeholder="用户名">
            <input type="password" name="password" class="form-control mt-2" placeholder="密码">
            <button class="xxr-btn xxr-btn-primary mt-2">登录</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 暴力破解 - 无锁定
            <hr>
            Flag 提交位置：<a href="/challenge/WM-YY-15" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-YY-15" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>