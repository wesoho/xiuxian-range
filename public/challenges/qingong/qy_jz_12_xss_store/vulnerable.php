<?php
// 存储型 XSS 漏洞
$msgFile = __DIR__ . '/comments.txt';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>留言板·存储型 XSS</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📜 留言板的诅咒</h2>
        <form method="POST">
            <textarea name="content" class="form-control" rows="3" placeholder="留言..."></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
            file_put_contents($msgFile, $_POST['content'] . "\n", FILE_APPEND);
        }
        ?>
        <h4 class="mt-4">💬 所有留言</h4>
        <?php
        if (file_exists($msgFile)) {
            // 【漏洞】读取时未转义
            foreach (file($msgFile) as $line) {
                echo '<div class="xxr-narrative">' . $line . '</div>';
            }
        }
        ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('xss'); ?>
</body>
</html>