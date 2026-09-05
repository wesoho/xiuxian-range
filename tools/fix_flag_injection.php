<?php
/**
 * 一次性修复脚本：恢复被损坏的 bootstrap 注入首行
 *
 * 背景：批量替换 Flag 时，shell 内联 PHP 的转义错误把原本以 `<?php`
 * 开头的文件首行替换成了 `\{require_once ...;}`（吞掉了 PHP 开标签）。
 * 本脚本将首行恢复为正确的 `<?php` + require 两行。
 *
 * 用法：php tools/fix_flag_injection.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$base = $root . '/public/challenges';

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
$fixed = 0;
$ok = 0;

foreach ($it as $f) {
    if ($f->getExtension() !== 'php') {
        continue;
    }
    $path = $f->getPathname();
    $src = file_get_contents($path);
    if (!str_contains($src, 'xxr_challenge_flag')) {
        continue;
    }
    $lines = explode("\n", $src);
    $first = ltrim($lines[0]);

    if (str_starts_with($first, '\\{require_once')) {
        // 首行损坏：恢复 <?php 开标签 + require
        $lines[0] = '<?php';
        array_splice($lines, 1, 0, ["require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';"]);
        file_put_contents($path, implode("\n", $lines));
        $fixed++;
        echo "FIXED  ", $path, "\n";
    } elseif (str_contains($src, 'bootstrap_challenge.php')) {
        $ok++;
    } else {
        echo "NOREQ  ", $path, "\n";
    }
}

echo "----\n修复 {$fixed} 个文件，正常 {$ok} 个\n";
