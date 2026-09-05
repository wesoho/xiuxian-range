<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * 【漏洞】DC-06 API 安全全链路
 */
echo '<h2>API 安全修真靶场</h2>';
echo '<p>REST/GraphQL API 全链路安全挑战。</p>';

echo '<h3>📚 API 安全要点</h3>';
echo '<ul>';
echo '<li>认证：JWT / OAuth</li>';
echo '<li>授权：scope / 资源权限</li>';
echo '<li>注入：SQL/NoSQL/命令</li>';
echo '<li>限流：令牌桶</li>';
echo '<li>SSRF：图片代理</li>';
echo '<li>信息泄露：错误信息</li>';
echo '</ul>';

echo '<h3>📚 修真靶场对应关卡</h3>';
echo '<ul>';
echo '<li><a href="/challenges/wanmozong/wm_hs_01_jwt_none/">JWT alg=none</a></li>';
echo '<li><a href="/challenges/qingong/qy_hs_04_oauth/">OAuth</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_05_cors/">CORS</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_05_ssrf_basic/">SSRF</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_10_idor_h/">IDOR</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 完整测试 API 安全，获取 Flag:<br>';
echo '<code class="xxr-mono">', xxr_challenge_flag(), '</code>';
echo '</div>';