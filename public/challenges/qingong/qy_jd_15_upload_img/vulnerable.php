<?php
// 图片马 + getimagesize 绕过
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>金身绘像·图片马</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🖼️ 金身绘像</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" class="form-control" accept="image/*">
            <button class="xxr-btn xxr-btn-primary mt-2">上传</button>
        </form>
        <?php
        if ($_FILES) {
            // 【漏洞】getimagesize 只能验证图片格式，但图片内可嵌入 PHP 代码
            if (@getimagesize($_FILES['file']['tmp_name'])) {
                move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
                echo '<div class="alert alert-success">上传成功</div>';
                echo '<p>访问 <a href="/challenges/qingong/qy_jd_15_upload_img/uploads/' . htmlspecialchars($_FILES['file']['name']) . '">上传的文件</a></p>';
            }
        }
        ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('upload'); ?>
</body>
</html>