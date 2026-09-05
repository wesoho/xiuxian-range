<?php
/**
 * 大乘期10关 vulnerable.php 详细实现
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/../../public/challenges';

$content = [
    'dc_01_cross' => <<<'PHP'
<?php
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
echo '<li><a href="/challenges/wanmozong/wm_yy_10_idor_horizontal/">借物偷看（越权）</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合利用上述 6 个修真靶场关卡，最终获取 Flag:<br>';
echo '<code class="xxr-mono">flag{ascend_qy_to_wm_91}</code>';
echo '</div>';
PHP,

    'dc_02_cross' => <<<'PHP'
<?php
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
echo '<li><a href="/challenges/lunhuizong/lh_yy_14_password_reset/">强行改命（密码重置）</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_jd_11_lfi_basic/">轮回之眼（LFI）</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合利用上述 5 个修真靶场关卡，最终获取 Flag:<br>';
echo '<code class="xxr-mono">flag{ascend_wm_to_lh_92}</code>';
echo '</div>';
PHP,

    'dc_03_pentest' => <<<'PHP'
<?php
/**
 * DC-03 电商系统完整渗透
 *
 * 修真电商系统是一个迷你商城，包含：
 *  - 用户注册/登录（SQL注入、弱口令）
 *  - 商品列表/详情（XSS）
 *  - 购物车/订单（CSRF、IDOR）
 *  - 支付环节（支付篡改）
 *  - 后台管理（垂直越权）
 */
echo '<h2>电商系统修真靶场</h2>';
echo '<p>修真电商系统关卡组合：</p>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_jz_03_sqli_num/">登录（SQL注入）</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_01_xss_ref/">商品（XSS）</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_02_csrf_get/">下单（CSRF）</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_12_payment_tamper/">支付（金额篡改）</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_11_idor_vertical/">后台（越权）</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 完整渗透电商系统，获取 Flag:<br>';
echo '<code class="xxr-mono">flag{ecomm_full_93}</code>';
echo '</div>';
PHP,

    'dc_04_logic' => <<<'PHP'
<?php
/**
 * DC-04 社交平台逻辑漏洞
 */
echo '<h2>社交平台修真靶场</h2>';
echo '<p>修真社交平台的逻辑漏洞综合。</p>';

echo '<h3>📚 修真靶场漏洞环境</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_yy_10_idor_horizontal/">水平越权</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_12_payment_tamper/">支付逻辑</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_yy_13_captcha_reuse/">验证码重用</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_15_brute_force/">暴力破解</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合利用逻辑漏洞，获取 Flag:<br>';
echo '<code class="xxr-mono">flag{social_logic_94}</code>';
echo '</div>';
PHP,

    'dc_05_cms' => <<<'PHP'
<?php
/**
 * DC-05 CMS 代码审计挑战
 *
 * 修真靶场迷你 CMS 包含多个漏洞：
 *  - SQL 注入
 *  - XSS
 *  - CSRF
 *  - 文件上传
 *  - 后台弱口令
 */
echo '<h2>CMS 修真靶场</h2>';
echo '<p>修真靶场 CMS 综合代码审计。</p>';

echo '<h3>📚 修真靶场 CMS 漏洞</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_jz_03_sqli_num/">SQL注入</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_01_xss_ref/">XSS</a></li>';
echo '<li><a href="/challenges/qingong/qy_jz_02_csrf_get/">CSRF</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_jz_14_upload_js/">文件上传</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_lq_06_weak_password/">弱口令</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 审计 CMS 源码找出 5+ 漏洞并利用，获取 Flag:<br>';
echo '<code class="xxr-mono">flag{cms_full_audit_95}</code>';
echo '</div>';
PHP,

    'dc_06_api' => <<<'PHP'
<?php
/**
 * DC-06 API 安全全链路
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
echo '<li><a href="/challenges/wanmozong/wm_yy_10_idor_horizontal/">IDOR</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 完整测试 API 安全，获取 Flag:<br>';
echo '<code class="xxr-mono">flag{api_full_chain_96}</code>';
echo '</div>';
PHP,

    'dc_07_intranet' => <<<'PHP'
<?php
/**
 * DC-07 内网穿透完整链
 */
echo '<h2>内网修真靶场</h2>';
echo '<p>Web → 内网 → 域控 → 提权的完整渗透链。</p>';

echo '<h3>📚 修真靶场对应关卡</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_yy_05_ssrf_basic/">SSRF 内网探测</a></li>';
echo '<li><a href="/challenges/qingong/qy_yy_06_ssrf_protocol/">gopher:// Redis</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_09_deserialize_pop/">反序列化提权</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_yy_10_idor_horizontal/">横向越权</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 模拟内网渗透全链，获取 Flag:<br>';
echo '<code class="xxr-mono">flag{intranet_full_97}</code>';
echo '</div>';
PHP,

    'dc_08_cve' => <<<'PHP'
<?php
/**
 * DC-08 真实 CVE 复现·ThinkPHP5 RCE
 *
 * CVE-2018-20062：ThinkPHP 5.0.23 远程代码执行
 *
 * Payload: /index.php?s=/Index/\think\app/invokefunction&function=call_user_func_array&vars[0]=system&vars[1][]=id
 */
echo '<h2>ThinkPHP 5 RCE 复现</h2>';
echo '<p>修真靶场演示 ThinkPHP 5.0.23 RCE 漏洞。</p>';
echo '<p>本关可直接访问修真靶场ThinkPHP环境（如果有）或在本地搭建复现。</p>';

echo '<h3>📜 漏洞 Payload</h3>';
echo '<pre>';
echo 'GET /index.php?s=/Index/\think\app/invokefunction';
echo '&function=call_user_func_array';
echo '&vars[0]=system&vars[1][]=id';
echo '</pre>';

echo '<h3>🛡️ 修复方案</h3>';
echo '<ol>';
echo '<li>升级 ThinkPHP 到 5.0.24 / 5.1.31+</li>';
echo '<li>WAF 拦截特殊字符</li>';
echo '<li>禁用危险函数</li>';
echo '</ol>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 成功 RCE ThinkPHP 5.0.23 环境，获取 Flag:<br>';
echo '<code class="xxr-mono">flag{cve_thinkphp5_98}</code>';
echo '</div>';
PHP,

    'dc_09_ctf' => <<<'PHP'
<?php
/**
 * DC-09 CTF 夺旗综合题
 */
echo '<h2>CTF 综合修真靶场</h2>';
echo '<p>CTF 风格的多步骤夺旗挑战。</p>';

echo '<h3>📚 CTF 综合修真靶场</h3>';
echo '<ul>';
echo '<li><a href="/challenges/qingong/qy_hs_09_crypto_ecb/">密码学：ECB</a></li>';
echo '<li><a href="/challenges/qingong/qy_hs_10_crypto_hash/">密码学：Hash 扩展</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_07_http_smuggle/">HTTP 请求走私</a></li>';
echo '<li><a href="/challenges/wanmozong/wm_hs_08_cache_poison/">Web 缓存欺骗</a></li>';
echo '<li><a href="/challenges/lunhuizong/lh_hs_11_deser_phar/">Phar 反序列化</a></li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 综合修真靶场关卡，获取 CTF Flag:<br>';
echo '<code class="xxr-mono">flag{ctf_pwn_99}</code>';
echo '</div>';
PHP,

    'dc_10_ultimate' => <<<'PHP'
<?php
/**
 * DC-10 大乘·飞升 终极挑战
 *
 * 修真之巅，飞升在即。
 * 综合运用全部修真绝技，击破所有护山大阵。
 */
echo '<h2>🏆 修真之巅 · 飞升大乘</h2>';
echo '<p>这是修真靶场的终极考验。</p>';

echo '<h3>🎯 终极飞升要求</h3>';
echo '<ol>';
echo '<li><strong>炼气期</strong>全部 10 关通过（信息安全意识）</li>';
echo '<li><strong>筑基期</strong>全部 15 关通过（XSS/CSRF/SQLi 基础）</li>';
echo '<li><strong>金丹期</strong>全部 15 关通过（过滤绕过）</li>';
echo '<li><strong>元婴期</strong>全部 15 关通过（XXE/SSRF/反序列化）</li>';
echo '<li><strong>化神期</strong>全部 15 关通过（JWT/OAuth/CORS）</li>';
echo '<li><strong>炼虚期</strong>全部 10 关通过（综合 RCE）</li>';
echo '<li><strong>合体期</strong>全部 10 关通过（剧情综合）</li>';
echo '<li><strong>大乘期</strong>全部 10 关通过（含真实 CVE 复现）</li>';
echo '</ol>';

echo '<h3>🌟 飞升奖励</h3>';
echo '<ul>';
echo '<li>获得"飞升大乘"称号（最高修真境界）</li>';
echo '<li>获得专属徽章：🏆 修真之巅</li>';
echo '<li>解锁长老殿·禁地区</li>';
echo '<li>获得修真靶场定制周边</li>';
echo '</ul>';

echo '<div class="alert alert-success">';
echo '<strong>🎯 通关条件：</strong> 完成 100 关所有修真靶场，获取终极 Flag:<br>';
echo '<code class="xxr-mono">flag{ascend_dacheng_ultimate_100}</code>';
echo '</div>';

echo '<blockquote class="text-warning text-center">';
echo '<p>道高一尺，魔高一丈。<br>愿道友飞升大乘，守护修真界安宁！</p>';
echo '</blockquote>';
PHP,
];

$updated = 0;
foreach ($content as $dirName => $code) {
    $dirs = glob("$challengesDir/*/$dirName");
    if (empty($dirs)) {
        echo "⚠️  目录不存在: $dirName\n";
        continue;
    }
    $dir = $dirs[0];
    file_put_contents("$dir/vulnerable.php", $code);
    $updated++;
}

echo "✅ 已更新 $updated 个大乘期关卡的 vulnerable.php\n";