<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合 SQL 注入：UNION + 盲注 + GetShell
echo '<h2>藏经阁·SQL 综合</h2>';
echo '<p>三阶段：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_jz_04_sqli_str/">第一阶段：字符型注入</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_jz_05_sqli_union/">第二阶段：UNION 注入</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_lx_03_sqli_shell/">第三阶段：SQLi GetShell</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';