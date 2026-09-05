<?php
/** @var string $csrf */
/** @var string $redirect */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>入山 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-5">
        <div class="xxr-form-card">
            <h2>🚪 入山</h2>
            <p class="text-center text-muted small mb-4">已有修真账号？在此登录</p>

            <form method="POST" action="/login">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

                <div class="mb-3">
                    <label class="form-label">修真代号 / 邮箱</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">密 码</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="xxr-btn xxr-btn-primary w-100">⚡ 入山修炼</button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">还没有账号？<a href="/register" class="text-gold">立即拜师</a></small>
            </div>

            <hr class="my-4" style="border-color: rgba(212,175,55,0.2);">
            <div class="small text-muted">
                <strong>🧪 测试账号：</strong><br>
                <code>admin</code> / <code>xxr_admin_2026</code> （管理员）<br>
                <code>qingyun</code> / <code>xxr123456</code> （青云宗弟子）
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>