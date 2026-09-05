<?php
/**
 * LH-JD-12 【轮回宗·金丹】PHP之源
 * 修真叙事：轮回宗用 PHP 伪协议读取源码。
 * 漏洞类型：lfi_filter
 * 难度：L3
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·金丹】PHP之源 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·金丹】PHP之源</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗用 PHP 伪协议读取源码。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">文件：</span>
                <input type="text" name="file" class="form-control" placeholder="php://filter/convert.base64-encode/resource=index.php">
                <button class="xxr-btn xxr-btn-primary">读取</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> php://filter 读源码
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JD-12" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JD-12" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>