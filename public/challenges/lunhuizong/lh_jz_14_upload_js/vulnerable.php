<?php
// 文件上传 JS 校验绕过
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>上传心法·JS 校验</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📤 上传心法</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" class="form-control" accept=".txt" id="fileInput">
            <button class="xxr-btn xxr-btn-primary mt-2" onclick="return check()">上传</button>
        </form>
        <?php
        if ($_FILES) {
            // 【漏洞】后端无校验
            $name = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
            echo '<div class="alert alert-success mt-3">上传成功：' . htmlspecialchars($name) . '</div>';
        }
        ?>
    </div>
    <script>
        function check() {
            // 【漏洞】仅前端校验
            var f = document.getElementById('fileInput').value;
            if (!f.endsWith('.txt')) {
                alert('只允许 .txt 文件！');
                return false;
            }
            return true;
        }
    </script>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('upload'); ?>
</body>
</html>