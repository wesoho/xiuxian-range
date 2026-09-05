<?php
// 修复：使用 password_verify（永远返回 bool）
$hash = password_hash('secret', PASSWORD_BCRYPT);
if (password_verify($_POST['password'] ?? '', $hash)) {
    echo '登录成功';
}