<?php
/**
 * 后台 - 仪表板
 */
$stats = [
    'total_users' => db()->fetchScalar('SELECT COUNT(*) FROM users'),
    'total_challenges' => db()->fetchScalar('SELECT COUNT(*) FROM challenges'),
    'completed_count' => db()->fetchScalar('SELECT COUNT(*) FROM progress WHERE status = ?', ['completed']),
    'total_points' => db()->fetchScalar('SELECT COALESCE(SUM(total_points), 0) FROM users'),
];

$realmStats = db()->fetchAll("
    SELECT c.realm, COUNT(DISTINCT c.id) as cnt,
           COUNT(DISTINCT CASE WHEN p.status = 'completed' THEN c.id END) as completed
    FROM challenges c
    LEFT JOIN progress p ON p.challenge_id = c.id
    GROUP BY c.realm
    ORDER BY MIN(c.order_num)
");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>长老殿 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">🏯 长老殿 · 后台管理</span>
            <a href="/" class="btn btn-sm btn-outline-warning">返回前台</a>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h2 class="text-gold">📊 靶场总览</h2>

        <div class="row g-3 my-4">
            <div class="col-md-3">
                <div class="bg-dark-translucent p-4 rounded text-center">
                    <h3 class="text-gold"><?= (int) $stats['total_users'] ?></h3>
                    <small class="text-muted">修真弟子</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-dark-translucent p-4 rounded text-center">
                    <h3 class="text-gold"><?= (int) $stats['total_challenges'] ?></h3>
                    <small class="text-muted">关卡总数</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-dark-translucent p-4 rounded text-center">
                    <h3 class="text-gold"><?= (int) $stats['completed_count'] ?></h3>
                    <small class="text-muted">累计通关</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-dark-translucent p-4 rounded text-center">
                    <h3 class="text-gold"><?= number_format((float) $stats['total_points']) ?></h3>
                    <small class="text-muted">累计点数</small>
                </div>
            </div>
        </div>

        <h4 class="text-gold mt-5">📜 各境界通关情况</h4>
        <table class="table table-dark table-striped">
            <thead>
                <tr><th>境界</th><th>关卡数</th><th>通关数</th><th>完成率</th></tr>
            </thead>
            <tbody>
                <?php foreach ($realmStats as $r): ?>
                    <tr>
                        <td><?= e(\XiuXian\Services\LevelService::REALM_NAMES[$r['realm']] ?? $r['realm']) ?></td>
                        <td><?= (int) $r['cnt'] ?></td>
                        <td><?= (int) $r['completed'] ?></td>
                        <td>
                            <?php
                            $rate = $r['cnt'] > 0 ? round($r['completed'] / $r['cnt'] * 100, 1) : 0;
                            echo $rate . '%';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-4">
            <a href="/admin/challenges" class="xxr-btn xxr-btn-primary me-2">⚙️ 关卡管理</a>
            <a href="/admin/users" class="xxr-btn xxr-btn-secondary me-2">👥 用户管理</a>
            <a href="/admin/settings" class="xxr-btn xxr-btn-secondary">🔧 系统设置</a>
        </div>
    </div>
</body>
</html>