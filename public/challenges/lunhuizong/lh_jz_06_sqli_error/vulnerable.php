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
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('sqli');
