<?php /** @var string $message */ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 · 道友迷路了 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
    <link href="/assets/css/egg.css" rel="stylesheet">
</head>
<body>
    <main class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="xxr-form-card text-center" style="max-width:560px;">
            <div class="xxr-realm-glyph mx-auto" style="--rc: var(--xxr-qing); width:76px; height:76px; font-size:2.4rem;">迷</div>
            <h1 style="font-size:3rem;">404</h1>
            <p class="xxr-form-sub">道友迷路了 · 此路不通</p>
            <p class="text-muted"><?= e($message ?? '页面未找到') ?></p>

            <!-- 迷路诗（老掌门题在断崖上的打油诗，偶尔显形） -->
            <div class="mt-4 p-3" style="border:1px dashed rgba(212,175,55,.45); border-radius:8px; background:rgba(212,175,55,.05);">
                <p class="mb-1 text-muted" style="letter-spacing:.3em;">迷路诗 · 其一</p>
                <p class="mb-1 text-warning" style="font-family:serif;">秘府三千不锁门</p>
                <p class="mb-1 text-warning" style="font-family:serif;">境由心生路自存</p>
                <p class="mb-1 text-warning" style="font-family:serif;">入梦方知天机近</p>
                <p class="mb-0 text-warning" style="font-family:serif;">口诀余韵在页根</p>
                <p class="mb-0 mt-2 small text-muted">（据说，把每句的第一个字连起来读，就会有好事发生。）</p>
            </div>

            <a href="/" class="xxr-btn xxr-btn-primary mt-3">返回山门</a>
        </div>
    </main>

    <!-- 「口诀余韵在页根」——残页·肆的口令就藏在你看不到的地方：
         天机残页·肆 · 口令：flag{egg_tianji_4}
         藏头「秘境入口」指向的下一站：/mijing
         （口令请到 /tianji 天机阁兑换） -->
    <script src="/assets/js/egg.js" defer></script>
</body>
</html>
