<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-LQ-06 最弱口令 - 弱口令登录
 */

// 修真靶场主 session 已启动，这里使用独立 session 名避免冲突
if (session_status() === PHP_SESSION_NONE) {
    session_name('XXR_DEMO');
    session_start();
}

// 已登录则直接显示成功
if (isset($_SESSION['demo_logged_in']) && $_SESSION['demo_logged_in']) {
    showSuccess();
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 【漏洞】明文比对 + 弱口令列表
    $weakAccounts = [
        'admin' => 'admin',
        'root'  => 'root',
        'user'  => 'user',
        'test'  => 'test123',
    ];

    if (isset($weakAccounts[$username]) && $weakAccounts[$username] === $password) {
        $_SESSION['demo_logged_in'] = true;
        $_SESSION['demo_user'] = $username;
        showSuccess();
        exit;
    }
    $error = '用户名或密码错误';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>轮回殿 · 弱口令 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🔮 轮回殿 · 入口</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗入门考验：轮回殿门口有守卫。据说用最简单的口令就能通过。
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="bg-dark-translucent p-4 rounded">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">用户名</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">密码</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="xxr-btn xxr-btn-primary w-100">入殿</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 尝试常见弱口令组合：<code>admin/admin</code>、<code>root/root</code>、<code>user/user</code>、<code>test/test123</code>。
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/LH-LQ-06" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>
<?php
function showSuccess() {
    ?>
    <div class="container py-5">
        <div class="alert alert-success text-center">
            <h3>🎉 恭喜道友突破轮回殿！</h3>
            <p class="mt-3">欢迎，<strong><?= htmlspecialchars($_SESSION['demo_user']) ?></strong>！</p>
            <p>本关 Flag: <code class="xxr-mono"><?= xxr_challenge_flag() ?></code></p>
            <a href="/challenge/LH-LQ-06" class="xxr-btn xxr-btn-primary mt-3">回到关卡提交 Flag</a>
        </div>
    </div>
    <?php
}
?>