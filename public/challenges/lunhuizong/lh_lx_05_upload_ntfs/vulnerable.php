<?php
// 漏洞：NTFS 流绕过（仅 Windows 有效）
// 文件名 "shell.php::$DATA" 在 Windows 实际存储为 shell.php
if ($_FILES) {
    $name = $_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
    echo "上传：$name";
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('upload');
