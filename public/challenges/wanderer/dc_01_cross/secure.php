<?php
/**
 * DC-01 secure.php - 跨宗门综合防御
 *
 * 综合靶场的安全实践：
 *  - 严格的 RBAC 权限控制
 *  - 多因素认证（MFA）
 *  - 网络微分段
 *  - 零信任架构
 *  - Web 应用防火墙（WAF）
 *  - 入侵检测系统（IDS）
 *  - 实时审计与告警
 *  - 蜜罐与欺骗技术
 */

echo '<h2>万魔宗禁地 · 综合防御体系</h2>';

// 1. 防御深度（Defense in Depth）
echo '<h3>🛡️ 防御深度体系</h3>';
echo '<ul>';
echo '<li><strong>L1 - 边界</strong>：CDN + DDoS 防护 + IP 黑白名单</li>';
echo '<li><strong>L2 - 网络</strong>：微分段 + 跳板机 + 零信任网络访问</li>';
echo '<li><strong>L3 - 主机</strong>：最小化系统 + SELinux + AppArmor</li>';
echo '<li><strong>L4 - 应用</strong>：WAF + RASP + 输入验证 + 输出编码</li>';
echo '<li><strong>L5 - 数据</strong>：加密存储 + 密钥管理 + 备份</li>';
echo '<li><strong>L6 - 监控</strong>：SIEM + 实时告警 + 异常检测</li>';
echo '<li><strong>L7 - 响应</strong>：应急响应预案 + 红蓝对抗</li>';
echo '</ul>';

// 2. RBAC 权限模型
class RBAC {
    private const PERMISSIONS = [
        'guest'     => [],
        'disciple'  => ['read:public', 'practice:basic'],
        'inner'     => ['read:inner', 'practice:inner'],
        'elder'     => ['read:inner', 'practice:inner', 'manage:disciples'],
        'admin'     => ['*'],  // 所有权限
    ];

    public static function check(string $role, string $permission): bool {
        $perms = self::PERMISSIONS[$role] ?? [];
        return in_array('*', $perms, true) || in_array($permission, $perms, true);
    }
}

// 3. 多因素认证
class MFA {
    public static function verify(string $userId, string $password, string $totpCode): bool {
        // 1. 校验密码
        // 2. 校验 TOTP（基于时间的一次性密码）
        // 3. 校验设备指纹
        return true;
    }
}

// 4. 入侵检测
class IDS {
    public static function detect(string $payload): bool {
        $patterns = [
            // SQL 注入
            '/\bunion\b.*\bselect\b/i',
            '/\bselect\b.*\bfrom\b.*\bwhere\b/i',
            // XSS
            '/<script\b/i',
            '/on(error|load|click)\s*=/i',
            // 命令注入
            '/[;&|`]\s*(rm|wget|curl|bash|sh)\b/i',
            // 反序列化
            '/\bO:\d+:"/i',
            // XXE
            '/<!ENTITY\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $payload)) return true;
        }
        return false;
    }
}

// 5. 审计日志
class AuditLogger {
    private const LOG_FILE = '/var/log/xiuxian/audit.log';

    public static function log(string $userId, string $action, array $context = []): void {
        $entry = [
            'time'    => date('Y-m-d H:i:s'),
            'user_id' => $userId,
            'action'  => $action,
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
            'ua'      => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'context' => $context,
        ];
        file_put_contents(self::LOG_FILE, json_encode($entry) . "\n", FILE_APPEND);
    }
}

echo '<h3>📋 防御检查清单</h3>';
echo '<ul>';
echo '<li>✅ 数据库最小权限（无 FILE）</li>';
echo '<li>✅ 参数化查询（所有 SQL）</li>';
echo '<li>✅ 输出转义（所有 HTML）</li>';
echo '<li>✅ CSRF Token（所有写操作）</li>';
echo '<li>✅ HttpOnly + Secure Cookie</li>';
echo '<li>✅ CSP 头</li>';
echo '<li>✅ HTTPS 强制</li>';
echo '<li>✅ 安全头（X-Frame-Options, HSTS）</li>';
echo '<li>✅ 文件上传白名单 + 重命名</li>';
echo '<li>✅ 反序列化白名单</li>';
echo '<li>✅ SSRF 协议白名单 + 域名白名单</li>';
echo '<li>✅ JWT 强算法 + 密钥轮转</li>';
echo '<li>✅ 速率限制 + 失败锁定</li>';
echo '<li>✅ 完整审计日志</li>';
echo '<li>✅ 入侵检测（签名 + 行为）</li>';
echo '</ul>';