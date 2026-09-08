<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-JZ-06 vulnerable.php - 漏洞演示
 * 分类：sqli_error
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';

// 【漏洞】报错注入 + 错误回显
try {
    $stmt = $pdo->query("SELECT * FROM demo_users WHERE id = '$id'");
    foreach ($stmt as $row) { print_r($row); }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}
if (xxr_db_driver() === 'sqlite') {
    echo xxr_driver_note('当前为 SQLite 演示环境：extractvalue()/updatexml() 报错取数为 MySQL 专属手法，SQLite 无等价函数；此处错误回显可用于判断后端数据库类型。完整报错注入体验请使用 Docker MySQL 环境。');
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('sqli');
