<?php
// WM-LQ-09 secure.php - 安全实践
/**
 * 修复：
 *  1. 生产环境 display_errors = Off
 *  2. 使用 prepared statement（参数化）
 *  3. 错误信息写入日志，不返回用户
 *  4. 自定义错误页
 */

// 生产环境应该：
ini_set('display_errors', '0');
error_reporting(0);

// 参数化查询
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    http_response_code(400);
    exit('Invalid ID');
}

$stmt = db()->pdo()->prepare('SELECT username, email FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row) {
    echo htmlspecialchars($row['username']);
} else {
    echo 'Not found';
}

// 错误应该记录到日志，而非输出
// error_log($e->getMessage());