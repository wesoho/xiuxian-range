<?php
// 漏洞：Docker 容器逃逸（教学演示）
// 容器以特权模式运行 + 挂载 /proc /sys /dev
echo '<h2>Docker 容器逃逸教学</h2>';
echo '<p>实际攻击需要特权容器模式 + 挂载敏感路径</p>';
echo '<p>本修真靶场为安全设计，未启用特权模式</p>';
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('escape');
