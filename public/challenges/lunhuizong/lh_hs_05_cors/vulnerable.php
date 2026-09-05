<?php
// CORS 配置错误
header('Access-Control-Allow-Origin: *');  // 【漏洞】允许所有域
header('Access-Control-Allow-Credentials: true');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>跨界之门·CORS</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌐 跨界之门</h2>
        <p>敏感数据：用户邮箱、Token、个人信息</p>
        <pre>user@example.com | token=abc123</pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('cors'); ?>
</body>
</html>