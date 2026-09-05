<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * 【漏洞】DC-10 大乘·飞升 终极挑战
 *
 * 修真之巅，飞升在即。
 * 综合运用全部修真绝技，击破所有护山大阵。
 */
echo '<h2>🏆 修真之巅 · 飞升大乘</h2>';
echo '<p>这是修真靶场的终极考验。</p>';

echo '<h3>🎯 终极飞升要求</h3>';
echo '<ol>';
echo '<li><strong>炼气期</strong>全部 10 关通过（信息安全意识）</li>';
echo '<li><strong>筑基期</strong>全部 15 关通过（XSS/CSRF/SQLi 基础）</li>';
echo '<li><strong>金丹期</strong>全部 15 关通过（过滤绕过）</li>';
echo '<li><strong>元婴期</strong>全部 15 关通过（XXE/SSRF/反序列化）</li>';
echo '<li><strong>化神期</strong>全部 15 关通过（JWT/OAuth/CORS）</li>';
echo '<li><strong>炼虚期</strong>全部 10 关通过（综合 RCE）</li>';
echo '<li><strong>合体期</strong>全部 10 关通过（剧情综合）</li>';
echo '<li><strong>大乘期</strong>全部 10 关通过（含真实 CVE 复现）</li>';
echo '</ol>';

echo '<h3>🌟 飞升奖励</h3>';
echo '<ul>';
echo '<li>获得"飞升大乘"称号（最高修真境界）</li>';
echo '<li>获得专属徽章：🏆 修真之巅</li>';
echo '<li>解锁长老殿·禁地区</li>';
echo '<li>获得修真靶场定制周边</li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 完成 100 关所有修真靶场，获取终极 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';

echo '<blockquote class="text-warning text-center">';
echo '<p>道高一尺，魔高一丈。<br>愿道友飞升大乘，守护修真界安宁！</p>';
echo '</blockquote>';