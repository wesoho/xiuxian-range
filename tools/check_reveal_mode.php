<?php
/**
 * 扫描：xxr_flag_reveal 调用是否处于 PHP 模式内
 * （若调用点之前最近的 PHP 开标签晚于最近的闭标签，则调用裸露在 HTML 里）
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/public/challenges', FilesystemIterator::SKIP_DOTS));
$bad = 0;
foreach ($it as $f) {
    if ($f->getExtension() !== 'php') {
        continue;
    }
    $src = file_get_contents($f->getPathname());
    if (!str_contains($src, 'xxr_flag_reveal')) {
        continue;
    }
    $call = strpos($src, 'xxr_flag_reveal(');
    $lastOpen = strrpos(substr($src, 0, $call), '<?php');
    $lastClose = strrpos(substr($src, 0, $call), '?>');
    if ($lastOpen === false || ($lastClose !== false && $lastClose > $lastOpen)) {
        echo "BAD-MODE  ", $f->getPathname(), "\n";
        $bad++;
    }
}
echo $bad === 0 ? "全部调用处于 PHP 模式内\n" : "共 {$bad} 个文件模式错误\n";
