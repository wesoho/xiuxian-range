<?php
/** @var ?array $user */
/** @var bool $ascended */
/** @var array $eggs */
/** @var array $earned */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>谢幕 · 大乘之后 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4" style="max-width: 820px;">
        <?php if (!$ascended): ?>
            <div class="text-center mb-4">
                <h1 class="text-gold">🎬 谢 幕</h1>
            </div>
            <div class="xxr-egg-card p-5 text-center">
                <div style="font-size:3rem;">🔒</div>
                <h4 class="text-gold">此卷轴只放给飞升者</h4>
                <p class="text-muted mb-0">百关全通、渡劫飞升之后，此处将放映谢幕卷轴，并揭晓全站彩蛋答案。<br>
                眼下道友的当务之急，是去 <a href="/challenges">境界地图</a> 再闯几关。</p>
            </div>
        <?php else: ?>
            <div class="text-center mb-4">
                <h1 class="text-gold">🎬 谢 幕</h1>
                <p class="text-muted">献给飞升者 <?= e($user['username']) ?> · 彩蛋答案尽在其中</p>
            </div>

            <div class="xxr-egg-card p-4">
                <div class="xxr-credits-roll">
                    <div class="xxr-credits-inner text-center py-3">
                        <p class="h4 text-gold mb-4">—— 修真网络安全靶场 ——</p>
                        <p class="mb-2">掌门 / 出题人 / 灵兽驯化师 / 天机阁掌柜<br><span class="text-muted">修真靶场项目组</span></p>
                        <p class="mb-2">特别出演<br><span class="text-muted">那只总在页脚散步的灵鹤</span></p>
                        <hr style="border-color: rgba(212,175,55,.3); max-width: 200px; margin: 1rem auto;">
                        <p class="mb-2">鸣谢 · 本靶场参考了以下优秀前辈项目</p>
                        <p class="small text-muted mb-1">DVWA · sqli-labs · upload-labs</p>
                        <p class="small text-muted mb-1">Pikachu · OWASP WebGoat · OWASP Juice Shop</p>
                        <p class="small text-muted mb-4">以及所有在深夜里对着源码较劲的安全学习者</p>
                        <hr style="border-color: rgba(212,175,55,.3); max-width: 200px; margin: 1rem auto;">
                        <p class="mb-2">二周目预告</p>
                        <p class="small text-muted mb-4">大罗秘境 · 敬请期待<br>（那里没有漏洞，只有漏洞写成的诗）</p>
                        <p class="h5 text-warning mt-4">道友，江湖再见。</p>
                    </div>
                </div>
            </div>

            <div class="xxr-egg-card p-4 mt-4">
                <h5 class="text-gold">🥚 彩蛋答案 · 全披露（<?= count($earned) ?>/<?= count($eggs) ?> 已入册）</h5>
                <table class="table table-dark table-sm mt-2 align-middle">
                    <thead><tr><th style="width:56px;">图标</th><th>彩蛋</th><th>获取方式</th><th style="width:80px;">状态</th></tr></thead>
                    <tbody>
                        <?php foreach ($eggs as $egg): $got = in_array($egg['code'], $earned, true); ?>
                            <tr class="<?= $got ? '' : 'text-muted' ?>">
                                <td><?= e($egg['icon']) ?></td>
                                <td class="<?= $got ? 'text-warning' : '' ?>"><?= e($egg['name']) ?></td>
                                <td class="small"><?= e($egg['description'] ?: ($egg['hint'] ?: '')) ?>
                                    <?php if (!empty($egg['secret'])): ?><code class="ms-1"><?= e($egg['secret']) ?></code><?php endif; ?>
                                </td>
                                <td><?= $got ? '<span class="badge bg-success">已得</span>' : '<span class="badge bg-secondary">未得</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="small text-muted mb-0">未集齐也没关系——彩蛋会一直等在原处，等你回头。</p>
            </div>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
</body>
</html>
