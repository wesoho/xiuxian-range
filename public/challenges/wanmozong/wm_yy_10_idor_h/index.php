<?php
/**
 * WM-YY-10 【万魔宗·元婴】借物偷看
 * 修真叙事：万魔宗弟子可以查看其他弟子的信息。
 * 漏洞类型：idor_horizontal
 * 难度：L4
 * 宗门：wanmozong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·元婴】借物偷看 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·元婴】借物偷看</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗弟子可以查看其他弟子的信息。
        </div>
        <p>查看其他弟子的订单（修改 URL 中的 id 参数）。</p>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 水平越权（IDOR）。修改 ID 访问他人数据
            <hr>
            Flag 提交位置：<a href="/challenge/WM-YY-10" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-YY-10" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>