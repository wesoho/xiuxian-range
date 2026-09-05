<?php
// 黑名单扩展名绕过
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>禁咒文件·扩展名绕过</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📜 禁咒文件</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" class="form-control">
            <button class="xxr-btn xxr-btn-primary mt-2">上传</button>
        </form>
        <?php
        if ($_FILES) {
            $blocked = ['php', 'asp', 'jsp'];
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            // 【漏洞】黑名单不全（.php5 .phtml .phar 可绕过）
            if (!in_array(strtolower($ext), $blocked)) {
                move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
                echo '<div class="alert alert-success">上传成功</div>';
            }
        }
        ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('upload'); ?>
</body>
</html>