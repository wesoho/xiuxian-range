<?php
// 漏洞：可上传 .htaccess
if ($_FILES && isset($_FILES['file'])) {
    $name = $_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
    echo "上传成功：$name";
}
// 攻击者可上传 AddType application/x-httpd-php .jpg
// 然后上传 .jpg 文件即可按 PHP 解析
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('upload');
