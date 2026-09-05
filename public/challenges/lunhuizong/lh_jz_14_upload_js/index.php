<?php
/**
 * LH-JZ-14 【轮回宗·筑基】上传心法
 * 修真叙事：轮回宗上传心法时只在前端检查格式。
 * 漏洞类型：upload_js
 * 难度：L2
 * 宗门：lunhuizong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·筑基】上传心法 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·筑基】上传心法</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗上传心法时只在前端检查格式。
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传心法 (.txt)</label>
                <input type="file" name="file" class="form-control" accept=".txt">
            </div>
            <button class="xxr-btn xxr-btn-primary">上传</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 文件上传 JS 前端校验绕过
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JZ-14" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JZ-14" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>