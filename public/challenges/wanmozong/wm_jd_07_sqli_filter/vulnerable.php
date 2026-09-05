<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-JD-07 vulnerable.php - 漏洞演示
 * 分类：sqli_filter
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】关键字过滤可被绕过
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';
// 过滤 union select
$id = preg_replace('/union|select/i', '', $id);  // 可被双写绕过：ununionion selselectect
try {
    $stmt = $pdo->query("SELECT username FROM demo_users WHERE id = $id");
    foreach ($stmt as $row) print_r($row);
} catch (PDOException $e) { echo $e->getMessage(); }
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('sqli');
