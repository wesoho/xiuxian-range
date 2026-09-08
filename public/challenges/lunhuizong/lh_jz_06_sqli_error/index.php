<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-JZ-06 【轮回宗·筑基】幽冥报错
 * 修真叙事：幽冥之地会把一切错误放大，让你看清 SQL 语句。
 * 漏洞类型：sqli_error
 * 难度：L2
 * 宗门：lunhuizong
 */
if (xxr_db_driver() === 'sqlite') {
    $sqliErrorPayload = "1'";
    $sqliErrorNote = xxr_driver_note('当前为 SQLite 演示环境：报错取数手法（<code>extractvalue</code>/<code>updatexml</code>）为 MySQL 专属，SQLite 无等价函数。当前环境可体验<strong>报错回显</strong>：提交 <code>1\'</code> 观察语法报错，或提交不存在的函数（如 <code>foo()</code>）观察 <code>no such function</code>——这正是判断后端数据库类型的指纹。完整报错注入体验请使用 Docker MySQL 环境。');
} else {
    $sqliErrorPayload = "1' AND extractvalue(1,concat(0x7e,version()))-- -";
    $sqliErrorNote = '';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·筑基】幽冥报错 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·筑基】幽冥报错</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 幽冥之地会把一切错误放大，让你看清 SQL 语句。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="试试: <?= e($sqliErrorPayload) ?>">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <?= $sqliErrorNote ?>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 报错注入。<?= xxr_db_driver() === 'sqlite'
                ? '报错取数为 MySQL 专属手法，SQLite 无等价函数；可体验报错回显与后端数据库指纹。'
                : '利用 <code>extractvalue</code> / <code>updatexml</code> 触发错误回显' ?>
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JZ-06" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JZ-06" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>