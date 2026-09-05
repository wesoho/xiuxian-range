<?php
/**
 * 【修复/安全】DC-02 secure.php - 轮回宗综合防御
 */

echo '<h2>轮回宗六道轮回 · 综合防御</h2>';

echo '<h3>🔄 攻击链防御</h3>';
echo '<p>本关攻击链为：LFI → 反序列化 → XSS → SSRF → JWT → 越权 → 提权</p>';
echo '<p>修真靶场防御策略：</p>';
echo '<ol>';
echo '<li><strong>LFI 防御</strong>：白名单文件 + 路径规范化</li>';
echo '<li><strong>反序列化防御</strong>：禁止 unserialize 不可信数据 + JSON 替代</li>';
echo '<li><strong>XSS 防御</strong>：CSP + 输出转义 + 输入过滤</li>';
echo '<li><strong>SSRF 防御</strong>：白名单域名 + 禁用危险协议 + 解析后 IP 检查</li>';
echo '<li><strong>JWT 防御</strong>：强算法（HS256/RS256）+ 密钥轮转 + kid 白名单</li>';
echo '<li><strong>越权防御</strong>：RBAC 资源级权限 + 审计</li>';
echo '<li><strong>提权防御</strong>：最小权限 + 强制访问控制（MAC）</li>';
echo '</ol>';

echo '<h3>🛡️ 零信任架构</h3>';
echo '<pre>
// 每个请求都需重新认证和授权
class ZeroTrust {
    public function check(Request $req): bool {
        // 1. 身份验证（多因素）
        // 2. 设备信任（设备指纹）
        // 3. 行为分析（异常检测）
        // 4. 资源授权（细粒度）
        // 5. 加密通信（端到端）
        return $this->identity->verify($req) &&
               $this->device->trust($req) &&
               $this->behavior->isNormal($req) &&
               $this->policy->authorize($req);
    }
}
</pre>';