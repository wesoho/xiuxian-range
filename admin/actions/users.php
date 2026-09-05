<?php
/**
 * 后台 - 用户管理
 */
$users = db()->fetchAll("
    SELECT u.id, u.username, u.email, u.sect, u.realm_level, u.total_points, u.role,
           u.created_at, u.last_login_at,
           SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END) AS completed_count
    FROM users u
    LEFT JOIN progress p ON p.user_id = u.id
    GROUP BY u.id
    ORDER BY u.total_points DESC
    LIMIT 100
");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>用户管理 · 长老殿</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">👥 用户管理</span>
            <a href="/admin" class="btn btn-sm btn-outline-light">← 返回</a>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h2 class="text-gold">👥 用户列表（前 100 名）</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th><th>用户名</th><th>邮箱</th><th>宗门</th><th>境界</th>
                    <th>点数</th><th>通关数</th><th>角色</th><th>注册时间</th><th>最后登录</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int) $u['id'] ?></td>
                        <td>
                            <?= e($u['username']) ?>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge bg-warning text-dark">长老</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($u['email'] ?? '-') ?></td>
                        <td><?= e(render_sect($u['sect'])) ?></td>
                        <td><?= e(render_realm($u['realm_level'])) ?></td>
                        <td><?= (int) $u['total_points'] ?></td>
                        <td><?= (int) $u['completed_count'] ?></td>
                        <td><?= e($u['role']) ?></td>
                        <td><?= e($u['created_at']) ?></td>
                        <td><?= e($u['last_login_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>