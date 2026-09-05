<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-JD-06 vulnerable.php - 漏洞演示
 * 分类：sqli_second
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

// 【漏洞】二次注入
session_start();
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p); } catch (PDOException $e) { die('fail'); }

// 注册时使用 escape，但存储时是原始值
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $stmt = $pdo->prepare('INSERT INTO demo_users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, 'pass']);
    echo '注册成功';
}

// 登录查询：未转义（触发二次注入）
if (isset($_GET['login'])) {
    $username = $_GET['login'];
    $stmt = $pdo->query("SELECT * FROM demo_users WHERE username = '$username'");  // 【漏洞】
    foreach ($stmt as $row) {
        echo "欢迎回来：" . htmlspecialchars($row['username']) . '<br>';
    }
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('sqli');
