<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-YY-10 vulnerable.php - 漏洞演示
 * 分类：idor_horizontal
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】未校验资源所有权
// 演示环境自愈：确保订单演示表存在（MySQL 主库由种子/迁移负责，SQLite 本地按需建表）
try {
    db()->execute('CREATE TABLE IF NOT EXISTS demo_orders (id INTEGER PRIMARY KEY, user_id INTEGER, item TEXT, amount INTEGER)');
    if ((int) db()->fetchScalar('SELECT COUNT(*) FROM demo_orders') === 0) {
        db()->execute("INSERT INTO demo_orders (id, user_id, item, amount) VALUES
            (1, 1, '灵石百斤', 100), (2, 2, '功法残卷', 55), (3, 3, '护身符', 20)");
    }
} catch (\Throwable $e) {
    // 建表失败不影响漏洞演示
}
$orderId = $_GET['id'] ?? 1;
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
$pdo = new PDO($dsn, $__xxr_u, $__xxr_p);
$stmt = null;
try {
    $stmt = $pdo->query("SELECT * FROM demo_orders WHERE id = $orderId");  // 无 user_id 校验
} catch (PDOException $e) {
    echo 'SQL 错误：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
if ($stmt) {
    foreach ($stmt as $row) {
        echo "订单 {$row['id']}：{$row['amount']}";
    }
}
xxr_flag_reveal('logic');
