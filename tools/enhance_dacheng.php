<?php
/**
 * 大乘期10关 - 终极挑战详细代码
 *
 * 为 DC-01 ~ DC-10 提供：
 *  - 详细的修真叙事
 *  - 多漏洞串联的攻击链
 *  - 完整的 secure.php（多层防御体系）
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/../../public/challenges';

$content = [
    'dc_01_cross' => [
        'narrative' => '修真界百年难遇的飞升试炼开启。你身为青云宗新一代弟子，肩负着潜入万魔宗禁地、获取《魔道真经》的重任。魔宗内部网络森严，从山门到藏经阁层层把守。',
        'flag_path' => '万魔宗禁地 → 藏经阁',
    ],
    'dc_02_cross' => [
        'narrative' => '你已成功从青云宗渗透到万魔宗。现在要反攻轮回宗，结合 SSRF、反序列化、XSS 三大绝技，取回《轮回天书》。',
        'flag_path' => '轮回宗六道轮回',
    ],
    'dc_03_pentest' => [
        'narrative' => '完整渗透一个修真电商系统：用户登录 → 商品浏览 → 下单 → 支付 → 后台管理。需要发现并利用系统中所有漏洞。',
        'flag_path' => '电商后台',
    ],
    'dc_04_logic' => [
        'narrative' => '修真社交平台的逻辑漏洞综合：粉丝、关注、私信、状态机。综合利用业务逻辑漏洞，实现越权、刷量、信息泄露。',
        'flag_path' => '社交平台数据库',
    ],
    'dc_05_cms' => [
        'narrative' => '审计一个完整的 CMS 系统，找出 5 个以上漏洞并利用。真实代码审计场景，从海量代码中发现蛛丝马迹。',
        'flag_path' => 'CMS 后台',
    ],
    'dc_06_api' => [
        'narrative' => 'REST/GraphQL API 的全链路安全挑战：认证、授权、注入、限流、信息泄露。考验你对现代 API 安全的全面掌握。',
        'flag_path' => 'API 管理后台',
    ],
    'dc_07_intranet' => [
        'narrative' => 'Web 漏洞 → 内网 → 域控 → 提权的完整链。模拟真实内网渗透，从 Web 入口一路打到域控。',
        'flag_path' => '内网域控',
    ],
    'dc_08_cve' => [
        'narrative' => '复现 ThinkPHP 5.0.23 远程代码执行漏洞（CVE-2018-20062）。真实漏洞复现，体验历史漏洞的危害。',
        'flag_path' => 'ThinkPHP 漏洞利用',
    ],
    'dc_09_ctf' => [
        'narrative' => 'CTF 风格的综合夺旗挑战：逆向 + Web + 密码学 + 隐写。多步骤组合，需要持续思考。',
        'flag_path' => 'CTF 综合',
    ],
    'dc_10_ultimate' => [
        'narrative' => '修真之巅，飞升在即。需综合运用所学全部修真绝技，击破所有护山大阵，方能飞升成仙。',
        'flag_path' => '飞升台',
    ],
];

// 修真叙事中需要替换的占位符
$narrative = [];
foreach ($content as $dirName => $info) {
    $narrative[$dirName] = $info['narrative'];
}

// 为大乘期10关生成详细的 secure.php
$secureTemplates = [
    'dc_01_cross' => <<<'PHP'
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
PHP,

    'dc_02_cross' => <<<'PHP'
<?php
/**
 * DC-02 secure.php - 轮回宗综合防御
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
PHP,

     'dc_03_pentest' => <<<'PHP'
<?php
/**
 * DC-03 secure.php - 电商系统综合防御
 */

echo '<h2>电商系统 · 综合防御</h2>';

echo '<h3>🛒 电商核心安全要点</h3>';

echo '<h4>1. 用户认证</h4>';
echo '<ul>';
echo '<li>Argon2id 密码哈希</li>';
echo '<li>多因素认证（短信/邮箱/TOTP）</li>';
echo '<li>登录失败锁定（5次/15分钟）</li>';
echo '<li>会话固定防护（regenerate_id）</li>';
echo '<li>设备指纹 + 异常登录检测</li>';
echo '</ul>';

echo '<h4>2. 支付安全</h4>';
echo '<ul>';
echo '<li>服务端重新计算价格（不信任客户端）</li>';
echo '<li>幂等性设计（防重复支付）</li>';
echo '<li>订单状态机（防状态篡改）</li>';
echo '<li>并发锁（防超卖）</li>';
echo '<li>支付回调签名验证（防伪造）</li>';
echo '</ul>';

echo '<h4>3. 个人信息保护</h4>';
echo '<ul>';
echo '<li>敏感信息加密存储（手机、身份证）</li>';
echo '<li>展示脱敏（138****8888）</li>';
echo '<li>日志脱敏</li>';
echo '<li>GDPR / 个保法合规</li>';
echo '</ul>';
PHP,

    'dc_04_logic' => <<<'PHP'
<?php
/**
 * DC-04 secure.php - 社交平台逻辑防御
 */

echo '<h2>社交平台 · 业务逻辑安全</h2>';

echo '<h3>👥 关注关系安全</h3>';
echo '<ul>';
echo '<li>关注/取消关注使用 POST + CSRF Token</li>';
echo '<li>关注关系检查（黑名单、拉黑）</li>';
echo '<li>关注数量限制（防爬虫）</li>';
echo '</ul>';

echo '<h3>💬 私信安全</h3>';
echo '<ul>';
echo '<li>非好友私信限制</li>';
echo '<li>敏感词过滤</li>';
echo '<li>举报机制</li>';
echo '<li>内容审核（人工 + AI）</li>';
echo '</ul>';

echo '<h3>📊 状态机安全</h3>';
echo '<ul>';
echo '<li>订单状态严格流转（待支付→已支付→已发货→已收货→已评价）</li>';
echo '<li>每个状态变更需要权限校验</li>';
echo '<li>状态变更日志</li>';
echo '</ul>';
PHP,

    'dc_05_cms' => <<<'PHP'
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
PHP,

    'dc_06_api' => <<<'PHP'
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
PHP,

    'dc_07_intranet' => <<<'PHP'
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
PHP,

    'dc_08_cve' => <<<'PHP'
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
PHP,

    'dc_09_ctf' => <<<'PHP'
<?php
/**
 * DC-09 secure.php - CTF 综合防御
 */

echo '<h2>CTF 综合 · 修真靶场视角</h2>';

echo '<h3>🎯 CTF 技巧</h3>';
echo '<ul>';
echo '<li>Web：源码分析、参数构造、协议利用</li>';
echo '<li>密码学：模数攻击、Padding Oracle、Hash 扩展</li>';
echo '<li>逆向：反编译、动态调试、符号执行</li>';
echo '<li>MISC：隐写、流量分析、编码识别</li>';
echo '</ul>';

echo '<h3>🛡️ CTF 思维转化到实际防御</h3>';
echo '<ol>';
echo '<li>每个 CTF 技巧都对应一个真实漏洞</li>';
echo '<li>理解漏洞原理比会利用更重要</li>';
echo '<li>从攻击者视角看问题，防御才能全面</li>';
echo '<li>不断学习新攻击技术，更新防御策略</li>';
echo '</ol>';

echo '<h3>📚 推荐 CTF 平台</h3>';
echo '<ul>';
echo '<li>Hack The Box</li>';
echo '<li>TryHackMe</li>';
echo '<li>CTFtime（赛事）</li>';
echo '<li>攻防世界（国内）</li>';
echo '<li>BugKu</li>';
echo '</ul>';
PHP,

    'dc_10_ultimate' => <<<'PHP'
<?php
/**
 * DC-10 secure.php - 飞升终极防御
 *
 * 修真之巅，飞升在即。
 * 这是修真靶场的终极考验，需要综合运用所有修真绝技。
 */

echo '<h1>🏆 修真之巅 · 飞升大乘 · 终极防御</h1>';

echo '<h2>📜 修真靶场核心防御心法</h2>';

echo '<h3>心法一：先明后行</h3>';
echo '<p>任何防御都需要先理解攻击原理。修真靶场的 100 关是修真弟子的历练之路，';
echo '每一关都揭示了一种攻击技术的核心原理。只有深刻理解，方能举一反三。</p>';

echo '<h3>心法二：多层防御</h3>';
echo '<p>修真讲究阵法，防御也讲纵深。从边界、网络、主机、应用、数据、监控、响应七个层面构建防御体系，';
echo '任何单层被攻破，其他层仍能阻止攻击者。</p>';

echo '<h3>心法三：最小权限</h3>';
echo '<p>修真弟子各司其职，权限分明。应用也应遵循最小权限原则，';
echo '数据库账户无 FILE、网络服务账户无 root、API 用户只授予必需 scope。</p>';

echo '<h3>心法四：失效安全</h3>';
echo '<p>修真之路凶险，防御也需假设失效。';
echo '即使某个组件被攻破，系统应应应应应应进入安全状态（默认拒绝），而非不安全状态。</p>';

echo '<h3>心法五：审计与可观测</h3>';
echo '<p>修真需要明镜照心，防御需要日志审计。所有敏感操作都应记录，';
echo '所有异常都应实时告警，方能在攻击发生的第一时间响应。</p>';

echo '<h3>心法六：持续学习</h3>';
echo '<p>修真无止境，安全亦无止境。';
echo '新攻击技术不断涌现（CVE、AI攻击、供应链），防御策略也需持续更新。</p>';

echo '<h2>📚 修真靶场推荐学习路径</h2>';
echo '<ol>';
echo '<li><strong>炼气期</strong>：建立安全意识（信息泄露、弱口令）</li>';
echo '<li><strong>筑基期</strong>：掌握基础漏洞（XSS、CSRF、SQL注入）</li>';
echo '<li><strong>金丹期</strong>：理解过滤与绕过</li>';
echo '<li><strong>元婴期</strong>：高阶漏洞（XXE、SSRF、反序列化）</li>';
echo '<li><strong>化神期</strong>：现代 Web 安全（JWT、OAuth、CORS）</li>';
echo '<li><strong>炼虚期</strong>：综合 RCE 与 GetShell</li>';
echo '<li><strong>合体期</strong>：剧情综合挑战</li>';
echo '<li><strong>大乘期</strong>：终极渗透 + 真实 CVE 复现</li>';
echo '</ol>';

echo '<h2>🧘 飞升寄语</h2>';
echo '<blockquote class="text-warning">';
echo '"道高一尺，魔高一丈。网络安全没有银弹，只有持续学习与谨慎实践。<br>';
echo '愿道友早日飞升大乘，以攻促防，守护修真界之安宁。"';
echo '</blockquote>';

echo '<p class="text-center text-muted">— 修真网络安全靶场 · XiuXian Range v1.0.0 —</p>';
PHP,
];

foreach ($secureTemplates as $dirName => $code) {
    $dirs = glob("$challengesDir/*/$dirName");
    if (empty($dirs)) {
        echo "⚠️  目录不存在: $dirName\n";
        continue;
    }
    $dir = $dirs[0];
    file_put_contents("$dir/secure.php", $code);
    echo "✅ 已更新 secure.php: $dirName\n";
}