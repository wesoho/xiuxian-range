<?php
/** @var ?array $user */
/** @var ?array $challenge */
$phase = $_GET['phase'] ?? 'learn';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($challenge['title']) ?> · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <!-- 关卡头 -->
        <div class="text-center mb-3">
            <span class="xxr-badge-sect xxr-badge-<?= e($challenge['sect']) ?>"><?= e(render_sect($challenge['sect'])) ?></span>
            <span class="xxr-mono"><?= e($challenge['id']) ?></span>
            <span class="xxr-difficulty"><?= e(render_difficulty((int) $challenge['difficulty'])) ?></span>
            <h2 class="text-gold mt-2"><?= e($challenge['title']) ?></h2>
        </div>

        <!-- 三阶段 Tab -->
        <div class="xxr-phase-tabs justify-content-center">
            <a href="?phase=learn"  class="xxr-phase-tab <?= $phase==='learn'  ? 'active' : '' ?>">📖 习道</a>
            <a href="?phase=fight"  class="xxr-phase-tab <?= $phase==='fight'  ? 'active' : '' ?>">⚔️ 试炼</a>
            <a href="?phase=review" class="xxr-phase-tab <?= $phase==='review' ? 'active' : '' ?>">🌟 悟道</a>
        </div>

        <?php if ($phase === 'learn'): ?>
            <!-- 第一阶段：习道（剧情 + 学习资料） -->
            <div class="xxr-narrative">
                <strong>📜 剧情：</strong><br>
                <?= nl2br(e($challenge['narrative'])) ?>
            </div>

            <div class="bg-dark-translucent p-4 rounded mt-3">
                <h5 class="text-gold">📚 漏洞原理</h5>
                <p><?= e($challenge['description']) ?></p>

                <h5 class="text-gold mt-4">🎯 攻击思路</h5>
                <p>请仔细阅读剧情，思考可能的攻击路径。</p>
                <p>关卡对应分类：<code class="xxr-mono"><?= e($challenge['category']) ?></code></p>
            </div>

            <div class="text-center mt-4">
                <a href="?phase=fight" class="xxr-btn xxr-btn-primary">⚔️ 进入试炼</a>
            </div>

        <?php elseif ($phase === 'fight'): ?>
            <!-- 第二阶段：试炼（实战 + 提交Flag） -->
            <div class="xxr-narrative">
                <strong>⚔️ 试炼提示：</strong> 点击下方按钮进入试炼环境，破解后提交 Flag 即可通关。
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="bg-dark-translucent p-4 rounded text-center">
                        <h5 class="text-gold mb-3">🎯 试炼靶场</h5>
                        <p>环境已就绪，点击下方按钮进入实战环境：</p>
                        <a href="/challenge/<?= e($challenge['id']) ?>/fight" target="_blank" class="xxr-btn xxr-btn-primary">
                            🚀 进入试炼环境（新窗口）
                        </a>
                        <p class="small text-muted mt-3">
                            ⚠️ 试炼环境与修真靶场共享同一域名<br>
                            建议在 Burp Suite 中拦截请求观察
                        </p>
                    </div>

                    <!-- Flag 提交 -->
                    <div class="xxr-flag-form">
                        <h5 class="text-gold mb-3">🏆 提交 Flag</h5>
                        <form id="flagForm" onsubmit="event.preventDefault(); xxr.submitFlag('<?= e($challenge['id']) ?>', this.flag.value);">
                            <div class="mb-3">
                                <input type="text" name="flag" class="xxr-flag-input" placeholder="flag{...}" required>
                            </div>
                            <button type="submit" class="xxr-btn xxr-btn-primary">⚡ 提交 Flag</button>
                        </form>
                    </div>

                    <!-- 源码查看 -->
                    <?php if ($challenge['source_viewable']): ?>
                        <div class="text-center mt-3">
                            <a href="/challenge/<?= e($challenge['id']) ?>/source" class="xxr-btn xxr-btn-secondary">📄 查看源码对比</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- 提示卡 -->
                    <div class="bg-dark-translucent p-3 rounded">
                        <h5 class="text-gold mb-3">💡 提示</h5>
                        <?php foreach ($challenge['hints'] ?? [] as $hint): ?>
                            <?php $used = in_array((int) $hint['id'], $challenge['hints_used'] ?? []); ?>
                            <div class="xxr-hint-card" id="hint-<?= (int) $hint['id'] ?>">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <?php
                                        $names = [1=>'弱提示', 2=>'中等提示', 3=>'完整答案'];
                                        echo $names[(int) $hint['level']] ?? '提示';
                                        ?>
                                    </strong>
                                    <span class="badge bg-warning text-dark">
                                        <?= (int) $hint['point_cost'] ?> 点
                                    </span>
                                </div>
                                <div class="xxr-hint-content mt-2">
                                    <?php if ($used): ?>
                                        <strong>提示：</strong><?= e($hint['content']) ?>
                                    <?php else: ?>
                                        <span class="xxr-hint-locked">（未解锁）</span>
                                        <button class="xxr-btn xxr-btn-secondary btn-sm mt-2"
                                                onclick="xxr.revealHint('<?= e($challenge['id']) ?>', <?= (int) $hint['id'] ?>, <?= (int) $hint['level'] ?>)">
                                            🔓 解锁提示
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($phase === 'review'): ?>
            <!-- 第三阶段：悟道（通关后） -->
            <?php if (($challenge['progress']['status'] ?? '') === 'completed'): ?>
                <div class="xxr-narrative text-center">
                    <strong>🌟 恭喜道友通关！</strong><br>
                    你已掌握 <?= e($challenge['category']) ?> 类漏洞的核心要点。
                </div>

                <div class="bg-dark-translucent p-4 rounded mt-3">
                    <h5 class="text-gold">📝 Writeup（解题报告）</h5>
                    <form id="writeupForm" onsubmit="event.preventDefault(); xxr.api('/challenge/save-writeup', {challenge_id: '<?= e($challenge['id']) ?>', writeup: this.writeup.value}).then(r => xxr.toast(r.message, r.code===0?'success':'error'));">
                        <textarea name="writeup" class="form-control" rows="10" placeholder="记录你的解题思路、踩过的坑、学到的知识点..."><?= e($challenge['progress']['writeup'] ?? '') ?></textarea>
                        <button type="submit" class="xxr-btn xxr-btn-primary mt-3">💾 保存 Writeup</button>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="/challenge/<?= e($challenge['id']) ?>/source" class="xxr-btn xxr-btn-secondary">📄 查看源码对比</a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    请先通关此关卡后再来悟道。⚔️
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
</body>
</html>