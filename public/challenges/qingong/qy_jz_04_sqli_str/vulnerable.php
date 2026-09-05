<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 字符型 SQL 注入
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p); } catch (PDOException $e) { die('fail'); }

$name = $_GET['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>丹方·SQL字符型</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📜 丹方的字符咒语</h2>
        <form method="GET">
            <input type="text" name="name" class="form-control" placeholder="弟子名（试: ' OR '1'='1）">
            <button class="xxr-btn xxr-btn-primary mt-2">查询</button>
        </form>
        <?php if ($name): ?>
            <div class="mt-4">
                <?php
                try {
                    // 【漏洞】字符型未闭合
                    $stmt = $pdo->query("SELECT email FROM demo_users WHERE username = '$name'");
                    foreach ($stmt as $row) {
                        echo '<div>邮箱：' . htmlspecialchars($row['email']) . '</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">错误：' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('sqli'); ?>
</body>
</html>