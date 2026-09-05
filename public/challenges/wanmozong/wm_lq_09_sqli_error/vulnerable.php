<?php
// WM-LQ-09 vulnerable.php - SQL 错误回显 + 直接拼接
/**
 * 漏洞：
 *  1. display_errors = On（教学环境故意开启）
 *  2. 直接字符串拼接 SQL，未参数化
 *  3. PDO::ERRMODE_EXCEPTION + 直接 echo 异常信息
 */

require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try {
    $pdo = new PDO($dsn, $__xxr_u, $__xxr_p, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die('数据库连接失败');
}

// 【漏洞】SQL 注入 + 错误回显（演示用 demo 表，绝不触碰平台 users 表）
$id = $_GET['id'] ?? '1';
try {
    $stmt = $pdo->query("SELECT username, email FROM demo_users WHERE id = $id");
    foreach ($stmt as $row) {
        echo '弟子：' . htmlspecialchars((string) $row['username'], ENT_QUOTES, 'UTF-8');
    }
} catch (PDOException $e) {
    echo $e->getMessage(); // 直接输出 SQL 语句片段
}
xxr_flag_reveal('sqli');
