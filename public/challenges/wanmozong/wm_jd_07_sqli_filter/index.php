<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-JD-07 【万魔宗·金丹】禁咒过滤
 * 修真叙事：万魔宗过滤了 union/select 等关键字。
 * 漏洞类型：sqli_filter
 * 难度：L3
 * 宗门：wanmozong
 */
$sqliFilterPayload = '-1 ununionion selselectect 1-- -';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·金丹】禁咒过滤 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·金丹】禁咒过滤</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗过滤了 union/select 等关键字。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="关键字过滤绕过">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> SQL 注入关键字过滤绕过。双写 <code>ununionion selselectect</code> 双驱动通用。Payload: <code><?= e($sqliFilterPayload) ?></code>（UNION 列数需与主查询一致）<br>
            <span style="font-size:12px;color:#8a97a8;">内联注释拼接（uni/**/on）利用 MySQL 词法特性，SQLite 将注释视为空白、不可用。</span>
            <hr>
            Flag 提交位置：<a href="/challenge/WM-JD-07" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JD-07" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>