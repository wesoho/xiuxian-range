<?php
/**
 * QY-JD-15 【青云宗·金丹】金身绘像
 * 修真叙事：青云宗只接受图片，但实际上可以藏入 PHP 代码。
 * 漏洞类型：upload_image
 * 难度：L3
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·金丹】金身绘像 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·金丹】金身绘像</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 青云宗只接受图片，但实际上可以藏入 PHP 代码。
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传图片（jpg/png/gif）</label>
                <input type="file" name="file" class="form-control" accept="image/*">
            </div>
            <button class="xxr-btn xxr-btn-primary">上传</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 文件上传 图片马
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JD-15" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JD-15" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>