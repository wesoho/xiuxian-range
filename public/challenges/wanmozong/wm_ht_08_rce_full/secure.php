<?php
// 【修复/安全】综合 RCE 防御：白名单 + 转义

// 1. 白名单参数
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    exit('Invalid IP');
}

// 2. escapeshellarg 转义
$cmd = 'ping -c 1 ' . escapeshellarg($ip);

// 3. 禁用高危函数（php.ini）
// disable_functions = exec, system, passthru, shell_exec, popen, proc_open, eval

// 4. RASP 运行时拦截
echo "RCE 防御就绪";