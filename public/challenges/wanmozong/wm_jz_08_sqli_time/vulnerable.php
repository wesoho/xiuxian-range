<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-JZ-08 vulnerable.php - 漏洞演示
 * 分类：sqli_time
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p); } catch (PDOException $e) { die('fail'); }

$name = $_GET['name'] ?? '';

// 【漏洞】时间盲注
$start = microtime(true);
$pdo->query("SELECT id FROM demo_users WHERE username = '$name'");
$time = microtime(true) - $start;

echo "查询耗时：{$time}s";
if ($time > 3) {
    echo '<div class="alert alert-warning">检测到 SLEEP() 调用</div>';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('sqli');
