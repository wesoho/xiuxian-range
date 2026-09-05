<?php
/**
 * QY-YY-05 【青云宗·元婴】元神出窍
 * 修真叙事：元婴期可以元神出窍，访问内网。
 * 漏洞类型：ssrf_basic
 * 难度：L4
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·元婴】元神出窍 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·元婴】元神出窍</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 元婴期可以元神出窍，访问内网。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">URL：</span>
                <input type="text" name="url" class="form-control" placeholder="file:///etc/passwd">
                <button class="xxr-btn xxr-btn-primary">拉取</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> SSRF 基础。访问 file://, gopher://
            <hr>
            Flag 提交位置：<a href="/challenge/QY-YY-05" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-YY-05" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>