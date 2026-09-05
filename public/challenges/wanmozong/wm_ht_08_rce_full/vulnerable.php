<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合 RCE：命令注入 + 代码执行
echo '<h2>血池·RCE 综合</h2>';
echo '<p>三道血咒：</p>';
echo '<ol>';
echo '<li><a href="/challenges/wanmozong/wm_jz_09_rce_basic/">血咒一：基础 RCE</a></li>';
echo '<li><a href="/challenges/qingong/qy_jd_09_rce_space/">血咒二：空格过滤</a></li>';
echo '<li><a href="/challenges/qingong/qy_jd_10_rce_filter/">血咒三：关键字过滤</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';