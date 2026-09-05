<?php
/**
 * WM-JD-13 【万魔宗·金丹】灵识伪装
 * 修真叙事：万魔宗灵识伪装术：上传时只检查 MIME 类型。
 * 漏洞类型：upload_mime
 * 难度：L3
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·金丹】灵识伪装 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·金丹】灵识伪装</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗灵识伪装术：上传时只检查 MIME 类型。
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传文件</label>
                <input type="file" name="file" class="form-control">
            </div>
            <button class="xxr-btn xxr-btn-primary">上传</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 文件上传 MIME 类型绕过
            <hr>
            Flag 提交位置：<a href="/challenge/WM-JD-13" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JD-13" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>