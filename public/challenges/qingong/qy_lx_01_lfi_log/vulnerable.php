<?php
/**
 * 炼虚期·QY-LX-01 日志成魔
 * 漏洞：LFI + 日志投毒 → RCE
 *
 * 攻击步骤：
 *   1. 通过 User-Agent 注入 PHP 代码到 access.log
 *   2. LFI 包含日志文件
 *   3. PHP 代码执行，获取 Flag
 */

// 修真靶场日志路径（教学演示）
$logFile = '/var/log/apache2/access.log';

if (isset($_GET['file'])) {
    // 【漏洞】未限制 LFI 路径
    $file = $_GET['file'];
    include $file;  // 可包含日志
} else {
    echo '<p>本文件支持 include 任意路径</p>';
    echo '<p>示例：?file=../../../etc/passwd</p>';
}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('lfi');
