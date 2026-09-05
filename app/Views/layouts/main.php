<?php
/** @var string $title */
/** @var ?array $user */
/** @var string $contentView */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? '修真网络安全靶场') ?></title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
    <link rel="icon" href="/assets/images/favicon.svg" type="image/svg+xml">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <!-- 主体内容 -->
    <main class="container-fluid py-4">
        <?= $contentView ?>
    </main>

    <!-- 页脚 -->
    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
    <?php if (!empty($pageScripts)): ?>
        <?php foreach ($pageScripts as $s): ?>
            <script src="<?= e($s) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>