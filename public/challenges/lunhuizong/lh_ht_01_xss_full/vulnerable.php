<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合 XSS：反射 + 存储 + DOM（三重利用链）
// 修真靶场汇总：访问其他 XSS 关卡完成合体
echo '<h2>试炼塔·XSS 综合</h2>';
echo '<p>三大子关：</p>';
echo '<ol>';
echo '<li><a href="/challenges/qingong/qy_jz_01_xss_ref/">试炼塔第一层（反射型）</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_12_xss_store/">试炼塔第二层（存储型）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_01_xss_dom/">试炼塔第三层（DOM型）</a></li>';
echo '</ol>';
echo '<p>三关全通后获得：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';