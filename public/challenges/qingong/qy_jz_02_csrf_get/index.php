<?php
/**
 * QY-JZ-02 【青云宗·筑基】转账幻阵
 * 修真叙事：你发现只要把转账链接告诉别人，他们的钱就会自动转入你账户。
 * 漏洞类型：csrf_get
 * 难度：L2
 * 宗门：qingong
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·筑基】转账幻阵 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·筑基】转账幻阵</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 你发现只要把转账链接告诉别人，他们的钱就会自动转入你账户。
        </div>
        <p>当前余额：<strong>1000</strong> 灵石</p>
        <a href="?transfer=1&to=attacker&amount=999" class="xxr-btn xxr-btn-primary">点击转账</a>
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> GET 型 CSRF。利用 <code>&lt;img&gt;</code> 自动请求
            <hr>
            Flag 提交位置：<a href="/challenge/QY-JZ-02" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-JZ-02" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>