<?php
/**
 * WM-YY-03 【万魔宗·元婴】魔影重重
 * 修真叙事：万魔宗弟子可以解析外部实体读取文件。
 * 漏洞类型：xxe_file
 * 难度：L4
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·元婴】魔影重重 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·元婴】魔影重重</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗弟子可以解析外部实体读取文件。
        </div>
        <form method="POST" class="mb-4">
            <textarea name="xml" class="form-control" rows="5" placeholder="XML payload"></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> XXE 文件读取
            <hr>
            Flag 提交位置：<a href="/challenge/WM-YY-03" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-YY-03" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>