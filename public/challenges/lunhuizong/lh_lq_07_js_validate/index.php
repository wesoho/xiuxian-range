<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-LQ-07 幻象结界 - JS 前端校验绕过
 */

// 修真靶场主 session 已启动，避免冲突
if (session_status() === PHP_SESSION_NONE) {
    session_name('XXR_DEMO');
    session_start();
}

if (isset($_SESSION['demo_logged_in_07']) && $_SESSION['demo_logged_in_07']) {
    showSuccess();
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 【漏洞】仅前端 JS 校验，后端无任何验证
    // 直接放行所有输入
    if ($username === 'admin' && $password === 'xxr_lh_07') {
        $_SESSION['demo_logged_in_07'] = true;
        showSuccess();
        exit;
    }
    $error = '登录失败';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>幻象结界 · JS校验绕过</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🌫️ 幻象结界</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回宗设有幻象结界，所有验证都在前端完成，请绕过后端直接突破。
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="bg-dark-translucent p-4 rounded">
                    <!-- 【漏洞】前端 JS 校验 -->
                    <form method="POST" onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label class="form-label">用户名</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">密码</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="xxr-btn xxr-btn-primary w-100">入殿</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong>
            <ul>
                <li>查看页面源代码，会看到 <code>validateForm()</code> 函数</li>
                <li>尝试禁用 JavaScript 后提交表单</li>
                <li>使用 Burp Suite 拦截请求直接修改</li>
                <li>前端校验只是给普通用户看的，绕过即可</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/LH-LQ-07" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>

    <script>
        function validateForm() {
            var u = document.getElementById('username').value;
            var p = document.getElementById('password').value;
            // 【漏洞】仅前端校验
            if (u.length < 3 || p.length < 6) {
                alert('用户名至少3位，密码至少6位！');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
<?php
function showSuccess() {
    ?>
    <div class="container py-5">
        <div class="alert alert-success text-center">
            <h3>🎉 幻象已破！</h3>
            <p class="mt-3">Flag: <code class="xxr-mono"><?= xxr_challenge_flag() ?></code></p>
            <a href="/challenge/LH-LQ-07" class="xxr-btn xxr-btn-primary mt-3">回到关卡提交 Flag</a>
        </div>
    </div>
    <?php
}
?>