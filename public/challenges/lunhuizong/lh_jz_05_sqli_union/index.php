<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-JZ-05 【轮回宗·筑基】联合试炼
 * 修真叙事：轮回宗的试炼需要你用 UNION 联结两个查询结果。
 * 漏洞类型：sqli_union
 * 难度：L2
 * 宗门：lunhuizong
 */
$sqliUnionPayload = xxr_driver_pick(
    "1' UNION SELECT 1,version(),3-- -",
    "1' UNION SELECT 1,sqlite_version(),3-- -"
);
$sqliUnionNote = xxr_db_driver() === 'sqlite'
    ? xxr_driver_note('当前为 SQLite 演示环境：版本函数用 <code>sqlite_version()</code>（MySQL 为 <code>version()</code>），联合注入手法完全一致；查系统表用 <code>sqlite_master</code>（MySQL 为 <code>information_schema</code>）。')
    : '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·筑基】联合试炼 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·筑基】联合试炼</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗的试炼需要你用 UNION 联结两个查询结果。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">编号：</span>
                <input type="text" name="id" class="form-control" placeholder="试试: <?= e($sqliUnionPayload) ?>" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <?= $sqliUnionNote ?>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> UNION 联合注入。Payload: <code><?= e($sqliUnionPayload) ?></code>
            <hr>
            Flag 提交位置：<a href="/challenge/LH-JZ-05" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/LH-JZ-05" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>