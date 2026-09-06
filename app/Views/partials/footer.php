<?php
    // 彩蛋系统挂钩：飞升者持有 theme_gold 装扮时，前端为其点亮金光主题
    $__footerUser = auth()->user();
    $__hasGoldTheme = false;
    if ($__footerUser) {
        try {
            $__hasGoldTheme = (int) db()->fetchScalar(
                "SELECT COUNT(*) FROM user_cosmetics WHERE user_id = ? AND cosmetic_code = 'theme_gold'",
                [(int) $__footerUser['id']]
            ) > 0;
        } catch (\Throwable $e) {
            $__hasGoldTheme = false; // 旧库尚未安装彩蛋表时静默降级
        }
    }
?>
<?php if ($__hasGoldTheme): ?><span id="xxrThemeFlag" hidden></span><?php endif; ?>
<footer class="xxr-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h6 class="d-flex align-items-center gap-2">
                    <span class="xxr-seal xxr-seal-sm" style="transform: scale(0.72) rotate(-2deg); margin: -8px -4px;">修真</span>
                    修真网络安全靶场
                </h6>
                <p class="small text-muted mb-1">XiuXian Range v1.0.0 · 修真网络安全教学平台</p>
                <p class="small text-muted">从炼气到大乘，一路修真一路飞升。</p>
            </div>
            <div class="col-md-3">
                <h6>快速链接</h6>
                <ul class="list-unstyled small">
                    <li><a href="/about" class="text-muted"><i class="bi bi-journal-bookmark me-1"></i>关于靶场</a></li>
                    <li><a href="/leaderboard" class="text-muted"><i class="bi bi-trophy me-1"></i>修真榜</a></li>
                    <li><a href="/tianji" class="text-muted"><i class="bi bi-stars me-1"></i>天机阁</a></li>
                    <li><a href="https://owasp.org" target="_blank" class="text-muted"><i class="bi bi-shield-check me-1"></i>OWASP</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>免责说明</h6>
                <p class="small text-muted">仅供网络安全学习与研究使用<br>请遵守网络安全法律法规</p>
            </div>
        </div>
        <hr style="border-color: rgba(212,175,55,0.18);">
        <div class="text-center small text-muted">
            © <?= date('Y') ?> 修真靶场 · Powered by 李叔AI · <span class="text-warning">愿道友早登大乘！</span>
        </div>
    </div>
</footer>
<link href="/assets/css/egg.css" rel="stylesheet">
<script src="/assets/js/egg.js" defer></script>
