<?php
// LFI 目录穿越
$file = $_GET['file'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>轮回之眼·LFI</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>👁️ 轮回之眼</h2>
        <form method="GET">
            <input type="text" name="file" class="form-control" placeholder="试：../../etc/passwd">
            <button class="xxr-btn xxr-btn-primary mt-2">读取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        // 【漏洞】未限制路径
        include $file;
        ?>
        </pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('lfi'); ?>
</body>
</html>