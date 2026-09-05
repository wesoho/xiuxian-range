<?php
/** @var ?array $user */
/** @var ?array $challenge */
/** @var string $vulnerableCode */
/** @var string $secureCode */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>源码对比 · <?= e($challenge['title']) ?></title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <h2 class="text-gold text-center mb-4">📄 源码对比 · <?= e($challenge['title']) ?></h2>

        <div class="alert alert-info">
            <strong>📚 学习建议：</strong> 左侧为漏洞代码（演示用），右侧为安全代码（生产实践）。逐行对比，体会差别。
        </div>

        <div class="xxr-source-compare">
            <div class="xxr-source-pane xxr-source-vulnerable">
                <div class="xxr-source-pane-header">⚠️ 漏洞代码 (vulnerable.php)</div>
                <pre><code><?= htmlspecialchars($vulnerableCode) ?></code></pre>
            </div>
            <div class="xxr-source-pane xxr-source-secure">
                <div class="xxr-source-pane-header">✅ 安全代码 (secure.php)</div>
                <pre><code><?= htmlspecialchars($secureCode) ?></code></pre>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/<?= e($challenge['id']) ?>" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>