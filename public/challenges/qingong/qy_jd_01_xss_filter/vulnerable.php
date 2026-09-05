<?php
// XSS 关键字过滤（可绕过）
$msg = $_GET['msg'] ?? '';

// 【过滤】移除 < > 字符
$msg = str_replace(['<', '>'], ['&lt;', '&gt;'], $msg);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>金光的过滤</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>✨ 金光的过滤</h2>
        <form method="GET">
            <input type="text" name="msg" class="form-control">
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <div class="xxr-narrative mt-3">
            <strong>回显：</strong>
            <!-- 【漏洞】属性注入可绕过 -->
            <div title="<?= $msg ?>">悬停查看 title</div>
        </div>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('xss'); ?>
</body>
</html>