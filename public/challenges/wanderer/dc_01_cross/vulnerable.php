<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * DC-01 跨宗渗透·青云→万魔
 * 攻击链：信息泄露 → SQL注入 → SSRF → 反序列化 → JWT爆破 → 越权
 *
 * 修真靶场默认配置：display_errors=On、allow_url_include=On
 */
echo '<h2>万魔宗综合靶场</h2>';
echo '<p>本文件为剧情入口，详细攻击步骤见修真靶场其他关卡。</p>';

// 修真靶场提供的真实漏洞环境链接
echo '<h3>📚 修真靶场真实漏洞环境（请访问修真靶场其他关卡）</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_lq_02_robots/">藏经阁入口（信息泄露）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_jz_05_sqli_union/">弟子名册（SQL注入）</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_03_xxe_file/">炼丹炉（XXE）</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_05_ssrf_basic/">元神出窍（SSRF）</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_01_jwt_none/">无相法印（JWT alg=none）</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_10_idor_h/">借物偷看（越权）</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合利用上述 6 个修真靶场关卡，最终获取 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';