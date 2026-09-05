<?php
// GET 型 CSRF 漏洞
session_start();

// 初始化余额（演示）
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 1000;
}

if (isset($_GET['transfer'])) {
    $to = $_GET['to'] ?? 'attacker';
    $amount = (float) ($_GET['amount'] ?? 0);

    // 【漏洞】GET 请求转账，无验证
    $_SESSION['balance'] -= $amount;
    echo "已向 <strong>$to</strong> 转账 <strong>$amount</strong> 灵石";
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>转账幻阵·CSRF GET</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>⚔️ 转账幻阵</h2>
        <p>当前余额：<strong><?= $_SESSION['balance'] ?></strong> 灵石</p>
        <a href="?transfer=1&to=attacker&amount=999" class="xxr-btn xxr-btn-primary">点击转账</a>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('csrf'); ?>
</body>
</html>