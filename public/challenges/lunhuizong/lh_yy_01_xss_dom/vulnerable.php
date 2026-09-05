<?php
// DOM 型 XSS
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>DOM幻象</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🔮 DOM幻象</h2>
        <p>本关演示 URL hash 触发的 DOM XSS：</p>
        <div id="output">将显示在 #output</div>
    </div>
    <script>
        // 【漏洞】从 URL hash 读取并使用 innerHTML
        const hash = location.hash.substring(1);
        document.getElementById('output').innerHTML = hash;  // DOM XSS
    </script>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('xss'); ?>
</body>
</html>