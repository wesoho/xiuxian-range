<?php
// 修复：完整属性转义
$msg = $_GET['msg'] ?? '';
?>
<div title="<?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>">悬停查看 title</div>