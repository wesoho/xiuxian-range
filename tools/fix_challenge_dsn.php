<?php
/**
 * 修真靶场 - 批量转换关卡页面的硬编码 MySQL DSN 为环境自适应连接
 *
 * 背景：上层关卡的演示页面硬编码了 Docker 专用 DSN（mysql:host=db），
 * 本地 SQLite 开发环境下页面在连接阶段即 fatal，整层关卡无法游玩。
 *
 * 转换规则（保持漏洞演示逻辑不变，仅替换连接参数来源）：
 *   1. $dsn = 'mysql:...'            -> [$dsn, $u, $p] = xxr_pdo_args();
 *   2. new PDO($dsn, 'x', 'y'        -> new PDO($dsn, $u, $p
 *   3. new PDO('mysql:...', 'x', 'y' -> new PDO(...xxr_pdo_args()
 *
 * 仅处理实际会被执行的文件（vulnerable.php / index.php / 其他入口），
 * secure.php / learn.php 只用于源码对比展示，不执行、不修改。
 *
 * 用法：php tools/fix_challenge_dsn.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$base = $root . '/public/challenges';

// 收集所有含硬编码 DSN 的「执行文件」
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
$targets = [];
foreach ($it as $f) {
    if ($f->getExtension() !== 'php') {
        continue;
    }
    $name = $f->getFilename();
    if (in_array($name, ['secure.php', 'learn.php'], true)) {
        continue; // 只展示源码，不执行
    }
    $path = $f->getPathname();
    $src = file_get_contents($path);
    if (!str_contains($src, 'mysql:host=')) {
        continue;
    }
    $targets[] = $path;
}

function ensure_bootstrap_top(string $src, string $path): string
{
    if (str_contains($src, 'bootstrap_challenge.php')) {
        return $src;
    }
    $req = "require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';";
    if (preg_match('/^([ \t]*<\?php(?:\s|\n))/', $src, $m)) {
        return substr_replace($src, $m[1] . $req . "\n", 0, strlen($m[1]));
    }
    return "<?php {$req} ?>\n" . $src;
}

$fixed = 0;
$skipped = [];
foreach ($targets as $path) {
    $src = file_get_contents($path);
    $orig = $src;

    // 1. $dsn = 'mysql:...';  ->  [$dsn, $u, $p] = xxr_pdo_args();
    $src = preg_replace(
        "/(\\\$\w+)\s*=\s*'mysql:[^']*';/",
        "[\$1, \$__xxr_u, \$__xxr_p] = xxr_pdo_args();",
        $src
    );

    // 2. new PDO($dsn, 'x', 'y'  ->  new PDO($dsn, $u, $p   （保留其后的选项参数等）
    $src = preg_replace(
        "/new\s+PDO\(\s*(\\\$\w+)\s*,\s*'[^']*'\s*,\s*'[^']*'/",
        "new PDO(\$1, \$__xxr_u, \$__xxr_p",
        $src
    );

    // 3. 内联 DSN：new PDO('mysql:...', 'x', 'y'  ->  new PDO(...xxr_pdo_args()
    $src = preg_replace(
        "/new\s+PDO\(\s*'mysql:[^']*'\s*,\s*'[^']*'\s*,\s*'[^']*'/",
        'new PDO(...xxr_pdo_args()',
        $src
    );

    if ($src === $orig) {
        $skipped[] = $path;
        continue;
    }

    $src = ensure_bootstrap_top($src, $path);

    // 语法校验后再落盘
    $tmp = sys_get_temp_dir() . '/xxr_dsn_' . md5($path) . '.php';
    file_put_contents($tmp, $src);
    $out = shell_exec('php -l ' . escapeshellarg($tmp) . ' 2>&1');
    unlink($tmp);
    if (!str_contains((string) $out, 'No syntax errors')) {
        echo "LINT-FAIL  {$path}\n";
        continue;
    }
    file_put_contents($path, $src);
    $fixed++;
    echo "FIXED  {$path}\n";
}

echo "----\n转换 {$fixed} 个文件，跳过 " . count($skipped) . " 个\n";

// 残留检查：执行文件中不应再有 mysql:host
foreach ($targets as $path) {
    $src = file_get_contents($path);
    if (str_contains($src, 'mysql:host=')) {
        echo "LEFTOVER  {$path}\n";
    }
}
