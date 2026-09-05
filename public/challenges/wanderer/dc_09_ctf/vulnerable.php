<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * 【漏洞】DC-09 CTF 夺旗综合题
 */
echo '<h2>CTF 综合修真靶场</h2>';
echo '<p>CTF 风格的多步骤夺旗挑战。</p>';

echo '<h3>📚 CTF 综合修真靶场</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_hs_09_crypto_ecb/">密码学：ECB</a></li>';
echo '<li><a href="/challenges/qingong/qy_hs_10_crypto_hash/">密码学：Hash 扩展</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_07_smuggle/">HTTP 请求走私</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_08_cache/">Web 缓存欺骗</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_11_deser_phar/">Phar 反序列化</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合修真靶场关卡，获取 CTF Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';