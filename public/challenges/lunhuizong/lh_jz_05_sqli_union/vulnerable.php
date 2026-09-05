<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-JZ-05 vulnerable.php - 漏洞演示
 * 分类：sqli_union
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';

// 【漏洞】UNION 注入
try {
    $stmt = $pdo->query("SELECT id, username, email FROM demo_users WHERE id = '$id'");
    foreach ($stmt as $row) {
        echo "ID={$row['id']} 用户={$row['username']} 邮箱={$row['email']}<br>";
    }
} catch (PDOException $e) {
    echo "错误：" . $e->getMessage();
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('sqli');
