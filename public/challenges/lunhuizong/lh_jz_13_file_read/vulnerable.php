<?php
// 目录穿越漏洞
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>忘川河底·文件读取</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌊 忘川河底的秘密</h2>
        <form method="GET">
            <input type="text" name="file" class="form-control" placeholder="文件路径（试：../../../etc/passwd）">
            <button class="xxr-btn xxr-btn-primary mt-2">读取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if (isset($_GET['file'])) {
            $content = @file_get_contents($_GET['file']);
            // 【漏洞】未限制路径
            echo htmlspecialchars($content);
        }
        ?>
        </pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('lfi'); ?>
</body>
</html>