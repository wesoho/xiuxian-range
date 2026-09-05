<?php
/**
 * 后台 - 关卡管理
 */
$challenges = db()->fetchAll('SELECT id, title, sect, realm, difficulty, points, enabled FROM challenges ORDER BY order_num');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>关卡管理 · 长老殿</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">⚙️ 关卡管理</span>
            <div>
                <a href="/admin" class="btn btn-sm btn-outline-light">← 返回仪表板</a>
                <a href="/" class="btn btn-sm btn-outline-warning">返回前台</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h2 class="text-gold">⚙️ 关卡列表（共 <?= count($challenges) ?> 关）</h2>

        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>编号</th>
                        <th>标题</th>
                        <th>宗门</th>
                        <th>境界</th>
                        <th>难度</th>
                        <th>点数</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($challenges as $c): ?>
                        <tr>
                            <td><code><?= e($c['id']) ?></code></td>
                            <td><?= e($c['title']) ?></td>
                            <td><?= e(render_sect($c['sect'])) ?></td>
                            <td><?= e(\XiuXian\Services\LevelService::REALM_NAMES[$c['realm']] ?? '') ?></td>
                            <td><?= e(render_difficulty((int) $c['difficulty'])) ?></td>
                            <td><?= (int) $c['points'] ?></td>
                            <td>
                                <?php if ($c['enabled']): ?>
                                    <span class="badge bg-success">启用</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">禁用</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/challenge/<?= e($c['id']) ?>" class="btn btn-sm btn-outline-info">查看</a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleEnabled('<?= e($c['id']) ?>', <?= (int) !$c['enabled'] ?>)">
                                    <?= $c['enabled'] ? '禁用' : '启用' ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    async function toggleEnabled(id, enable) {
        if (!confirm(enable ? '确定要启用此关卡？' : '确定要禁用此关卡？')) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const fd = new FormData();
        fd.append('_token', csrf);
        fd.append('challenge_id', id);
        fd.append('enabled', enable ? '1' : '0');
        const res = await fetch('/admin/updateChallenge', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.code === 0) {
            alert('操作成功');
            location.reload();
        } else {
            alert('失败：' + data.message);
        }
    }
    </script>
</body>
</html>