<?php
/**
 * QY-LQ-03 祖师的Git事故 - .git 目录泄露
 */

// 教学夹具自愈：.git/config 中的 Flag 与数据库保持一致（Flag 随机化后需动态同步）
try {
    require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
    $__fixture = __DIR__ . '/.git/config';
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
    <title>祖师的Git事故 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📜 祖师的 Git 事故</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 祖师爷曾将毕生所学放在一个名为 <code>.git</code> 的时间法器中。但巡山弟子发现，这个法器被遗忘在山门入口。
        </div>
        <div class="alert alert-info">
            <strong>💡 习道提示：</strong> 尝试访问 <a href="/challenges/qingong/qy_lq_03_git/.git/HEAD" class="text-gold">/.git/HEAD</a> 查看暴露的版本控制文件。
        </div>
        <h4>📚 入门心法</h4>
        <p>Git 是修真界常用的版本管理法器，但其 <code>.git</code> 目录若未被清理，会泄露全部源码历史。</p>
        <p>利用工具：<code>git-dumper</code>、<code>wget --mirror</code></p>
        <div class="text-center mt-4">
            <a href="/challenge/QY-LQ-03" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>