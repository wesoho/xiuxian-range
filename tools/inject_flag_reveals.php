<?php
/**
 * 修真靶场 - 批量为「无 Flag 揭示路径」的关卡注入试炼印记
 *
 * 对 vulnerable.php（缺省回退 index.php）注入：
 *   <?php require bootstrap; xxr_flag_reveal('<签名键>'); ?>
 * 插入点：最后一个 </body> 之前；无 </body> 时按文件结尾模式追加。
 *
 * 签名键由关卡 category 前缀映射而来，见 CATEGORY_MAP。
 * 可重复执行（幂等：已有 xxr_flag_reveal 调用的文件自动跳过）。
 *
 * 用法：php tools/inject_flag_reveals.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/app/bootstrap_challenge.php';

const REQ = "require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';";

/** category 前缀 -> 攻击特征签名键 */
const CATEGORY_MAP = [
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
];

function map_category(string $category): string
{
    foreach (CATEGORY_MAP as $prefix => $sig) {
        if ($category === $prefix || str_starts_with($category, $prefix)) {
            return $sig;
        }
    }
    return 'logic'; // 兜底：POST/带参即揭示，最宽松
}

$db = db();
$challenges = $db->fetchAll('SELECT id, flag, category FROM challenges WHERE enabled = 1');

// 旧种子 flag 映射（识别 STALE，跳过它们——另案修复）
$oldFlags = [];
foreach ([$root . '/tests/xxr_test.db', $root . '/xxr_test.db'] as $tf) {
    if (is_file($tf)) {
        try {
            $t = new PDO('sqlite:' . $tf, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            foreach ($t->query('SELECT id, flag FROM challenges') as $r) {
                $oldFlags[$r['id']] = $r['flag'];
            }
            if ($oldFlags) break;
        } catch (Throwable $e) {
        }
    }
}

$injected = 0;
$skipped = [];
$failed = [];

foreach ($challenges as $c) {
    $id = $c['id'];
    $prefix = strtolower(str_replace('-', '_', $id));
    $dirs = glob($root . '/public/challenges/*/' . $prefix . '*', GLOB_ONLYDIR);
    $dir = $dirs[0] ?? null;
    if (!$dir) {
        continue;
    }

    // 幂等 + 状态判定：目录内已有揭示/动态渲染则跳过；残留旧值的（STALE）跳过另案处理
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    $already = false;
    $stale = false;
    foreach ($rii as $f) {
        if (!$f->isFile() || $f->getSize() > 512 * 1024) continue;
        $content = @file_get_contents($f->getPathname());
        if ($content === false) continue;
        if (str_contains($content, 'xxr_flag_reveal') || str_contains($content, 'xxr_challenge_flag')) {
            $already = true;
        }
        if (($oldFlags[$id] ?? '') !== '' && str_contains($content, $oldFlags[$id])) {
            $stale = true;
        }
    }
    if ($already || $stale) {
        $skipped[] = $id . ($stale ? '(STALE另案)' : '(已有)');
        continue;
    }

    $target = is_file($dir . '/vulnerable.php') ? $dir . '/vulnerable.php' : $dir . '/index.php';
    if (!is_file($target)) {
        $failed[] = $id . '：无 vulnerable.php/index.php';
        continue;
    }

    $sig = map_category((string) $c['category']);
    $snippet = "<?php " . REQ . " xxr_flag_reveal('{$sig}'); ?>";

    $src = file_get_contents($target);
    $bodyPos = strripos($src, '</body>');
    if ($bodyPos !== false) {
        $src = substr($src, 0, $bodyPos) . $snippet . "\n" . substr($src, $bodyPos);
    } elseif (preg_match('/\?>\s*$/', $src)) {
        // HTML 模式结尾
        $src = rtrim($src) . "\n" . $snippet . "\n";
    } else {
        // PHP 模式结尾：直接追加调用（无需开标签）
        $src = rtrim($src) . "\n" . REQ . "\nxxr_flag_reveal('{$sig}');\n";
    }

    if (php_lint_ok($target, $src)) {
        file_put_contents($target, $src);
        $injected++;
        echo "INJECT  {$id}  [{$sig}]  " . basename($target) . "\n";
    } else {
        $failed[] = $id . '：注入后 lint 失败（已放弃，原文件未改）';
    }
}

function php_lint_ok(string $path, string $content): bool
{
    $tmp = sys_get_temp_dir() . '/xxr_lint_' . md5($path) . '.php';
    file_put_contents($tmp, $content);
    $out = shell_exec('php -l ' . escapeshellarg($tmp) . ' 2>&1');
    unlink($tmp);
    return str_contains((string) $out, 'No syntax errors');
}

echo "----\n注入 {$injected} 个关卡，跳过 " . count($skipped) . " 个，失败 " . count($failed) . " 个\n";
foreach ($failed as $msg) {
    echo "FAIL  {$msg}\n";
}
