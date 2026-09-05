<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * DC-02 跨宗渗透·万魔→轮回
 */
echo '<h2>轮回宗综合靶场</h2>';
echo '<p>本关综合 SSRF、反序列化、XSS 三大绝技。</p>';

echo '<h3>📚 修真靶场漏洞环境</h3>';
echo '<ul>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_01_xss_dom/">DOM幻象（DOM XSS）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_07_ssrf_rebind/">轮回转世（SSRF DNS rebinding）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_08_deser_wakeup/">反向召唤（反序列化）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_14_pwd_reset/">强行改命（密码重置）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_jd_11_lfi_basic/">轮回之眼（LFI）</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合利用上述 5 个修真靶场关卡，最终获取 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';