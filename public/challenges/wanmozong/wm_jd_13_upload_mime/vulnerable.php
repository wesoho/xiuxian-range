<?php
// 文件上传 MIME 绕过
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>灵识伪装·MIME 绕过</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🎭 灵识伪装</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" class="form-control">
            <button class="xxr-btn xxr-btn-primary mt-2">上传</button>
        </form>
        <?php
        if ($_FILES) {
            // 【漏洞】只检查 Content-Type（可伪造）
            $allowed = ['image/jpeg', 'image/png'];
            if (in_array($_FILES['file']['type'], $allowed)) {
                move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
                echo '<div class="alert alert-success">上传成功</div>';
            }
        }
        ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('upload'); ?>
</body>
</html>