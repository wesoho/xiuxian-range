<?php
/**
 * 修真靶场 - 元婴/化神/炼虚/合体/大乘期修真叙事增强
 *
 * 为每个已有修真关卡补充修真叙事
 */

declare(strict_types=1);

$root = dirname(__DIR__) . '/public/challenges';

// 元婴期
$yy = [
    'lh_yy_01_xss_dom' => ['title' => '【轮回宗·元婴】DOM幻象', 'sect' => 'lunhuizong', 'category' => 'XSS DOM型', 'fix' => '使用textContent而非innerHTML；CSP禁止内联脚本'],
    'lh_yy_02_xss_cookie' => ['title' => '【轮回宗·元婴】盗取灵识', 'sect' => 'lunhuizong', 'category' => 'XSS Cookie窃取', 'fix' => 'HttpOnly Cookie；CSP；SameSite'],
    'wm_yy_03_xxe_file' => ['title' => '【万魔宗·元婴】魔影重重', 'sect' => 'wanmozong', 'category' => 'XXE文件读取', 'fix' => 'libxml_disable_entity_loader；使用JSON替代XML'],
    'wm_yy_04_xxe_ssrf' => ['title' => '【万魔宗·元婴】内网探秘', 'sect' => 'wanmozong', 'category' => 'XXE内网探测', 'fix' => '禁用DTD；使用白名单URL'],
    'qy_yy_05_ssrf_basic' => ['title' => '【青云宗·元婴】元神出窍', 'sect' => 'qingong', 'category' => 'SSRF基础', 'fix' => '白名单URL；禁用危险协议；解析IP检查'],
    'qy_yy_06_ssrf_proto' => ['title' => '【青云宗·元婴】法器协议', 'sect' => 'qingong', 'category' => 'SSRF协议利用', 'fix' => '禁用gopher/dict等协议；只允许http(s)'],
    'lh_yy_07_ssrf_rebind' => ['title' => '【轮回宗·元婴】轮回转世', 'sect' => 'lunhuizong', 'category' => 'SSRF DNS rebinding', 'fix' => '解析后再次检查IP；TTL=0防护'],
    'lh_yy_08_deser_wakeup' => ['title' => '【轮回宗·元婴】反向召唤', 'sect' => 'lunhuizong', 'category' => '反序列化__wakeup', 'fix' => '使用JSON；allowed_classes白名单'],
    'wm_yy_09_deser_pop' => ['title' => '【万魔宗·元婴】魔器链', 'sect' => 'wanmozong', 'category' => '反序列化POP链', 'fix' => '禁用unserialize；HMAC签名'],
    'wm_yy_10_idor_h' => ['title' => '【万魔宗·元婴】借物偷看', 'sect' => 'wanmozong', 'category' => '水平越权IDOR', 'fix' => '资源级RBAC；user_id+resource_id双校验'],
    'qy_yy_11_idor_v' => ['title' => '【青云宗·元婴】跨界探访', 'sect' => 'qingong', 'category' => '垂直越权', 'fix' => '严格角色校验；最小权限'],
    'qy_yy_12_payment' => ['title' => '【青云宗·元婴】灵石篡改', 'sect' => 'qingong', 'category' => '支付漏洞金额篡改', 'fix' => '服务端价格计算；签名验证；幂等性'],
    'lh_yy_13_captcha' => ['title' => '【轮回宗·元婴】轮回符复用', 'sect' => 'lunhuizong', 'category' => '验证码重用', 'fix' => '验证码一次性 + 过期时间'],
    'lh_yy_14_pwd_reset' => ['title' => '【轮回宗·元婴】强行改命', 'sect' => 'lunhuizong', 'category' => '任意密码重置', 'fix' => '邮件一次性Token；身份二次验证'],
    'wm_yy_15_brute' => ['title' => '【万魔宗·元婴】魔锤试炼', 'sect' => 'wanmozong', 'category' => '暴力破解无锁定', 'fix' => '失败锁定；验证码；速率限制'],
];

// 化神期
$hs = [
    'wm_hs_01_jwt_none' => ['title' => '【万魔宗·化神】无相法印', 'sect' => 'wanmozong', 'category' => 'JWT alg=none', 'fix' => '强制HS256/RS256；使用firebase/php-jwt'],
    'wm_hs_02_jwt_weak' => ['title' => '【万魔宗·化神】密钥爆破', 'sect' => 'wanmozong', 'category' => 'JWT弱密钥', 'fix' => '使用32字节强随机密钥；定期轮转'],
    'qy_hs_03_jwt_kid' => ['title' => '【青云宗·化神】kid 注入', 'sect' => 'qingong', 'category' => 'JWT kid注入', 'fix' => 'kid白名单；使用UUID'],
    'qy_hs_04_oauth' => ['title' => '【青云宗·化神】夺舍重生', 'sect' => 'qingong', 'category' => 'OAuth劫持', 'fix' => 'redirect_uri精确白名单；state参数'],
    'lh_hs_05_cors' => ['title' => '【轮回宗·化神】跨界之门', 'sect' => 'lunhuizong', 'category' => 'CORS配置错误', 'fix' => '精确Origin白名单；禁用通配符+Credentials'],
    'lh_hs_06_csrf_token' => ['title' => '【轮回宗·化神】轮回令牌', 'sect' => 'lunhuizong', 'category' => 'CSRF Token绑定缺陷', 'fix' => 'Token与Session严格绑定；一次性使用'],
    'wm_hs_07_smuggle' => ['title' => '【万魔宗·化神】魔影分流', 'sect' => 'wanmozong', 'category' => 'HTTP请求走私', 'fix' => '使用HTTP/2；统一CL/TE解析'],
    'wm_hs_08_cache' => ['title' => '【万魔宗·化神】缓存幻影', 'sect' => 'wanmozong', 'category' => 'Web缓存欺骗', 'fix' => 'Vary头包含用户身份；Cache-Control: private'],
    'qy_hs_09_crypto_ecb' => ['title' => '【青云宗·化神】古典加密', 'sect' => 'qingong', 'category' => 'AES-ECB块重排', 'fix' => '使用AES-GCM/CBC；带IV；带认证'],
    'qy_hs_10_crypto_hash' => ['title' => '【青云宗·化神】哈希延展', 'sect' => 'qingong', 'category' => 'Hash长度扩展', 'fix' => '使用HMAC；或SHA-3'],
    'lh_hs_11_deser_phar' => ['title' => '【轮回宗·化神】phar 反噬', 'sect' => 'lunhuizong', 'category' => 'Phar反序列化', 'fix' => '禁用phar://包装器；严格文件上传校验'],
    'lh_hs_12_deser_sess' => ['title' => '【轮回宗·化神】session 反转', 'sect' => 'lunhuizong', 'category' => 'Session反序列化', 'fix' => '统一session序列化处理器；session.strict_mode=1'],
    'wm_hs_13_php_type' => ['title' => '【万魔宗·化神】弱类型幻象', 'sect' => 'wanmozong', 'category' => 'PHP弱类型比较', 'fix' => '使用===严格比较；password_verify'],
    'wm_hs_14_php_var' => ['title' => '【万魔宗·化神】变量覆盖', 'sect' => 'wanmozong', 'category' => 'PHP变量覆盖', 'fix' => '禁用extract()；显式赋值'],
    'qy_hs_15_in_array' => ['title' => '【青云宗·化神】in_array 陷阱', 'sect' => 'qingong', 'category' => 'in_array弱比较', 'fix' => '第三个参数传true严格模式'],
];

// 炼虚期
$lx = [
    'qy_lx_01_lfi_log' => ['title' => '【青云宗·炼虚】日志成魔', 'sect' => 'qingong', 'category' => 'LFI日志投毒', 'fix' => 'LFI白名单；日志访问控制；过滤UA注入'],
    'lh_lx_02_lfi_sess' => ['title' => '【轮回宗·炼虚】轮回之袋', 'sect' => 'lunhuizong', 'category' => 'Session文件包含', 'fix' => 'session存储路径不可web访问'],
    'wm_lx_03_sqli_shell' => ['title' => '【万魔宗·炼虚】SQL通幽', 'sect' => 'wanmozong', 'category' => 'SQL注入GetShell', 'fix' => '数据库账户无FILE权限；secure_file_priv=null'],
    'qy_lx_04_upload_hta' => ['title' => '【青云宗·炼虚】.htaccess 诡道', 'sect' => 'qingong', 'category' => '上传.htaccess', 'fix' => '禁止上传.htaccess；AllowOverride None'],
    'lh_lx_05_upload_ntfs' => ['title' => '【轮回宗·炼虚】NTFS 流', 'sect' => 'lunhuizong', 'category' => 'NTFS流绕过', 'fix' => '文件名过滤NTFS流字符 :::$DATA'],
    'wm_lx_06_strcmp' => ['title' => '【万魔宗·炼虚】strcmp 陷阱', 'sect' => 'wanmozong', 'category' => 'strcmp数组绕过', 'fix' => '使用hash_equals或password_verify'],
    'qy_lx_07_sqli_multi' => ['title' => '【青云宗·炼虚】mysqli 多语句', 'sect' => 'qingong', 'category' => 'mysqli多语句', 'fix' => '使用单语句PDO prepare'],
    'lh_lx_08_docker' => ['title' => '【轮回宗·炼虚】Docker 越界', 'sect' => 'lunhuizong', 'category' => '容器逃逸', 'fix' => '非特权模式；seccomp；AppArmor'],
    'wm_lx_09_php_cgi' => ['title' => '【万魔宗·炼虚】CGI 漏洞', 'sect' => 'wanmozong', 'category' => 'PHP-CGI漏洞', 'fix' => '升级PHP 8.x；使用PHP-FPM'],
    'qy_lx_10_cache' => ['title' => '【青云宗·炼虚】缓存成魔', 'sect' => 'qingong', 'category' => '高级缓存投毒', 'fix' => '严格Cache-Control; Vary'],
];

// 合体期
$ht = [
    'lh_ht_01_xss_full' => ['title' => '【轮回宗·合体】试炼塔·XSS篇', 'sect' => 'lunhuizong', 'category' => 'XSS综合', 'fix' => 'CSP + 输出转义 + 严格Cookie'],
    'wm_ht_02_deser_full' => ['title' => '【万魔宗·合体】魔窟·反序列化篇', 'sect' => 'wanmozong', 'category' => '反序列化综合', 'fix' => 'JSON替代；HMAC；严格类型'],
    'qy_ht_03_sqli_full' => ['title' => '【青云宗·合体】藏经阁·SQL篇', 'sect' => 'qingong', 'category' => 'SQL注入综合', 'fix' => '参数化+权限最小化+审计'],
    'lh_ht_04_auth_full' => ['title' => '【轮回宗·合体】轮回殿·认证篇', 'sect' => 'lunhuizong', 'category' => '认证漏洞综合', 'fix' => 'MFA + 强密码 + 会话固定防护'],
    'wm_ht_05_ssrf_full' => ['title' => '【万魔宗·合体】炼魂殿·SSRF篇', 'sect' => 'wanmozong', 'category' => 'SSRF综合', 'fix' => '白名单+IP检查+协议过滤'],
    'qy_ht_06_csrf_full' => ['title' => '【青云宗·合体】阵法台·CSRF篇', 'sect' => 'qingong', 'category' => 'CSRF综合', 'fix' => 'Token+CORS+SameSite+Referer'],
    'lh_ht_07_crypto_full' => ['title' => '【轮回宗·合体】幽冥界·密码学篇', 'sect' => 'lunhuizong', 'category' => '密码学综合', 'fix' => 'GCM+HMAC+强随机'],
    'wm_ht_08_rce_full' => ['title' => '【万魔宗·合体】血池·RCE篇', 'sect' => 'wanmozong', 'category' => 'RCE综合', 'fix' => '白名单+escapeshellarg+disable_functions'],
    'qy_ht_09_code_review' => ['title' => '【青云宗·合体】禁地·代码审计篇', 'sect' => 'qingong', 'category' => '代码审计综合', 'fix' => 'SAST/DAST/SCA全流程'],
    'lh_ht_10_logic_full' => ['title' => '【轮回宗·合体】万魔殿·业务逻辑篇', 'sect' => 'lunhuizong', 'category' => '业务逻辑综合', 'fix' => '服务端权威+状态机+并发锁'],
];

// 大乘期
$dacheng = [
    'dc_01_cross' => ['title' => '【大乘】跨宗渗透·青云→万魔', 'sect' => 'wanderer', 'category' => '跨宗门综合', 'fix' => '深度防御+零信任+SIEM'],
    'dc_02_cross' => ['title' => '【大乘】跨宗渗透·万魔→轮回', 'sect' => 'wanderer', 'category' => '跨宗门综合', 'fix' => '微分段+最小权限+检测响应'],
    'dc_03_pentest' => ['title' => '【大乘】电商系统完整渗透', 'sect' => 'wanderer', 'category' => '电商综合', 'fix' => 'WAF+审计+威胁情报'],
    'dc_04_logic' => ['title' => '【大乘】社交平台逻辑漏洞', 'sect' => 'wanderer', 'category' => '逻辑综合', 'fix' => '权限模型+状态机'],
    'dc_05_cms' => ['title' => '【大乘】CMS 代码审计挑战', 'sect' => 'wanderer', 'category' => '审计综合', 'fix' => 'SAST+代码评审+依赖管理'],
    'dc_06_api' => ['title' => '【大乘】API 安全全链路', 'sect' => 'wanderer', 'category' => 'API安全', 'fix' => 'OAuth2+scope限+速率限制'],
    'dc_07_intranet' => ['title' => '【大乘】内网穿透完整链', 'sect' => 'wanderer', 'category' => '内网渗透', 'fix' => '微分段+EDR+零信任'],
    'dc_08_cve' => ['title' => '【大乘】真实 CVE 复现·ThinkPHP5 RCE', 'sect' => 'wanderer', 'category' => 'CVE复现', 'fix' => '及时打补丁+WAF'],
    'dc_09_ctf' => ['title' => '【大乘】CTF 夺旗综合题', 'sect' => 'wanderer', 'category' => 'CTF综合', 'fix' => '综合防御'],
    'dc_10_ultimate' => ['title' => '【大乘·飞升】终极挑战', 'sect' => 'wanderer', 'category' => '终极飞升', 'fix' => '修真无止境，飞升需持续学习'],
];

$all = array_merge($yy, $hs, $lx, $ht, $dacheng);

foreach ($all as $dirName => $info) {
    $dir = "$root/{$info['sect']}/{$dirName}";
    if (!is_dir($dir)) continue;

    $titleEsc = addslashes($info['title']);
    $narrativeEsc = addslashes($info['narrative'] ?? '修真靶场剧情');
    $category = $info['category'];
    $fix = $info['fix'];

    $parts = explode('_', $dirName);
    $id = strtoupper(implode('-', array_slice($parts, 0, 3)));

    $learnContent = <<<PHP
<?php
/**
 * {$id} {$titleEsc}
 * 修真叙事
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{$titleEsc} · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 {$titleEsc}</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> {$narrativeEsc}
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞类型</h5>
            <p class="text-muted">{$category}</p>
        </div>
        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <p>{$fix}</p>
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/{$id}" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>
PHP;
    file_put_contents("$dir/learn.php", $learnContent);
}

echo "✅ 修真叙事增强完成（" . count($all) . " 个关卡）\n";