<?php
// 修复：白名单
$allowed = ['home', 'about', 'contact'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed, true)) exit('Not allowed');
include __DIR__ . '/pages/' . $file . '.php';