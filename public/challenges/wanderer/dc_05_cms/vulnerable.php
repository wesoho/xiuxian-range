<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * DC-05 CMS 代码审计挑战
 *
 * 修真靶场迷你 CMS 包含多个漏洞：
 *  - SQL 注入
 *  - XSS
 *  - CSRF
 *  - 文件上传
 *  - 后台弱口令
 */
echo '<h2>CMS 修真靶场</h2>';
echo '<p>修真靶场 CMS 综合代码审计。</p>';

echo '<h3>📚 修真靶场 CMS 漏洞</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_jz_03_sqli_num/">SQL注入</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_01_xss_ref/">XSS</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_02_csrf_get/">CSRF</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_jz_14_upload_js/">文件上传</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_lq_06_weak_password/">弱口令</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 审计 CMS 源码找出 5+ 漏洞并利用，获取 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';