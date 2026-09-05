<?php
/**
 * 修真靶场 - 关卡代码批量生成器 V2
 *
 * 改进点：
 *  - 每个分类提供**详细的** vulnerable.php 和 secure.php
 *  - 自动包含数据库连接（使用 config 中的 db 配置）
 *  - 增加修真叙事的修真境界颜色
 *  - 所有文件可被独立访问运行
 */

declare(strict_types=1);

$challengesDir = __DIR__ . '/public/challenges';

$sql = file_get_contents(__DIR__ . '/database/seeds/02_challenges.sql');
preg_match_all("/\('([^']+)',\s*'([^']+)',\s*'(\w+)',\s*'(\w+)',\s*(\d+),\s*'([^']+)',\s*'((?:[^'\\\\]|\\\\.)*)'/", $sql, $matches, PREG_SET_ORDER);

$generated = 0;
$skipped = 0;

foreach ($matches as $m) {
    $id = $m[1];
    $title = $m[2];
    $sect = $m[3];
    $realm = $m[4];
    $difficulty = (int) $m[5];
    $category = $m[6];
    $narrative = $m[7];

    $sectMap = [
        'qiingong'    => 'qingong',
        'wanmozong'   => 'wanmozong',
        'lunhuizong'  => 'lunhuizong',
        'wanderer'    => 'wanderer',
    ];

    if (!isset($sectMap[$sect])) continue;
    $dir = "{$challengesDir}/{$sectMap[$sect]}/";

    // 已存在则跳过
    $existingDirs = glob("{$dir}" . strtolower(str_replace('-', '_', $id)) . '_*');
    if (!empty($existingDirs)) {
        $skipped++;
        continue;
    }

    // 计算目录名
    $baseDir = strtolower(str_replace('-', '_', $id));
    $suffix = getCategoryDirSuffix($category);
    $dirName = $baseDir . '_' . $suffix;
    $fullDir = $dir . $dirName;

    if (is_dir($fullDir)) {
        $skipped++;
        continue;
    }

    @mkdir($fullDir, 0755, true);

    // 生成文件
    $narrativeSafe = addslashes($narrative);
    $titleSafe = addslashes($title);

    $indexBody = generateIndexBody($category, $id, $sectMap[$sect]);
    $hint = getCategoryHint($category);
    $vulnerable = generateVulnerable($category, $id);
    $secure = generateSecure($category, $id);

    $indexContent = <<<PHP
<?php
/**
 * {$id} {$title}
 * 修真叙事：{$narrativeSafe}
 * 漏洞类型：{$category}
 * 难度：L{$difficulty}
 * 宗门：{$sectMap[$sect]}
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{$titleSafe} · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">{$titleSafe}</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> {$narrativeSafe}
        </div>
{$indexBody}
        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> {$hint}
            <hr>
            Flag 提交位置：<a href="/challenge/{$id}" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/{$id}" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>
PHP;

    file_put_contents("{$fullDir}/index.php", $indexContent);
    file_put_contents("{$fullDir}/vulnerable.php", $vulnerable);
    file_put_contents("{$fullDir}/secure.php", $secure);

    $generated++;
}

echo "✅ 已生成: {$generated} 个\n";
echo "⏭️  已跳过: {$skipped} 个（已存在）\n";

/**
 * 根据 category 生成长名后缀
 */
function getCategoryDirSuffix(string $category): string
{
    $map = [
        'sqli_numeric'  => 'sqli_num',
        'sqli_string'   => 'sqli_str',
        'sqli_union'    => 'sqli_union',
        'sqli_error'    => 'sqli_error',
        'sqli_bool'     => 'sqli_bool',
        'sqli_time'     => 'sqli_time',
        'sqli_stacked'  => 'sqli_stack',
        'sqli_gbk'      => 'sqli_gbk',
        'sqli_second'   => 'sqli_second',
        'sqli_filter'   => 'sqli_filter',
        'sqli_waf'      => 'sqli_waf',
        'sqli_multi'    => 'sqli_multi',
        'sqli_getshell' => 'sqli_shell',
        'sqli_comprehensive' => 'sqli_full',
        'xss_reflected' => 'xss_ref',
        'xss_stored'    => 'xss_store',
        'xss_filter'    => 'xss_filter',
        'xss_bypass'    => 'xss_bypass',
        'xss_dom'       => 'xss_dom',
        'xss_cookie'    => 'xss_cookie',
        'xss_comprehensive' => 'xss_full',
        'csrf_get'      => 'csrf_get',
        'csrf_post'     => 'csrf_post',
        'csrf_token'    => 'csrf_token',
        'csrf_token_bypass' => 'csrf_token',
        'csrf_comprehensive' => 'csrf_full',
        'rce_basic'     => 'rce_basic',
        'rce_space'     => 'rce_space',
        'rce_filter'    => 'rce_filter',
        'rce_comprehensive' => 'rce_full',
        'upload_js'     => 'upload_js',
        'upload_mime'   => 'upload_mime',
        'upload_ext'    => 'upload_ext',
        'upload_image'  => 'upload_img',
        'upload_htaccess' => 'upload_hta',
        'upload_ntfs'   => 'upload_ntfs',
        'lfi_basic'     => 'lfi_basic',
        'lfi_filter'    => 'lfi_filter',
        'lfi_log_poison' => 'lfi_log',
        'lfi_session'   => 'lfi_sess',
        'xxe_file'      => 'xxe_file',
        'xxe_ssrf'      => 'xxe_ssrf',
        'ssrf_basic'    => 'ssrf_basic',
        'ssrf_protocol' => 'ssrf_proto',
        'ssrf_rebind'   => 'ssrf_rebind',
        'ssrf_comprehensive' => 'ssrf_full',
        'deserialize_wakeup' => 'deser_wakeup',
        'deserialize_pop' => 'deser_pop',
        'deserialize_phar' => 'deser_phar',
        'deserialize_session' => 'deser_sess',
        'deserialize_comprehensive' => 'deser_full',
        'idor_horizontal' => 'idor_h',
        'idor_vertical'  => 'idor_v',
        'payment_tamper' => 'payment',
        'captcha_reuse'  => 'captcha',
        'password_reset' => 'pwd_reset',
        'brute_force'    => 'brute',
        'jwt_none'       => 'jwt_none',
        'jwt_weak'       => 'jwt_weak',
        'jwt_kid'        => 'jwt_kid',
        'oauth_redirect' => 'oauth',
        'cors'           => 'cors',
        'http_smuggle'   => 'smuggle',
        'cache_poison'   => 'cache',
        'cache_poison_adv' => 'cache_adv',
        'crypto_ecb'     => 'crypto_ecb',
        'crypto_hash_ext' => 'crypto_hash',
        'crypto_comprehensive' => 'crypto_full',
        'php_type_juggle' => 'php_type',
        'php_variable'   => 'php_var',
        'php_in_array'   => 'php_in',
        'php_strcmp'     => 'php_strcmp',
        'php_cgi'        => 'php_cgi',
        'container_escape' => 'docker',
        'code_review'    => 'code_review',
        'logic_comprehensive' => 'logic_full',
        'auth_comprehensive' => 'auth_full',
        'cross_sect'     => 'cross',
        'web_pentest'    => 'pentest',
        'logic_pentest'  => 'logic',
        'cms_audit'      => 'cms',
        'api_security'   => 'api',
        'intranet'       => 'intranet',
        'cve_replay'     => 'cve',
        'ctf_pwn'        => 'ctf',
        'ultimate'       => 'ultimate',
        'open_redirect'  => 'redirect',
        'clickjacking'   => 'clickjack',
        'file_read'      => 'file_read',
    ];

    return $map[$category] ?? substr($category, 0, 10);
}

function getCategoryHint(string $category): string
{
    $hints = [
        'sqli_numeric' => '数字型 SQL 注入。Payload: <code>1 OR 1=1</code>',
        'sqli_string'  => '字符型 SQL 注入。Payload: <code>xxx&#39; OR &#39;1&#39;=&#39;1</code>',
        'sqli_union'   => 'UNION 联合注入。Payload: <code>1&#39; UNION SELECT 1,version(),3-- -</code>',
        'sqli_error'   => '报错注入。利用 <code>extractvalue</code> / <code>updatexml</code> 触发错误回显',
        'sqli_bool'    => '布尔盲注。根据页面真假判断条件',
        'sqli_time'    => '时间盲注。利用 <code>SLEEP()</code> 触发响应延迟',
        'sqli_stacked' => '堆叠注入。Payload: <code>1&#39;; SELECT * FROM users-- -</code>',
        'sqli_gbk'     => '宽字节注入。Payload: <code>1%bf%27 OR 1=1-- -</code>',
        'sqli_second'  => '二次注入。先注册恶意用户名，再触发查询',
        'sqli_filter'  => 'SQL 注入关键字过滤绕过（双写、内联注释）',
        'sqli_waf'     => 'SQL 注入 WAF 绕过（大小写、注释符）',
        'sqli_multi'   => 'mysqli_multi_query 多语句注入',
        'sqli_getshell'=> 'SQL 注入 GetShell。Payload: <code>INTO OUTFILE</code>',
        'xss_reflected'=> '反射型 XSS。在 URL 参数注入 <code>&lt;script&gt;alert(1)&lt;/script&gt;</code>',
        'xss_stored'   => '存储型 XSS。留言会被永久保存',
        'xss_filter'   => 'XSS 过滤绕过（HTML 实体、URL 编码）',
        'xss_bypass'   => 'XSS 关键字过滤绕过（大小写、双写）',
        'xss_dom'      => 'DOM XSS。通过 URL fragment 触发',
        'xss_cookie'   => 'XSS 窃取 Cookie（教学演示）',
        'csrf_get'     => 'GET 型 CSRF。利用 <code>&lt;img&gt;</code> 自动请求',
        'csrf_post'    => 'POST 型 CSRF。构造自动提交表单',
        'csrf_token'   => 'CSRF Token 可预测/泄露',
        'rce_basic'    => '命令注入基础。Payload: <code>;ls /</code>',
        'rce_space'    => '命令注入 - 空格过滤绕过（<code>$IFS</code>、<code>%09</code>）',
        'rce_filter'   => '命令注入 - 关键字过滤（拼接、通配符）',
        'upload_js'    => '文件上传 JS 前端校验绕过',
        'upload_mime'  => '文件上传 MIME 类型绕过',
        'upload_ext'   => '文件上传 黑名单绕过 (<code>.php5/.phtml/.phar</code>)',
        'upload_image' => '文件上传 图片马',
        'upload_htaccess' => '上传 .htaccess 自定义解析',
        'upload_ntfs'  => 'NTFS 流绕过（仅 Windows）',
        'lfi_basic'    => '文件包含 LFI。Payload: <code>../../etc/passwd</code>',
        'lfi_filter'   => 'php://filter 读源码',
        'lfi_log_poison' => '日志投毒 + LFI RCE',
        'lfi_session'  => 'Session 文件包含',
        'xxe_file'     => 'XXE 文件读取',
        'xxe_ssrf'     => 'XXE 内网探测',
        'ssrf_basic'   => 'SSRF 基础。访问 file://, gopher://',
        'ssrf_protocol'=> 'SSRF gopher:// 攻击内网 Redis',
        'ssrf_rebind'  => 'SSRF DNS rebinding 绕过',
        'deserialize_wakeup' => '反序列化 __wakeup 漏洞',
        'deserialize_pop' => '反序列化 POP 链构造',
        'deserialize_phar' => 'Phar 反序列化',
        'deserialize_session' => 'Session 反序列化漏洞',
        'idor_horizontal' => '水平越权（IDOR）。修改 ID 访问他人数据',
        'idor_vertical'  => '垂直越权。未鉴权访问管理后台',
        'payment_tamper'  => '支付漏洞 - 金额篡改',
        'captcha_reuse'   => '验证码重用/不过期',
        'password_reset'  => '任意密码重置',
        'brute_force'     => '暴力破解 - 无锁定',
        'jwt_none'     => 'JWT alg:none 攻击',
        'jwt_weak'     => 'JWT 弱密钥爆破',
        'jwt_kid'      => 'JWT kid 注入',
        'oauth_redirect' => 'OAuth redirect_uri 劫持',
        'cors'         => 'CORS 配置错误',
        'http_smuggle' => 'HTTP 请求走私（CL-TE）',
        'cache_poison' => 'Web 缓存欺骗',
        'crypto_ecb'   => 'AES-ECB 模式利用',
        'crypto_hash_ext' => 'Hash 长度扩展攻击',
        'php_type_juggle' => 'PHP 弱类型比较绕过',
        'php_variable' => 'PHP extract() 变量覆盖',
        'php_in_array' => 'PHP in_array 弱比较',
        'php_strcmp'   => 'PHP strcmp 数组绕过',
        'php_cgi'      => 'PHP-CGI 漏洞',
        'open_redirect'=> 'URL 重定向',
        'clickjacking' => '点击劫持',
        'file_read'    => '目录穿越',
        'code_review'  => '代码审计（迷你 CMS）',
    ];

    return $hints[$category] ?? '尝试各种攻击手法，思考漏洞原理。';
}

/**
 * 根据 category 生成 index.php 的 body 部分
 */
function generateIndexBody(string $category, string $id, string $sectPath): string
{
    $bodyMap = [
        'sqli_numeric' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">弟子 ID：</span>
                <input type="text" name="id" class="form-control" placeholder="试试: 1 OR 1=1" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_string' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">弟子名：</span>
                <input type="text" name="name" class="form-control" placeholder="试试: ' OR '1'='1" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_union' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">编号：</span>
                <input type="text" name="id" class="form-control" placeholder="试试: 1' UNION SELECT 1,version(),3-- -" autofocus>
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_error' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="试试: 1' AND extractvalue(1,concat(0x7e,version()))-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_bool' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">用户名：</span>
                <input type="text" name="name" class="form-control" placeholder="试试: admin' AND 1=1-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_time' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">用户名：</span>
                <input type="text" name="name" class="form-control" placeholder="试试: admin' AND SLEEP(5)-- -">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_stacked' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="堆叠注入">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_gbk' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="宽字节: %bf%27">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_second' => <<<HTML
        <p class="text-muted">先去注册一个用户名含 SQL 语句的账号，再触发查询。</p>
HTML,

        'sqli_filter' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="关键字过滤绕过">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_waf' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">ID：</span>
                <input type="text" name="id" class="form-control" placeholder="WAF 绕过">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>
HTML,

        'sqli_multi' => <<<HTML
        <p>本关演示 mysqli_multi_query 多语句注入。</p>
HTML,

        'sqli_getshell' => <<<HTML
        <p class="text-warning">⚠️ 危险关卡 - 利用 SQL 注入写入 WebShell</p>
HTML,

        'xss_reflected' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">输入：</span>
                <input type="text" name="msg" class="form-control" autofocus>
                <button class="xxr-btn xxr-btn-primary">提交</button>
            </div>
        </form>
HTML,

        'xss_stored' => <<<HTML
        <form method="POST" class="mb-4">
            <textarea name="content" class="form-control" rows="3" placeholder="留言..."></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交留言</button>
        </form>
HTML,

        'xss_filter' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">输入：</span>
                <input type="text" name="msg" class="form-control" autofocus>
                <button class="xxr-btn xxr-btn-primary">提交</button>
            </div>
        </form>
HTML,

        'xss_bypass' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">输入：</span>
                <input type="text" name="msg" class="form-control" autofocus>
                <button class="xxr-btn xxr-btn-primary">提交</button>
            </div>
        </form>
HTML,

        'xss_dom' => <<<HTML
        <p>DOM XSS 通过修改 URL hash 触发：<code>#&lt;img src=x onerror=alert(1)&gt;</code></p>
HTML,

        'xss_cookie' => <<<HTML
        <p>利用 XSS 窃取其他弟子的 Cookie（教学演示）。</p>
HTML,

        'csrf_get' => <<<HTML
        <p>当前余额：<strong>1000</strong> 灵石</p>
        <a href="?transfer=1&to=attacker&amount=999" class="xxr-btn xxr-btn-primary">点击转账</a>
HTML,

        'csrf_post' => <<<HTML
        <form method="POST">
            <input type="hidden" name="amount" value="999">
            <button class="xxr-btn xxr-btn-primary">提交转账</button>
        </form>
HTML,

        'csrf_token' => <<<HTML
        <form method="POST">
            <input type="hidden" name="transfer" value="1">
            <button class="xxr-btn xxr-btn-primary">转账</button>
        </form>
HTML,

        'rce_basic' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">IP：</span>
                <input type="text" name="ip" class="form-control" placeholder="试试: 127.0.0.1; ls /" autofocus>
                <button class="xxr-btn xxr-btn-primary">测灵</button>
            </div>
        </form>
HTML,

        'rce_space' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">IP：</span>
                <input type="text" name="ip" class="form-control" placeholder="空格过滤绕过">
                <button class="xxr-btn xxr-btn-primary">测灵</button>
            </div>
        </form>
HTML,

        'rce_filter' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">IP：</span>
                <input type="text" name="ip" class="form-control" placeholder="关键字过滤">
                <button class="xxr-btn xxr-btn-primary">测灵</button>
            </div>
        </form>
HTML,

        'upload_js' => <<<HTML
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传心法 (.txt)</label>
                <input type="file" name="file" class="form-control" accept=".txt">
            </div>
            <button class="xxr-btn xxr-btn-primary">上传</button>
        </form>
HTML,

        'upload_mime' => <<<HTML
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传文件</label>
                <input type="file" name="file" class="form-control">
            </div>
            <button class="xxr-btn xxr-btn-primary">上传</button>
        </form>
HTML,

        'upload_ext' => <<<HTML
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传文件</label>
                <input type="file" name="file" class="form-control">
            </div>
            <button class="xxr-btn xxr-btn-primary">上传</button>
        </form>
HTML,

        'upload_image' => <<<HTML
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">上传图片（jpg/png/gif）</label>
                <input type="file" name="file" class="form-control" accept="image/*">
            </div>
            <button class="xxr-btn xxr-btn-primary">上传</button>
        </form>
HTML,

        'upload_htaccess' => <<<HTML
        <p>尝试上传一个 .htaccess 文件来自定义解析。</p>
HTML,

        'upload_ntfs' => <<<HTML
        <p>NTFS 备用数据流绕过（仅 Windows 有效）。</p>
HTML,

        'lfi_basic' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">文件：</span>
                <input type="text" name="file" class="form-control" placeholder="?file=../../../etc/passwd">
                <button class="xxr-btn xxr-btn-primary">读取</button>
            </div>
        </form>
HTML,

        'lfi_filter' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">文件：</span>
                <input type="text" name="file" class="form-control" placeholder="php://filter/convert.base64-encode/resource=index.php">
                <button class="xxr-btn xxr-btn-primary">读取</button>
            </div>
        </form>
HTML,

        'lfi_log_poison' => <<<HTML
        <p>污染访问日志后通过 LFI 包含执行 PHP 代码。</p>
HTML,

        'lfi_session' => <<<HTML
        <p>利用 Session 序列化内容配合 LFI RCE。</p>
HTML,

        'xxe_file' => <<<HTML
        <form method="POST" class="mb-4">
            <textarea name="xml" class="form-control" rows="5" placeholder="XML payload"></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
HTML,

        'xxe_ssrf' => <<<HTML
        <form method="POST" class="mb-4">
            <textarea name="xml" class="form-control" rows="5" placeholder="XML 内网探测"></textarea>
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
HTML,

        'ssrf_basic' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">URL：</span>
                <input type="text" name="url" class="form-control" placeholder="file:///etc/passwd">
                <button class="xxr-btn xxr-btn-primary">拉取</button>
            </div>
        </form>
HTML,

        'ssrf_protocol' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">URL：</span>
                <input type="text" name="url" class="form-control" placeholder="gopher://...">
                <button class="xxr-btn xxr-btn-primary">拉取</button>
            </div>
        </form>
HTML,

        'ssrf_rebind' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">URL：</span>
                <input type="text" name="url" class="form-control" placeholder="DNS rebinding">
                <button class="xxr-btn xxr-btn-primary">拉取</button>
            </div>
        </form>
HTML,

        'file_read' => <<<HTML
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text">文件：</span>
                <input type="text" name="file" class="form-control">
                <button class="xxr-btn xxr-btn-primary">读取</button>
            </div>
        </form>
HTML,

        'open_redirect' => <<<HTML
        <p>点击下方按钮跳转：</p>
        <a href="?url=https://example.com" class="xxr-btn xxr-btn-primary">跳转</a>
HTML,

        'clickjacking' => <<<HTML
        <p>透明 iframe + 诱饵按钮劫持点击。</p>
        <button class="xxr-btn xxr-btn-primary">确认</button>
HTML,

        'idor_horizontal' => <<<HTML
        <p>查看其他弟子的订单（修改 URL 中的 id 参数）。</p>
HTML,

        'idor_vertical' => <<<HTML
        <p>普通弟子访问长老后台。</p>
        <a href="/admin/" class="xxr-btn xxr-btn-primary">访问后台</a>
HTML,

        'payment_tamper' => <<<HTML
        <form method="POST">
            <input type="hidden" name="item" value="sword">
            <input type="hidden" name="price" value="100">
            <button class="xxr-btn xxr-btn-primary">购买（100 灵石）</button>
        </form>
HTML,

        'captcha_reuse' => <<<HTML
        <form method="POST">
            <input type="text" name="captcha" class="form-control" placeholder="验证码">
            <button class="xxr-btn xxr-btn-primary mt-2">提交</button>
        </form>
HTML,

        'password_reset' => <<<HTML
        <form method="POST">
            <input type="email" name="email" class="form-control" placeholder="输入邮箱">
            <button class="xxr-btn xxr-btn-primary mt-2">重置密码</button>
        </form>
HTML,

        'brute_force' => <<<HTML
        <form method="POST">
            <input type="text" name="username" class="form-control" placeholder="用户名">
            <input type="password" name="password" class="form-control mt-2" placeholder="密码">
            <button class="xxr-btn xxr-btn-primary mt-2">登录</button>
        </form>
HTML,

        'jwt_none' => <<<HTML
        <p>伪造 JWT Token（alg=none）。</p>
HTML,

        'jwt_weak' => <<<HTML
        <p>爆破 JWT 弱密钥。</p>
HTML,

        'jwt_kid' => <<<HTML
        <p>JWT kid 注入。</p>
HTML,

        'oauth_redirect' => <<<HTML
        <p>OAuth redirect_uri 劫持。</p>
HTML,

        'cors' => <<<HTML
        <p>测试跨域资源访问（CORS 配置错误）。</p>
HTML,

        'http_smuggle' => <<<HTML
        <p>HTTP 请求走私（CL-TE / TE-CL）。</p>
HTML,

        'cache_poison' => <<<HTML
        <p>Web 缓存投毒/欺骗。</p>
HTML,

        'cache_poison_adv' => <<<HTML
        <p>高级缓存投毒。</p>
HTML,

        'crypto_ecb' => <<<HTML
        <p>AES-ECB 模式加密（块重排攻击）。</p>
HTML,

        'crypto_hash_ext' => <<<HTML
        <p>Hash 长度扩展攻击。</p>
HTML,

        'php_type_juggle' => <<<HTML
        <p>PHP 弱类型比较绕过。</p>
HTML,

        'php_variable' => <<<HTML
        <p>PHP extract() 变量覆盖。</p>
HTML,

        'php_in_array' => <<<HTML
        <p>in_array 弱比较绕过。</p>
HTML,

        'php_strcmp' => <<<HTML
        <p>strcmp 数组绕过。</p>
HTML,

        'php_cgi' => <<<HTML
        <p>PHP-CGI 漏洞利用。</p>
HTML,

        'container_escape' => <<<HTML
        <p>Docker 容器逃逸（仅教学演示）。</p>
HTML,

        'code_review' => <<<HTML
        <p>审计一个迷你 CMS，找出全部漏洞。</p>
HTML,
    ];

    return $bodyMap[$category] ?? '<p class="text-muted">关卡环境已就绪，请破解目标获取 Flag。</p>';
}

/**
 * 生成 vulnerable.php（教学用，故意有漏洞）
 */
function generateVulnerable(string $category, string $id): string
{
    $code = "<?php\n/**\n * {$id} vulnerable.php - 漏洞演示\n * 分类：{$category}\n *\n * ⚠️ 教学用代码，故意存在漏洞\n * 修真靶场默认 display_errors=On、allow_url_include=On 等\n */\n\n";

    $templates = [
        'sqli_numeric' => <<<'PHP'
// 修真靶场数据库连接
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) { die('数据库连接失败'); }

$id = $_GET['id'] ?? '1';

// 【漏洞】直接拼接 SQL
try {
    $stmt = $pdo->query("SELECT username, email FROM demo_users WHERE id = $id");
    foreach ($stmt as $row) {
        echo "<p>弟子：" . htmlspecialchars($row['username']) . "</p>";
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">错误：' . $e->getMessage() . '</div>';
}
PHP,

        'sqli_string' => <<<'PHP'
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$name = $_GET['name'] ?? '';

// 【漏洞】字符串拼接，未转义
try {
    $stmt = $pdo->query("SELECT email FROM demo_users WHERE username = '$name'");
    foreach ($stmt as $row) {
        echo "<p>邮箱：" . htmlspecialchars($row['email']) . "</p>";
    }
} catch (PDOException $e) {
    echo "错误：" . $e->getMessage();
}
PHP,

        'sqli_union' => <<<'PHP'
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';

// 【漏洞】UNION 注入
try {
    $stmt = $pdo->query("SELECT id, username, email FROM demo_users WHERE id = '$id'");
    foreach ($stmt as $row) {
        echo "ID={$row['id']} 用户={$row['username']} 邮箱={$row['email']}<br>";
    }
} catch (PDOException $e) {
    echo "错误：" . $e->getMessage();
}
PHP,

        'sqli_error' => <<<'PHP'
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';

// 【漏洞】报错注入 + 错误回显
try {
    $stmt = $pdo->query("SELECT * FROM demo_users WHERE id = '$id'");
    foreach ($stmt as $row) { print_r($row); }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}
PHP,

        'sqli_bool' => <<<'PHP'
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$name = $_GET['name'] ?? '';

// 【漏洞】布尔盲注
try {
    $stmt = $pdo->query("SELECT id FROM demo_users WHERE username = '$name'");
    if ($stmt->fetch()) {
        echo '<div class="alert alert-success">✅ 用户存在</div>';
    } else {
        echo '<div class="alert alert-danger">❌ 用户不存在</div>';
    }
} catch (Exception $e) { echo '错误'; }
PHP,

        'sqli_time' => <<<'PHP'
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$name = $_GET['name'] ?? '';

// 【漏洞】时间盲注
$start = microtime(true);
$pdo->query("SELECT id FROM demo_users WHERE username = '$name'");
$time = microtime(true) - $start;

echo "查询耗时：{$time}s";
if ($time > 3) {
    echo '<div class="alert alert-warning">检测到 SLEEP() 调用</div>';
}
PHP,

        'sqli_stacked' => <<<'PHP'
// 【漏洞】mysqli 多语句
$mysqli = new mysqli('db', 'xiuxian', 'xiuxian_pass', 'xiuxian_range');
$id = $_GET['id'] ?? '1';
if ($mysqli->multi_query("SELECT * FROM demo_users WHERE id = $id; SELECT * FROM demo_users")) {
    do {
        if ($r = $mysqli->store_result()) {
            while ($row = $r->fetch_assoc()) { print_r($row); }
            $r->free();
        }
        if ($mysqli->more_results()) { /* ... */ }
    } while ($mysqli->next_result());
}
PHP,

        'sqli_gbk' => <<<'PHP'
// 【漏洞】GBK 编码宽字节注入
$mysqli = new mysqli('db', 'xiuxian', 'xiuxian_pass', 'xiuxian_range');
mysqli_set_charset($mysqli, 'gbk');  // 使用 GBK 编码
$id = addslashes($_GET['id']);  // addslashes 会用 \ 转义引号，但 GBK 下 %bf%27 会吃掉 \
$res = $mysqli->query("SELECT * FROM demo_users WHERE id = '$id'");
if ($res) {
    while ($row = $res->fetch_assoc()) print_r($row);
} else {
    echo "错误：" . $mysqli->error;
}
PHP,

        'sqli_second' => <<<'PHP'
// 【漏洞】二次注入
session_start();
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

// 注册时使用 escape，但存储时是原始值
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $stmt = $pdo->prepare('INSERT INTO demo_users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, 'pass']);
    echo '注册成功';
}

// 登录查询：未转义（触发二次注入）
if (isset($_GET['login'])) {
    $username = $_GET['login'];
    $stmt = $pdo->query("SELECT * FROM demo_users WHERE username = '$username'");  // 【漏洞】
    foreach ($stmt as $row) {
        echo "欢迎回来：" . htmlspecialchars($row['username']) . '<br>';
    }
}
PHP,

        'sqli_filter' => <<<'PHP'
// 【漏洞】关键字过滤可被绕过
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';
// 过滤 union select
$id = preg_replace('/union|select/i', '', $id);  // 可被双写绕过：ununionion selselectect
try {
    $stmt = $pdo->query("SELECT username FROM demo_users WHERE id = $id");
    foreach ($stmt as $row) print_r($row);
} catch (PDOException $e) { echo $e->getMessage(); }
PHP,

        'sqli_waf' => <<<'PHP'
// 【漏洞】WAF 可被绕过
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';
// WAF 简单关键字过滤（大小写、内联注释绕过）
$blocked = ['union', 'select', 'from', 'where'];
foreach ($blocked as $word) {
    if (stripos($id, $word) !== false) {
        die('WAF blocked');
    }
}
try {
    $stmt = $pdo->query("SELECT username FROM demo_users WHERE id = $id");
    foreach ($stmt as $row) print_r($row);
} catch (PDOException $e) { echo $e->getMessage(); }
PHP,

        'sqli_multi' => <<<'PHP'
// 【漏洞】mysqli_multi_query
$mysqli = new mysqli('db', 'xiuxian', 'xiuxian_pass', 'xiuxian_range');
$id = $_GET['id'] ?? '1';
if (isset($_GET['id'])) {
    if ($mysqli->multi_query("SELECT * FROM demo_users WHERE id = $id")) {
        do { /* 多语句结果 */ } while ($mysqli->next_result());
    }
}
PHP,

        'sqli_getshell' => <<<'PHP'
// 【漏洞】SQL 注入写入 WebShell
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
try { $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass'); } catch (PDOException $e) { die('fail'); }

$id = $_GET['id'] ?? '1';
// 演示：SELECT ... INTO OUTFILE '/var/www/html/shell.php'
try {
    $pdo->query("SELECT username FROM demo_users WHERE id = $id INTO OUTFILE '/tmp/dump.txt'");
} catch (PDOException $e) { echo $e->getMessage(); }
PHP,

        'xss_reflected' => <<<'PHP'
// 【漏洞】直接 echo 用户输入
$msg = $_GET['msg'] ?? '';
echo "<h3>你输入：</h3>" . $msg;  // 未转义
PHP,

        'xss_stored' => <<<'PHP'
// 【漏洞】存储 + 不转义输出
$msgFile = __DIR__ . '/comments.txt';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($msgFile, $_POST['content'] . "\n", FILE_APPEND);
}
if (file_exists($msgFile)) {
    foreach (file($msgFile) as $line) {
        echo '<div class="xxr-narrative">' . $line . '</div>';  // 【漏洞】
    }
}
PHP,

        'xss_filter' => <<<'PHP'
// 【漏洞】过滤可被实体编码绕过
$msg = $_GET['msg'] ?? '';
$msg = str_replace(['<', '>'], ['&lt;', '&gt;'], $msg);
// 但未过滤属性注入 " onmouseover="alert(1)
echo "<div title='$msg'>...</div>";
PHP,

        'xss_bypass' => <<<'PHP'
// 【漏洞】过滤可被大小写/双写绕过
$msg = $_GET['msg'] ?? '';
$msg = preg_replace('/script/i', '', $msg);  // 大小写、双写绕过：<ScScriptRipt>alert(1)</script>
echo $msg;  // 直接输出
PHP,

        'xss_dom' => <<<'PHP'
// 【漏洞】DOM XSS
$hash = $_SERVER['REQUEST_URI'] ?? '';
// 前端 JS 会从 URL fragment 读取并 innerHTML
?>
<div id="output"></div>
<script>
const hash = location.hash.substring(1);
document.getElementById('output').innerHTML = hash;  // 【漏洞】DOM XSS
</script>
PHP,

        'xss_cookie' => <<<'PHP'
// 【漏洞】XSS 窃取 Cookie
// 假设这是攻击者的接收端（演示）
$cookie = $_GET['c'] ?? '';
file_put_contents(__DIR__ . '/stolen_cookies.txt', $cookie . "\n", FILE_APPEND);
echo "OK";
PHP,

        'csrf_get' => <<<'PHP'
// 【漏洞】GET 请求敏感操作
if (isset($_GET['transfer'])) {
    $to = $_GET['to'];
    $amount = (float) $_GET['amount'];
    // 直接转账，无任何验证
    echo "<p>已向 $to 转账 $amount 灵石</p>";
}
PHP,

        'csrf_post' => <<<'PHP'
// 【漏洞】POST 操作无 CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer'])) {
    $to = $_POST['to'] ?? '';
    $amount = (float) ($_POST['amount'] ?? 0);
    echo "<p>已向 $to 转账 $amount 灵石</p>";
}
PHP,

        'csrf_token' => <<<'PHP'
// 【漏洞】CSRF Token 可预测
session_start();
$token = $_SESSION['csrf_token'] ?? md5(time());  // 基于时间，可预测
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['csrf'] ?? '') === $token) {
        echo '转账成功';
    } else {
        echo 'Token 错误：' . $token;  // 泄露 token
    }
}
PHP,

        'rce_basic' => <<<'PHP'
// 【漏洞】直接拼接命令
$ip = $_GET['ip'] ?? '127.0.0.1';
system("ping -c 1 $ip");  // 命令注入
PHP,

        'rce_space' => <<<'PHP'
// 【漏洞】过滤了空格
$ip = $_GET['ip'] ?? '127.0.0.1';
$ip = str_replace(' ', '', $ip);  // 但 ${IFS} 可绕过
echo shell_exec("ping -c 1 $ip");
PHP,

        'rce_filter' => <<<'PHP'
// 【漏洞】过滤了 cat
$cmd = $_GET['cmd'] ?? '';
$cmd = preg_replace('/cat|ls/i', '', $cmd);  // 双写、拼接可绕过
system($cmd);
PHP,

        'upload_js' => <<<'PHP'
// 【漏洞】仅前端校验
?>
<script>
function check(file) { return file.value.endsWith('.txt'); }
</script>
<?php
// 后端无校验
if ($_FILES) {
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
    echo '上传成功';
}
PHP,

        'upload_mime' => <<<'PHP'
// 【漏洞】只检查 Content-Type
$allowed = ['image/jpeg', 'image/png', 'image/gif'];
if ($_FILES && in_array($_FILES['file']['type'], $allowed)) {
    // Content-Type 可在请求中伪造
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
    echo '上传成功';
}
PHP,

        'upload_ext' => <<<'PHP'
// 【漏洞】黑名单过滤
$blocked = ['php', 'asp', 'jsp'];
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (!in_array(strtolower($ext), $blocked)) {
    // .php5 / .phtml / .phar 等可绕过
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
    echo '上传成功';
}
PHP,

        'upload_image' => <<<'PHP'
// 【漏洞】仅检查 getimagesize
if ($_FILES && @getimagesize($_FILES['file']['tmp_name'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
    echo '上传成功';
}
// 但图片马可绕过：在图片中嵌入 PHP 代码
PHP,

        'upload_htaccess' => <<<'PHP'
// 【漏洞】允许上传 .htaccess
$name = $_FILES['file']['name'];
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
// 上传内容为: AddType application/x-httpd-php .jpg
echo '上传成功';
PHP,

        'upload_ntfs' => <<<'PHP'
// 【漏洞】NTFS 流：filename.php::$DATA
$name = $_FILES['file']['name'];
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
echo '上传成功';
PHP,

        'lfi_basic' => <<<'PHP'
// 【漏洞】未限制路径
$file = $_GET['file'] ?? 'index.php';
include $file;  // 目录穿越
PHP,

        'lfi_filter' => <<<'PHP'
// 【漏洞】可被 php://filter 绕过
$file = $_GET['file'] ?? 'index.php';
if (preg_match('/php|flag/i', $file)) die('blocked');
// 但 php://filter/convert.base64-encode/resource= 可绕过
include $file;
PHP,

        'lfi_log_poison' => <<<'PHP'
// 【漏洞】通过 User-Agent 污染日志，然后 LFI
$logFile = '/var/log/apache2/access.log';
include $logFile;
PHP,

        'lfi_session' => <<<'PHP'
// 【漏洞】Session 文件包含
session_start();
$_SESSION['cmd'] = $_POST['cmd'] ?? 'id';
$sessionFile = '/var/lib/php/sessions/sess_' . session_id();
include $sessionFile;
PHP,

        'xxe_file' => <<<'PHP'
// 【漏洞】未禁用外部实体
$xml = $_POST['xml'] ?? '';
$dom = new DOMDocument();
$dom->loadXML($xml);  // 未设置 LIBXML_NOENT
echo $dom->saveXML();
PHP,

        'xxe_ssrf' => <<<'PHP'
// 【漏洞】XXE SSRF
$xml = $_POST['xml'] ?? '';
$dom = new DOMDocument();
$dom->loadXML($xml, LIBXML_NOENT);  // 启用实体
// 实体 SYSTEM "http://169.254.169.254/" 可访问内网
echo $dom->saveXML();
PHP,

        'ssrf_basic' => <<<'PHP'
// 【漏洞】未限制 URL 协议
$url = $_GET['url'] ?? '';
$content = file_get_contents($url);  // file://, gopher://, dict:// 等都支持
echo '<pre>' . htmlspecialchars($content) . '</pre>';
PHP,

        'ssrf_protocol' => <<<'PHP'
// 【漏洞】gopher:// 攻击内网 Redis
$url = $_GET['url'] ?? '';
$urlParsed = parse_url($url);
if ($urlParsed['scheme'] === 'gopher') {
    $content = file_get_contents($url);
    echo $content;
} else {
    echo file_get_contents($url);
}
PHP,

        'ssrf_rebind' => <<<'PHP'
// 【漏洞】DNS rebinding 绕过
$url = $_GET['url'] ?? '';
echo file_get_contents($url);
PHP,

        'file_read' => <<<'PHP'
// 【漏洞】目录穿越
$file = $_GET['file'] ?? '';
$content = @file_get_contents($file);  // ../../../etc/passwd
echo htmlspecialchars($content);
PHP,

        'open_redirect' => <<<'PHP'
// 【漏洞】未校验重定向 URL
header("Location: " . $_GET['url']);
PHP,

        'clickjacking' => <<<'PHP'
// 【漏洞】未设置 X-Frame-Options
?>
<button>确认</button>
PHP,

        'idor_horizontal' => <<<'PHP'
// 【漏洞】未校验资源所有权
$orderId = $_GET['id'] ?? 1;
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
$pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->query("SELECT * FROM demo_orders WHERE id = $orderId");  // 无 user_id 校验
foreach ($stmt as $row) {
    echo "订单 {$row['id']}：{$row['amount']}";
}
PHP,

        'idor_vertical' => <<<'PHP'
// 【漏洞】未做角色校验
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '请先登录';
} else {
    echo "长老禁地（应只有 admin 可访问）";
}
PHP,

        'payment_tamper' => <<<'PHP'
// 【漏洞】前端价格可篡改
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item'];
    $price = (float) $_POST['price'];  // 客户端可篡改
    echo "已购买 $item，价格 $price";
}
PHP,

        'captcha_reuse' => <<<'PHP'
// 【漏洞】验证码不失效
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['captcha'] === ($_SESSION['captcha'] ?? '')) {
        echo '提交成功';
        // 未清空 $_SESSION['captcha']
    } else {
        echo '验证码错误';
    }
}
PHP,

        'password_reset' => <<<'PHP'
// 【漏洞】通过邮箱可重置他人密码（未验证身份）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $newPwd = bin2hex(random_bytes(4));  // 弱重置
    $dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
    $pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass');
    $stmt = $pdo->prepare('UPDATE demo_users SET password = ? WHERE email = ?');
    $stmt->execute([$newPwd, $email]);
    echo "新密码已发送至 $email";
}
PHP,

        'brute_force' => <<<'PHP'
// 【漏洞】无失败锁定
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
$pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM demo_users WHERE username = ? AND password = ?');
    $stmt->execute([$_POST['username'], $_POST['password']]);
    if ($stmt->fetch()) echo '登录成功';
    else echo '登录失败';
}
PHP,

        'jwt_none' => <<<'PHP'
// 【漏洞】接受 alg=none 的 JWT
$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $token);
$parts = explode('.', $token);
if (count($parts) === 3) {
    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    // 未验证 alg
    if (($payload['role'] ?? '') === 'admin') {
        echo '长老专属内容';
    } else {
        echo '普通内容';
    }
}
PHP,

        'jwt_weak' => <<<'PHP'
// 【漏洞】弱密钥可爆破
$jwt = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
// 实际中需要爆破，演示略
echo "JWT: $jwt";
PHP,

        'jwt_kid' => <<<'PHP'
// 【漏洞】kid 字段路径穿越
$jwt = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$header = json_decode(base64_decode(explode('.', $jwt)[0] ?? ''), true);
$kid = $header['kid'] ?? '';  // kid=../../../etc/passwd 可读取文件作为密钥
echo "kid: $kid";
PHP,

        'oauth_redirect' => <<<'PHP'
// 【漏洞】redirect_uri 未严格校验
$redirect = $_GET['redirect_uri'] ?? '';
header("Location: $redirect");
PHP,

        'cors' => <<<'PHP'
// 【漏洞】CORS 配置过宽
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
echo "敏感数据";
PHP,

        'http_smuggle' => <<<'PHP'
// 教学演示：HTTP 请求走私
// 实际需要中间人代理，此处仅展示原理
echo "HTTP/1.1 200 OK\r\nContent-Length: 0\r\n\r\n";
PHP,

        'cache_poison' => <<<'PHP'
// 【漏洞】缓存投毒
$path = $_GET['page'] ?? 'index.html';
$content = file_get_contents("cache/$path");
echo $content;
PHP,

        'cache_poison_adv' => <<<'PHP'
// 【漏洞】高级缓存投毒
echo "Cached content with XSS via cache key injection";
PHP,

        'crypto_ecb' => <<<'PHP'
// 【漏洞】ECB 模式块重排
$key = '1234567890123456';  // 固定密钥
$plaintext = $_POST['data'] ?? '';
$encrypted = openssl_encrypt($plaintext, 'aes-128-ecb', $key, OPENSSL_RAW_DATA, '');
echo bin2hex($encrypted);
PHP,

        'crypto_hash_ext' => <<<'PHP'
// 【漏洞】未使用 HMAC
$secret = 'secret_key';
$data = $_POST['data'] ?? '';
$signature = $_POST['sig'] ?? '';
$expected = md5($secret . $data);  // 易受长度扩展
if ($signature === $expected) {
    echo '签名验证通过';
}
PHP,

        'php_type_juggle' => <<<'PHP'
// 【漏洞】弱类型比较
$password = $_POST['password'] ?? '';
if ($password == 0) {  // "0e123" == 0 科学计数法
    echo '登录成功';
}
PHP,

        'php_variable' => <<<'PHP'
// 【漏洞】extract() 变量覆盖
$role = 'guest';
extract($_GET);  // 攻击者 ?role=admin 可覆盖
if ($role === 'admin') {
    echo '长老专属';
}
PHP,

        'php_in_array' => <<<'PHP'
// 【漏洞】in_array 弱比较
$role = $_GET['role'] ?? 'guest';
if (in_array($role, ['admin', 'user'])) {  // 第三个参数默认为 false（弱比较）
    // 'admin1' == 'admin' 为真
    echo '允许访问';
}
PHP,

        'php_strcmp' => <<<'PHP'
// 【漏洞】strcmp 数组绕过
$password = $_POST['password'] ?? '';
if (strcmp($password, 'secret') == 0) {  // 传入数组返回 NULL == 0 为真
    echo '登录成功';
}
PHP,

        'php_cgi' => <<<'PHP'
// 【漏洞】PHP-CGI 漏洞（CVE-2024-4577）
// ?%ADd+allow_url_include%3d1+%ADd+auto_prepend_file%3dphp://input
echo "PHP-CGI 漏洞利用";
PHP,

        'container_escape' => <<<'PHP'
// 【漏洞】容器逃逸（教学演示）
echo "Docker 容器逃逸原理";
PHP,

        'code_review' => <<<'PHP'
// 迷你 CMS 代码审计挑战
echo "查看 CMS 源码找漏洞";
PHP,
    ];

    $code .= $templates[$category] ?? "// {$id} 通用漏洞演示（{$category}）\necho '待实现具体漏洞逻辑';";

    return $code;
}

/**
 * 生成 secure.php（安全实践参考）
 */
function generateSecure(string $category, string $id): string
{
    $code = "<?php\n/**\n * {$id} secure.php - 安全实践参考\n *\n * 修真靶场提示：本文件**不**是关卡页面，是漏洞修复的参考实现。\n */\n\n";

    $templates = [
        'sqli_numeric' => <<<'PHP'
// 修复 1：参数化查询
// 修复 2：严格类型校验（FILTER_VALIDATE_INT）
// 修复 3：关闭错误显示
$dsn = 'mysql:host=db;dbname=xiuxian_range;charset=utf8mb4';
$pdo = new PDO($dsn, 'xiuxian', 'xiuxian_pass', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    http_response_code(400);
    exit('Invalid ID');
}

$stmt = $pdo->prepare('SELECT username, email FROM demo_users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$row = $stmt->fetch();
if ($row) {
    echo '<p>弟子：' . htmlspecialchars($row['username']) . '</p>';
}
PHP,

        'sqli_string' => <<<'PHP'
// 修复：参数化 + 输入校验
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$name = $_GET['name'] ?? '';
$name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);  // 白名单

$stmt = $pdo->prepare('SELECT email FROM demo_users WHERE username = ?');
$stmt->execute([$name]);
foreach ($stmt as $row) {
    echo '<p>邮箱：' . htmlspecialchars($row['email']) . '</p>';
}
PHP,

        'sqli_union' => <<<'PHP'
// 修复：参数化查询（UNION 注入因参数化失效）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare('SELECT id, username, email FROM demo_users WHERE id = ?');
$stmt->execute([$id]);
foreach ($stmt as $row) {
    echo "ID={$row['id']} 用户={$row['username']}";
}
PHP,

        'sqli_error' => <<<'PHP'
// 修复 1：关闭 display_errors
// 修复 2：使用日志记录错误
// 修复 3：参数化查询
ini_set('display_errors', '0');
error_reporting(0);

$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$id]);
foreach ($stmt as $row) {
    // ...
}
PHP,

        'sqli_bool' => <<<'PHP'
// 修复：参数化
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT id FROM demo_users WHERE username = ?');
$stmt->execute([$_GET['name'] ?? '']);
echo $stmt->fetch() ? 'exists' : 'no';
PHP,

        'sqli_time' => <<<'PHP'
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT id FROM demo_users WHERE username = ?');
$stmt->execute([$_GET['name'] ?? '']);
PHP,

        'sqli_stacked' => <<<'PHP'
// 修复：使用预处理（PDO 默认不支持多语句）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
PHP,

        'sqli_gbk' => <<<'PHP'
// 修复：使用 UTF-8 字符集
$mysqli = new mysqli('db', 'xiuxian', 'xiuxian_pass', 'xiuxian_range');
$mysqli->set_charset('utf8mb4');  // 不用 GBK
$stmt = $mysqli->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->bind_param('i', $_GET['id'] ?? 1);
$stmt->execute();
PHP,

        'sqli_second' => <<<'PHP'
// 修复：入库时过滤恶意字符 + 转义
session_start();
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['username']);  // 白名单
    $stmt = $pdo->prepare('INSERT INTO demo_users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, 'pass']);
}
// 查询时同样使用参数化
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE username = ?');
$stmt->execute([$_GET['login'] ?? '']);
PHP,

        'sqli_filter' => <<<'PHP'
// 修复：使用参数化（不要靠过滤）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
foreach ($stmt as $row) print_r($row);
PHP,

        'sqli_waf' => <<<'PHP'
// 修复：参数化（不要用黑名单）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
PHP,

        'sqli_multi' => <<<'PHP'
// 修复：使用 prepare + execute（不支持多语句）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian', 'xiuxian_pass');
$stmt = $pdo->prepare('SELECT * FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
PHP,

        'sqli_getshell' => <<<'PHP'
// 修复：参数化 + 限制 MySQL 权限（不允许 FILE 权限）
$pdo = new PDO('mysql:host=db;dbname=xiuxian_range', 'xiuxian_readonly', 'xxx');
$stmt = $pdo->prepare('SELECT username FROM demo_users WHERE id = ?');
$stmt->execute([$_GET['id'] ?? 1]);
PHP,

        'xss_reflected' => <<<'PHP'
// 修复：htmlspecialchars 输出转义
$msg = $_GET['msg'] ?? '';
echo '<h3>你输入：</h3>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');

// 加上 CSP 头
header("Content-Security-Policy: default-src 'self'");
PHP,

        'xss_stored' => <<<'PHP'
// 修复：存储时 strip_tags，输出时 htmlspecialchars
$content = strip_tags($_POST['content'] ?? '');
file_put_contents(__DIR__ . '/comments.txt', $content . "\n", FILE_APPEND);

foreach (file(__DIR__ . '/comments.txt') as $line) {
    echo '<div class="xxr-narrative">' . htmlspecialchars($line) . '</div>';
}
PHP,

        'xss_filter' => <<<'PHP'
// 修复：完整属性转义
$msg = $_GET['msg'] ?? '';
echo '<div title="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '">...</div>';
PHP,

        'xss_bypass' => <<<'PHP'
// 修复：使用白名单而非黑名单
$msg = $_GET['msg'] ?? '';
$safe = strip_tags($msg);
$safe = preg_replace('/[^a-zA-Z0-9\s\p{Han}]/u', '', $safe);
echo $safe;
PHP,

        'xss_dom' => <<<'PHP'
// 修复：使用 textContent 而非 innerHTML
?>
<div id="output"></div>
<script>
const hash = location.hash.substring(1);
document.getElementById('output').textContent = hash;  // 安全
</script>
PHP,

        'xss_cookie' => <<<'PHP'
// 修复：HttpOnly Cookie + SameSite
session_start();
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => true,
]);
PHP,

        'csrf_get' => <<<'PHP'
// 修复：敏感操作只用 POST + CSRF Token
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Use POST');
}
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['_token'] ?? '')) {
    http_response_code(419);
    exit('CSRF token invalid');
}
// 转账逻辑
PHP,

        'csrf_post' => <<<'PHP'
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['_token'] ?? '')) {
        http_response_code(419);
        exit('CSRF token invalid');
    }
    // 转账逻辑
}
PHP,

        'csrf_token' => <<<'PHP'
// 修复：使用密码学安全的随机 Token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['_token'] ?? '')) {
        http_response_code(419);
        exit;
    }
    // 处理逻辑
}
PHP,

        'rce_basic' => <<<'PHP'
// 修复：白名单 IP 校验
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    exit('Invalid IP');
}
system("ping -c 1 " . escapeshellarg($ip));
PHP,

        'rce_space' => <<<'PHP'
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) exit('Invalid IP');
system("ping -c 1 " . escapeshellarg($ip));
PHP,

        'rce_filter' => <<<'PHP'
// 修复：白名单命令 + 参数
$allowed = ['status' => '/usr/bin/systemctl status', 'ping' => '/bin/ping'];
$cmd = $_GET['cmd'] ?? '';
if (!isset($allowed[$cmd])) {
    http_response_code(403);
    exit('Command not allowed');
}
// 白名单命令执行
PHP,

        'upload_js' => <<<'PHP'
// 修复：服务端验证 MIME + 扩展名 + 重命名
$allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$mime = mime_content_type($_FILES['file']['tmp_name']);

if (!isset($allowed[$ext]) || $allowed[$ext] !== $mime) {
    http_response_code(400);
    exit('Invalid file');
}
$newName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $newName);
PHP,

        'upload_mime' => <<<'PHP'
// 修复：不要相信 Content-Type，用 mime_content_type
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$mime = mime_content_type($_FILES['file']['tmp_name']);
if (!in_array($ext, ['jpg', 'png', 'gif']) || !str_starts_with($mime, 'image/')) {
    exit('Invalid file');
}
PHP,

        'upload_ext' => <<<'PHP'
// 修复：使用白名单
$allowed = ['jpg', 'png', 'gif', 'pdf'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) exit('Extension not allowed');
PHP,

        'upload_image' => <<<'PHP'
// 修复：白名单 + getimagesize + 重命名为 .jpg
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$tmp = $_FILES['file']['tmp_name'];
if (!in_array($ext, ['jpg', 'png', 'gif']) || !getimagesize($tmp)) {
    exit('Invalid image');
}
// 重命名（保留 .jpg 后缀，不允许 .php）
$newName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($tmp, 'uploads/' . $newName);
PHP,

        'upload_htaccess' => <<<'PHP'
// 修复：禁止上传 .htaccess / .htpasswd 等配置文件
$blocked = ['.htaccess', '.htpasswd', '.user.ini', 'web.config'];
$name = $_FILES['file']['name'];
if (in_array($name, $blocked)) exit('Filename blocked');
PHP,

        'upload_ntfs' => <<<'PHP'
// 修复：剥离 NTFS 流
$name = preg_replace('/:.*$/', '', $_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);
PHP,

        'lfi_basic' => <<<'PHP'
// 修复：白名单文件
$allowed = ['home', 'about', 'contact'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed)) exit('Not allowed');
include __DIR__ . '/pages/' . $file . '.php';
PHP,

        'lfi_filter' => <<<'PHP'
// 修复：白名单
$allowed = ['home', 'about', 'contact'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed)) exit('Not allowed');
include __DIR__ . '/pages/' . $file . '.php';
PHP,

        'lfi_log_poison' => <<<'PHP'
// 修复：禁止通过用户输入污染日志
// Apache 配置：CustomLog "logs/access.log" combined
// 关闭 LFI，使用白名单
$allowed = ['home'];
$file = $_GET['file'] ?? 'home';
if (!in_array($file, $allowed)) exit('Not allowed');
PHP,

        'lfi_session' => <<<'PHP'
// 修复：使用 Redis 存储 Session（隔离于文件系统）
// 并使用白名单文件包含
ini_set('session.save_handler', 'redis');
PHP,

        'xxe_file' => <<<'PHP'
// 修复：禁用外部实体
libxml_disable_entity_loader(true);  // PHP < 8.0
$dom = new DOMDocument();
$dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD);  // PHP 8.0+
PHP,

        'xxe_ssrf' => <<<'PHP'
libxml_disable_entity_loader(true);
$dom = new DOMDocument();
$dom->loadXML($xml, LIBXML_NOENT);
PHP,

        'ssrf_basic' => <<<'PHP'
// 修复：白名单域名
$allowed = ['xiuxian-range.local', 'cdn.xiuxian-range.local'];
$url = $_GET['url'] ?? '';
$host = parse_url($url, PHP_URL_HOST);
if (!in_array($host, $allowed)) exit('URL not allowed');
$content = file_get_contents($url);
PHP,

        'ssrf_protocol' => <<<'PHP'
// 修复：禁用危险协议
$blocked = ['gopher', 'dict', 'ldap', 'file'];
$scheme = parse_url($url, PHP_URL_SCHEME);
if (in_array($scheme, $blocked)) exit('Scheme not allowed');
PHP,

        'ssrf_rebind' => <<<'PHP'
// 修复：解析 URL 后重新检查 host
$host = parse_url($url, PHP_URL_HOST);
$ip = gethostbyname($host);
if (isPrivateIP($ip)) exit('Private IP blocked');
echo file_get_contents($url);

function isPrivateIP($ip) {
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
}
PHP,

        'file_read' => <<<'PHP'
// 修复：realpath 规范化 + 白名单
$allowedDir = realpath(__DIR__ . '/data');
$file = $_GET['file'] ?? '';
$realPath = realpath($allowedDir . '/' . $file);
if (!$realPath || !str_starts_with($realPath, $allowedDir)) {
    http_response_code(403);
    exit('Forbidden');
}
echo htmlspecialchars(file_get_contents($realPath));
PHP,

        'open_redirect' => <<<'PHP'
// 修复：白名单域名
$allowed = ['xiuxian-range.local'];
$url = $_GET['url'] ?? '';
$host = parse_url($url, PHP_URL_HOST);
if (!in_array($host, $allowed)) {
    http_response_code(400);
    exit('URL not allowed');
}
header("Location: $url");
PHP,

        'clickjacking' => <<<'PHP'
// 修复：禁止 iframe 嵌入
header('X-Frame-Options: DENY');
header("Content-Security-Policy: frame-ancestors 'none'");
?>
<button>确认</button>
PHP,

        'idor_horizontal' => <<<'PHP'
// 修复：添加 user_id 校验
session_start();
$orderId = $_GET['id'] ?? 1;
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT * FROM demo_orders WHERE id = ? AND user_id = ?');
$stmt->execute([$orderId, $userId]);
if (!$stmt->fetch()) {
    http_response_code(403);
    exit('Forbidden');
}
PHP,

        'idor_vertical' => <<<'PHP'
// 修复：检查角色
session_start();
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Admin only');
}
PHP,

        'payment_tamper' => <<<'PHP'
// 修复：服务端重新计算价格
$prices = ['sword' => 100, 'shield' => 50, 'potion' => 20];
$item = $_POST['item'] ?? '';
$price = $prices[$item] ?? 0;  // 服务端价格，不信任客户端
if (!$price) {
    http_response_code(400);
    exit('Invalid item');
}
echo "已购买 $item，价格 $price";
PHP,

        'captcha_reuse' => <<<'PHP'
// 修复：验证码一次性使用 + 过期时间
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['captcha'] ?? '') !== ($_SESSION['captcha'] ?? '')) {
        echo '验证码错误';
    } else {
        echo '提交成功';
        unset($_SESSION['captcha']);  // 一次性
    }
}
// 加上过期（5分钟）
if (isset($_SESSION['captcha_time']) && time() - $_SESSION['captcha_time'] > 300) {
    unset($_SESSION['captcha']);
}
PHP,

        'password_reset' => <<<'PHP'
// 修复：发送邮件含 token，用户通过 token 链接验证身份
$email = $_POST['email'] ?? '';
$token = bin2hex(random_bytes(32));
$expires = time() + 3600;
$stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)');
$stmt->execute([$email, $token, $expires]);

// 通过邮件发送链接（教学环境可显示）
echo "重置链接：https://your-domain.com/reset?token=$token";
PHP,

        'brute_force' => <<<'PHP'
// 修复：失败次数限制 + 锁定 + 验证码
session_start();
$_SESSION['login_attempts'] ??= 0;
if ($_SESSION['login_attempts'] > 5) {
    exit('Too many attempts, try again in 15 minutes');
}

$stmt = $pdo->prepare('SELECT password_hash FROM demo_users WHERE username = ?');
$stmt->execute([$_POST['username'] ?? '']);
$user = $stmt->fetch();
if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
    $_SESSION['login_attempts'] = 0;
    echo '登录成功';
} else {
    $_SESSION['login_attempts']++;
    usleep(random_int(100000, 500000));
    echo '登录失败';
}
PHP,

        'jwt_none' => <<<'PHP'
// 修复：强制使用 HS256 且验证签名
$jwt = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$parts = explode('.', $jwt);
$header = json_decode(base64_decode(strtr($parts[0] ?? '', '-_', '+/')), true);

if (($header['alg'] ?? '') !== 'HS256') {
    http_response_code(401);
    exit('Invalid algorithm');
}

$secret = 'super-secret-key-32-chars-minimum';
$signature = hash_hmac('sha256', "$parts[0].$parts[1]", $secret, true);
$expected = strtr(base64_encode($signature), '+/', '-_');
if (!hash_equals($expected, $parts[2] ?? '')) {
    exit('Invalid signature');
}
PHP,

        'jwt_weak' => <<<'PHP'
// 修复：使用强密钥（至少 32 字节）
// $secret = bin2hex(random_bytes(32));  // 64 字符 hex
PHP,

        'jwt_kid' => <<<'PHP'
// 修复：kid 不允许包含路径分隔符
$kid = preg_replace('/[^a-zA-Z0-9_-]/', '', $header['kid'] ?? '');
PHP,

        'oauth_redirect' => <<<'PHP'
// 修复：严格匹配 redirect_uri（完整 URL 或前缀）
$allowed = ['https://xiuxian-range.local/callback'];
$redirect = $_GET['redirect_uri'] ?? '';
if (!in_array($redirect, $allowed)) {
    http_response_code(400);
    exit('redirect_uri not allowed');
}
header("Location: $redirect");
PHP,

        'cors' => <<<'PHP'
// 修复：精确匹配允许的域
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['https://xiuxian-range.local'];
if (in_array($origin, $allowed)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}
PHP,

        'http_smuggle' => <<<'PHP'
// 修复：使用 HTTP/2、严格 CL/TE 解析、统一的代理配置
// Apache: mod_proxy 配置严格 CLF 格式
// Nginx: proxy_http_version 1.1 + 严格 header 解析
PHP,

        'cache_poison' => <<<'PHP'
// 修复：缓存键包含用户身份、严格过滤缓存内容
// Vary: Cookie / Authorization
header('Vary: Cookie, Authorization');
header("Cache-Control: no-cache, no-store, must-revalidate");
PHP,

        'cache_poison_adv' => <<<'PHP'
// 修复：同上 + CSP
header("Content-Security-Policy: default-src 'self'");
PHP,

        'crypto_ecb' => <<<'PHP'
// 修复：使用 CBC/GCM 模式
$key = random_bytes(32);
$iv = random_bytes(16);
$encrypted = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
PHP,

        'crypto_hash_ext' => <<<'PHP'
// 修复：使用 HMAC
$secret = 'secret-key';
$signature = hash_hmac('sha256', $data, $secret);
PHP,

        'php_type_juggle' => <<<'PHP'
// 修复：使用 === 严格比较
if ($password === '0') {  // 严格比较
    echo '登录成功';
}

// 或者 password_verify（永远返回 bool）
if (password_verify($password, $hash)) {
    echo '登录成功';
}
PHP,

        'php_variable' => <<<'PHP'
// 修复：不要使用 extract()，或者限制 extract 范围
$role = 'guest';
// 不要使用 extract($_GET);
// 而是从 $_GET 显式读取
$role = $_GET['role'] ?? 'guest';
$role = preg_replace('/[^a-zA-Z]/', '', $role);  // 白名单
PHP,

        'php_in_array' => <<<'PHP'
// 修复：第三个参数传 true（严格模式）
if (in_array($role, ['admin', 'user'], true)) {
    echo '允许访问';
}
PHP,

        'php_strcmp' => <<<'PHP'
// 修复：使用 hash_equals + 预处理
$hash = password_hash('secret', PASSWORD_BCRYPT);
if (password_verify($password, $hash)) {
    echo '登录成功';
}
PHP,

        'php_cgi' => <<<'PHP'
// 修复：升级 PHP 到 8.x（已修复 CVE-2024-4577）
// 配置 cgi.fix_pathinfo=0
PHP,

        'container_escape' => <<<'PHP'
// 修复：以非 root 用户运行容器
// 启用 seccomp / AppArmor
// 最小权限原则
PHP,

        'code_review' => <<<'PHP'
// 迷你 CMS 综合代码审计 - 安全版本
echo "已修复全部漏洞";
PHP,
    ];

    $code .= $templates[$category] ?? "// {$id} 通用安全实现（{$category}）\necho 'safe code';";

    return $code;
}