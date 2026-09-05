<?php
// 修复：白名单 + 输出转义
$msg = preg_replace('/[^a-zA-Z0-9\s\p{Han}]/u', '', $_GET['msg'] ?? '');
echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');