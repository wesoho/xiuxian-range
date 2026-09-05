<?php
// 漏洞：Session 注入 + LFI
session_start();

// 在 session 中存储可控数据
if (isset($_POST['name'])) {
    $_SESSION['username'] = $_POST['name'];  // 【漏洞】未过滤
}

// LFI 包含 Session 文件
if (isset($_GET['file'])) {
    include $_GET['file'];  // 可包含 /var/lib/php/sessions/sess_xxx
} else {
    echo '<form method="POST"><input name="name"><button>提交</button></form>';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('lfi');
