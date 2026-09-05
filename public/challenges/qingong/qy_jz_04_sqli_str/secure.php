<?php
// 修复：参数化
$stmt = $pdo->prepare('SELECT email FROM demo_users WHERE username = ? LIMIT 1');
$stmt->execute([$_GET['name'] ?? '']);
foreach ($stmt as $row) {
    echo '<div>邮箱：' . htmlspecialchars($row['email']) . '</div>';
}