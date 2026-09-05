<?php
/**
 * 修真靶场 - 关卡代码批量生成脚本
 *
 * 用法：php generate_challenges.php
 *
 * 根据 database/seeds/02_challenges.sql 中已存在的元数据，
 * 自动生成所有缺失的关卡目录与基础文件结构。
 *
 * 注意：此脚本只生成**模板**文件，每个关卡的 vulnerable.php
 * 需要根据漏洞类型手工实现具体漏洞逻辑。
 *
 * 已完成的10个炼气期关卡不会被覆盖。
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/public/challenges';

// 读取种子数据
$sql = file_get_contents(__DIR__ . '/database/seeds/02_challenges.sql');

// 解析 INSERT 语句，提取所有关卡
preg_match_all("/\('([^']+)',\s*'([^']+)',\s*'(\w+)',\s*'(\w+)',\s*(\d+),\s*'([^']+)',\s*'((?:[^'\\\\]|\\\\.)*)'/", $sql, $matches, PREG_SET_ORDER);

$generated = 0;
$skipped = 0;
$missing = [];

foreach ($matches as $m) {
    $id = $m[1];
    $title = $m[2];
    $sect = $m[3];
    $realm = $m[4];
    $difficulty = (int) $m[5];
    $category = $m[6];
    $narrative = $m[7];

    // 计算目录名（带 category 后缀以避免冲突，例如 qy_lq_01_html_comment）
    $dirName = strtolower(str_replace('-', '_', $id)) . '_' . strtolower(preg_replace('/_.*$/', '', $category));

    // 宗门映射
    $sectMap = [
        'qiingong'    => 'qingong',
        'wanmozong'   => 'wanmozong',
        'lunhuizong'  => 'lunhuizong',
        'wanderer'    => 'wanderer',
    ];

    if (!isset($sectMap[$sect])) continue;
    $dir = "{$challengesDir}/{$sectMap[$sect]}/{$dirName}";

    // 跳过已存在
    if (is_dir($dir)) {
        $skipped++;
        continue;
    }

    @mkdir($dir, 0755, true);

    // 生成 index.php
    $narrativeSafe = addslashes($narrative);
    $titleSafe = addslashes($title);

    // 根据 category 生成对应的 vulnerable.php / secure.php 模板
    $categoryFiles = generateCategoryFiles($category, $id, $narrative, $title);

    $indexContent = <<<PHP
<?php
/**
 * {$id} {$title}
 * 修真叙事：{$narrativeSafe}
 * 漏洞类型：{$category}
 * 难度：L{$difficulty}
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{$titleSafe} · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">{$titleSafe}</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> {$narrativeSafe}
        </div>
        {$categoryFiles['index_body']}
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> {$categoryFiles['hint']}
            <hr>
            Flag: <code class="xxr-mono">flag{$categoryFiles['flag_suffix']}</code>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/{$id}" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>
PHP;

    file_put_contents("{$dir}/index.php", $indexContent);
    file_put_contents("{$dir}/vulnerable.php", $categoryFiles['vulnerable']);
    file_put_contents("{$dir}/secure.php", $categoryFiles['secure']);

    $generated++;
}

echo "✅ 已生成: {$generated} 个\n";
echo "⏭️  已跳过: {$skipped} 个（已存在）\n";


/**
 * 根据漏洞分类生成对应的模板内容
 */
function generateCategoryFiles(string $category, string $id, string $narrative, string $title): array
{
    // 根据关卡序号生成 flag 后缀
    $orderNum = extractOrderNum($id);
    $flagSuffix = "_" . strtolower(str_replace('-', '_', $category)) . "_{$orderNum}";

    $default = [
        'index_body' => '<p class="text-muted">关卡环境已就绪，请破解目标获取 Flag。</p>',
        'hint' => '尝试各种攻击手法，思考漏洞原理。',
        'flag_suffix' => $flagSuffix,
        'vulnerable' => "<?php\n// {$id} vulnerable.php\n/**\n * 漏洞：根据关卡分类 {$category} 设计漏洞\n * 待完善\n */\n",
        'secure' => "<?php\n// {$id} secure.php\n/**\n * 安全版本：根据关卡分类 {$category} 设计安全代码\n * 待完善\n */\n",
    ];

    // 不同分类的模板
    $templates = [
        'xss_reflected' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">输入：</span>
                    <input type="text" name="msg" class="form-control" autofocus>
                    <button class="xxr-btn xxr-btn-primary">提交</button>
                </div>
            </form>
            <?php if (!empty(\$_GET['msg'])): ?>
            <div class="xxr-narrative">
                <strong>回显：</strong> <?= \$_GET['msg'] ?>
            </div>
            <?php endif; ?>
HTML,
            'hint' => '反射型 XSS。在 URL 参数注入 <code>&lt;script&gt;alert(1)&lt;/script&gt;</code>',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：直接 echo 用户输入
$msg = $_GET['msg'] ?? '';
echo "回显：$msg";  // 【漏洞】
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：使用 htmlspecialchars
$msg = $_GET['msg'] ?? '';
echo '回显：' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
header("Content-Security-Policy: default-src 'self'");
PHP,
        ],

        'csrf_get' => [
            'index_body' => <<<HTML
            <p>当前余额：<strong id="balance">1000</strong> 灵石</p>
            <a href="?transfer=1&to=attacker&amount=999" class="xxr-btn xxr-btn-primary">转账</a>
HTML,
            'hint' => 'GET 型 CSRF。利用 <code>&lt;img src="转账URL"&gt;</code> 让其他用户无意中转账',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：GET 请求敏感操作
if (isset($_GET['transfer'])) {
    $to = $_GET['to'];
    $amount = $_GET['amount'];
    // 【漏洞】无 token 验证，直接转账
    echo "已向 $to 转账 $amount 灵石";
}
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：CSRF Token + POST 请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
if (!hash_equals($_SESSION['csrf_token'], $_POST['_token'] ?? '')) {
    http_response_code(419);
    exit('CSRF token invalid');
}
// 转账逻辑
PHP,
        ],

        'sqli_numeric' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">弟子 ID：</span>
                    <input type="text" name="id" class="form-control" placeholder="试试: 1 OR 1=1" autofocus>
                    <button class="xxr-btn xxr-btn-primary">查询</button>
                </div>
            </form>
            <?php
            if (isset(\$_GET['id'])) {
                \$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
                try {
                    \$pdo = new PDO(\$dsn, 'xiuxian', 'xiuxian_pass', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    \$stmt = \$pdo->query("SELECT username, email FROM demo_users WHERE id = " . \$_GET['id']);
                    foreach (\$stmt as \$row) {
                        echo "<p>弟子：<code>" . htmlspecialchars(\$row['username']) . "</code></p>";
                    }
                } catch (PDOException \$e) {
                    echo '<div class="alert alert-danger">错误：' . \$e->getMessage() . '</div>';
                }
            }
            ?>
HTML,
            'hint' => '数字型 SQL 注入。Payload: <code>1 OR 1=1</code>',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：直接拼接 SQL（数字型）
$id = $_GET['id'];
$pdo->query("SELECT * FROM users WHERE id = $id");  // 【漏洞】
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：参数化 + 类型校验
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Invalid ID');
}
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
PHP,
        ],

        'sqli_string' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">弟子名：</span>
                    <input type="text" name="name" class="form-control" placeholder="试试: ' OR '1'='1" autofocus>
                    <button class="xxr-btn xxr-btn-primary">查询</button>
                </div>
            </form>
            <?php
            if (isset(\$_GET['name'])) {
                \$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
                try {
                    \$pdo = new PDO(\$dsn, 'xiuxian', 'xiuxian_pass');
                    \$stmt = \$pdo->query("SELECT email FROM demo_users WHERE username = '" . \$_GET['name'] . "'");
                    foreach (\$stmt as \$row) {
                        echo "<p>邮箱：" . htmlspecialchars(\$row['email']) . "</p>";
                    }
                } catch (PDOException \$e) {
                    echo '<div class="alert alert-danger">错误：' . \$e->getMessage() . '</div>';
                }
            }
            ?>
HTML,
            'hint' => '字符型 SQL 注入。Payload: <code>xxx&#39; OR &#39;1&#39;=&#39;1</code>',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：直接拼接 SQL（字符型，未闭合引号）
$name = $_GET['name'];
$pdo->query("SELECT * FROM users WHERE name = '$name'");  // 【漏洞】
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：参数化
$name = $_GET['name'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE name = ?');
$stmt->execute([$name]);
PHP,
        ],

        'sqli_union' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">编号：</span>
                    <input type="text" name="id" class="form-control" placeholder="试试: 1' UNION SELECT 1,version(),3-- -" autofocus>
                    <button class="xxr-btn xxr-btn-primary">查询</button>
                </div>
            </form>
HTML,
            'hint' => 'UNION 联合注入。Payload: <code>1&#39; UNION SELECT 1,2,3-- -</code>',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：UNION 注入
$id = $_GET['id'];
$stmt = $pdo->query("SELECT id, username, email FROM users WHERE id = '$id'");
foreach ($stmt as $row) {
    echo "ID={$row[0]} 用户={$row[1]} 邮箱={$row[2]}<br>";
}
PHP,
            'secure' => <<<'PHP'
<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = ?');
$stmt->execute([$id]);
PHP,
        ],

        'sqli_error' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">ID：</span>
                    <input type="text" name="id" class="form-control" placeholder="试试: 1' AND extractvalue(1,concat(0x7e,version()))-- -">
                    <button class="xxr-btn xxr-btn-primary">查询</button>
                </div>
            </form>
HTML,
            'hint' => '报错注入。利用 <code>extractvalue</code> / <code>updatexml</code> 触发错误回显',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：报错注入
$id = $_GET['id'];
$stmt = $pdo->query("SELECT * FROM users WHERE id = '$id'");  // 错误会输出
foreach ($stmt as $row) { print_r($row); }
PHP,
            'secure' => <<<'PHP'
<?php
ini_set('display_errors', '0');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
PHP,
        ],

        'sqli_bool' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">用户名：</span>
                    <input type="text" name="name" class="form-control" placeholder="试试: admin' AND 1=1-- -">
                    <button class="xxr-btn xxr-btn-primary">查询</button>
                </div>
            </form>
            <?php
            if (isset(\$_GET['name'])) {
                \$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
                try {
                    \$pdo = new PDO(\$dsn, 'xiuxian', 'xiuxian_pass');
                    \$stmt = \$pdo->query("SELECT id FROM demo_users WHERE username = '" . \$_GET['name'] . "'");
                    if (\$stmt->fetch()) {
                        echo '<div class="alert alert-success">✅ 用户存在</div>';
                    } else {
                        echo '<div class="alert alert-danger">❌ 用户不存在</div>';
                    }
                } catch (Exception \$e) { echo '错误'; }
            }
            ?>
HTML,
            'hint' => '布尔盲注。根据页面的"用户存在/不存在"真假来推断数据',
            'vulnerable' => <<<'PHP'
<?php
$name = $_GET['name'];
$stmt = $pdo->query("SELECT id FROM users WHERE name = '$name'");
if ($stmt->fetch()) echo 'exists'; else echo 'no';
PHP,
            'secure' => <<<'PHP'
<?php
$stmt = $pdo->prepare('SELECT id FROM users WHERE name = ?');
$stmt->execute([$_GET['name']]);
echo $stmt->fetch() ? 'exists' : 'no';
PHP,
        ],

        'sqli_time' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">用户名：</span>
                    <input type="text" name="name" class="form-control" placeholder="试试: admin' AND SLEEP(5)-- -">
                    <button class="xxr-btn xxr-btn-primary">查询</button>
                </div>
            </form>
HTML,
            'hint' => '时间盲注。利用 <code>SLEEP()</code> 触发响应延迟判断条件真假',
            'vulnerable' => <<<'PHP'
<?php
$name = $_GET['name'];
$pdo->query("SELECT * FROM users WHERE name = '$name'");
echo 'done';
PHP,
            'secure' => <<<'PHP'
<?php
$stmt = $pdo->prepare('SELECT * FROM users WHERE name = ?');
$stmt->execute([$_GET['name']]);
PHP,
        ],

        'rce_basic' => [
            'index_body' => <<<HTML
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">IP：</span>
                    <input type="text" name="ip" class="form-control" placeholder="试试: 127.0.0.1; ls /" autofocus>
                    <button class="xxr-btn xxr-btn-primary">测灵</button>
                </div>
            </form>
            <?php
            if (isset(\$_GET['ip'])) {
                \$ip = \$_GET['ip'];
                // 【漏洞】直接拼接命令
                echo '<pre>';
                system("ping -c 1 \$ip");
                echo '</pre>';
            }
            ?>
HTML,
            'hint' => '命令注入基础。Payload: <code>127.0.0.1; ls /</code> 或 <code>127.0.0.1 &amp;&amp; id</code>',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：直接拼接命令
$ip = $_GET['ip'];
system("ping -c 1 $ip");  // 【漏洞】
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：严格校验 + escapeshellarg
$ip = $_GET['ip'];
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    exit('Invalid IP');
}
system("ping -c 1 " . escapeshellarg($ip));
PHP,
        ],

        'csrf_post' => [
            'index_body' => <<<HTML
            <form method="POST" action="">
                <input type="hidden" name="transfer" value="1">
                <input type="hidden" name="to" value="attacker">
                <input type="hidden" name="amount" value="999">
                <button class="xxr-btn xxr-btn-primary">提交转账（POST）</button>
            </form>
HTML,
            'hint' => 'POST 型 CSRF。利用自动提交表单强制用户转账',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：POST 操作无 CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer'])) {
    $to = $_POST['to'];
    $amount = $_POST['amount'];
    echo "已向 $to 转账 $amount";
}
PHP,
            'secure' => <<<'PHP'
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['_token'] ?? '')) {
        http_response_code(419); exit;
    }
    if (isset($_POST['transfer'])) {
        $to = $_POST['to'];
        $amount = $_POST['amount'];
        // 转账逻辑
    }
}
PHP,
        ],

        'open_redirect' => [
            'index_body' => <<<HTML
            <p>欢迎回来！</p>
            <a href="?url=https://xiuxian-range.local" class="xxr-btn xxr-btn-primary">返回主页</a>
            <?php
            if (isset(\$_GET['url'])) {
                \$url = \$_GET['url'];
                header("Location: \$url");
                exit;
            }
            ?>
HTML,
            'hint' => 'URL 重定向。Payload: <code>?url=http://evil.com</code>',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：未校验重定向 URL
$url = $_GET['url'];
header("Location: $url");  // 【漏洞】
PHP,
            'secure' => <<<'PHP'
<?php
$url = $_GET['url'];
$allowed = ['https://xiuxian-range.local'];
if (!in_array($url, $allowed, true)) {
    http_response_code(400);
    exit('URL not allowed');
}
header("Location: $url");
PHP,
        ],

        'xss_stored' => [
            'index_body' => <<<HTML
            <form method="POST">
                <textarea name="content" class="form-control" rows="3" placeholder="留言..."></textarea>
                <button class="xxr-btn xxr-btn-primary mt-2">提交留言</button>
            </form>
            <hr>
            <h4>📜 留言板</h4>
            <?php
            if (\$_SERVER['REQUEST_METHOD'] === 'POST' && !empty(\$_POST['content'])) {
                \$content = \$_POST['content'];
                \$file = __DIR__ . '/comments.txt';
                file_put_contents(\$file, \$content . "\n", FILE_APPEND);
            }
            \$file = __DIR__ . '/comments.txt';
            if (file_exists(\$file)) {
                foreach (file(\$file) as \$line) {
                    echo '<div class="xxr-narrative">' . \$line . '</div>';  // 【漏洞】
                }
            }
            ?>
HTML,
            'hint' => '存储型 XSS。留言会被永久保存，所有访问者都会受影响',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：存储后未转义输出
$content = $_POST['content'];
file_put_contents('comments.txt', $content . "\n", FILE_APPEND);
// 读取时直接 echo，未转义
foreach (file('comments.txt') as $line) {
    echo $line;  // 【漏洞】
}
PHP,
            'secure' => <<<'PHP'
<?php
$content = $_POST['content'] ?? '';
// 输入净化（去除 HTML 标签）
$safe = strip_tags($content);
file_put_contents('comments.txt', $safe . "\n", FILE_APPEND);
// 输出转义
foreach (file('comments.txt') as $line) {
    echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
}
PHP,
        ],

        'file_read' => [
            'index_body' => <<<HTML
            <form method="GET">
                <div class="input-group">
                    <span class="input-group-text">文件路径：</span>
                    <input type="text" name="file" class="form-control" placeholder="试试: ../../../etc/passwd">
                    <button class="xxr-btn xxr-btn-primary">读取</button>
                </div>
            </form>
            <pre class="bg-dark-translucent p-3 mt-3">
            <?php
            if (isset(\$_GET['file'])) {
                \$file = \$_GET['file'];
                \$content = @file_get_contents(\$file);  // 【漏洞】未限制路径
                echo htmlspecialchars(\$content);
            }
            ?>
            </pre>
HTML,
            'hint' => '目录穿越。Payload: <code>../../../etc/passwd</code>',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：未限制路径
$file = $_GET['file'];
echo file_get_contents($file);  // 【漏洞】
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：白名单 + 路径规范化
$file = $_GET['file'] ?? '';
$realPath = realpath(__DIR__ . '/data/' . $file);
if (!$realPath || !str_starts_with($realPath, realpath(__DIR__ . '/data/'))) {
    http_response_code(403);
    exit('Forbidden');
}
echo htmlspecialchars(file_get_contents($realPath));
PHP,
        ],

        'upload_js' => [
            'index_body' => <<<HTML
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">弟子心法文件 (.txt)</label>
                    <input type="file" name="file" class="form-control" accept=".txt">
                </div>
                <button class="xxr-btn xxr-btn-primary">上传</button>
            </form>
HTML,
            'hint' => 'JS 前端校验绕过。禁用 JS 后上传 .php 文件即可',
            'vulnerable' => <<<'PHP'
<script>
// 【漏洞】仅前端校验
function checkExt(file) {
    return file.endsWith('.txt');
}
</script>
<?php
// 后端无任何校验
if ($_FILES) {
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
    echo '上传成功';
}
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：服务端校验 MIME + 扩展名
$allowed = ['txt', 'pdf', 'jpg', 'png'];
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$mime = mime_content_type($_FILES['file']['tmp_name']);
if (!in_array(strtolower($ext), $allowed) || !str_starts_with($mime, 'text/')) {
    http_response_code(400);
    exit('Invalid file');
}
$newName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $newName);
PHP,
        ],

        'clickjacking' => [
            'index_body' => <<<HTML
            <p>点击下方按钮确认身份：</p>
            <button class="xxr-btn xxr-btn-primary" onclick="alert('OK')">确认</button>
            <hr>
            <p>演示：</p>
            <code>
            &lt;iframe src="http://your-target" style="opacity:0.1"&gt;&lt;/iframe&gt;<br>
            &lt;div style="position:absolute;top:100px;left:100px"&gt;诱饵按钮&lt;/div&gt;
            </code>
HTML,
            'hint' => '点击劫持。利用透明 iframe 覆盖诱饵按钮，诱导用户点击隐藏的确认按钮',
            'vulnerable' => <<<'PHP'
<?php
// 漏洞：未设置 X-Frame-Options
?>
<button>确认</button>
PHP,
            'secure' => <<<'PHP'
<?php
// 修复：X-Frame-Options 禁止 iframe 嵌入
header('X-Frame-Options: DENY');
header("Content-Security-Policy: frame-ancestors 'none'");
?>
<button>确认</button>
PHP,
        ],
    ];

    // 查找匹配的模板
    foreach ($templates as $key => $tpl) {
        if (str_starts_with($category, $key)) {
            return array_merge($default, $tpl);
        }
    }

    return $default;
}

/**
 * 从关卡ID提取序号数字
 */
function extractOrderNum(string $id): string
{
    // 例如 QY-JZ-01 -> 11
    $parts = explode('-', $id);
    if (count($parts) < 3) return '99';
    $code = $parts[0]; // QY
    $realm = $parts[1]; // JZ
    $seq = (int) $parts[2]; // 01

    // 境界起始序号
    $starts = [
        'LQ' => 1, 'JZ' => 11, 'JD' => 26, 'YY' => 41,
        'HS' => 56, 'LX' => 71, 'HT' => 81, 'DC' => 91,
    ];
    $offset = $starts[$realm] ?? 1;
    return (string)($offset + $seq - 1);
}