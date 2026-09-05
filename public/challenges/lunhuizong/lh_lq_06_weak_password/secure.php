<?php
// LH-LQ-06 secure.php - 安全登录实现
/**
 * 安全实践：
 * 1. 密码必须用 password_hash() 哈希存储（Argon2id / bcrypt）
 * 2. 强制密码强度策略（长度、复杂度）
 * 3. 失败次数限制（rate limiting）+ 验证码
 * 4. 多因素认证
 * 5. 审计日志
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 限流（演示：5次/分钟）
    session_start();
    $_SESSION['login_attempts'] ??= 0;
    if ($_SESSION['login_attempts'] > 5) {
        http_response_code(429);
        exit('Too Many Requests');
    }

    // 通过预哈希查询（参数化）
    $stmt = db()->pdo()->prepare(
        'SELECT id, password_hash FROM users WHERE username = ? LIMIT 1'
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login_attempts'] = 0;
        echo '登录成功';
    } else {
        $_SESSION['login_attempts']++;
        usleep(random_int(100000, 500000)); // 防时序攻击
        echo '登录失败';
    }
}