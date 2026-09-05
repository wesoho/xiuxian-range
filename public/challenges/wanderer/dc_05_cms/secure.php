<?php
/**
 * DC-05 secure.php - CMS 代码审计防御
 */

echo '<h2>CMS 综合防御 · 代码审计最佳实践</h2>';

echo '<h3>📚 代码审计工具链</h3>';
echo '<ul>';
echo '<li><strong>SAST（静态扫描）</strong>：SonarQube / Semgrep / PHP_CodeSniffer</li>';
echo '<li><strong>DAST（动态扫描）</strong>：OWASP ZAP / Burp Suite Pro</li>';
echo '<li><strong>SCA（依赖扫描）</strong>：Composer Audit / Snyk</li>';
echo '<li><strong>RASP（运行时保护）</strong>：OpenRASP / Sentinel</li>';
echo '</ul>';

echo '<h3>📋 CMS 安全清单</h3>';
echo '<ul>';
echo '<li>✅ 所有 SQL 参数化</li>';
echo '<li>✅ 所有输出转义</li>';
echo '<li>✅ CSRF Token</li>';
echo '<li>✅ 文件上传白名单</li>';
echo '<li>✅ 后台 RBAC</li>';
echo '<li>✅ 操作审计</li>';
echo '<li>✅ 弱口令检测</li>';
echo '<li>✅ 漏洞奖励计划（漏洞披露）</li>';
echo '</ul>';