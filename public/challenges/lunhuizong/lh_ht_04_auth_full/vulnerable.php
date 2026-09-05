<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 综合认证漏洞
echo '<h2>轮回殿·认证综合</h2>';
echo '<p>三道防线：</p>';
echo '<ol>';
echo '<li><a href="/challenges/wanmozong/wm_lq_09_sqli_error/">防线一：SQL 注入获取凭据</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_01_jwt_none/">防线二：JWT alg=none</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_14_pwd_reset/">防线三：任意密码重置</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';