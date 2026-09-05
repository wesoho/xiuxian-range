<?php
/**
 * 修真靶场 - 金丹期/元婴期/化神期关卡完善
 *
 * 为高阶关卡补充完整可玩代码
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/../../public/challenges';

// 金丹期关卡（元婴、化神类似）
$content = [
    // ====== 金丹期 ======
    'qy_jd_01_xss_filter' => [
        'vulnerable' => <<<'PHP'
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
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：完整属性转义
$msg = $_GET['msg'] ?? '';
?>
<div title="<?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>">悬停查看 title</div>
PHP,
    ],

    'qy_jd_02_xss_bypass' => [
        'vulnerable' => <<<'PHP'
<?php
// XSS 关键字过滤（可大小写/双写绕过）
$msg = $_GET['msg'] ?? '';
$msg = preg_replace('/script/i','', $msg);  // 仅过滤一次
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>咒语变形</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🔮 咒语变形</h2>
        <form method="GET">
            <input type="text" name="msg" class="form-control">
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <div class="mt-3">
            <!-- 【漏洞】<scrscriptipt> 可绕过 -->
            <?= $msg ?>
        </div>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单 + 输出转义
$msg = preg_replace('/[^a-zA-Z0-9\s\p{Han}]/u', '', $_GET['msg'] ?? '');
echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
PHP,
    ],

    'lh_jd_11_lfi_basic' => [
        'vulnerable' => <<<'PHP'
<?php
// LFI 目录穿越
$file = $_GET['file'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>轮回之眼·LFI</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>👁️ 轮回之眼</h2>
        <form method="GET">
            <input type="text" name="file" class="form-control" placeholder="试：../../etc/passwd">
            <button class="xxr-btn xxr-btn-primary mt-2">读取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        // 【漏洞】未限制路径
        include $file;
        ?>
        </pre>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单
$allowed = ['home', 'about', 'contact'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed, true)) exit('Not allowed');
include __DIR__ . '/pages/' . $file . '.php';
PHP,
    ],

    'lh_jd_12_lfi_filter' => [
        'vulnerable' => <<<'PHP'
<?php
// LFI + php://filter 读源码
$file = $_GET['file'] ?? 'index.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>PHP之源·伪协议</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📖 PHP之源</h2>
        <form method="GET">
            <input type="text" name="file" class="form-control" placeholder="php://filter/convert.base64-encode/resource=index.php">
            <button class="xxr-btn xxr-btn-primary mt-2">读取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if (preg_match('/php|flag/i', $file)) {
            // 简单过滤可绕过
            echo 'blocked';
        } else {
            include $file;
        }
        ?>
        </pre>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单
$allowed = ['home', 'about'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed, true)) exit('Not allowed');
include __DIR__ . '/pages/' . $file . '.php';
PHP,
    ],

    'wm_jd_13_upload_mime' => [
        'vulnerable' => <<<'PHP'
<?php
// 文件上传 MIME 绕过
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>灵识伪装·MIME 绕过</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🎭 灵识伪装</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" class="form-control">
            <button class="xxr-btn xxr-btn-primary mt-2">上传</button>
        </form>
        <?php
        if ($_FILES) {
            // 【漏洞】只检查 Content-Type（可伪造）
            $allowed = ['image/jpeg', 'image/png'];
            if (in_array($_FILES['file']['type'], $allowed)) {
                move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
                echo '<div class="alert alert-success">上传成功</div>';
            }
        }
        ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 mime_content_type 而非客户端 Content-Type
$allowed = ['jpg', 'png'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$realMime = mime_content_type($_FILES['file']['tmp_name']);

if (!in_array($ext, $allowed, true) || !str_starts_with($realMime, 'image/')) {
    http_response_code(400);
    exit('Invalid file');
}
$newName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $newName);
PHP,
    ],

    'wm_jd_14_upload_ext' => [
        'vulnerable' => <<<'PHP'
<?php
// 黑名单扩展名绕过
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>禁咒文件·扩展名绕过</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📜 禁咒文件</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" class="form-control">
            <button class="xxr-btn xxr-btn-primary mt-2">上传</button>
        </form>
        <?php
        if ($_FILES) {
            $blocked = ['php', 'asp', 'jsp'];
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            // 【漏洞】黑名单不全（.php5 .phtml .phar 可绕过）
            if (!in_array(strtolower($ext), $blocked)) {
                move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
                echo '<div class="alert alert-success">上传成功</div>';
            }
        }
        ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单
$allowed = ['jpg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed, true)) exit('Extension not allowed');
PHP,
    ],

    'qy_jd_15_upload_img' => [
        'vulnerable' => <<<'PHP'
<?php
// 图片马 + getimagesize 绕过
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>金身绘像·图片马</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🖼️ 金身绘像</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" class="form-control" accept="image/*">
            <button class="xxr-btn xxr-btn-primary mt-2">上传</button>
        </form>
        <?php
        if ($_FILES) {
            // 【漏洞】getimagesize 只能验证图片格式，但图片内可嵌入 PHP 代码
            if (@getimagesize($_FILES['file']['tmp_name'])) {
                move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
                echo '<div class="alert alert-success">上传成功</div>';
                echo '<p>访问 <a href="/challenges/qingong/qy_jd_15_upload_img/uploads/' . htmlspecialchars($_FILES['file']['name']) . '">上传的文件</a></p>';
            }
        }
        ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 GD 重新生成图片（彻底去除 PHP 代码）
$allowed = ['jpg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$tmp = $_FILES['file']['tmp_name'];

if (!in_array($ext, $allowed, true) || !getimagesize($tmp)) {
    exit('Invalid image');
}

// 用 GD 重新生成（剥离所有 PHP 代码）
$image = match($ext) {
    'jpg' => imagecreatefromjpeg($tmp),
    'png' => imagecreatefrompng($tmp),
    'gif' => imagecreatefromgif($tmp),
};

$newName = bin2hex(random_bytes(8)) . '.' . $ext;
$dstPath = 'uploads/' . $newName;
match($ext) {
    'jpg' => imagejpeg($image, $dstPath),
    'png' => imagepng($image, $dstPath),
    'gif' => imagegif($image, $dstPath),
};
PHP,
    ],

    'qy_jd_09_rce_space' => [
        'vulnerable' => <<<'PHP'
<?php
// 命令注入空格过滤绕过
$ip = $_GET['ip'] ?? '127.0.0.1';
$ip = str_replace(' ', '', $ip);  // 仅过滤空格
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>空间的缝隙</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌌 空间的缝隙</h2>
        <form method="GET">
            <input type="text" name="ip" class="form-control" placeholder="${IFS}cat${IFS}/etc/passwd">
            <button class="xxr-btn xxr-btn-primary mt-2">测灵</button>
        </form>
        <pre>
        <?php
        echo shell_exec("ping -c 1 $ip");
        // 【漏洞】空格过滤可被 ${IFS} 绕过
        ?>
        </pre>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单 IP
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) exit('Invalid IP');
echo shell_exec('ping -c 1 ' . escapeshellarg($ip));
PHP,
    ],

    'qy_jd_10_rce_filter' => [
        'vulnerable' => <<<'PHP'
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
        system($cmd);
        // 【漏洞】双写、通配符可绕过（ca${x}at 等）
        ?>
        </pre>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单命令
$allowed = ['status', 'ping', 'uptime'];
$cmd = $_GET['cmd'] ?? '';
if (!in_array($cmd, $allowed, true)) exit('Command not allowed');

// 安全执行
echo shell_exec($cmd . ' localhost');
PHP,
    ],

    // ====== 元婴期 ======
    'lh_yy_01_xss_dom' => [
        'vulnerable' => <<<'PHP'
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
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 textContent 而非 innerHTML
?>
<div id="output"></div>
<script>
const hash = location.hash.substring(1);
document.getElementById('output').textContent = hash;  // 安全
</script>
PHP,
    ],

    'lh_yy_08_deser_wakeup' => [
        'vulnerable' => <<<'PHP'
<?php
// 反序列化 __wakeup 绕过
class User {
    public $username;
    public $role = 'guest';

    public function __wakeup() {
        // 【漏洞】__wakeup 可以绕过
        if ($this->role !== 'admin') {
            $this->role = 'guest';
        }
    }
}

$data = $_POST['data'] ?? '';
$user = @unserialize($data);

if ($user instanceof User) {
    echo "用户：{$user->username} | 角色：{$user->role}";
}
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 allowed_classes + JSON 替代
class User {
    public $username;
    public $role = 'guest';
}

$data = $_POST['data'] ?? '';
// PHP 7+ allowed_classes 限制可反序列化类
$user = @unserialize($data, ['allowed_classes' => ['User']]);

if ($user instanceof User && $user->role === 'admin') {
    echo "Admin 用户：{$user->username}";
}

// 或更安全：使用 JSON
// $user = json_decode(file_get_contents('php://input'), true);
PHP,
    ],

    'wm_yy_03_xxe_file' => [
        'vulnerable' => <<<'PHP'
<?php
// XXE 文件读取
$xml = $_POST['xml'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>魔影重重·XXE</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>👹 魔影重重</h2>
        <form method="POST">
            <textarea name="xml" class="form-control" rows="6" placeholder='<!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><foo>&xxe;</foo>'></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if ($xml) {
            // 【漏洞】未禁用外部实体
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            echo htmlspecialchars($dom->saveXML());
        }
        ?>
        </pre>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：禁用外部实体
libxml_disable_entity_loader(true);  // PHP < 8.0
$xml = $_POST['xml'] ?? '';

$dom = new DOMDocument();
$dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD);  // PHP 8.0+
echo htmlspecialchars($dom->saveXML());
PHP,
    ],

    'qy_yy_05_ssrf_basic' => [
        'vulnerable' => <<<'PHP'
<?php
// SSRF 基础
$url = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>元神出窍·SSRF</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌀 元神出窍</h2>
        <form method="GET">
            <input type="text" name="url" class="form-control" placeholder="file:///etc/passwd">
            <button class="xxr-btn xxr-btn-primary mt-2">拉取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if ($url) {
            // 【漏洞】未限制协议
            $content = @file_get_contents($url);
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
// 修复：白名单
$allowed = ['https://xiuxian-range.local'];
$url = $_GET['url'] ?? '';
$host = parse_url($url, PHP_URL_HOST);
if (!in_array($host, $allowed, true)) exit('URL not allowed');

$content = @file_get_contents($url);
echo htmlspecialchars($content);
PHP,
    ],

    'wm_yy_09_deser_pop' => [
        'vulnerable' => <<<'PHP'
<?php
// POP 链构造
class Gadget {
    public $cmd;

    public function __destruct() {
        system($this->cmd);  // 【漏洞】RCE
    }
}

class Trigger {
    public $gadget;

    public function __toString() {
        return is_object($this->gadget) ? serialize($this->gadget) : '';
    }
}

class Chain {
    public $next;

    public function __wakeup() {
        // 触发链式调用
        echo $this->next;  // 触发 __toString
    }
}

$data = $_POST['data'] ?? '';
unserialize($data);
// 攻击者构造: O:5:"Chain":1:{s:4:"next";O:7:"Trigger":1:{s:6:"gadget";O:6:"Gadget":1:{s:3:"cmd";s:2:"id";}}}
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：allowed_classes 限制
$data = $_POST['data'] ?? '';
// 限制可反序列化的类
$obj = @unserialize($data, ['allowed_classes' => []]);  // 完全禁止对象
// 或使用 JSON
PHP,
    ],

    // ====== 化神期 ======
    'wm_hs_01_jwt_none' => [
        'vulnerable' => <<<'PHP'
<?php
// JWT alg=none 攻击
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>无相法印·JWT</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🔓 无相法印</h2>
        <p>JWT Token 鉴权</p>
        <div id="result"></div>
    </div>
    <script>
    async function check() {
        const token = prompt('输入 JWT Token（试 alg=none）');
        const res = await fetch('/api/check-jwt', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({token})
        });
        document.getElementById('result').innerHTML = await res.text();
    }
    check();
    </script>
    <?php
    // 后端验证逻辑（教学演示）
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $token = $input['token'] ?? '';
        $parts = explode('.', $token);
        if (count($parts) === 3) {
            $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
            // 【漏洞】未验证 alg
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            echo "用户：{$payload['user']}, 角色：{$payload['role']}";
        }
    }
    ?>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 firebase/php-jwt 库
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;

$secret = 'super-secret-key-32-chars-minimum-12345678';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? '';

    try {
        // 强制使用 HS256，禁用 alg=none
        $decoded = JWT::decode($token, new \Firebase\JWT\Key($secret, 'HS256'));
        echo "用户：{$decoded->user}, 角色：{$decoded->role}";
    } catch (Exception $e) {
        http_response_code(401);
        echo 'Invalid token';
    }
}
PHP,
    ],

    'wm_hs_02_jwt_weak' => [
        'vulnerable' => <<<'PHP'
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
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 32+ 字节的强密钥
$secret = bin2hex(random_bytes(32));  // 64 字符 hex
// 或使用环境变量管理密钥
$secret = getenv('JWT_SECRET');
PHP,
    ],

    'lh_hs_05_cors' => [
        'vulnerable' => <<<'PHP'
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
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：精确 CORS 白名单
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['https://xiuxian-range.local'];

if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}
PHP,
    ],

    'wm_hs_13_php_type' => [
        'vulnerable' => <<<'PHP'
<?php
// PHP 弱类型比较
$password = $_POST['password'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>弱类型幻象</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🪞 弱类型幻象</h2>
        <form method="POST">
            <input type="text" name="password" class="form-control" placeholder="试：0e123">
            <button class="xxr-btn xxr-btn-primary mt-2">登录</button>
        </form>
        <?php
        // 【漏洞】弱类型比较
        if ($password == 0) {
            // "0e123" == "0e456" 都为 0
            echo '<div class="alert alert-success">登录成功（实际密码是 0）</div>';
        }
        ?>
    </div>
</body>
</html>
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：严格比较 ===
$password = $_POST['password'] ?? '';
if ($password === '0') {  // 严格类型
    echo '登录成功';
}

// 或使用 password_verify
$hash = password_hash('0', PASSWORD_BCRYPT);
if (password_verify($password, $hash)) {
    echo '登录成功';
}
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

echo "✅ 已更新 $updated 个金丹/元婴/化神期关卡的完整可玩代码\n";