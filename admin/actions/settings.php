<?php
/**
 * 后台 - 系统设置
 */
$settings = db()->fetchAll('SELECT `key`, `value`, description FROM settings');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>系统设置 · 长老殿</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">🔧 系统设置</span>
            <a href="/admin" class="btn btn-sm btn-outline-light">← 返回</a>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="text-gold">🔧 系统配置</h2>
        <table class="table table-dark">
            <thead><tr><th>配置项</th><th>当前值</th><th>说明</th></tr></thead>
            <tbody>
                <?php foreach ($settings as $s): ?>
                    <tr>
                        <td><code><?= e($s['key']) ?></code></td>
                        <td><?= e($s['value'] ?? '') ?></td>
                        <td><?= e($s['description'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted">📝 在线编辑功能开发中，敬请期待。</p>
    </div>
</body>
</html>