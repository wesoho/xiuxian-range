<?php
/** @var ?array $user */
/** @var array $users */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>排行榜 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <h2 class="text-gold text-center mb-4">🏆 修真榜</h2>
        <p class="text-center text-muted mb-4">综合积分排行 · 修真弟子们的荣耀</p>

        <?php if (!empty($ascension)): ?>
            <div class="alert text-center" style="border:1px solid rgba(212,175,55,.5); background:rgba(212,175,55,.08); color:#f0d879;">
                🎉 喜报：道友 <strong class="xxr-gold-name"><?= e($ascension['username']) ?></strong>
                于 <?= e(date('m 月 d 日', strtotime($ascension['ascended_at']))) ?> 渡劫飞升，全服同贺！
            </div>
        <?php endif; ?>

        <?php if (!$users): ?>
            <div class="text-center text-muted py-5">
                <p>暂无弟子上榜，快去闯关吧！</p>
            </div>
        <?php else: ?>
            <?php foreach ($users as $idx => $u): ?>
                <?php $rank = $idx + 1; $ascended = !empty($u['ascended_at']); ?>
                <div class="xxr-rank-row <?= $rank <= 3 ? 'rank-'.$rank : '' ?>">
                    <div class="xxr-rank-num"><?= $rank <= 3 ? ['🥇','🥈','🥉'][$rank-1] : '#'.$rank ?></div>
                    <div>
                        <strong class="<?= $ascended ? 'xxr-gold-name' : 'text-gold' ?>" <?= $ascended ? 'title="已飞升 · 渡劫成功"' : '' ?>><?= e($u['username']) ?></strong>
                        <span class="xxr-badge-sect xxr-badge-<?= e($u['sect']) ?> ms-2"><?= e(render_sect($u['sect'])) ?></span>
                        <div class="small text-muted">
                            <?= e(render_realm($u['realm_level'])) ?> ·
                            通关 <?= (int) ($u['completed_count'] ?? 0) ?> 关 ·
                            <?= e($u['title'] ?? '炼气小修') ?>
                        </div>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-warning text-dark"><?= (int) $u['total_points'] ?> 点</span>
                    </div>
                    <div class="small text-muted text-end">
                        <?php if ($u['last_login_at']): ?>
                            最后登录<br><?= e($u['last_login_at']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>