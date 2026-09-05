<?php
/**
 * LH-JD-05 vulnerable.php - 漏洞演示
 * 分类：sqli_gbk
 *
 * ⚠️ 教学用代码，故意存在漏洞
 * 修真靶场默认 display_errors=On、allow_url_include=On 等
 */

require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';

// 【漏洞】GBK 宽字节注入：addslashes 转义引号，但 GBK 下 %bf%27 会"吃掉"反斜杠
// 演示环境（SQLite）无 GBK 编码特性，保留同样的拼接漏洞并回显错误辅助理解
$pdo = db()->pdo();
$id = addslashes($_GET['id'] ?? '');
try {
    $stmt = $pdo->query("SELECT * FROM demo_users WHERE id = '$id'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (PDOException $e) {
    echo '错误：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
xxr_flag_reveal('sqli');
