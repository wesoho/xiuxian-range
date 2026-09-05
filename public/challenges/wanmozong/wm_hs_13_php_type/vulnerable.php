<?php
// PHP 弱类型比较
$password = $_POST['password'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>弱类型幻象</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🪞 弱类型幻象</h2>
        <form method="POST">
            <input type="text" name="password" class="form-control" placeholder="试：0e123">
            <button class="xxr-btn xxr-btn-primary mt-2">登录</button>
        </form>
        <?php
        // 【漏洞】弱类型比较
        if ($password == 0) {
            // "0e123" == "0e456" 都为 0
            echo '<div class="alert alert-success">登录成功（实际密码是 0）</div>';
        }
        ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('phpweak'); ?>
</body>
</html>