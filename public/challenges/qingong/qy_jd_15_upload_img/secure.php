<?php
// 修复：使用 GD 重新生成图片（彻底去除 PHP 代码）
$allowed = ['jpg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$tmp = $_FILES['file']['tmp_name'];

if (!in_array($ext, $allowed, true) || !getimagesize($tmp)) {
    exit('Invalid image');
}

// 用 GD 重新生成（剥离所有 PHP 代码）
$image = match($ext) {
    'jpg' => imagecreatefromjpeg($tmp),
    'png' => imagecreatefrompng($tmp),
    'gif' => imagecreatefromgif($tmp),
};

$newName = bin2hex(random_bytes(8)) . '.' . $ext;
$dstPath = 'uploads/' . $newName;
match($ext) {
    'jpg' => imagejpeg($image, $dstPath),
    'png' => imagepng($image, $dstPath),
    'gif' => imagegif($image, $dstPath),
};