<?php
// 修复：服务端验证 MIME + 扩展名
$allowedExt = ['txt', 'pdf', 'jpg', 'png'];
$allowedMime = ['text/plain', 'application/pdf', 'image/jpeg', 'image/png'];

$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$mime = mime_content_type($_FILES['file']['tmp_name']);

if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
    http_response_code(400);
    exit('Invalid file');
}

$newName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $newName);