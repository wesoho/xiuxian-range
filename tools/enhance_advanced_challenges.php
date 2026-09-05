<?php
/**
 * 修真靶场 - 高阶关卡 vulnerable.php / secure.php 增强生成器
 *
 * 修真靶场的高阶关卡（炼虚期/合体期/大乘期）需要详细的 vulnerable.php
 * 和 secure.php，提供：
 *  - 多漏洞串联的攻击链
 *  - 真实漏洞代码
 *  - 完整的防御代码
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/../../public/challenges';

// 详细的高阶关卡 vulnerable/secure 代码
$advancedContent = [
    // ========== 炼虚期 ==========
    'qy_lx_01_lfi_log' => [
        'vulnerable' => <<<'PHP'
<?php
/**
 * 炼虚期·QY-LX-01 日志成魔
 * 漏洞：LFI + 日志投毒 → RCE
 *
 * 攻击步骤：
 *   1. 通过 User-Agent 注入 PHP 代码到 access.log
 *   2. LFI 包含日志文件
 *   3. PHP 代码执行，获取 Flag
 */

// 修真靶场日志路径（教学演示）
$logFile = '/var/log/apache2/access.log';

if (isset($_GET['file'])) {
    // 【漏洞】未限制 LFI 路径
    $file = $_GET['file'];
    include $file;  // 可包含日志
} else {
    echo '<p>本文件支持 include 任意路径</p>';
    echo '<p>示例：?file=../../../etc/passwd</p>';
}
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：白名单文件包含
$allowed = ['home', 'about', 'contact'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed, true)) {
    http_response_code(403);
    exit('File not allowed');
}

// 防止日志投毒：过滤 User-Agent 中的特殊字符
if (preg_match('/<\?|<script/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
    http_response_code(400);
    exit('Invalid User-Agent');
}

include __DIR__ . '/pages/' . $file . '.php';
PHP,
    ],
    'lh_lx_02_lfi_sess' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：Session 注入 + LFI
session_start();

// 在 session 中存储可控数据
if (isset($_POST['name'])) {
    $_SESSION['username'] = $_POST['name'];  // 【漏洞】未过滤
}

// LFI 包含 Session 文件
if (isset($_GET['file'])) {
    include $_GET['file'];  // 可包含 /var/lib/php/sessions/sess_xxx
} else {
    echo '<form method="POST"><input name="name"><button>提交</button></form>';
}
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 Redis 存储 Session + 严格过滤
session_start();

// 严格过滤
$name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['name'] ?? '');
$_SESSION['username'] = $name;

// LFI 路径限制
$allowed = ['home', 'profile'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed, true)) exit('Not allowed');
include __DIR__ . '/pages/' . $file . '.php';
PHP,
    ],
    'wm_lx_03_sqli_shell' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：SQL 注入 + INTO OUTFILE GetShell
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';
// 【漏洞】直接拼接 + 数据库用户具有 FILE 权限
$pdo->query("SELECT username FROM demo_users WHERE id = $id INTO OUTFILE '/var/www/html/uploads/shell.php'");
// 写入内容是 PHP 代码
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：参数化 + 数据库用户最小权限（无 FILE）
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'xiuxian_readonly', 'xxx',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) { die('fail'); }

$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
PHP,
    ],
    'qy_lx_04_upload_hta' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：可上传 .htaccess
if ($_FILES && isset($_FILES['file'])) {
    $name = $_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
    echo "上传成功：$name";
}
// 攻击者可上传 AddType application/x-httpd-php .jpg
// 然后上传 .jpg 文件即可按 PHP 解析
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：禁止上传 .htaccess 等配置文件
$blocked = ['.htaccess', '.htpasswd', '.user.ini', 'web.config'];
$name = $_FILES['file']['name'] ?? '';
if (in_array($name, $blocked, true)) exit('Filename blocked');
if (preg_match('/\.(htaccess|htpasswd|user\.ini|web\.config)$/i', $name)) exit('Blocked');

// 同时检查 Apache 配置：<Directory> AllowOverride None
PHP,
    ],
    'lh_lx_05_upload_ntfs' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：NTFS 流绕过（仅 Windows 有效）
// 文件名 "shell.php::$DATA" 在 Windows 实际存储为 shell.php
if ($_FILES) {
    $name = $_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
    echo "上传：$name";
}
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：剥离 NTFS 流
$name = preg_replace('/:.*$/', '', $_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
PHP,
    ],
    'wm_lx_06_php_strcmp' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：strcmp 数组绕过
$password = $_POST['password'] ?? '';
if (strcmp($password, 'secret') == 0) {  // 传入数组返回 NULL == 0 为真
    echo '登录成功';
}
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 password_verify（永远返回 bool）
$hash = password_hash('secret', PASSWORD_BCRYPT);
if (password_verify($_POST['password'] ?? '', $hash)) {
    echo '登录成功';
}
PHP,
    ],
    'qy_lx_07_sqli_multi' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：mysqli_multi_query
$mysqli = new mysqli('db', 'xiuxian', 'xiuxian_pass', 'xiuxian_range');
$id = $_GET['id'] ?? '1';
if (isset($_GET['id'])) {
    if ($mysqli->multi_query("SELECT * FROM demo_users WHERE id = $id")) {
        do { /* 多语句结果 */ } while ($mysqli->next_result());
    }
}
// 攻击者可追加 UPDATE 修改管理员密码
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：使用 prepare（不支持多语句）
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
$pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
PHP,
    ],
    'lh_lx_08_docker' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：Docker 容器逃逸（教学演示）
// 容器以特权模式运行 + 挂载 /proc /sys /dev
echo '<h2>Docker 容器逃逸教学</h2>';
echo '<p>实际攻击需要特权容器模式 + 挂载敏感路径</p>';
echo '<p>本修真靶场为安全设计，未启用特权模式</p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：
// 1. Docker 容器以非 root 用户运行
// 2. 禁用 --privileged
// 3. 启用 seccomp / AppArmor
// 4. 最小权限原则（只读文件系统、drop capabilities）
// 5. 使用 Distroless 镜像
PHP,
    ],
    'wm_lx_09_php_cgi' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：PHP-CGI 参数注入（CVE-2024-4577）
// 攻击：?%ADd+allow_url_include%3d1+%ADd+auto_prepend_file%3dphp://input
echo "PHP-CGI 漏洞利用";
echo "<p>攻击者可注入 php.ini 配置实现 RCE</p>";
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：
// 1. 升级 PHP 8.x（已修复 CVE-2024-4577）
// 2. 使用 PHP-FPM 替代 PHP-CGI
// 3. 配置 cgi.fix_pathinfo=0
// 4. NginX/Apache 拒绝畸形请求
header('X-Frame-Options: DENY');
PHP,
    ],
    'qy_lx_10_cache_adv' => [
        'vulnerable' => <<<'PHP'
<?php
// 漏洞：高级缓存投毒
$path = $_GET['page'] ?? 'index.html';
// Varnish 缓存键包含 page 但不包含 User-Agent
// 攻击者通过 X-Forwarded-Host 投毒
header("X-Cache: MISS");
echo file_get_contents("cache/$path");
PHP,
        'secure' => <<<'PHP'
<?php
// 修复：缓存键包含用户身份
header('Vary: Cookie, Authorization, User-Agent, X-Forwarded-Host');
header("Cache-Control: private, no-cache");
PHP,
    ],

    // ========== 合体期（剧情综合） ==========
    'lh_ht_01_xss_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合 XSS：反射 + 存储 + DOM
// 修真靶场汇总：访问其他 XSS 关卡完成合体
echo '<h2>试炼塔·XSS 综合</h2>';
echo '<p>三大子关：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_jz_01_xss_ref/">试炼塔第一层（反射型）</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_12_xss_store/">试炼塔第二层（存储型）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_01_xss_dom/">试炼塔第三层（DOM型）</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">flag{xss_tower_81}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合 XSS 防御：CSP + 输出转义
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'");

$msg = $_GET['msg'] ?? '';
echo '回显：' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
PHP,
    ],
    'wm_ht_02_deser_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合反序列化：__wakeup + POP + Phar + Session
// 修真靶场汇总：访问其他反序列化关卡
echo '<h2>魔窟·反序列化综合</h2>';
echo '<p>四大子关：</p>';
echo '<ol>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_08_deser_wakeup/">魔窟第一层（__wakeup）</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_09_deserialize_pop/">魔窟第二层（POP 链）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_11_deser_phar/">魔窟第三层（Phar）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_12_deser_sess/">魔窟第四层（Session）</a></li>';
echo '</ol>';
echo '<p>四关全通后获得：<code class="xxr-mono">flag{deserialize_dungeon_82}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合反序列化防御
// 1. 永远不要反序列化不可信数据
// 2. 使用 allowed_classes 选项限制可反序列化的类
// 3. 使用 json_encode/decode 替代 serialize/unserialize
// 4. Phar 包装器禁用

$data = $_POST['data'] ?? '';
// 修复：使用 JSON + 签名
$decoded = json_decode(base64_decode($data), true);
if (!is_array($decoded)) {
    http_response_code(400);
    exit('Invalid data');
}
PHP,
    ],
    'qy_ht_03_sqli_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合 SQL 注入：UNION + 盲注 + GetShell
echo '<h2>藏经阁·SQL 综合</h2>';
echo '<p>三阶段：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_jz_04_sqli_str/">第一阶段：字符型注入</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_jz_05_sqli_union/">第二阶段：UNION 注入</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_lx_03_sqli_shell/">第三阶段：SQLi GetShell</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">flag{sqli_library_83}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合 SQL 注入防御：参数化查询（终极方案）
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
$pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$id]);
PHP,
    ],
    'lh_ht_04_auth_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合认证漏洞
echo '<h2>轮回殿·认证综合</h2>';
echo '<p>三道防线：</p>';
echo '<ol>';
echo '<li><a href="/challenges/wanmozong/wm_lq_09_sqli_error/">防线一：SQL 注入获取凭据</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_01_jwt_none/">防线二：JWT alg=none</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_14_password_reset/">防线三：任意密码重置</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">flag{auth_palace_84}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合认证防御：多因素认证 + 安全密码哈希
session_start();
session_regenerate_id(true);

// 1. 密码哈希
$hash = password_hash($password, PASSWORD_ARGON2ID);

// 2. 多因素
// 3. 失败锁定
$_SESSION['attempts'] ??= 0;
if ($_SESSION['attempts'] > 5) {
    http_response_code(429);
    exit('Too many attempts');
}
PHP,
    ],
    'wm_ht_05_ssrf_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合 SSRF
echo '<h2>炼魂殿·SSRF 综合</h2>';
echo '<p>三阶段：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_yy_05_ssrf_basic/">第一阶段：基础 SSRF</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_06_ssrf_protocol/">第二阶段：gopher 攻击 Redis</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_07_ssrf_rebind/">第三阶段：DNS rebinding</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">flag{ssrf_soul_temple_85}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合 SSRF 防御
$url = $_GET['url'] ?? '';
$parsed = parse_url($url);

// 1. 白名单域名
$allowed = ['xiuxian-range.local'];
if (!in_array($parsed['host'] ?? '', $allowed, true)) {
    exit('Domain not allowed');
}

// 2. 禁用危险协议
$blocked = ['gopher', 'dict', 'ldap', 'file', 'ftp'];
if (in_array($parsed['scheme'] ?? '', $blocked, true)) {
    exit('Protocol not allowed');
}

// 3. 解析后再次检查 IP（防 DNS rebinding）
$ip = gethostbyname($parsed['host']);
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
    exit('Private IP blocked');
}
PHP,
    ],
    'qy_ht_06_csrf_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合 CSRF
echo '<h2>阵法台·CSRF 综合</h2>';
echo '<p>四道阵法：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_jz_02_csrf_get/">阵法一：GET 型 CSRF</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_jz_10_csrf_post/">阵法二：POST 型 CSRF</a></li>';
echo '<li><a href="/challenges/qingong/qy_jd_03_csrf_token/">阵法三：Token 可预测</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_05_cors/">阵法四：CORS 配置错误</a></li>';
echo '</ol>';
echo '<p>四关全通后获得：<code class="xxr-mono">flag{csrf_array_86}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合 CSRF 防御
session_start();

// 1. CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. SameSite Cookie
session_set_cookie_params(['samesite' => 'Strict']);

// 3. 验证 Token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['_token'] ?? '')) {
        http_response_code(419);
        exit('CSRF token invalid');
    }
}

// 4. CORS 配置
header("Access-Control-Allow-Origin: https://xiuxian-range.local");
header("Access-Control-Allow-Credentials: true");
PHP,
    ],
    'lh_ht_07_crypto_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合密码学
echo '<h2>幽冥界·密码学综合</h2>';
echo '<p>四道谜题：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_hs_09_crypto_ecb/">谜题一：ECB 模式</a></li>';
echo '<li><a href="/challenges/qingong/qy_hs_10_crypto_hash/">谜题二：Hash 长度扩展</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_01_jwt_none/">谜题三：JWT alg=none</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_02_jwt_weak/">谜题四：JWT 弱密钥</a></li>';
echo '</ol>';
echo '<p>四关全通后获得：<code class="xxr-mono">flag{crypto_underworld_87}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合密码学实践

// 1. AES-GCM（而非 ECB）
$key = random_bytes(32);
$iv = random_bytes(12);  // GCM 用 12 字节 IV
$tag = '';
$ciphertext = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

// 2. HMAC（而非 H(secret||data)）
$signature = hash_hmac('sha256', $data, $secret);

// 3. 强 JWT
// 使用 lib 库（firebase/php-jwt），指定算法，禁止 alg=none
PHP,
    ],
    'wm_ht_08_rce_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 综合 RCE
echo '<h2>血池·RCE 综合</h2>';
echo '<p>三道血咒：</p>';
echo '<ol>';
echo '<li><a href="/challenges/wanmozong/wm_jz_09_rce_basic/">血咒一：基础 RCE</a></li>';
echo '<li><a href="/challenges/qingong/qy_jd_09_rce_space/">血咒二：空格过滤</a></li>';
echo '<li><a href="/challenges/qingong/qy_jd_10_rce_filter/">血咒三：关键字过滤</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">flag{rce_blood_pool_88}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 综合 RCE 防御

// 1. 白名单参数
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    exit('Invalid IP');
}

// 2. escapeshellarg 转义
$cmd = 'ping -c 1 ' . escapeshellarg($ip);

// 3. 禁用高危函数（php.ini）
// disable_functions = exec, system, passthru, shell_exec, popen, proc_open, eval

// 4. RASP 运行时拦截
echo "RCE 防御就绪";
PHP,
    ],
    'qy_ht_09_code_review' => [
        'vulnerable' => <<<'PHP'
<?php
// 迷你 CMS 代码审计挑战
echo '<h2>禁地·代码审计</h2>';
echo '<p>本修真靶场提供一个迷你 CMS 供代码审计：</p>';
echo '<p>请访问 <a href="/challenges/qingong/qy_jz_03_sqli_num/">SQL 注入关卡</a>、<a href="/challenges/qingong/qy_jz_01_xss_ref/">XSS 关卡</a>、<a href="/challenges/qingong/qy_jz_02_csrf_get/">CSRF 关卡</a> 综合审计。</p>';
echo '<p>修真靶场 CMS 综合关卡：<code class="xxr-mono">flag{cms_audit_89}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// CMS 安全实践：
// 1. 所有输入参数化
// 2. 所有输出转义
// 3. CSRF Token 全局
// 4. RBAC 权限控制
// 5. 文件上传白名单 + 重命名
// 6. 定期代码审计（SonarQube / Snyk）
// 7. 依赖扫描（Composer Audit）
echo "CMS 安全版本";
PHP,
    ],
    'lh_ht_10_logic_full' => [
        'vulnerable' => <<<'PHP'
<?php
// 业务逻辑综合
echo '<h2>万魔殿·业务逻辑综合</h2>';
echo '<p>三道考验：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_yy_12_payment_tamper/">考验一：支付篡改</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_13_captcha_reuse/">考验二：验证码重用</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_15_brute_force/">考验三：暴力破解</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">flag{logic_demon_palace_90}</code></p>';
PHP,
        'secure' => <<<'PHP'
<?php
// 业务逻辑安全实践
// 1. 服务端重新计算价格（不信任客户端）
// 2. 状态机强制流转
// 3. 并发控制（数据库锁、乐观锁）
// 4. 验证码一次性使用
// 5. 失败锁定 + 速率限制
// 6. 业务审计日志
echo "业务逻辑安全版本";
PHP,
    ],
];

$updated = 0;
$failed = 0;
foreach ($advancedContent as $dirName => $content) {
    $dirs = glob("$challengesDir/*/$dirName");
    if (empty($dirs)) {
        echo "⚠️  目录不存在: $dirName\n";
        $failed++;
        continue;
    }
    $dir = $dirs[0];

    if (isset($content['vulnerable'])) {
        file_put_contents("$dir/vulnerable.php", $content['vulnerable']);
    }
    if (isset($content['secure'])) {
        file_put_contents("$dir/secure.php", $content['secure']);
    }
    $updated++;
}

echo "✅ 已更新 $updated 个高阶关卡的 vulnerable.php / secure.php\n";
if ($failed > 0) echo "⚠️  $failed 个目录未找到\n";