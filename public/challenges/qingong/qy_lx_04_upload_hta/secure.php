<?php
// 修复：禁止上传 .htaccess 等配置文件
$blocked = ['.htaccess', '.htpasswd', '.user.ini', 'web.config'];
$name = $_FILES['file']['name'] ?? '';
if (in_array($name, $blocked, true)) exit('Filename blocked');
if (preg_match('/\.(htaccess|htpasswd|user\.ini|web\.config)$/i', $name)) exit('Blocked');

// 同时检查 Apache 配置：<Directory> AllowOverride None