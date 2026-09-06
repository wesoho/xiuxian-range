<?php
/** @var ?array $user */
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top xxr-navbar">
    <div class="container-fluid">
        <a class="navbar-brand xxr-brand" href="/">
            <span class="xxr-seal xxr-seal-sm">修真</span>
            <span class="xxr-brand-text">修真靶场</span>
            <small class="xxr-brand-sub d-none d-md-inline">XiuXian Range</small>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#xxrNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="xxrNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/"><i class="bi bi-door-open me-1"></i>山门</a></li>
                <li class="nav-item"><a class="nav-link" href="/challenges"><i class="bi bi-map me-1"></i>境界地图</a></li>
                <li class="nav-item"><a class="nav-link" href="/leaderboard"><i class="bi bi-trophy me-1"></i>排行榜</a></li>
                <li class="nav-item"><a class="nav-link" href="/doufatai"><i class="bi bi-lightning-charge me-1"></i>斗法台</a></li>
                <li class="nav-item"><a class="nav-link" href="/xuanshang"><i class="bi bi-scroll me-1"></i>悬赏令</a></li>
                <li class="nav-item"><a class="nav-link" href="/wanbaolou"><i class="bi bi-shop me-1"></i>万宝楼</a></li>
                <?php
                    // 天机阁：隐藏入口。已敲过山门印章（session 或已获彩蛋）的道友可见
                    $__tianjiOpen = false;
                    try {
                        $__tianjiOpen = session()->has('tianji_revealed')
                            || (auth()->check() && \XiuXian\Services\EggService::hasAny((int) auth()->id()));
                    } catch (\Throwable $e) {
                        $__tianjiOpen = false; // 旧库未安装彩蛋表时静默降级
                    }
                ?>
                <li class="nav-item<?= $__tianjiOpen ? '' : ' d-none' ?>" id="xxrTianjiNav">
                    <a class="nav-link" href="/tianji" title="天机阁 · 彩蛋收集册">✨天机阁</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="/about"><i class="bi bi-journal-bookmark me-1"></i>关于</a></li>
            </ul>
            <ul class="navbar-nav">
                <?php if ($user): ?>
                    <li class="nav-item">
                        <span class="nav-link xxr-realm-badge">
                            <?= e(render_realm($user['realm_level'])) ?>
                            <span class="badge bg-warning text-dark ms-1" id="xxrPoints" title="修真点数（随获取/消耗实时同步）"><?= (int) $user['total_points'] ?> 点</span>
                        </span>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-person-circle me-1"></i><?= e($user['username']) ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person-vcard me-2"></i>修真档案</a></li>
                            <?php if (auth()->isAdmin()): ?><li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="/admin/"><i class="bi bi-shield-lock me-2"></i>后台</a></li><?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>注销</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/login">入山</a></li>
                    <li class="nav-item"><a class="nav-link btn-xxr-primary" href="/register">拜师</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <?php if (($msg = flash('success'))): ?>
        <div class="alert alert-success alert-dismissible fade show"><strong>🎉 善哉！</strong> <?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (($msg = flash('error'))): ?>
        <div class="alert alert-danger alert-dismissible fade show"><strong>⚠️ 道友注意：</strong> <?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
</div>
