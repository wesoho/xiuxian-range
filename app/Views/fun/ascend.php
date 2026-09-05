<?php
/** @var ?array $user */
/** @var int $done */
/** @var int $rank */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>渡劫飞升 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4" style="max-width: 820px;">
        <div class="text-center mb-4">
            <h1 class="text-gold">⚡ 渡劫飞升 ⚡</h1>
            <p class="text-muted">百关已尽 · 天劫已至 · 道友请当心头顶</p>
        </div>

        <!-- 渡劫台 -->
        <div class="xxr-tribulation-stage p-4 text-center" id="tribulationStage">
            <div id="tribulationHint" class="text-muted py-5">
                <p class="mb-1" style="font-size:1.2rem;">乌云压顶，紫电绕梁……</p>
                <p class="small mb-0">点击「渡劫」开始，共九道天雷。撑过去，便是大罗。</p>
            </div>
            <button class="xxr-btn xxr-btn-primary px-5 mb-3" id="btnDujie">道友，渡劫</button>
        </div>

        <!-- 通关文牒（渡劫完成后显现） -->
        <div id="certificateWrap" class="mt-4" style="display:none;">
            <div id="xxrCertificate" class="text-center xxr-ascend-glow">
                <p class="small text-muted mb-1" style="letter-spacing:.5em;">修真网络安全靶场 · 掌门亲颁</p>
                <h2 class="text-gold mb-3">通 关 文 牒</h2>
                <p class="mb-1">兹有修真弟子</p>
                <h3 class="text-warning my-2"><?= e($user['username']) ?></h3>
                <p class="mb-1">
                    自入山以来，砺剑百关，<b><?= (int) $done ?></b> 关全数打通，
                    渡九重天雷，于 <b><?= e(date('Y 年 m 月 d 日', strtotime($user['ascended_at']))) ?></b> 飞升。
                </p>
                <p class="mb-1">今列修真榜第 <b><?= (int) $rank ?></b> 位，特授：</p>
                <h4 class="text-gold my-3">「 大乘飞升者 · 道祖亲临 」</h4>
                <p class="small text-muted mb-0">从炼气到大乘，一路修真一路飞升。</p>
                <div class="mt-4 mb-2">
                    <span class="xxr-cert-seal">掌门</span>
                </div>
            </div>
            <div class="text-center mt-3 d-grid gap-2" style="max-width:420px; margin:0 auto;">
                <button class="xxr-btn xxr-btn-secondary" id="btnPrint">🖨 保存 / 打印文牒</button>
                <a href="/dacheng-final" class="xxr-btn xxr-btn-primary">🎬 观看谢幕卷轴（彩蛋答案在此揭晓）</a>
                <a href="/leaderboard" class="xxr-btn xxr-btn-secondary">🏆 领取金光昵称 · 查看修真榜</a>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
    <script>
        document.getElementById('btnDujie').addEventListener('click', function () {
            this.disabled = true;
            document.getElementById('tribulationHint').innerHTML =
                '<p class="mb-1" style="font-size:1.2rem;">天雷滚滚——</p><p class="small mb-0">第 <span id="boltCount">0</span> / 9 道</p>';

            const stage = document.getElementById('tribulationStage');
            let i = 0;
            const timer = setInterval(function () {
                i++;
                // 落雷
                const bolt = document.createElement('div');
                bolt.className = 'xxr-bolt active';
                bolt.style.left = (12 + Math.random() * 76) + '%';
                bolt.style.height = '0';
                stage.appendChild(bolt);
                stage.classList.remove('shake');
                void stage.offsetWidth; // 重启动画
                stage.classList.add('shake');
                const cnt = document.getElementById('boltCount');
                if (cnt) cnt.textContent = i;

                if (i >= 9) {
                    clearInterval(timer);
                    setTimeout(function () {
                        stage.insertAdjacentHTML('beforeend',
                            '<p class="text-warning" style="font-size:1.3rem;">九雷已过，金光灌体——道友，飞升了！</p>');
                        document.getElementById('certificateWrap').style.display = '';
                        document.getElementById('certificateWrap').scrollIntoView({ behavior: 'smooth' });
                    }, 700);
                }
            }, 650);
        });

        document.getElementById('btnPrint').addEventListener('click', function () { window.print(); });
    </script>
</body>
</html>
