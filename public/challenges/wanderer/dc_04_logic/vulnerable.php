<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * DC-04 社交平台逻辑漏洞
 */
echo '<h2>社交平台修真靶场</h2>';
echo '<p>修真社交平台的逻辑漏洞综合。</p>';

echo '<h3>📚 修真靶场漏洞环境</h3>';
echo '<ul>';
echo '<li><a href="/challenges/wanmozong/wm_yy_10_idor_h/">水平越权</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_12_payment/">支付逻辑</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_13_captcha/">验证码重用</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_15_brute/">暴力破解</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合利用逻辑漏洞，获取 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';