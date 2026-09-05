<?php
/** @var ?array $user */
/** @var array $slips */
/** @var int $slipTotal */
/** @var bool $master */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>秘境 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4" style="max-width: 760px;">
        <div class="text-center mb-4">
            <h1 class="text-gold">🌌 天机秘境</h1>
            <p class="text-muted">五页残卷所指之处 · 有缘人自会踏足</p>
        </div>

        <div class="xxr-egg-card p-4 mb-4">
            <h5 class="text-gold">🗿 残页祭坛</h5>
            <p class="small text-muted">祭坛之上刻着五个凹槽，与你的残页一一对应。</p>
            <div class="my-3 text-center">
                <?php for ($i = 1; $i <= $slipTotal; $i++):
                    $cn = ['壹', '贰', '叁', '肆', '伍'][$i - 1]; ?>
                    <span class="xxr-slip-slot <?= in_array($i, $slips, true) ? 'filled' : '' ?>" style="width:64px; height:84px; font-size:2rem;">
                        <?= in_array($i, $slips, true) ? $cn : '?' ?>
                    </span>
                <?php endfor; ?>
            </div>
            <?php if (count($slips) < $slipTotal): ?>
                <p class="small text-muted mb-0 text-center">还差 <?= $slipTotal - count($slips) ?> 页。口令集齐后，此处自会有变。</p>
            <?php endif; ?>
        </div>

        <?php if ($master): ?>
            <div class="xxr-egg-card p-4 text-center" style="border-color: rgba(212,175,55,.8); box-shadow: 0 0 24px rgba(212,175,55,.25);">
                <div style="font-size:3rem;">🌟</div>
                <h3 class="text-gold">天机子 · 传承已开</h3>
                <p class="mb-2">「五页天机尽入你手，从今往后，你就是新的天机子。」</p>
                <p class="small text-muted mb-3">传奇彩蛋【天机子】已入册，金光主题已授予。全站隐藏彩蛋的答案，等你飞升那天，谢幕卷轴会一一揭晓。</p>
                <div class="d-grid gap-2" style="max-width:320px; margin:0 auto;">
                    <a href="/leaderboard" class="xxr-btn xxr-btn-primary">🏆 去修真榜看看</a>
                </div>
            </div>
        <?php else: ?>
            <div class="xxr-egg-card p-4">
                <h5 class="text-gold">🙏 最后一道石门</h5>
                <p class="small text-muted">石门上刻着一行字：「把最后一页的名字，念给祭坛听。」</p>
                <div class="input-group">
                    <input type="text" class="form-control" id="mijingSecret" placeholder="flag{egg_xxx}" autocomplete="off">
                    <button class="xxr-btn xxr-btn-primary" id="btnMijingClaim" type="button">念出口令</button>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
    <script>
        document.getElementById('btnMijingClaim')?.addEventListener('click', function () {
            const secret = document.getElementById('mijingSecret').value.trim();
            if (!secret) return xxr.toast('先念出口令来。', 'warning');
            xxr.api('/egg/claim', { secret: secret }).then(function (res) {
                xxr.toast(res.message, res.code === 0 ? 'success' : 'error');
                if (res.code === 0) setTimeout(() => location.reload(), 1600);
            });
        });
    </script>
</body>
</html>
