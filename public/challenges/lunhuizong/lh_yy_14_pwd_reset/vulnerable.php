<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-YY-14 vulnerable.php - 漏洞演示
 * 分类：password_reset
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】通过邮箱可重置他人密码（未验证身份）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $newPwd = bin2hex(random_bytes(4));  // 弱重置
    [$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
    $pdo = new PDO($dsn, $__xxr_u, $__xxr_p);
    $stmt = $pdo->prepare('UPDATE demo_users SET password = ? WHERE email = ?');
    $stmt->execute([$newPwd, $email]);
    echo "新密码已发送至 $email";
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('logic');
