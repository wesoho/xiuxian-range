<?php
/** @var ?array $user */
/** @var array $realms */
/** @var string $currentRealm */
/** @var array $challenges */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e(\XiuXian\Services\LevelService::REALM_NAMES[$currentRealm]) ?> · 修真境界地图</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <div class="text-center mb-4">
            <h1 class="text-gold"><?= e(\XiuXian\Services\LevelService::REALM_NAMES[$currentRealm]) ?></h1>
            <p class="text-muted">修真境界 · 共 <?= count($challenges) ?> 关</p>
        </div>

        <!-- 境界切换器 -->
        <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
            <?php foreach ($realms as $r): ?>
                <a href="/challenges/realm/<?= e($r) ?>"
                   class="xxr-phase-tab <?= $r === $currentRealm ? 'active' : '' ?>">
                    <?= e(\XiuXian\Services\LevelService::REALM_NAMES[$r]) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($tianjiHidden)): ?>
        <!-- 彩蛋：天机残页·叁（?tianji=1 触发） -->
        <div class="my-4 p-3 rounded text-center" style="border:1px dashed rgba(212,175,55,.5); background:rgba(212,175,55,.06);">
            <p class="mb-1 text-muted">「地图本无秘密，念咒的人多了，便有了。」</p>
            <p class="mb-1"><strong class="text-warning">🧾 天机残页·叁 · 口令：<code><?= e(xxr_egg_secret('egg_slip_3')) ?></code></strong></p>
            <p class="mb-0 small text-muted">（口令请到 <a href="/tianji">✨天机阁</a> 兑换。下一环线索：迷路的时候，把迷路诗每句的第一个字连起来读。）</p>
        </div>
        <?php endif; ?>

        <!-- 关卡列表 -->
        <?php if (!$challenges): ?>
            <div class="text-center text-muted py-5">
                <p>此境界关卡正在筹备中…</p>
            </div>
        <?php else: ?>
            <?php foreach ($challenges as $c): ?>
                <div class="xxr-challenge-card <?= e($c['user_status']) ?>">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <span class="xxr-badge-sect xxr-badge-<?= e($c['sect']) ?>"><?= e(render_sect($c['sect'])) ?></span>
                            <h5 class="mb-0 ms-2">
                                <a href="/challenge/<?= e($c['id']) ?>" class="text-decoration-none text-light">
                                    <?= e($c['title']) ?>
                                </a>
                            </h5>
                        </div>
                        <div class="xxr-challenge-meta">
                            <span class="xxr-mono"><?= e($c['id']) ?></span>
                            · <?= e(render_difficulty((int) $c['difficulty'])) ?>
                            · <?= (int) $c['points'] ?> 点
                            · 分类: <?= e($c['category']) ?>
                        </div>
                    </div>
                    <div class="text-end">
                        <?php if ($c['user_status'] === 'completed'): ?>
                            <span class="badge bg-success">✓ 已通关</span>
                        <?php elseif ($c['user_status'] === 'in_progress'): ?>
                            <span class="badge bg-warning text-dark">⚔️ 试炼中</span>
                        <?php elseif ($c['user_status'] === 'unlocked'): ?>
                            <span class="badge bg-info">🔓 可挑战</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">🔒 未解锁</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
</body>
</html>