<?php
/**
 * DC-08 secure.php - CVE 防御
 */

echo '<h2>ThinkPHP 5.0.23 RCE (CVE-2018-20062) 防御</h2>';

echo '<h3>📜 漏洞描述</h3>';
echo '<p>ThinkPHP 5.0.23 路由处理缺陷导致 RCE。攻击者可构造特殊 URL 远程执行 PHP 代码。</p>';

echo '<h3>🛡️ 修复方案</h3>';
echo '<ol>';
echo '<li><strong>升级</strong>：升级到 ThinkPHP 5.0.24+ / 5.1.31+</li>';
echo '<li><strong>WAF</strong>：拦截 <code>s=</code>、<code>method=</code>、<code>filter[]=</code> 等特征</li>';
echo '<li><strong>禁用危险函数</strong>：<code>disable_functions</code></li>';
echo '<li><strong>RASP</strong>：运行时拦截</li>';
echo '<li><strong>最小权限</strong>：Web 服务器以非 root 运行</li>';
echo '</ol>';

echo '<h3>📋 CVE 防御通用流程</h3>';
echo '<ol>';
echo '<li>订阅 CVE 通知（CVE.org, NVD）</li>';
echo '<li>依赖扫描（Composer Audit / OWASP Dependency-Check）</li>';
echo '<li>补丁管理（定期更新）</li>';
echo '<li>虚拟补丁（WAF 规则）</li>';
echo '<li>应急响应</li>';
echo '</ol>';