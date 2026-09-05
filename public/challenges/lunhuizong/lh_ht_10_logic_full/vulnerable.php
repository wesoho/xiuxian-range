<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】业务逻辑综合：支付篡改 + 越权 + 竞态
echo '<h2>万魔殿·业务逻辑综合</h2>';
echo '<p>三道考验：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_yy_12_payment/">考验一：支付篡改</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_13_captcha/">考验二：验证码重用</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_15_brute/">考验三：暴力破解</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';