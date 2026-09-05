<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】迷你 CMS 代码审计挑战（SQL注入 / XSS / CSRF 综合审计）
echo '<h2>禁地·代码审计</h2>';
echo '<p>本修真靶场提供一个迷你 CMS 供代码审计：</p>';
echo '<p>请访问 <a href="/challenges/qingong/qy_jz_03_sqli_num/">SQL 注入关卡</a>、<a href="/challenges/qingong/qy_jz_01_xss_ref/">XSS 关卡</a>、<a href="/challenges/qingong/qy_jz_02_csrf_get/">CSRF 关卡</a> 综合审计。</p>';
echo '<p>修真靶场 CMS 综合关卡：<code class="xxr-mono">', xxr_challenge_flag(), '</code></p>';