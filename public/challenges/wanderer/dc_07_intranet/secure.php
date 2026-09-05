<?php
/**
 * DC-07 secure.php - 内网穿透防御
 */

echo '<h2>内网安全 · 完整防御体系</h2>';

echo '<h3>🔒 内网安全原则</h3>';
echo '<ul>';
echo '<li><strong>网络分段</strong>：VLAN 隔离 + 防火墙策略</li>';
echo '<li><strong>最小权限</strong>：基于角色的访问控制（RBAC）</li>';
echo '<li><strong>零信任</strong>：每个请求都需验证</li>';
echo '<li><strong>凭证轮转</strong>：定期修改密码 + 密钥</li>';
echo '<li><strong>日志审计</strong>：集中化日志 + 异常检测</li>';
echo '<li><strong>补丁管理</strong>：及时更新安全补丁</li>';
echo '<li><strong>EDR</strong>：终端检测与响应</li>';
echo '<li><strong>NDR</strong>：网络检测与响应</li>';
echo '</ul>';

echo '<h3>🛡️ 域控制器保护</h3>';
echo '<ul>';
echo '<li>Tier-0 模型（域控独立管理）</li>';
echo '<li>PAW（特权访问工作站）</li>';
echo '<li>Credential Guard（凭证保护）</li>';
echo '<li>定期域账户审计</li>';
echo '<li>LAPS（本地管理员密码轮转）</li>';
echo '</ul>';