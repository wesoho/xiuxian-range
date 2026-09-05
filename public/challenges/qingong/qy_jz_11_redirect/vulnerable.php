<?php
// 开放重定向漏洞
$url = $_GET['url'] ?? '/';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>传送门·URL重定向</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌌 传送门的诡计</h2>
        <p>点击下方按钮跳转：</p>
        <a href="?url=https://example.com" class="xxr-btn xxr-btn-primary">跳转</a>
        <?php
        if ($url !== '/' && !headers_sent()) {
            // 【漏洞】未校验 URL
            header("Location: $url");
            exit;
        }
        ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('redirect'); ?>
</body>
</html>