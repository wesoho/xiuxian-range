<?php
/**
 * QY-LQ-04 整站打包泄露 - 备份文件下载
 */

// 教学夹具自愈：www.zip（模拟备份说明文件）中的 Flag 与数据库保持一致
try {
    require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
    $__fixture = __DIR__ . '/www.zip';
    $__flag = xxr_challenge_flag();
    if (is_file($__fixture) && $__flag !== '[FLAG_UNAVAILABLE]') {
        $__current = (string) file_get_contents($__fixture);
        if (!str_contains($__current, $__flag)) {
            file_put_contents($__fixture, (string) preg_replace('/flag\{[a-z0-9_]{4,40}\}/i', $__flag, $__current));
        }
    }
} catch (\Throwable $e) {
    // 夹具自愈失败不影响页面展示
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>整站打包泄露 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📦 整站打包泄露</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 门派管理弟子一时疏忽，把整站备份压缩包放到了 webroot 下。某日巡查时发现可被弟子下载。
        </div>
        <div class="alert alert-info">
            <strong>💡 习道提示：</strong> 常见的备份文件名有 <code>www.zip</code>、<code>backup.zip</code>、<code>site.tar.gz</code>、<code>web.rar</code> 等，尝试访问这些路径。
        </div>
        <h4>📚 备份文件风险</h4>
        <p>整站备份压缩包通常包含源码、配置文件、SQL 文件等敏感内容。</p>
        <p>扫描工具：<code>dirsearch</code>、<code>御剑后台扫描</code></p>
        <div class="text-center mt-4">
            <a href="/challenge/QY-LQ-04" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>