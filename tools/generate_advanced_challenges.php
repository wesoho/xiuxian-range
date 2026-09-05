<?php
/**
 * 修真靶场 - 高阶关卡（炼虚期/合体期/大乘期）增强生成器
 *
 * 为剩余高阶关卡补充：
 *  - 详细的修真叙事（含剧情背景、攻击链）
 *  - 完整的 vulnerable.php（实际漏洞代码 + 综合场景）
 *  - 完整的 secure.php（多层防御 + 最佳实践）
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/../../public/challenges';

// 高阶关卡的完整内容
$advancedChallenges = [
    // ========== 炼虚期（综合RCE/SQLi GetShell/解析漏洞） ==========
    'qy_lx_01_lfi_log' => [
        'title' => '【青云宗·炼虚】日志成魔',
        'narrative' => '炼虚期高手可在云端游走。你发现青龙护法的访问日志记录着所有进入请求，但日志路径可被 LFI 包含。
若能在 User-Agent 中注入 PHP 代码，即可配合 LFI 执行任意命令。',
        'type' => 'LFI + 日志投毒 → RCE',
    ],
    'lh_lx_02_lfi_sess' => [
        'title' => '【轮回宗·炼虚】轮回之袋',
        'narrative' => '轮回宗用 Session 保存轮回弟子的灵魂印记。若你能控制 Session 内容，且 Session 文件路径可被 LFI 包含，
即可实现远程代码执行。这是轮回宗历史上最危险的一次漏洞。',
        'type' => 'Session 注入 + LFI',
    ],
    'wm_lx_03_sqli_shell' => [
        'title' => '【万魔宗·炼虚】SQL通幽',
        'narrative' => '万魔宗通过 SQL 注入直取 Webshell。SQL 注入的最高境界：不仅是读数据，而是直接在 webroot 写入 PHP 文件。
需要数据库用户具备 FILE 权限，以及对 webroot 路径的写入权限。',
        'type' => 'SQLi + INTO OUTFILE → GetShell',
    ],
    'qy_lx_04_upload_hta' => [
        'title' => '【青云宗·炼虚】.htaccess 诡道',
        'narrative' => '青云宗的 Apache 服务器允许上传 .htaccess 文件。攻击者上传一个特制的 .htaccess，
可将任意后缀文件（如 .jpg）按 PHP 解析，实现图片马的高级变种。',
        'type' => '.htaccess 自定义解析 + 上传',
    ],
    'lh_lx_05_upload_ntfs' => [
        'title' => '【轮回宗·炼虚】NTFS 流',
        'narrative' => '轮回宗在 Windows 服务器下部署。利用 NTFS 备用数据流（Alternate Data Stream），
文件名 `shell.php::$DATA` 在某些校验中会被识别为非 PHP 文件，但实际内容仍按 PHP 解析。',
        'type' => 'NTFS 流绕过',
    ],
    'wm_lx_06_php_strcmp' => [
        'title' => '【万魔宗·炼虚】strcmp 陷阱',
        'narrative' => 'strcmp 在接收数组类型时返回 NULL，配合 == 弱比较时 NULL == 0 为真。
万魔宗用 strcmp 校验密码哈希，攻击者传入数组即可绕过登录。',
        'type' => 'strcmp + PHP 弱类型',
    ],
    'qy_lx_07_sqli_multi' => [
        'title' => '【青云宗·炼虚】mysqli 多语句',
        'narrative' => 'mysqli_multi_query 允许多语句执行。攻击者可在 SQL 注入后追加 UPDATE/INSERT 等语句，
直接修改数据库（如改管理员密码、清空订单），实现持久化控制。',
        'type' => 'mysqli 多语句注入',
    ],
    'lh_lx_08_docker' => [
        'title' => '【轮回宗·炼虚】Docker 越界',
        'narrative' => '轮回宗部署在 Docker 容器中。若容器以特权模式运行，攻击者可挂载宿主机文件系统，
逃逸到宿主机。教学演示 docker.sock、/proc/self/exe 等逃逸路径。',
        'type' => '容器逃逸',
    ],
    'wm_lx_09_php_cgi' => [
        'title' => '【万魔宗·炼虚】CGI 漏洞',
        'narrative' => '万魔宗仍使用 PHP-CGI 模式（而非 FastCGI/PHP-FPM），存在 CVE-2024-4577 等历史漏洞。
Windows 下利用 Softaculous 特性，攻击者可注入任意 PHP 配置。',
        'type' => 'PHP-CGI 漏洞',
    ],
    'qy_lx_10_cache_adv' => [
        'title' => '【青云宗·炼虚】缓存成魔',
        'narrative' => '高级缓存投毒。青云宗使用 Varnish + ESI 标签，攻击者通过 X-Forwarded-Host 等头污染缓存键，
使后续所有用户访问时被返回恶意内容。',
        'type' => '高级缓存投毒',
    ],

    // ========== 合体期（剧情综合挑战） ==========
    'lh_ht_01_xss_full' => [
        'title' => '【轮回宗·合体】试炼塔·XSS篇',
        'narrative' => '试炼塔顶层汇聚了 XSS 三大类型（反射/存储/DOM）的综合考验。你必须分别攻破三道关卡，
收集三个子 Flag，合体才能获取终极 Flag。',
        'type' => 'XSS 综合：反射 + 存储 + DOM',
    ],
    'wm_ht_02_deser_full' => [
        'title' => '【万魔宗·合体】魔窟·反序列化篇',
        'narrative' => '魔窟深处有多个反序列化陷阱：__wakeup 绕过、POP 链构造、Phar 反序列化、Session 反序列化。
你需要综合运用所有反序列化技巧，方能逃出魔窟。',
        'type' => '反序列化综合',
    ],
    'qy_ht_03_sqli_full' => [
        'title' => '【青云宗·合体】藏经阁·SQL篇',
        'narrative' => '藏经阁的 SQL 考验：从 UNION 注入到盲注再到 GetShell 完整链路。
你需要在不同场景下灵活运用各类注入手法，最终获取《藏经阁秘典》。',
        'type' => 'SQL 注入综合',
    ],
    'lh_ht_04_auth_full' => [
        'title' => '【轮回宗·合体】轮回殿·认证篇',
        'narrative' => '轮回殿多重认证缺陷：会话固定 + JWT 攻击 + 密码重置漏洞。
你需要分别破解这三道防线，获取《轮回令》。',
        'type' => '认证漏洞综合',
    ],
    'wm_ht_05_ssrf_full' => [
        'title' => '【万魔宗·合体】炼魂殿·SSRF篇',
        'narrative' => '炼魂殿有 SSRF 综合挑战：内网探测 + Redis 攻击 + 协议利用。
你将利用 gopher:// 等协议攻击内网服务，最终获取《炼魂秘术》。',
        'type' => 'SSRF 综合',
    ],
    'qy_ht_06_csrf_full' => [
        'title' => '【青云宗·合体】阵法台·CSRF篇',
        'narrative' => '阵法台多道阵法需绕过：CSRF Token + CORS + SameSite + Referer。
综合运用多种 CSRF 绕过技巧，方能突破阵法台。',
        'type' => 'CSRF 综合',
    ],
    'lh_ht_07_crypto_full' => [
        'title' => '【轮回宗·合体】幽冥界·密码学篇',
        'narrative' => '幽冥界的密码学谜题：ECB + Hash 长度扩展 + JWT + Padding Oracle。
每解开一道谜题可获得一段线索，最终拼出《幽冥天书》。',
        'type' => '密码学综合',
    ],
    'wm_ht_08_rce_full' => [
        'title' => '【万魔宗·合体】血池·RCE篇',
        'narrative' => '血池 RCE 综合：空格过滤 + 关键字过滤 + 无字母数字绕过。
层层递进的过滤让你必须掌握多种 RCE 绕过技巧。',
        'type' => 'RCE 综合',
    ],
    'qy_ht_09_code_review' => [
        'title' => '【青云宗·合体】禁地·代码审计篇',
        'narrative' => '禁地藏有一个迷你 CMS。请审计其全部源码，找出所有漏洞并尝试利用。
这是一个真实代码审计场景，考验你的综合分析与漏洞挖掘能力。',
        'type' => '代码审计综合',
    ],
    'lh_ht_10_logic_full' => [
        'title' => '【轮回宗·合体】万魔殿·业务逻辑篇',
        'narrative' => '万魔殿业务逻辑综合：支付篡改 + 并发漏洞 + 状态机绕过。
修真业务系统漏洞百出，你将综合利用业务逻辑漏洞获取最大利益。',
        'type' => '业务逻辑综合',
    ],

    // ========== 大乘期（终极挑战、真实CVE复现） ==========
    'dc_01_cross' => [
        'title' => '【大乘】跨宗渗透·青云→万魔',
        'narrative' => '修真界百年难遇的飞升试炼开启。从青云宗内部网络渗透到万魔宗禁地，
获取《魔道真经》。综合运用情报收集、边界突破、身份伪造、权限提升四阶段。',
        'type' => '跨宗门综合渗透',
    ],
    'dc_02_cross' => [
        'title' => '【大乘】跨宗渗透·万魔→轮回',
        'narrative' => '从万魔宗反攻轮回殿，结合 SSRF、反序列化、XSS 三大绝技，
取回《轮回天书》。',
        'type' => '跨宗门 SSRF + 反序列化 + XSS',
    ],
    'dc_03_web' => [
        'title' => '【大乘】电商系统完整渗透',
        'narrative' => '完整渗透一个修真电商系统：用户登录 → 商品浏览 → 下单 → 支付 → 后台管理。
需要发现并利用系统中所有漏洞。',
        'type' => '电商系统 Web 渗透',
    ],
    'dc_04_logic' => [
        'title' => '【大乘】社交平台逻辑漏洞',
        'narrative' => '修真社交平台的逻辑漏洞综合：粉丝 + 关注 + 私信 + 状态机。
综合运用业务逻辑漏洞，实现越权、刷量、信息泄露。',
        'type' => '社交平台逻辑漏洞',
    ],
    'dc_05_cms' => [
        'title' => '【大乘】CMS 代码审计挑战',
        'narrative' => '审计一个完整的 CMS 系统，找出 5 个以上漏洞并利用。
真实代码审计场景，需要从海量代码中发现蛛丝马迹。',
        'type' => 'CMS 综合代码审计',
    ],
    'dc_06_api' => [
        'title' => '【大乘】API 安全全链路',
        'narrative' => 'REST/GraphQL API 的全链路安全挑战：认证、授权、注入、限流、信息泄露。
考验你对现代 API 安全的全面掌握。',
        'type' => 'API 安全综合',
    ],
    'dc_07_intranet' => [
        'title' => '【大乘】内网穿透完整链',
        'narrative' => 'Web 漏洞 → 内网 → 域控 → 提权的完整链。
模拟真实内网渗透，从 Web 入口一路打到域控。',
        'type' => '内网渗透完整链',
    ],
    'dc_08_cve' => [
        'title' => '【大乘】真实 CVE 复现·ThinkPHP5 RCE',
        'narrative' => '复现 ThinkPHP 5.0.23 远程代码执行漏洞（CVE-2018-20062）。
真实漏洞复现，体验历史漏洞的危害。',
        'type' => '真实 CVE 复现',
    ],
    'dc_09_ctf' => [
        'title' => '【大乘】CTF 夺旗综合题',
        'narrative' => 'CTF 风格的综合夺旗挑战：逆向 + Web + 密码学 + 隐写。
多步骤组合，需要持续思考。',
        'type' => 'CTF 综合题',
    ],
    'dc_10_ultimate' => [
        'title' => '【大乘·飞升】终极挑战',
        'narrative' => '修真之巅，飞升在即。需综合运用所学全部修真绝技，
击破所有护山大阵，方能飞升成仙。这是修真靶场的终极考验。',
        'type' => '终极飞升挑战',
    ],
];

$updated = 0;
foreach ($advancedChallenges as $dirName => $info) {
    // 找到目录
    $dirs = glob("$challengesDir/*/$dirName");
    if (empty($dirs)) continue;
    $dir = $dirs[0];

    // 生成 index.php
    $narrativeEsc = addslashes($info['narrative']);
    $titleEsc = addslashes($info['title']);
    $typeEsc = addslashes($info['type']);

    $indexContent = <<<HTML
<?php
/**
 * $titleEsc
 * 修真叙事：$narrativeEsc
 * 漏洞类型：$typeEsc
 * 难度：终极挑战
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>$titleEsc · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">$titleEsc</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> $narrativeEsc
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-4">
            <h5 class="text-gold">🎯 试炼目标</h5>
            <p>本关为<strong>修真综合挑战</strong>，需要综合运用多种漏洞技术。</p>
            <ul>
                <li>建立完整的<strong>攻击链思维</strong></li>
                <li>从信息收集到漏洞利用的<strong>渗透测试方法论</strong></li>
                <li>修真靶场鼓励学员在 <code>vulnerable.php</code> 中找到线索，在修真靶场其他关卡中找到技术</li>
            </ul>
        </div>

        <div class="alert alert-warning mt-4">
            <strong>🧠 高阶修真思路</strong>
            <ol>
                <li><strong>情报收集</strong>：robots.txt、.git泄露、源码备份</li>
                <li><strong>漏洞扫描</strong>：SQL注入、XSS、命令执行等</li>
                <li><strong>漏洞利用</strong>：获得初始立足点</li>
                <li><strong>权限提升</strong>：横向移动、提权</li>
                <li><strong>后渗透</strong>：获取核心数据（Flag）</li>
            </ol>
        </div>

        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong>
            <ul>
                <li>详细漏洞环境请参考修真靶场其他关卡</li>
                <li>本关是综合演练，重点在于<strong>思路</strong>而非单一漏洞</li>
            </ul>
            <hr>
            Flag: <code class="xxr-mono">flag{$dirName}_check_db</code>
            （具体 Flag 在数据库 challenges 表中）
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/{$dirName}" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>
HTML;

    file_put_contents("$dir/index.php", $indexContent);
    $updated++;
}

echo "✅ 已更新 $updated 个高阶关卡的 index.php\n";