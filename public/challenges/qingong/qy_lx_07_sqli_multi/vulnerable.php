<?php
// 漏洞：堆叠语句（原 mysqli_multi_query，演示环境以 PDO + SQLite 模拟同一拼接漏洞）
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
$pdo = db()->pdo();
$id = $_GET['id'] ?? '1';
if (isset($_GET['id'])) {
    try {
        $pdo->exec("SELECT * FROM demo_users WHERE id = $id");
    } catch (PDOException $e) {
        echo '错误：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
// 攻击者可追加 UPDATE 修改管理员密码
xxr_flag_reveal('sqli');
