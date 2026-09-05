<?php
/** @var string $csrf */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>拜师 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-5">
        <div class="xxr-form-card">
            <h2>🎓 拜师入门</h2>
            <p class="text-center text-muted small mb-4">从这一刻起，你正式踏入修真之路</p>

            <form method="POST" action="/register">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">

                <div class="mb-3">
                    <label class="form-label">修真代号 <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required
                           pattern="[a-zA-Z0-9_]{3,20}" title="3-20位字母/数字/下划线" autofocus>
                    <small class="text-muted">3-20位字母/数字/下划线，用于登录</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">邮箱（可选）</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">密 码 <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">确认密码 <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="8">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">所属宗门</label>
                    <select name="sect" class="form-control">
                        <option value="wanderer">🌿 散修（暂不选）</option>
                        <option value="qiingong">🏯 青云宗（正道）</option>
                        <option value="wanmozong">🔥 万魔宗（魔道）</option>
                        <option value="lunhuizong">🔮 轮回宗（中立）</option>
                    </select>
                </div>

                <button type="submit" class="xxr-btn xxr-btn-primary w-100">🌟 拜师入门</button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">已有账号？<a href="/login" class="text-gold">直接入山</a></small>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>