<?php
// JWT 弱密钥
$secret = 'secret';  // 【漏洞】弱密钥
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>密钥爆破</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🔑 密钥爆破</h2>
        <p>JWT 密钥爆破（弱密钥）</p>
        <p>教学演示：使用 hashcat -m 16500 jwt.txt wordlist.txt</p>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('jwt'); ?>
</body>
</html>