<?php
// 修复：参数化查询
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Invalid ID');
}

$stmt = $pdo->prepare('SELECT username, email FROM demo_users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$row = $stmt->fetch();
if ($row) {
    echo '<div>弟子：' . htmlspecialchars($row['username']) . '</div>';
}