<?php
// 修复：输入净化 + 输出转义
$content = strip_tags($_POST['content'] ?? '');
file_put_contents(__DIR__ . '/comments.txt', $content . "\n", FILE_APPEND);

foreach (file(__DIR__ . '/comments.txt') as $line) {
    echo '<div class="xxr-narrative">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</div>';
}