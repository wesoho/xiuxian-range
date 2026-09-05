<?php
// XSS 关键字过滤（可大小写/双写绕过）
$msg = $_GET['msg'] ?? '';
$msg = preg_replace('/script/i','', $msg);  // 仅过滤一次
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>咒语变形</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🔮 咒语变形</h2>
        <form method="GET">
            <input type="text" name="msg" class="form-control">
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <div class="mt-3">
            <!-- 【漏洞】<scrscriptipt> 可绕过 -->
            <?= $msg ?>
        </div>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('xss'); ?>
</body>
</html>