<?php
/** @var ?array $user */
/** @var array $progress */
/** @var int $completedCount */

use XiuXian\Services\LevelService;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>修真档案 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <h2 class="text-gold text-center mb-4">📜 修真档案</h2>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="bg-dark-translucent p-4 rounded text-center">
                    <div style="font-size:4rem;">👤</div>
                    <h4 class="text-gold"><?= e($user['username']) ?></h4>
                    <p class="text-muted"><?= e(render_sect($user['sect'])) ?> · <?= e($user['title'] ?? '炼气小修') ?></p>

                    <hr style="border-color: rgba(212,175,55,0.2);">

                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-gold"><?= (int) $user['total_points'] ?></h5>
                            <small class="text-muted">修真点数</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-gold"><?= $completedCount ?></h5>
                            <small class="text-muted">已通关</small>
                        </div>
                    </div>

                    <hr style="border-color: rgba(212,175,55,0.2);">

                    <div>
                        <h6 class="text-gold"><?= e(render_realm($user['realm_level'])) ?></h6>
                        <?php
                        $progressData = LevelService::realmProgress((int) $user['id'], $user['realm_level']);
                        ?>
                        <?php if ($progressData['next']): ?>
                            <div class="progress" style="height: 8px; background: rgba(212,175,55,0.2);">
                                <div class="progress-bar bg-warning" style="width: <?= $progressData['percent'] ?>%"></div>
                            </div>
                            <small class="text-muted">
                                本境界已通关 <?= (int) $progressData['done'] ?> / <?= (int) $progressData['total'] ?> 关
                                · 通关全部可晋升 <?= e(LevelService::REALM_NAMES[$progressData['next']]) ?>
                            </small>
                        <?php else: ?>
                            <p class="text-warning">🌟 你已飞升大乘，无可晋升之境！</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="bg-dark-translucent p-4 rounded">
                    <h5 class="text-gold mb-3">🏆 我的闯关记录</h5>
                    <?php if (!$progress): ?>
                        <p class="text-muted">尚未闯关，<a href="/challenges" class="text-gold">立即开始修炼</a>！</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr><th>关卡</th><th>境界</th><th>状态</th><th>点数</th><th>完成时间</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($progress as $p): ?>
                                        <tr>
                                            <td>
                                                <a href="/challenge/<?= e($p['challenge_id']) ?>" class="text-light text-decoration-none">
                                                    <?= e($p['title']) ?>
                                                </a>
                                            </td>
                                            <td><?= e(\XiuXian\Services\LevelService::REALM_NAMES[$p['realm']] ?? '') ?></td>
                                            <td>
                                                <?php if ($p['status'] === 'completed'): ?>
                                                    <span class="badge bg-success">✓ 已通关</span>
                                                <?php elseif ($p['status'] === 'in_progress'): ?>
                                                    <span class="badge bg-warning text-dark">⚔️ 试炼中</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">🔒 未开始</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= (int) ($p['points_earned'] ?? 0) ?></td>
                                            <td><?= e($p['completed_at'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>