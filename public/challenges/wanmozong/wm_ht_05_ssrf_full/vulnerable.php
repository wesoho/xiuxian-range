<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合 SSRF：内网探测 + 协议利用
echo '<h2>炼魂殿·SSRF 综合</h2>';
echo '<p>三阶段：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_yy_05_ssrf_basic/">第一阶段：基础 SSRF</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_06_ssrf_proto/">第二阶段：gopher 攻击 Redis</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_07_ssrf_rebind/">第三阶段：DNS rebinding</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';