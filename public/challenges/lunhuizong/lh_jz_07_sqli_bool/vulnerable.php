<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-JZ-07 vulnerable.php - 漏洞演示
 * 分类：sqli_bool
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p); } catch (PDOException $e) { die('fail'); }

$name = $_GET['name'] ?? '';

// 【漏洞】布尔盲注
try {
    $stmt = $pdo->query("SELECT id FROM demo_users WHERE username = '$name'");
    if ($stmt->fetch()) {
        echo '<div class="alert alert-success">✅ 用户存在</div>';
    } else {
        echo '<div class="alert alert-danger">❌ 用户不存在</div>';
    }
} catch (Exception $e) { echo '错误'; }
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('sqli');
