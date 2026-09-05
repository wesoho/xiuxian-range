<?php
// 修复：使用 Redis 存储 Session + 严格过滤
session_start();

// 严格过滤
$name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['name'] ?? '');
$_SESSION['username'] = $name;

// LFI 路径限制
$allowed = ['home', 'profile'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed, true)) exit('Not allowed');
include __DIR__ . '/pages/' . $file . '.php';