<?php
// 命令注入关键字过滤绕过
$cmd = $_GET['cmd'] ?? '';
// 【过滤】移除 cat/ls/...
$cmd = preg_replace('/cat|ls|tac|head|tail|more|less/i', '', $cmd);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>禁咒搜寻</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🔍 禁咒搜寻</h2>
        <form method="GET">
            <input type="text" name="cmd" class="form-control" placeholder="试：c${IFS}at${IFS}/etc/passwd">
            <button class="xxr-btn xxr-btn-primary mt-2">执行</button>
        </form>
        <pre>
        <?php
        if ($cmd !== '') {
            system($cmd);  // 【漏洞】双写、通配符可绕过（ca${x}at 等）
        }
        ?>
        </pre>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('rce'); ?>
</body>
</html>