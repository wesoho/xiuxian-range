<?php
/**
 * 修真靶场 - 筑基期HTML类关卡完善
 *
 * 为筑基期中重要的HTML交互关卡（XSS、CSRF、上传等）补充完整可玩代码
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/../../public/challenges';

// 筑基期关卡详细代码
$content = [
    // 筑基期 QY-JZ-01 XSS 反射型
    'qy_jz_01_xss_ref' => [
        'vulnerable' => <<<'PHP'
<?php
// 反射型 XSS 漏洞
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>练功房·反射型 XSS</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>⚔️ 练功房的咒语</h2>
        <form method="GET">
            <input type="text" name="msg" class="form-control" placeholder="你的咒语..." autofocus>
            <button class="xxr-btn xxr-btn-primary mt-2">念出</button>
        </form>
        <?php if ($msg): ?>
            <div class="xxr-narrative mt-4">
                <strong>石壁回响：</strong>
                <?= $msg ?>
                <!-- 【漏洞】未转义 -->
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：htmlspecialchars + CSP
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
$msg = $_GET['msg'] ?? '';
echo '石壁回响：' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
PHP,
    ],

    // 筑基期 QY-JZ-02 CSRF GET
    'qy_jz_02_csrf_get' => [
        'vulnerable' => <<<'PHP'
<?php
// GET 型 CSRF 漏洞
session_start();

// 初始化余额（演示）
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 1000;
}

if (isset($_GET['transfer'])) {
    $to = $_GET['to'] ?? 'attacker';
    $amount = (float) ($_GET['amount'] ?? 0);

    // 【漏洞】GET 请求转账，无验证
    $_SESSION['balance'] -= $amount;
    echo "已向 <strong>$to</strong> 转账 <strong>$amount</strong> 灵石";
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>转账幻阵·CSRF GET</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>⚔️ 转账幻阵</h2>
        <p>当前余额：<strong><?= $_SESSION['balance'] ?></strong> 灵石</p>
        <a href="?transfer=1&to=attacker&amount=999" class="xxr-btn xxr-btn-primary">点击转账</a>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：POST + CSRF Token
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['_token'] ?? '')) {
        http_response_code(419);
        exit('CSRF token invalid');
    }
    // 转账逻辑
    $amount = (float) ($_POST['amount'] ?? 0);
    $_SESSION['balance'] -= $amount;
}
PHP,
    ],

    // 筑基期 QY-JZ-03 SQL 数字型
    'qy_jz_03_sqli_num' => [
        'vulnerable' => <<<'PHP'
<?php
// 数字型 SQL 注入
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) { die('数据库连接失败'); }

$id = $_GET['id'] ?? '1';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>丹房·SQL数字型</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>💊 丹房的数字谜题</h2>
        <form method="GET">
            <input type="text" name="id" class="form-control" placeholder="弟子 ID" autofocus>
            <button class="xxr-btn xxr-btn-primary mt-2">查询</button>
        </form>
        <?php if (isset($_GET['id'])): ?>
            <div class="mt-4">
                <?php
                try {
                    // 【漏洞】直接拼接 SQL（数字型）
                    $stmt = $pdo->query("SELECT username, email FROM demo_users WHERE id = $id");
                    foreach ($stmt as $row) {
                        echo '<div class="xxr-narrative">弟子：<strong>' . htmlspecialchars($row['username']) . '</strong> | 邮箱：' . htmlspecialchars($row['email']) . '</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">错误：' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：参数化查询
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Invalid ID');
}

$stmt = $pdo->prepare('SELECT username, email FROM demo_users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$row = $stmt->fetch();
if ($row) {
    echo '<div>弟子：' . htmlspecialchars($row['username']) . '</div>';
}
PHP,
    ],

    // 筑基期 QY-JZ-04 SQL 字符型
    'qy_jz_04_sqli_str' => [
        'vulnerable' => <<<'PHP'
<?php
// 字符型 SQL 注入
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

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
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：参数化
$stmt = $pdo->prepare('SELECT email FROM demo_users WHERE username = ? LIMIT 1');
$stmt->execute([$_GET['name'] ?? '']);
foreach ($stmt as $row) {
    echo '<div>邮箱：' . htmlspecialchars($row['email']) . '</div>';
}
PHP,
    ],

    // 筑基期 QY-JZ-11 开放重定向
    'qy_jz_11_redirect' => [
        'vulnerable' => <<<'PHP'
<?php
// 开放重定向漏洞
$url = $_GET['url'] ?? '/';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>传送门·URL重定向</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌌 传送门的诡计</h2>
        <p>点击下方按钮跳转：</p>
        <a href="?url=https://example.com" class="xxr-btn xxr-btn-primary">跳转</a>
        <?php
        if ($url !== '/' && !headers_sent()) {
            // 【漏洞】未校验 URL
            header("Location: $url");
            exit;
        }
        ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单 URL
$allowed = ['/home', '/about', '/contact', '/challenges'];
$url = $_GET['url'] ?? '/';
if (!in_array($url, $allowed, true)) {
    http_response_code(400);
    exit('URL not allowed');
}
header("Location: $url");
PHP,
    ],

    // 筑基期 QY-JZ-12 XSS 存储型
    'qy_jz_12_xss_store' => [
        'vulnerable' => <<<'PHP'
<?php
// 存储型 XSS 漏洞
$msgFile = __DIR__ . '/comments.txt';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>留言板·存储型 XSS</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📜 留言板的诅咒</h2>
        <form method="POST">
            <textarea name="content" class="form-control" rows="3" placeholder="留言..."></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
            file_put_contents($msgFile, $_POST['content'] . "\n", FILE_APPEND);
        }
        ?>
        <h4 class="mt-4">💬 所有留言</h4>
        <?php
        if (file_exists($msgFile)) {
            // 【漏洞】读取时未转义
            foreach (file($msgFile) as $line) {
                echo '<div class="xxr-narrative">' . $line . '</div>';
            }
        }
        ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：输入净化 + 输出转义
$content = strip_tags($_POST['content'] ?? '');
file_put_contents(__DIR__ . '/comments.txt', $content . "\n", FILE_APPEND);

foreach (file(__DIR__ . '/comments.txt') as $line) {
    echo '<div class="xxr-narrative">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</div>';
}
PHP,
    ],

    // 筑基期 LH-JZ-13 文件读取
    'lh_jz_13_file_read' => [
        'vulnerable' => <<<'PHP'
<?php
// 目录穿越漏洞
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>忘川河底·文件读取</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌊 忘川河底的秘密</h2>
        <form method="GET">
            <input type="text" name="file" class="form-control" placeholder="文件路径（试：../../../etc/passwd）">
            <button class="xxr-btn xxr-btn-primary mt-2">读取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if (isset($_GET['file'])) {
            $content = @file_get_contents($_GET['file']);
            // 【漏洞】未限制路径
            echo htmlspecialchars($content);
        }
        ?>
        </pre>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单 + 路径规范化
$allowedDir = realpath(__DIR__ . '/data');
$file = $_GET['file'] ?? '';
$realPath = realpath($allowedDir . '/' . $file);

if (!$realPath || !str_starts_with($realPath, $allowedDir)) {
    http_response_code(403);
    exit('Forbidden');
}

echo htmlspecialchars(file_get_contents($realPath));
PHP,
    ],

    // 筑基期 LH-JZ-14 文件上传 (JS)
    'lh_jz_14_upload_js' => [
        'vulnerable' => <<<'PHP'
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
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：服务端验证 MIME + 扩展名
$allowedExt = ['txt', 'pdf', 'jpg', 'png'];
$allowedMime = ['text/plain', 'application/pdf', 'image/jpeg', 'image/png'];

$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$mime = mime_content_type($_FILES['file']['tmp_name']);

if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
    http_response_code(400);
    exit('Invalid file');
}

$newName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $newName);
PHP,
    ],
];

$updated = 0;
foreach ($content as $dirName => $files) {
    $dirs = glob("$challengesDir/*/$dirName");
    if (empty($dirs)) {
        echo "⚠️  目录不存在: $dirName\n";
        continue;
    }
    $dir = $dirs[0];

    if (isset($files['vulnerable'])) {
        file_put_contents("$dir/vulnerable.php", $files['vulnerable']);
    }
    if (isset($files['secure'])) {
        file_put_contents("$dir/secure.php", $files['secure']);
    }
    $updated++;
}

echo "✅ 已更新 $updated 个筑基期关卡的完整可玩代码\n";