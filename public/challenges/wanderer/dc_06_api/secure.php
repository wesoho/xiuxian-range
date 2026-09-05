<?php
/**
 * DC-06 secure.php - API 安全全链路
 */

echo '<h2>API 安全 · 全链路防御</h2>';

echo '<h3>🔐 API 安全要点</h3>';
echo '<ul>';
echo '<li><strong>认证</strong>：OAuth 2.0 + JWT + 短期 access_token + refresh_token</li>';
echo '<li><strong>授权</strong>：scope-based + 资源级权限</li>';
echo '<li><strong>签名</strong>：HMAC 签名（防篡改）</li>';
echo '<li><strong>限流</strong>：令牌桶 / 滑动窗口</li>';
echo '<li><strong>输入验证</strong>：JSON Schema / OpenAPI</li>';
echo '<li><strong>输出</strong>：避免泄露内部栈、字段过滤</li>';
echo '<li><strong>CORS</strong>：精确白名单</li>';
echo '<li><strong>HTTPS</strong>：强制 TLS 1.2+</li>';
echo '</ul>';

echo '<h3>📜 OWASP API Security Top 10 (2023)</h3>';
echo '<ol>';
echo '<li>API1: Broken Object Level Authorization</li>';
echo '<li>API2: Broken Authentication</li>';
echo '<li>API3: Broken Object Property Level Authorization</li>';
echo '<li>API4: Unrestricted Resource Consumption</li>';
echo '<li>API5: Broken Function Level Authorization</li>';
echo '<li>API6: Unrestricted Access to Sensitive Business Flows</li>';
echo '<li>API7: Server Side Request Forgery</li>';
echo '<li>API8: Security Misconfiguration</li>';
echo '<li>API9: Improper Inventory Management</li>';
echo '<li>API10: Unsafe Consumption of APIs</li>';
echo '</ol>';