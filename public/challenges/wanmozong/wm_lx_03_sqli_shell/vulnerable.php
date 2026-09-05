<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 漏洞：SQL 注入 + INTO OUTFILE GetShell
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();
try { $pdo = new PDO($dsn, $__xxr_u, $__xxr_p); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';
// 【漏洞】直接拼接 + 数据库用户具有 FILE 权限
// 演示环境（SQLite）不支持 INTO OUTFILE，捕获语法差异后继续展示攻击特征揭示
try {
    $pdo->query("SELECT username FROM demo_users WHERE id = $id INTO OUTFILE '/var/www/html/uploads/shell.php'");
    // 写入内容是 PHP 代码
} catch (Throwable $e) {
    echo '<div class="xxr-narrative">SQL 执行异常（演示环境）：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
}
xxr_flag_reveal('sqli');
