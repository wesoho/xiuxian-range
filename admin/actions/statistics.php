<?php
/**
 * 后台 - 统计
 */
// 今日边界（跨库：避免 MySQL 专有的 CURDATE()）
$todayStart = date('Y-m-d') . ' 00:00:00';
$tomorrowStart = date('Y-m-d', strtotime('+1 day')) . ' 00:00:00';

$stats = [
    'today_signups'  => db()->fetchScalar(
        'SELECT COUNT(*) FROM users WHERE created_at >= ? AND created_at < ?',
        [$todayStart, $tomorrowStart]
    ),
    'today_progress' => db()->fetchScalar(
        'SELECT COUNT(*) FROM progress WHERE completed_at >= ? AND completed_at < ?',
        [$todayStart, $tomorrowStart]
    ),
    'top_solvers'    => db()->fetchAll("
        SELECT u.username, u.total_points, COUNT(p.id) as solved
        FROM users u LEFT JOIN progress p ON p.user_id = u.id AND p.status = 'completed'
        GROUP BY u.id ORDER BY solved DESC LIMIT 10
    "),
    'category_distribution' => db()->fetchAll("
        SELECT category, COUNT(*) as cnt FROM challenges GROUP BY category ORDER BY cnt DESC
    "),
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>统计 · 长老殿</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">📊 数据统计</span>
            <a href="/admin" class="btn btn-sm btn-outline-light">← 返回</a>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="text-gold">📊 靶场数据</h2>

        <div class="row g-3 my-4">
            <div class="col-md-6">
                <div class="bg-dark-translucent p-4 rounded text-center">
                    <h3 class="text-gold"><?= (int) $stats['today_signups'] ?></h3>
                    <small class="text-muted">今日注册</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-dark-translucent p-4 rounded text-center">
                    <h3 class="text-gold"><?= (int) $stats['today_progress'] ?></h3>
                    <small class="text-muted">今日通关</small>
                </div>
            </div>
        </div>

        <h4 class="text-gold mt-4">🏆 通关榜</h4>
        <table class="table table-dark table-striped">
            <thead><tr><th>用户</th><th>点数</th><th>通关数</th></tr></thead>
            <tbody>
                <?php foreach ($stats['top_solvers'] as $s): ?>
                    <tr>
                        <td><?= e($s['username']) ?></td>
                        <td><?= (int) $s['total_points'] ?></td>
                        <td><?= (int) $s['solved'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>