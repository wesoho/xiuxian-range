<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-JD-08 【万魔宗·金丹】护山结界
 * 修真叙事：万魔宗的山门有护山大阵（WAF）阻挡入侵。
 * 漏洞类型：sqli_waf
 * 难度：L3
 * 宗门：wanmozong
 */
if (xxr_db_driver() === 'sqlite') {
    $sqliWafPayload = "1 OR 1=1-- -";
    $sqliWafHint = '黑名单未拦 OR/恒真式，直接绕过（内联注释拼接 uni/**/on 利用 MySQL 词法特性，SQLite 将注释视为空白、不可用）';
} else {
    $sqliWafPayload = '-1 uni/**/on sel/**/ect version()-- -';
    $sqliWafHint = '利用大小写无意义（黑名单已忽略大小写）、内联注释拼接关键字绕过';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·金丹】护山结界 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【万魔宗·金丹】护山结界</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗的山门有护山大阵（WAF）阻挡入侵。
        </div>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="WAF 绕过">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> SQL 注入 WAF 绕过。<?= $sqliWafHint ?>。Payload: <code><?= e($sqliWafPayload) ?></code>
            <hr>
            Flag 提交位置：<a href="/challenge/WM-JD-08" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/WM-JD-08" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>