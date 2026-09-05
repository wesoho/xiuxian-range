<?php /** @var int $status @var string $message */ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (int) ($status ?? 500) ?> · 走火入魔 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <main class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="xxr-form-card text-center" style="max-width:560px;">
            <div class="xxr-realm-glyph mx-auto" style="--rc: var(--xxr-cinnabar); width:76px; height:76px; font-size:2.4rem;">厄</div>
            <h1 style="font-size:3rem;"><?= (int) ($status ?? 500) ?></h1>
            <p class="xxr-form-sub">走火入魔 · 请稍后再试</p>
            <p class="text-muted"><?= e($message ?? '靶场发生了意外，长老们正在抢修') ?></p>
            <a href="/" class="xxr-btn xxr-btn-primary mt-3">返回山门</a>
        </div>
    </main>
</body>
</html>
