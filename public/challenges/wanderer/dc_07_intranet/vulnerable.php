<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * 【漏洞】DC-07 内网穿透完整链
 */
echo '<h2>内网修真靶场</h2>';
echo '<p>Web → 内网 → 域控 → 提权的完整渗透链。</p>';

echo '<h3>📚 修真靶场对应关卡</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_yy_05_ssrf_basic/">SSRF 内网探测</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_06_ssrf_proto/">gopher:// Redis</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_09_deser_pop/">反序列化提权</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_10_idor_h/">横向越权</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 模拟内网渗透全链，获取 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';