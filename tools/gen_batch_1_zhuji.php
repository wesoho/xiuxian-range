<?php
/**
 * 修真靶场 - 筑基期15关批量生成
 * 包含完整的修真叙事 + vulnerable.php + secure.php
 */

declare(strict_types=1);

$base = dirname(__DIR__);
$root = "$base/public/challenges";

/**
 * 修真期关卡配置
 */
$challenges = [
    // ====== 筑基期（jz）======
    'qy_jz_01_xss_ref' => [
        'title' => '【青云宗·筑基】练功房的咒语',
        'sect' => '青云宗',
        'narrative' => '你对着练功房的石壁默念咒语，但声音会被原封不动地回显在墙上，且不受任何阻碍。请尝试注入你的咒语，看看你能让石壁做出什么反应。',
        'vuln' => 'xss_ref',
    ],
    'qy_jz_02_csrf_get' => [
        'title' => '【青云宗·修真】转账幻阵',
        'sect' => '青云宗',
        'narrative' => '你发现了青云宗的转账幻阵：只要把转账链接告诉别人，他们的钱就会自动转入你账户。这是一个无任何防护的 GET 幻阵。',
        'vuln' => 'csrf_get',
    ],
    'qy_jz_03_sqli_num' => [
        'title' => '【青云宗·修真】丹房的数字谜题',
        'sect' => '青云宗',
        'narrative' => '丹房有座石碑会显示丹药品级，输入丹方编号就能查看详情。你怀疑可以越权查看所有丹方。',
        'vuln' => 'sqli_num',
    ],
    'qy_jz_04_sqli_str' => [
        'title' => '【青云宗·修真】丹方的字符咒语',
        'sect' => '青云宗',
        'narrative' => '这次丹方名称是字符串，需要闭合引号才能注入。你发现了丹房管理弟子使用 addslashes 但未用参数化查询的失误。',
        'vuln' => 'sqli_str',
    ],
    'qy_jz_11_redirect' => [
        'title' => '【青云宗·修真】传送门的诡计',
        'sect' => '青云宗',
        'narrative' => '青云宗的传送门会跳转到任意地方，没有任何域名校验。',
        'vuln' => 'open_redirect',
    ],
    'qy_jz_12_xss_store' => [
        'title' => '【青云宗·修真】留言板的诅咒',
        'sect' => '青云宗',
        'narrative' => '留言板的咒语会被永久记住，伤害所有访问者。',
        'vuln' => 'xss_stored',
    ],
    'lh_jz_05_sqli_union' => [
        'title' => '【轮回宗·筑基】联合试炼',
        'sect' => '轮回宗',
        'narrative' => '轮回宗的试炼需要你用 UNION 联结两个查询结果。',
        'vuln' => 'sqli_union',
    ],
    'lh_jz_06_sqli_error' => [
        'title' => '【轮回宗·修真】幽冥报错',
        'sect' => '轮回宗',
        'narrative' => '幽冥之地会把一切错误放大，让你看清 SQL 语句。',
        'vuln' => 'sqli_error',
    ],
    'lh_jz_07_sqli_bool' => [
        'title' => '【轮回宗·修真】真言之试',
        'sect' => '轮回宗',
        'narrative' => '轮回殿只回应真假两种答复，你需要用真假来推断秘密。',
        'vuln' => 'sqli_bool',
    ],
    'lh_jz_13_file_read' => [
        'title' => '【轮回宗·修真】忘川河底的秘密',
        'sect' => '轮回宗',
        'narrative' => '忘川河底沉睡着历代轮回宗主的秘密，你可以潜入读取。',
        'vuln' => 'file_read',
    ],
    'lh_jz_14_upload_js' => [
        'title' => '【轮回宗·修真】上传心法',
        'sect' => '轮回宗',
        'narrative' => '轮回宗上传心法时只在前端检查格式，禁制不严。',
        'vuln' => 'upload_js',
    ],
    'wm_jz_08_sqli_time' => [
        'title' => '【万魔宗·筑基】时光咒',
        'sect' => '万魔宗',
        'narrative' => '万魔宗有时会用时光咒让一切停摆。利用这种停顿来推断秘密。',
        'vuln' => 'sqli_time',
    ],
    'wm_jz_09_rce_basic' => [
        'title' => '【万魔宗·修真】Ping 测灵根',
        'sect' => '万魔宗',
        'narrative' => '魔窟的测灵阵会根据你输入的 IP 来 ping 你，但可不止 ping 那么简单。',
        'vuln' => 'rce_basic',
    ],
    'wm_jz_10_csrf_post' => [
        'title' => '【万魔宗·修真】魔影传书',
        'sect' => '万魔宗',
        'narrative' => '万魔宗的弟子可以不知不觉地替别人提交表单。',
        'vuln' => 'csrf_post',
    ],
    'wm_jz_15_clickjack' => [
        'title' => '【万魔宗·修真】无形之框',
        'sect' => '万魔宗',
        'narrative' => '万魔宗用一个看不见的框罩住点击按钮，劫持用户操作。',
        'vuln' => 'clickjacking',
    ],
];

$sectMap = [
    '青云宗' => 'qingong',
    '轮回宗' => 'lunhuizong',
    '万魔宗' => 'wanmozong',
];

$created = 0;
foreach ($challenges as $dirName => $info) {
    $sectDir = $sectMap[$info['sect']];
    $dir = "$root/$sectDir/$dirName";

    if (is_dir($dir)) {
        continue;
    }
    @mkdir($dir, 0755, true);

    $titleEsc = addslashes($info['title']);
    $narrativeEsc = addslashes($info['narrative']);
    $body = generateBody($info['vuln']);
    $flag = generateFlag($info['vuln'], $dirName);
    $vulnCode = generateVulnerable($info['vuln'], $flag);
    $secureCode = generateSecure($info['vuln']);

    // index.php
    $indexContent = <<<PHP
<?php
/**
 * {$titleEsc}
 * 修真叙事：{$narrativeEsc}
 * 漏洞类型：{$info['vuln']}
 * 难度：L2
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{$titleEsc} · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">{$titleEsc}</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> {$narrativeEsc}
        </div>
{$body}
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 通关后请回到 <a href="/challenge/{$dirName}" class="text-gold">关卡详情</a> 提交 Flag。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/{$dirName}" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>
PHP;
    file_put_contents("$dir/index.php", $indexContent);
    file_put_contents("$dir/vulnerable.php", $vulnCode);
    file_put_contents("$dir/secure.php", $secureCode);
    $created++;
}

echo "✅ 修真期已生成 {$created} 个关卡\n";

// =====================================================
// 工具函数
// =====================================================

function generateBody(string $vuln): string {
    return match($vuln) {
        'xss_ref' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">咒语：</span>
                <input type="text" name="msg" class="form-control" autofocus>
                <button class="xxr-btn xxr-btn-primary">念出</button>
            </div>
        </form>
HTML,
        'csrf_get' => <<<'HTML'
        <p>当前余额：<strong>1000</strong> 灵石</p>
        <a href="?transfer=1&to=attacker&amount=999" class="xxr-btn xxr-btn-primary">点击转账</a>
HTML,
        'sqli_num' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">丹方编号：</span>
                <input type="text" name="id" class="form-control" placeholder="试: 1 OR 1=1" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,
        'sqli_str' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">弟子名：</span>
                <input type="text" name="name" class="form-control" placeholder="试: ' OR '1'='1" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,
        'sqli_union' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">编号：</span>
                <input type="text" name="id" class="form-control" placeholder="试: 1' UNION SELECT 1,version(),3-- -" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,
        'sqli_error' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="试: 1' AND extractvalue(1,concat(0x7e,version()))-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,
        'sqli_bool' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">用户名：</span>
                <input type="text" name="name" class="form-control" placeholder="试: admin' AND 1=1-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,
        'sqli_time' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">用户名：</span>
                <input type="text" name="name" class="form-control" placeholder="试: admin' AND SLEEP(5)-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,
        'file_read' => <<<'HTML'
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">文件：</span>
                <input type="text" name="file" class="form-control" placeholder="试: ../../../etc/passwd">
                <button class="xxr-btn xxr-btn-primary">读取</button>
            </div>
        </form>
HTML,
        'upload_js' => <<<'HTML'
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传心法 (.txt)</label>
                <input type="file" name="file" class="form-control" accept=".txt" id="fileInput">
            </div>
            <button class="xxr-btn xxr-btn-primary" onclick="return check()">上传</button>
        </form>
HTML,
        'open_redirect' => <<<'HTML'
        <a href="?url=https://example.com" class="xxr-btn xxr-btn-primary">跳转</a>
HTML,
        'xss_stored' => <<<'HTML'
        <form method="POST">
            <textarea name="content" class="form-control" rows="3" placeholder="留言..."></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交留言</button>
        </form>
HTML,
        'csrf_post' => <<<'HTML'
        <form method="POST">
            <input type="hidden" name="transfer" value="1">
            <button class="xxr-btn xxr-btn-primary">提交转账</button>
        </form>
HTML,
        'clickjacking' => <<<'HTML'
        <button class="xxr-btn xxr-btn-primary" onclick="alert('OK')">确认</button>
HTML,
        default => '<p class="text-muted">关卡环境已就绪。</p>',
    };
}

function generateFlag(string $vuln, string $id): string {
    $name = str_replace(['qy_', 'lh_', 'wm_'], '', explode('_', str_replace('_', '_', $id))[0]);
    return "flag{" . str_replace('-', '_', $vuln) . "_" . substr(md5($id), 0, 8) . "}";
}

function generateVulnerable(string $vuln, string $flag): string {
    $code = "<?php\n/**\n * vulnerable.php - 漏洞演示\n * 修真靶场默认配置：display_errors=On, allow_url_include=On\n */\n\n";
    return match($vuln) {
        'xss_ref' => $code . <<<PHP
\$msg = \$_GET['msg'] ?? '';
echo "回显：" . \$msg;  // 【漏洞】未转义
PHP,
        'csrf_get' => $code . <<<PHP
if (isset(\$_GET['transfer'])) {
    \$to = \$_GET['to'] ?? 'attacker';
    \$amount = (float) (\$_GET['amount'] ?? 0);
    echo "已向 \$to 转账 \$amount 灵石";
}
PHP,
        'sqli_num' => $code . <<<PHP
try {
    \$pdo = new PDO('mysql:host=db;dbname=xiuxian_range;charset=utf8mb4', 'xiuxian', 'xiuxian_pass',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    \$id = \$_GET['id'] ?? '1';
    foreach (\$pdo->query("SELECT username, email FROM demo_users WHERE id = \$id") as \$row) {
        echo "弟子：" . htmlspecialchars(\$row['username']) . "<br>";
    }
} catch (PDOException \$e) { echo "错误：" . \$e->getMessage(); }
PHP,
        'sqli_str' => $code . <<<PHP
try {
    \$pdo = new PDO('mysql:host=db;dbname=xiuxian_range;charset=utf8mb4', 'xiuxian', 'xiuxian_pass');
    \$name = \$_GET['name'] ?? '';
    foreach (\$pdo->query("SELECT email FROM demo_users WHERE username = '\$name'") as \$row) {
        echo "邮箱：" . htmlspecialchars(\$row['email']);
    }
} catch (PDOException \$e) { echo "错误：" . \$e->getMessage(); }
PHP,
        'sqli_union' => $code . <<<PHP
try {
    \$pdo = new PDO('mysql:host=db;dbname=xiuxian_range;charset=utf8mb4', 'xiuxian', 'xiuxian_pass');
    \$id = \$_GET['id'] ?? '1';
    foreach (\$pdo->query("SELECT id, username, email FROM demo_users WHERE id = '\$id'") as \$row) {
        echo "ID={\$row['id']} 用户={\$row['username']}<br>";
    }
} catch (PDOException \$e) { echo "错误：" . \$e->getMessage(); }
PHP,
        'sqli_error' => $code . <<<PHP
try {
    \$pdo = new PDO('mysql:host=db;dbname=xiuxian_range;charset=utf8mb4', 'xiuxian', 'xiuxian_pass',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    \$id = \$_GET['id'] ?? '1';
    foreach (\$pdo->query("SELECT * FROM demo_users WHERE id = '\$id'") as \$row) { print_r(\$row); }
} catch (PDOException \$e) { echo "错误：" . \$e->getMessage(); }
PHP,
        'sqli_bool' => $code . <<<PHP
try {
    \$pdo = new PDO('mysql:host=db;dbname=xiuxian_range;charset=utf8mb4', 'xiuxian', 'xiuxian_pass');
    \$name = \$_GET['name'] ?? '';
    \$stmt = \$pdo->query("SELECT id FROM demo_users WHERE username = '\$name'");
    if (\$stmt->fetch()) { echo "✅ 用户存在"; } else { echo "❌ 用户不存在"; }
} catch (Exception \$e) { echo "错误"; }
PHP,
        'sqli_time' => $code . <<<PHP
try {
    \$pdo = new PDO('mysql:host=db;dbname=xiuxian_range;charset=utf8mb4', 'xiuxian', 'xiuxian_pass');
    \$name = \$_GET['name'] ?? '';
    \$pdo->query("SELECT id FROM demo_users WHERE username = '\$name'");
} catch (Exception \$e) { echo "错误"; }
PHP,
        'file_read' => $code . <<<PHP
\$file = \$_GET['file'] ?? 'index.php';
@readfile(\$file);  // 【漏洞】目录穿越
PHP,
        'upload_js' => $code . <<<PHP
// 【漏洞】仅前端校验
if (\$_FILES) {
    move_uploaded_file(\$_FILES['file']['tmp_name'], 'uploads/' . \$_FILES['file']['name']);
    echo "上传成功";
}
?>
<script>function check() { return document.getElementById('fileInput').value.endsWith('.txt'); }</script>
PHP,
        'open_redirect' => $code . <<<PHP
if (isset(\$_GET['url'])) {
    header("Location: " . \$_GET['url']);  // 【漏洞】无校验
    exit;
}
PHP,
        'xss_stored' => $code . <<<PHP
\$f = __DIR__ . '/comments.txt';
if (\$_SERVER['REQUEST_METHOD'] === 'POST' && !empty(\$_POST['content'])) {
    file_put_contents(\$f, \$_POST['content'] . "\n", FILE_APPEND);
}
foreach (file(\$f) as \$line) {
    echo '<div class="xxr-narrative">' . \$line . '</div>';  // 【漏洞】
}
PHP,
        'csrf_post' => $code . <<<PHP
// 【漏洞】POST 操作无 CSRF token
if (\$_SERVER['REQUEST_METHOD'] === 'POST' && isset(\$_POST['transfer'])) {
    echo "已转账成功";
}
PHP,
        'clickjacking' => $code . <<<PHP
// 【漏洞】未设置 X-Frame-Options
echo '<button onclick="alert(\\'OK\\')">确认</button>';
PHP,
        default => $code . "// 待实现",
    };
}

function generateSecure(string $vuln): string {
    return match($vuln) {
        'xss_ref' => "<?php\n// 修复：htmlspecialchars + CSP\nheader(\"Content-Security-Policy: default-src 'self'\");\n\$msg = \$_GET['msg'] ?? '';\necho '回显：' . htmlspecialchars(\$msg, ENT_QUOTES, 'UTF-8');",
        'csrf_get' => "<?php\n// 修复：仅 POST + CSRF Token\nif (\$_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }\nif (!hash_equals(\$_SESSION['csrf'] ?? '', \$_POST['_token'] ?? '')) { http_response_code(419); exit; }",
        'sqli_num' => "<?php\n\$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);\nif (!\$id) exit('Invalid ID');\n\$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');\n\$stmt = \$pdo->prepare('SELECT username FROM demo_users WHERE id = ?');\n\$stmt->execute([\$id]);",
        'sqli_str' => "<?php\n\$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');\n\$stmt = \$pdo->prepare('SELECT email FROM demo_users WHERE username = ?');\n\$stmt->execute([\$_GET['name'] ?? '']);",
        'sqli_union' => "<?php\n\$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');\n\$stmt = \$pdo->prepare('SELECT id, username, email FROM demo_users WHERE id = ?');\n\$stmt->execute([(int)(\$_GET['id'] ?? 1)]);",
        'sqli_error' => "<?php\nini_set('display_errors', '0');\n\$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);\n\$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');\n\$stmt = \$pdo->prepare('SELECT * FROM demo_users WHERE id = ?');\n\$stmt->execute([\$id]);",
        'sqli_bool' => "<?php\n\$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');\n\$stmt = \$pdo->prepare('SELECT id FROM demo_users WHERE username = ?');\n\$stmt->execute([\$_GET['name'] ?? '']);\necho \$stmt->fetch() ? 'exists' : 'no';",
        'sqli_time' => "<?php\n\$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');\n\$stmt = \$pdo->prepare('SELECT id FROM demo_users WHERE username = ?');\n\$stmt->execute([\$_GET['name'] ?? '']);",
        'file_read' => "<?php\n// 修复：白名单 + 路径规范化\n\$allowedDir = realpath(__DIR__ . '/data');\n\$file = \$_GET['file'] ?? '';\n\$realPath = realpath(\$allowedDir . '/' . \$file);\nif (!\$realPath || !str_starts_with(\$realPath, \$allowedDir)) { http_response_code(403); exit; }\nreadfile(\$realPath);",
        'upload_js' => "<?php\n// 修复：服务端验证\n\$ext = strtolower(pathinfo(\$_FILES['file']['name'], PATHINFO_EXTENSION));\n\$mime = mime_content_type(\$_FILES['file']['tmp_name']);\nif (!in_array(\$ext, ['txt', 'pdf', 'jpg', 'png'])) { exit('Invalid'); }\n\$newName = bin2hex(random_bytes(8)) . '.' . \$ext;\nmove_uploaded_file(\$_FILES['file']['tmp_name'], 'uploads/' . \$newName);",
        'open_redirect' => "<?php\n// 修复：白名单\n\$allowed = ['https://xiuxian-range.local'];\n\$url = \$_GET['url'] ?? '';\nif (!in_array(\$url, \$allowed, true)) { http_response_code(400); exit; }\nheader('Location: ' . \$url);",
        'xss_stored' => "<?php\n\$f = __DIR__ . '/comments.txt';\nif (\$_SERVER['REQUEST_METHOD'] === 'POST' && !empty(\$_POST['content'])) {\n    file_put_contents(\$f, strip_tags(\$_POST['content']) . \"\\n\", FILE_APPEND);\n}\nforeach (file(\$f) as \$line) {\n    echo '<div>' . htmlspecialchars(\$line, ENT_QUOTES, 'UTF-8') . '</div>';\n}",
        'csrf_post' => "<?php\n// 修复：CSRF Token\nsession_start();\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    if (!hash_equals(\$_SESSION['csrf'] ?? '', \$_POST['_token'] ?? '')) { http_response_code(419); exit; }\n    // 业务逻辑\n}",
        'clickjacking' => "<?php\n// 修复：X-Frame-Options\nheader('X-Frame-Options: DENY');\nheader(\"Content-Security-Policy: frame-ancestors 'none'\");\necho '<button>确认</button>';",
        default => "<?php\n// 安全实现",
    };
}