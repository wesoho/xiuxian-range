<?php
/** @var ?array $user */
/** @var array $bounties */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>悬赏令 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4" style="max-width: 860px;">
        <div class="text-center mb-4">
            <h1 class="text-gold">📜 悬赏令</h1>
            <p class="text-muted">每日三道悬赏 · 达成后领取灵石 · 零点刷新</p>
        </div>

        <?php foreach ($bounties as $b): ?>
            <div class="xxr-egg-card p-3 mb-3 d-flex justify-content-between align-items-center <?= $b['claimed'] ? 'xxr-egg-locked' : '' ?>">
                <div>
                    <strong class="text-gold"><?= e($b['name']) ?></strong>
                    <span class="badge bg-dark ms-2"><?= e($b['desc']) ?></span>
                    <div class="small text-muted mt-1">酬劳：<?= (int) $b['points'] ?> 灵石</div>
                </div>
                <div class="text-end">
                    <?php if ($b['claimed']): ?>
                        <span class="badge bg-success">已领取</span>
                    <?php elseif ($b['done']): ?>
                        <button class="xxr-btn xxr-btn-primary btn-sm" data-claim="<?= e($b['key']) ?>">领取赏格</button>
                    <?php else: ?>
                        <span class="badge bg-secondary">进行中…</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="text-center small text-muted mt-4">
            领取状态即时刷新；完成新悬赏后回来点一下「领取赏格」即可。
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
    <script>
        document.querySelectorAll('[data-claim]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                btn.disabled = true;
                xxr.api('/xuanshang/claim', { key: btn.dataset.claim }).then(function (res) {
                    xxr.toast(res.message, res.code === 0 ? 'success' : 'error');
                    if (res.code === 0) setTimeout(() => location.reload(), 1200);
                    else btn.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
