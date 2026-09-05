<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合密码学：ECB 模式 + 弱哈希 + 弱随机数
echo '<h2>幽冥界·密码学综合</h2>';
echo '<p>四道谜题：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_hs_09_crypto_ecb/">谜题一：ECB 模式</a></li>';
echo '<li><a href="/challenges/qingong/qy_hs_10_crypto_hash/">谜题二：Hash 长度扩展</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_01_jwt_none/">谜题三：JWT alg=none</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_02_jwt_weak/">谜题四：JWT 弱密钥</a></li>';
echo '</ol>';
echo '<p>四关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';