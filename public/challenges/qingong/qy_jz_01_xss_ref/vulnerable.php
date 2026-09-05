<?php
// 反射型 XSS 漏洞
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>练功房·反射型 XSS</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>⚔️ 练功房的咒语</h2>
        <form method="GET">
            <input type="text" name="msg" class="form-control" placeholder="你的咒语..." autofocus>
            <button class="xxr-btn xxr-btn-primary mt-2">念出</button>
        </form>
        <?php if ($msg): ?>
            <div class="xxr-narrative mt-4">
                <strong>石壁回响：</strong>
                <?= $msg ?>
                <!-- 【漏洞】未转义 -->
            </div>
        <?php endif; ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('xss'); ?>
</body>
</html>