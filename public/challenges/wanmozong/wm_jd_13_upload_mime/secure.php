<?php
// 修复：使用 mime_content_type 而非客户端 Content-Type
$allowed = ['jpg', 'png'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$realMime = mime_content_type($_FILES['file']['tmp_name']);

if (!in_array($ext, $allowed, true) || !str_starts_with($realMime, 'image/')) {
    http_response_code(400);
    exit('Invalid file');
}
$newName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $newName);