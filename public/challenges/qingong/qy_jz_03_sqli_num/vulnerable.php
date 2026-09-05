<?php
// 数字型 SQL 注入
// 连接平台数据库（MySQL / SQLite 开发模式均可），便于本地体验
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
$pdo = db()->pdo();

$id = $_GET['id'] ?? '1';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>丹房·SQL数字型</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>💊 丹房的数字谜题</h2>
        <form method="GET">
            <input type="text" name="id" class="form-control" placeholder="弟子 ID" autofocus>
            <button class="xxr-btn xxr-btn-primary mt-2">查询</button>
        </form>

        <div class="xxr-narrative mt-3">
            <strong>🧪 丹童小声说：</strong> 师父查弟子名册时嫌参数化麻烦，一直是拿字符串拼出来的。
            听说名册库的暗格里还压着一部《宗门秘史》，表名里带个 <code>manual</code>——不过那是给"会顺便多查一张表的人"看的。
        </div>

        <?php if (isset($_GET['id'])): ?>
            <div class="mt-4">
                <?php
                try {
                    // 【漏洞】直接拼接 SQL（数字型）
                    $stmt = $pdo->query("SELECT username, email FROM demo_users WHERE id = $id");
                    foreach ($stmt as $row) {
                        echo '<div class="xxr-narrative">弟子：<strong>' . htmlspecialchars((string) $row['username']) . '</strong> | 邮箱：' . htmlspecialchars((string) $row['email']) . '</div>';
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
