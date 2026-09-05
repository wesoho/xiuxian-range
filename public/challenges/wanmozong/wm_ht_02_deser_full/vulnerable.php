<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合反序列化：__wakeup + POP + Phar + Session
// 修真靶场汇总：访问其他反序列化关卡
echo '<h2>魔窟·反序列化综合</h2>';
echo '<p>四大子关：</p>';
echo '<ol>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_08_deser_wakeup/">魔窟第一层（__wakeup）</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_09_deser_pop/">魔窟第二层（POP 链）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_11_deser_phar/">魔窟第三层（Phar）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_12_deser_sess/">魔窟第四层（Session）</a></li>';
echo '</ol>';
echo '<p>四关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';