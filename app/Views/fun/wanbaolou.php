<?php
/** @var ?array $user */
/** @var array $items */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>万宝楼 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4" style="max-width: 980px;">
        <div class="text-center mb-4">
            <h1 class="text-gold">🏮 万宝楼</h1>
            <p class="text-muted">灵石易物 · 只卖装扮，不卖功法</p>
            <p class="small text-muted mb-0">当前头衔：<strong class="text-warning"><?= e($user['title'] ?: '（无）') ?></strong> · 身家：<strong class="text-gold"><?= (int) $user['total_points'] ?></strong> 灵石</p>
        </div>

        <div class="row g-3">
            <?php foreach ($items as $it): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="xxr-egg-card p-3 h-100 d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <span style="font-size:1.8rem;"><?= e($it['icon']) ?></span>
                            <strong class="ms-2 text-gold"><?= e($it['name']) ?></strong>
                        </div>
                        <p class="small text-muted flex-grow-1"><?= e($it['description']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-warning"><?= (int) $it['price'] ?> 灵石</span>
                            <?php if ($it['equipped']): ?>
                                <span class="badge bg-success">装备中</span>
                            <?php elseif ($it['owned']): ?>
                                <button class="xxr-btn xxr-btn-secondary btn-sm" data-equip="<?= e($it['code']) ?>">装备</button>
                            <?php else: ?>
                                <button class="xxr-btn xxr-btn-primary btn-sm" data-buy="<?= e($it['code']) ?>" <?= (int) $user['total_points'] < (int) $it['price'] ? 'disabled title="灵石不足"' : '' ?>>购入</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center small text-muted mt-4">
            头衔装备后将显示在排行榜与个人档案。飞升者另有专属金光主题，万宝楼有钱也买不到。
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
    <script>
        document.querySelectorAll('[data-buy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                btn.disabled = true;
                xxr.api('/wanbaolou/buy', { code: btn.dataset.buy }).then(function (res) {
                    xxr.toast(res.message, res.code === 0 ? 'success' : 'error');
                    if (res.code === 0) setTimeout(() => location.reload(), 1200);
                    else btn.disabled = false;
                });
            });
        });
        document.querySelectorAll('[data-equip]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                btn.disabled = true;
                xxr.api('/wanbaolou/equip', { code: btn.dataset.equip }).then(function (res) {
                    xxr.toast(res.message, res.code === 0 ? 'success' : 'error');
                    if (res.code === 0) setTimeout(() => location.reload(), 1000);
                    else btn.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
