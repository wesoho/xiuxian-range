<?php
/**
 * 修真靶场 - 逐关通关测试
 *
 * 对全部启用关卡执行完整闭环验证：
 *   第一步【取Flag】按每关的实际揭示方式获取当前随机 Flag，并比对数据库值；
 *   第二步【提交】用管理员会话调用 /challenge/submit-flag，确认平台判定通关。
 *
 * 揭示方式：
 *   - 默认按 category 发起对应攻击请求（GET 全家桶 / POST / multipart）；
 *   - 10 个有特殊动作的关卡走 OVERRIDES 定制输入（弱口令登录、响应头、夹具文件等）；
 *   - 任何一步失败都会在报告中标注，供人工复核。
 *
 * 用法：先启动开发服务器，再执行 php tools/test_passability.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/app/bootstrap_challenge.php';

$baseUrl = 'http://127.0.0.1:8686';
$pdo = db()->pdo();
$challenges = $pdo->query('SELECT id, flag, category, realm FROM challenges WHERE enabled = 1 ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

// ---------- 特殊关卡的定制输入 ----------
const OVERRIDES = [
    'QY-LQ-01' => [['get', '/challenges/qingong/qy_lq_01_html_comment/index.php', '']],
    'QY-LQ-02' => [['get', '/robots.txt', '']],
    'QY-LQ-03' => [['get', '/challenges/qingong/qy_lq_03_git/index.php', ''], ['get', '/challenges/qingong/qy_lq_03_git/.git/config', '']],
    'QY-LQ-04' => [['get', '/challenges/qingong/qy_lq_04_backup/index.php', ''], ['get', '/challenges/qingong/qy_lq_04_backup/www.zip', '']],
    'QY-LQ-05' => [['get', '/challenges/qingong/qy_lq_05_phpinfo/phpinfo.php', '']],
    'LH-LQ-06' => [['post', '/challenges/lunhuizong/lh_lq_06_weak_password/index.php', 'username=admin&password=admin']],
    'LH-LQ-07' => [['post', '/challenges/lunhuizong/lh_lq_07_js_validate/index.php', 'username=admin&password=xxr_lh_07']],
    'LH-LQ-08' => [['get', '/challenges/lunhuizong/lh_lq_08_header_leak/index.php', '']],
    'WM-LQ-09' => [['get', '/challenges/wanmozong/wm_lq_09_sqli_error/index.php?id=999', '']],
    'WM-LQ-10' => [['get', '/challenges/wanmozong/wm_lq_10_default_admin/index.php', '']],
];

const ATTACK_GET = '?id=1%27%20OR%201%3D1--'
    . '&x=%3Cscript%3Ealert(1)%3C%2Fscript%3E'
    . '&file=..%2F..%2Fetc%2Fpasswd'
    . '&cmd=ls%20%2F'
    . '&url=http%3A%2F%2F127.0.0.1%2F'
    . '&data=O%3A8%3A%22X%22%3A1%3A%7B%7D'
    . '&token=eyJhbGciOiJub25lIn0.eyJhIjoxfQ.sig'
    . '&hash=0e123456789'
    . '&a%5B%5D=1'
    . '&redirect=http%3A%2F%2Fevil.com'
    . '&origin=http%3A%2F%2Fevil.com';

function http(string $url, bool $post = false, string $body = '', array $headers = []): array
{
    $ch = curl_init($url);
    $opt = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_HEADER => true];
    if ($post) {
        $opt[CURLOPT_POST] = true;
        $opt[CURLOPT_POSTFIELDS] = $body;
    }
    if ($headers) {
        $opt[CURLOPT_HTTPHEADER] = $headers;
    }
    curl_setopt_array($ch, $opt);
    $raw = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    $headLen = (int) (curl_getinfo($ch, CURLINFO_HEADER_SIZE) ?: 0);
    // curl_close 之后 getinfo 不可靠，直接按 \r\n\r\n 切分
    $split = strpos($raw, "\r\n\r\n");
    return [$code, $split !== false ? substr($raw, 0, $split) : '', $split !== false ? substr($raw, $split + 4) : $raw];
}

function dir_url(string $root, string $id): ?string
{
    $prefix = strtolower(str_replace('-', '_', $id));
    $dirs = glob($root . '/public/challenges/*/' . $prefix . '*', GLOB_ONLYDIR);
    if (!$dirs) {
        return null;
    }
    $dir = $dirs[0];
    $file = is_file($dir . '/vulnerable.php') ? $dir . '/vulnerable.php' : $dir . '/index.php';
    return str_replace('\\', '/', substr($file, strlen($root . '/public/') - 1));
}

function category_sig(string $category): string
{
    foreach ([
        'sqli' => 'sqli', 'xss' => 'xss', 'csrf' => 'csrf', 'upload' => 'upload',
        'rce' => 'rce', 'lfi' => 'lfi', 'file_read' => 'lfi', 'ssrf' => 'ssrf',
        'xxe' => 'xxe', 'deserialize' => 'deser', 'jwt' => 'jwt',
        'oauth_redirect' => 'redirect', 'open_redirect' => 'redirect',
        'cors' => 'cors', 'crypto' => 'crypto', 'php_' => 'phpweak',
        'payment_tamper' => 'logic', 'password_reset' => 'logic',
        'captcha_reuse' => 'logic', 'brute_force' => 'logic',
        'idor_horizontal' => 'logic', 'idor_vertical' => 'logic',
        'http_smuggle' => 'smuggle', 'cache_poison' => 'poison',
        'container_escape' => 'escape', 'clickjacking' => 'clickjack',
    ] as $prefix => $sig) {
        if ($category === $prefix || str_starts_with($category, $prefix)) {
            return $sig;
        }
    }
    return 'logic';
}

// ---------- 管理员会话（提交用） ----------
function admin_login(string $baseUrl): array
{
    [$code, , $body] = http($baseUrl . '/login');
    preg_match('/name="csrf-token" content="([^"]+)"/', $body, $m);
    $jar = tempnam(sys_get_temp_dir(), 'xxr_admin_');
    [$code, , ] = http($baseUrl . '/login', true, http_build_query([
        'username' => 'admin', 'password' => 'xxr_admin_2026', '_token' => $m[1] ?? '',
    ]), ["Cookie: " . basename($jar) . "=1"]);
    // Cookie 需要手动管理：从 Set-Cookie 提取
    return [$jar, $m[1] ?? ''];
}

// 更省事：用 curl 的 CookieJar
function admin_session(string $baseUrl): array
{
    $jar = tempnam(sys_get_temp_dir(), 'xxr_admin_');
    $ch = curl_init($baseUrl . '/login');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar]);
    $body = (string) curl_exec($ch);
    curl_close($ch);
    preg_match('/name="csrf-token" content="([^"]+)"/', $body, $m);
    $ch = curl_init($baseUrl . '/login');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
        CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query([
            'username' => 'admin', 'password' => 'xxr_admin_2026', '_token' => $m[1] ?? '',
        ]),
    ]);
    curl_exec($ch);
    curl_close($ch);
    return [$jar];
}

[$adminJar] = admin_session($baseUrl);

function submit_flag(string $baseUrl, string $jar, string $id, string $flag): array
{
    // 每次提交先取新鲜 CSRF Token
    $ch = curl_init($baseUrl . '/');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar]);
    $body = (string) curl_exec($ch);
    curl_close($ch);
    preg_match('/name="csrf-token" content="([^"]+)"/', $body, $m);

    $ch = curl_init($baseUrl . '/challenge/submit-flag');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['challenge_id' => $id, 'flag' => $flag, '_token' => $m[1] ?? '']),
    ]);
    $out = (string) curl_exec($ch);
    curl_close($ch);
    $json = json_decode($out, true);
    return [$json['code'] ?? -1, $json['message'] ?? '解析失败'];
}

// ---------- 逐关测试 ----------
$results = [];
foreach ($challenges as $c) {
    $id = $c['id'];
    $flag = $c['flag'];
    $rel = dir_url($root, $id);
    if (!$rel) {
        $results[$id] = ['reveal' => 'NO-DIR', 'submit' => '-', 'note' => '无试炼目录'];
        continue;
    }
    $url = $baseUrl . $rel;

    // ---- 第一步：取 Flag ----
    $revealBody = '';
    if (isset(OVERRIDES[$id])) {
        foreach (OVERRIDES[$id] as [$method, $path, $data]) {
            [, $head, $body] = http($baseUrl . $path, $method === 'post', $data);
            $revealBody .= $head . $body;
        }
    } else {
        $sig = category_sig($c['category']);
        [, , $body] = http($url . ATTACK_GET);
        $revealBody = $body;
        if (!str_contains($body, $flag)) {
            [, , $body] = http($url, true, 'submit=1&username=test&password=test');
            $revealBody .= $body;
        }
        if (!str_contains($body, $flag) && in_array($sig, ['csrf', 'logic', 'smuggle', 'upload'], true)) {
            [, , $body] = http($url, true, 'submit=1&username=test&password=test',
                ['Content-Type: multipart/form-data; boundary=----xxr']);
            $revealBody .= $body;
        }
    }
    $revealOk = str_contains($revealBody, $flag) && !str_contains($revealBody, 'FLAG_UNAVAILABLE');

    // ---- 第二步：提交 ----
    [$code, $msg] = submit_flag($baseUrl, $adminJar, $id, $flag);
    if ($msg !== '' && str_contains($msg, '频繁')) {
        sleep(65); // 提交限流窗口
        [$code, $msg] = submit_flag($baseUrl, $adminJar, $id, $flag);
    }
    $submitOk = $code === 0 || str_contains($msg, '已通关');

    $note = '';
    if (!$revealOk) {
        $note = str_contains($revealBody, 'FLAG_UNAVAILABLE') ? 'Flag 占位符（目录定位失败）'
            : (str_contains($revealBody, 'Fatal error') ? '页面 Fatal' : '按已知方式未见 Flag，需人工复核');
    }
    $results[$id] = [
        'reveal' => $revealOk ? 'OK' : 'FAIL',
        'submit' => $submitOk ? 'OK' : 'FAIL(' . $code . ':' . mb_substr($msg, 0, 30) . ')',
        'note' => $note,
    ];
    printf("%s  取Flag:%-4s  提交:%-4s  %s %s\n",
        $id, $results[$id]['reveal'], $submitOk ? 'OK' : 'FAIL', $c['category'], $note);
}

$revealFail = array_filter($results, fn($r) => $r['reveal'] !== 'OK');
$submitFail = array_filter($results, fn($r) => $r['submit'] !== 'OK');
echo "\n========== 汇总 ==========\n";
echo '总关卡: ', count($results), PHP_EOL;
echo '取Flag 成功: ', count($results) - count($revealFail), '  失败: ', count($revealFail), PHP_EOL;
echo '提交通关 成功: ', count($results) - count($submitFail), '  失败: ', count($submitFail), PHP_EOL;
foreach ($revealFail as $id => $r) {
    echo "  [取Flag失败] {$id} - {$r['note']}\n";
}
foreach ($submitFail as $id => $r) {
    echo "  [提交失败] {$id} - {$r['submit']}\n";
}
