<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合 CSRF：GET / POST / Token 绕过
echo '<h2>阵法台·CSRF 综合</h2>';
echo '<p>四道阵法：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_jz_02_csrf_get/">阵法一：GET 型 CSRF</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_jz_10_csrf_post/">阵法二：POST 型 CSRF</a></li>';
echo '<li><a href="/challenges/qingong/qy_jd_03_csrf_token/">阵法三：Token 可预测</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_05_cors/">阵法四：CORS 配置错误</a></li>';
echo '</ol>';
echo '<p>四关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';