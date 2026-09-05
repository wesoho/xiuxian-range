<?php
// SSRF 基础
$url = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>元神出窍·SSRF</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌀 元神出窍</h2>
        <form method="GET">
            <input type="text" name="url" class="form-control" placeholder="file:///etc/passwd">
            <button class="xxr-btn xxr-btn-primary mt-2">拉取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if ($url) {
            // 【漏洞】未限制协议
            $content = @file_get_contents($url);
            echo htmlspecialchars($content);
        }
        ?>
        </pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('ssrf'); ?>
</body>
</html>