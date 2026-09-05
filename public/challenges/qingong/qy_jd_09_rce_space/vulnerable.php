<?php
// 命令注入空格过滤绕过
$ip = $_GET['ip'] ?? '127.0.0.1';
$ip = str_replace(' ', '', $ip);  // 仅过滤空格
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>空间的缝隙</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌌 空间的缝隙</h2>
        <form method="GET">
            <input type="text" name="ip" class="form-control" placeholder="${IFS}cat${IFS}/etc/passwd">
            <button class="xxr-btn xxr-btn-primary mt-2">测灵</button>
        </form>
        <pre>
        <?php
        echo shell_exec("ping -c 1 $ip");
        // 【漏洞】空格过滤可被 ${IFS} 绕过
        ?>
        </pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('rce'); ?>
</body>
</html>