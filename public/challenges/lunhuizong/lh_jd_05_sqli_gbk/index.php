<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-JD-05 【轮回宗·金丹】宽字节迷阵
 * 修真叙事：轮回宗使用 GBK 编码，引号会被吞掉。
 * 漏洞类型：sqli_gbk
 * 难度：L3
 * 宗门：lunhuizong
 */
if (xxr_db_driver() === 'sqlite') {
    $sqliGbkPayload = "1' OR 1=1-- -";
    $sqliGbkNote = xxr_driver_note('当前为 SQLite 演示环境：SQLite 恒为 UTF-8，无 GBK 宽字节特性。但 SQLite 字符串中反斜杠<strong>不是转义符</strong>，addslashes 根本不构成防护——普通单引号即可注入，这正是 addslashes 只在 MySQL 下近似可靠的教训。宽字节手法本身需 MySQL（GBK）环境体验。');
} else {
    $sqliGbkPayload = '1%bf%27 OR 1=1-- -';
    $sqliGbkNote = '';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·金丹】宽字节迷阵 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·金丹】宽字节迷阵</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗使用 GBK 编码，引号会被吞掉。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="试试: <?= e($sqliGbkPayload) ?>">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <?= $sqliGbkNote ?>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> <?= xxr_db_driver() === 'sqlite'
                ? 'addslashes 失效（SQLite 中反斜杠非转义符）。'
                : '宽字节注入。' ?>Payload: <code><?= e($sqliGbkPayload) ?></code>
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JD-05" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JD-05" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>