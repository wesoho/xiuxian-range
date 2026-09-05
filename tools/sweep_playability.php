<?php
/**
 * 修真靶场 - 全量关卡可玩性扫描
 *
 * 对全部启用关卡的实际执行页面（vulnerable.php / index.php）做三种请求：
 *   1. 普通访问      —— 应正常渲染，且不泄露 Flag
 *   2. 攻击 GET      —— 携带各签名类攻击参数，应无 Fatal 且揭示 Flag（GET 类签名）
 *   3. 攻击 POST     —— 覆盖 csrf/logic/smuggle/upload 类签名
 *
 * 用法：先启动开发服务器（php -S 127.0.0.1:8686 -t public server.php），再执行
 *       php tools/sweep_playability.php [base_url]
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/app/bootstrap_challenge.php';

$baseUrl = $argv[1] ?? 'http://127.0.0.1:8686';
$pdo = db()->pdo();
$challenges = $pdo->query('SELECT id, flag, category FROM challenges WHERE enabled = 1 ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

function dir_for(string $root, string $id): ?string
{
    $prefix = strtolower(str_replace('-', '_', $id));
    $dirs = glob($root . '/public/challenges/*/' . $prefix . '*', GLOB_ONLYDIR);
    return $dirs[0] ?? null;
}

function fetch_url(string $url, bool $post = false, array $headers = []): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8]);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'submit=1&username=test&password=test');
    }
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $out = (string) curl_exec($ch);
    curl_close($ch);
    return $out;
}

// 通用攻击参数（一次覆盖 GET 类签名：sqli/xss/lfi/rce/ssrf/deser/jwt/crypto/phpweak/redirect）
$attack = '?id=1%27%20OR%201%3D1--'
    . '&x=%3Cscript%3Ealert(1)%3C%2Fscript%3E'
    . '&file=..%2F..%2Fetc%2Fpasswd'
    . '&cmd=ls%20%2F'
    . '&url=http%3A%2F%2F127.0.0.1%2F'
    . '&data=O%3A8%3A%22X%22%3A1%3A%7B%7D'
    . '&token=eyJhbGciOiJub25lIn0.eyJhIjoxfQ.sig'
    . '&hash=0e123456789'
    . '&a%5B%5D=1'
    . '&redirect=http%3A%2F%2Fevil.com';

$plainLeak = [];
$fatals = [];
$revealed = 0;
$noReveal = [];

foreach ($challenges as $c) {
    $id = $c['id'];
    $dir = dir_for($root, $id);
    if (!$dir) {
        $fatals[] = $id . '(无试炼目录)';
        continue;
    }
    $file = is_file($dir . '/vulnerable.php') ? $dir . '/vulnerable.php' : $dir . '/index.php';
    $rel = str_replace('\\', '/', substr($file, strlen($root . '/public/') - 1));
    $url = rtrim($baseUrl, '/') . $rel;

    // 1) 普通访问
    $plain = fetch_url($url);
    if (str_contains($plain, 'Fatal error')) {
        $fatals[] = $id . '(plain)';
    }
    if (str_contains($plain, $c['flag'])) {
        $plainLeak[] = $id;
    }

    // 2) 攻击 GET
    $atk = fetch_url($url . $attack);
    $atkFatal = str_contains($atk, 'Fatal error');
    $atkShown = str_contains($atk, $c['flag']);
    if ($atkFatal) {
        $detail = preg_match('/Uncaught [^<]{0,90}/', $atk, $m) ? $m[0] : 'Fatal error';
        $fatals[] = $id . '(atk: ' . $detail . ')';
    } elseif ($atkShown) {
        $revealed++;
        continue; // 已揭示，无需再测 POST
    }

    // 3) 攻击 POST（csrf/logic/smuggle/upload + multipart）
    $post = fetch_url($url, true, ['Content-Type: multipart/form-data; boundary=----xxr']);
    if (str_contains($post, 'Fatal error')) {
        $detail = preg_match('/Uncaught [^<]{0,90}/', $post, $m) ? $m[0] : 'Fatal error';
        $fatals[] = $id . '(post: ' . $detail . ')';
    } elseif (str_contains($post, $c['flag'])) {
        $revealed++;
    } elseif (!$atkShown) {
        $noReveal[] = $id . ' [' . $c['category'] . ']';
    }
}

echo '总关卡: ', count($challenges), PHP_EOL;
echo '普通访问泄露（应为 0）: ', count($plainLeak), $plainLeak ? '  ' . implode(', ', $plainLeak) : '', PHP_EOL;
echo 'Fatal 页面（应为 0）: ', count($fatals), PHP_EOL;
foreach ($fatals as $f) {
    echo '   ', $f, PHP_EOL;
}
echo '攻击下成功揭示: ', $revealed, PHP_EOL;
echo '攻击下仍未揭示（需人工核对）: ', count($noReveal), PHP_EOL;
foreach ($noReveal as $s) {
    echo '   ', $s, PHP_EOL;
}
