<?php
// 修复：白名单
$allowed = ['jpg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed, true)) exit('Extension not allowed');