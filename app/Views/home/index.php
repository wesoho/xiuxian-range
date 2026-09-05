<?php
/** @var ?array $user */
/** @var array $stats */
/** @var array $realms */
/** @var string $announcement */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>山门 · 修真网络安全靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <!-- 公告 -->
        <?php if ($announcement): ?>
            <div class="alert alert-warning text-center">
                <strong>📢 掌门通告：</strong> <?= e($announcement) ?>
            </div>
        <?php endif; ?>

        <!-- Hero：宣纸卷轴 -->
        <section class="xxr-hero">
            <span class="xxr-couplet left">以攻砺剑</span>
            <span class="xxr-couplet right">以守筑基</span>
            <span class="xxr-seal xxr-seal-md">试炼</span>

            <p class="xxr-hero-kicker">网络安全 · 修真炼心</p>
            <h1>修真网络安全靶场</h1>
            <p>修真八阶，三大宗门的试炼等你来闯。<br>从炼气到大乘，一路修真一路飞升。</p>
            <div>
                <a href="/challenges" class="xxr-btn xxr-btn-primary me-2">进入试炼</a>
                <?php if (!$user): ?>
                    <a href="/register" class="xxr-btn xxr-btn-secondary">拜师入门</a>
                <?php endif; ?>
            </div>
            <div class="row mt-5 text-center">
                <div class="col-md-4">
                    <div class="xxr-realm-icon">🏯</div>
                    <h4 class="text-gold"><?= (int) $stats['total_challenges'] ?></h4>
                    <p class="text-muted">修真关卡</p>
                </div>
                <div class="col-md-4">
                    <div class="xxr-realm-icon">👥</div>
                    <h4 class="text-gold"><?= (int) $stats['total_users'] ?></h4>
                    <p class="text-muted">修真弟子</p>
                </div>
                <div class="col-md-4">
                    <div class="xxr-realm-icon">⚡</div>
                    <h4 class="text-gold">100%</h4>
                    <p class="text-muted">安全教学</p>
                </div>
            </div>
        </section>

        <?php if (!empty($daoHidden)): ?>
        <!-- 彩蛋：天机残页·贰（?dao=1 触发） -->
        <section class="my-4 p-4 rounded" style="border:1px dashed rgba(212,175,55,.5); background:rgba(212,175,55,.06);">
            <h5 class="text-gold">🌫️ 云雾散去，山门之上浮现出几行小字……</h5>
            <p class="mb-1 text-muted">「修行之人，抬头见道。你既然找到了这里，这一页便赠予你。」</p>
            <p class="mb-1"><strong class="text-warning">🧾 天机残页·贰 · 口令：<code>flag{egg_tianji_2}</code></strong></p>
            <p class="mb-0 small text-muted">（口令请到 <a href="/tianji">✨天机阁</a> 兑换。下一环线索：看地图的时候，试试对地图念一句「天机」的咒语。）</p>
        </section>
        <?php endif; ?>

        <!-- 修真境界地图 -->
        <section class="my-5">
            <h2 class="xxr-section-title">修真境界地图</h2>
            <div class="xxr-ornament"></div>
            <p class="xxr-section-sub">八阶之路 · 循序渐进</p>
            <div class="row g-3">
                <?php
                $glyphs = ['liqi'=>'炼','zhuji'=>'筑','jindan'=>'金','yuanying'=>'婴','huashen'=>'神','lianxu'=>'虚','heti'=>'合','dacheng'=>'乘'];
                foreach ($realms as $realm): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <a href="/challenges/realm/<?= e($realm['code']) ?>"
                           class="xxr-realm-card <?= $realm['is_current'] ? 'current' : '' ?>"
                           style="--rc: var(--realm-<?= e($realm['code']) ?>, var(--xxr-gold-dim));">
                            <div class="xxr-realm-glyph"><?= $glyphs[$realm['code']] ?? '仙' ?></div>
                            <div class="xxr-realm-name"><?= e($realm['name']) ?></div>
                            <div class="xxr-realm-count"><?= (int) $realm['count'] ?> 关</div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 三宗门介绍 -->
        <section class="my-5">
            <h2 class="xxr-section-title">三大宗门</h2>
            <div class="xxr-ornament"></div>
            <p class="xxr-section-sub">正道 · 魔道 · 玄门</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="xxr-realm-card" style="--rc: var(--realm-lianxu);">
                        <div class="xxr-realm-glyph" style="--rc: var(--xxr-qing);">青</div>
                        <div class="xxr-realm-name" style="color: var(--xxr-qing);">青云宗</div>
                        <p class="small text-muted">正道之名门，以 XSS / CSRF / 认证等正道绝学见长。练功房、丹房、藏经阁、阵法台……处处皆试炼。</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="xxr-realm-card" style="--rc: var(--xxr-cinnabar);">
                        <div class="xxr-realm-glyph" style="--rc: var(--xxr-cinnabar);">魔</div>
                        <div class="xxr-realm-name" style="color: var(--xxr-red);">万魔宗</div>
                        <p class="small text-muted">魔道之圣地，专研反序列化、RCE、SSRF 等魔道秘术。魔窟、血池、炼魂殿，险象环生。</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="xxr-realm-card" style="--rc: var(--xxr-zi);">
                        <div class="xxr-realm-glyph" style="--rc: var(--xxr-zi);">轮</div>
                        <div class="xxr-realm-name" style="color: var(--xxr-zi);">轮回宗</div>
                        <p class="small text-muted">中立之玄门，钻研 SQL 注入、XXE、加密学等玄妙之道。轮回殿、忘川河、六道轮回。</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 学习流程 -->
        <section class="my-5 bg-dark-translucent p-4 rounded">
            <h2 class="xxr-section-title">三阶段学习路径</h2>
            <div class="xxr-ornament"></div>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="xxr-realm-glyph" style="--rc: var(--xxr-qing); margin-bottom: 10px;">学</div>
                    <h5 class="text-gold">第一阶段 · 习道</h5>
                    <p class="small text-muted">习武先明理。每个关卡先学漏洞原理、攻击场景、防御方法。</p>
                </div>
                <div class="col-md-4">
                    <div class="xxr-realm-glyph" style="--rc: var(--xxr-cinnabar); margin-bottom: 10px;">战</div>
                    <h5 class="text-gold">第二阶段 · 试炼</h5>
                    <p class="small text-muted">进入真实靶场环境实战，提交 Flag 证明通关。三级提示为你指点迷津。</p>
                </div>
                <div class="col-md-4">
                    <div class="xxr-realm-glyph" style="--rc: var(--realm-jindan); margin-bottom: 10px;">悟</div>
                    <h5 class="text-gold">第三阶段 · 悟道</h5>
                    <p class="small text-muted">查看完整 Writeup，源码对比，根因分析，由攻击者变为守护者。</p>
                </div>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
</body>
</html>
