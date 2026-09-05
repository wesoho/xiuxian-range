<?php
/**
 * LH-YY-14 secure.php - 安全实践参考
 *
 * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。
 */

// 修复：发送邮件含 token，用户通过 token 链接验证身份
$email = $_POST['email'] ?? '';
$token = bin2hex(random_bytes(32));
$expires = time() + 3600;
$stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)');
$stmt->execute([$email, $token, $expires]);

// 通过邮件发送链接（教学环境可显示）
echo "重置链接：https://your-domain.com/reset?token=$token";