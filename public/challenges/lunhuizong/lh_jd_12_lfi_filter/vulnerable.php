<?php
// 【漏洞】LFI + php://filter 读源码
$file = $_GET['file'] ?? 'index.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>PHP之源·伪协议</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📖 PHP之源</h2>
        <form method="GET">
            <input type="text" name="file" class="form-control" placeholder="php://filter/convert.base64-encode/resource=index.php">
            <button class="xxr-btn xxr-btn-primary mt-2">读取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if (preg_match('/php|flag/i', $file)) {
            // 简单过滤可绕过
            echo 'blocked';
        } else {
            include $file;
        }
        ?>
        </pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('lfi'); ?>
</body>
</html>