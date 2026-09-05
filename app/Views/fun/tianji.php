<?php
/** @var ?array $user */
/** @var ?array $fortune */
/** @var array $slips */
/** @var int $slipTotal */
/** @var array $eggs */
/** @var array $earned */
/** @var int $crane */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>天机阁 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4" style="max-width: 980px;">
        <div class="text-center mb-4">
            <h1 class="text-gold">🔮 天机阁</h1>
            <p class="text-muted">天机不可尽泄 · 但可稍稍泄露</p>
        </div>

        <div class="row g-4">
            <!-- 每日求签 -->
            <div class="col-md-5">
                <div class="xxr-egg-card p-4 h-100">
                    <h5 class="text-gold">🎲 每日求签</h5>
                    <p class="small text-muted">每位道友每日一签，签中偶有灵石。</p>
                    <?php if ($fortune): ?>
                        <div class="p-3 rounded" style="background:rgba(212,175,55,.08);">
                            <p class="mb-2" style="font-family:serif; font-size:1.05rem;"><?= e($fortune['text']) ?></p>
                            <p class="mb-0 small text-warning">灵石入账：<?= (int) $fortune['points'] ?> 点</p>
                        </div>
                        <p class="small text-muted mt-2 mb-0">今日已求签，明日请早。</p>
                    <?php else: ?>
                        <button class="xxr-btn xxr-btn-primary w-100" id="btnFortune">响鼓求签</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 兑换祭坛 -->
            <div class="col-md-7">
                <div class="xxr-egg-card p-4 h-100">
                    <h5 class="text-gold">🙏 口令兑换祭坛</h5>
                    <p class="small text-muted">在宗门各处（注释里、表缝里、符文夹层里、迷路时……）找到的彩蛋口令，拿到这里兑换。</p>
                    <form id="claimForm" onsubmit="return false;">
                        <div class="input-group">
                            <input type="text" class="form-control" id="eggSecret" placeholder="flag{egg_xxx}" autocomplete="off">
                            <button class="xxr-btn xxr-btn-primary" id="btnClaim" type="button">兑换</button>
                        </div>
                    </form>

                    <hr style="border-color: rgba(212,175,55,.25);">

                    <h6 class="text-gold">🧾 天机残页（<?= count($slips) ?>/<?= (int) $slipTotal ?>）</h6>
                    <div class="my-2">
                        <?php for ($i = 1; $i <= $slipTotal; $i++):
                            $cn = ['壹', '贰', '叁', '肆', '伍'][$i - 1]; ?>
                            <span class="xxr-slip-slot <?= in_array($i, $slips, true) ? 'filled' : '' ?>"
                                  title="<?= in_array($i, $slips, true) ? "残页·{$cn} 已收集" : "残页·{$cn} 待寻" ?>">
                                <?= in_array($i, $slips, true) ? $cn : '?' ?>
                            </span>
                        <?php endfor; ?>
                    </div>
                    <?php if (count($slips) >= $slipTotal): ?>
                        <p class="small text-warning mb-0">五页已齐！去 <a href="/mijing">秘境</a> 领取天机子的传承。</p>
                    <?php else: ?>
                        <p class="small text-muted mb-0">集齐五张残页，秘境自开。第一环的线索，掌门连爬虫都要立规矩的地方……</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 彩蛋收集册 -->
        <div class="xxr-egg-card p-4 mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="text-gold mb-0">📖 彩蛋收集册</h5>
                <span class="badge bg-warning text-dark"><?= count($earned) ?> / <?= count($eggs) ?></span>
            </div>
            <div class="row g-3 mt-1">
                <?php foreach ($eggs as $code => $egg): $got = in_array($code, $earned, true); ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="xxr-egg-card p-3 h-100 <?= $got ? '' : 'xxr-egg-locked' ?>">
                            <div class="d-flex align-items-center mb-1">
                                <span style="font-size:1.6rem;"><?= e($egg['icon']) ?></span>
                                <strong class="ms-2 <?= $got ? 'text-gold' : 'text-muted' ?>"><?= $got ? e($egg['name']) : '？？?' ?></strong>
                            </div>
                            <p class="small mb-0 <?= $got ? 'text-muted' : 'text-muted fst-italic' ?>">
                                <?= $got ? e($egg['description']) : '「' . e($egg['hint'] ?? '天机未至。') . '」' ?>
                            </p>
                            <span class="badge mt-2 <?= $egg['tier'] === 'legendary' ? 'bg-warning text-dark' : ($egg['tier'] === 'gold' ? 'bg-danger' : ($egg['tier'] === 'silver' ? 'bg-secondary' : 'bg-dark')) ?>">
                                <?= ['bronze' => '铜纹', 'silver' => '银纹', 'gold' => '金纹', 'legendary' => '传奇'][$egg['tier']] ?? $egg['tier'] ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 灵兽图鉴 -->
        <div class="xxr-egg-card p-4 mt-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-gold mb-1">🦢 灵兽图鉴</h5>
                <p class="small text-muted mb-0">在关卡前发呆三分钟，也许会有灵兽从页脚路过。点击即可抓捕。</p>
            </div>
            <div class="text-center">
                <div class="text-gold" style="font-size:2rem;"><?= (int) $crane ?></div>
                <span class="small text-muted">累计捕获</span>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
    <script>
        document.getElementById('btnFortune')?.addEventListener('click', function () {
            this.disabled = true;
            xxr.api('/tianji/draw', {}).then(function (res) {
                xxr.toast(res.message, res.code === 0 ? 'success' : 'error');
                if (res.code === 0) setTimeout(() => location.reload(), 1200);
                else document.getElementById('btnFortune').disabled = false;
            });
        });

        document.getElementById('btnClaim')?.addEventListener('click', function () {
            const secret = document.getElementById('eggSecret').value.trim();
            if (!secret) return xxr.toast('先念出口令来。', 'warning');
            xxr.api('/egg/claim', { secret: secret }).then(function (res) {
                xxr.toast(res.message, res.code === 0 ? 'success' : 'error');
                if (res.code === 0) setTimeout(() => location.reload(), 1600);
            });
        });
    </script>
</body>
</html>
