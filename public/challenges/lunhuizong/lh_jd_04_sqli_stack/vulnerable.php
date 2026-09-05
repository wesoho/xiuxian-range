<?php
/**
 * LH-JD-04 vulnerable.php - 漏洞演示
 * 分类：sqli_stacked
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';

// 【漏洞】堆叠语句：注入点后可追加任意语句（多语句一次性执行）
$pdo = db()->pdo();
$id = $_GET['id'] ?? '1';
try {
    // 演示环境以 SQLite 模拟：exec 支持一次性执行多条语句，注入语义不变
    $pdo->exec("SELECT * FROM demo_users WHERE id = $id; SELECT * FROM demo_users");
    $stmt = $pdo->query('SELECT * FROM demo_users');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (PDOException $e) {
    echo '错误：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
xxr_flag_reveal('sqli');
