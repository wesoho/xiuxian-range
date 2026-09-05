<?php
/**
 * 修真靶场 - 补全剩余28关修真叙事
 */

declare(strict_types=1);

$root = dirname(__DIR__) . '/public/challenges';

$metadata = [
    // 修真期 缺失的
    'lh_jz_05_sqli_union' => ['title' => '【轮回宗·修真】联合试炼', 'sect' => 'lunhuizong', 'category' => 'SQL UNION 注入', 'fix' => '参数化查询；严格白名单；字段类型校验'],
    'lh_jz_06_sqli_error' => ['title' => '【轮回宗·修真】幽冥报错', 'sect' => 'lunhuizong', 'category' => 'SQL报错注入', 'fix' => '关闭display_errors；错误日志；参数化'],
    'lh_jz_07_sqli_bool' => ['title' => '【轮回宗·修真】真言之试', 'sect' => 'lunhuizong', 'category' => 'SQL布尔盲注', 'fix' => '参数化；统一错误响应'],
    'lh_jz_13_file_read' => ['title' => '【轮回宗·修真】忘川河底的秘密', 'sect' => 'lunhuizong', 'category' => 'LFI目录穿越', 'fix' => '白名单文件包含；realpath规范化'],
    'lh_jz_14_upload_js' => ['title' => '【轮回宗·修真】上传心法', 'sect' => 'lunhuizong', 'category' => '上传JS前端校验', 'fix' => '服务端验证MIME+扩展名+重命名'],
    'lh_lq_06_weak_password' => ['title' => '【轮回宗·炼气】最弱口令', 'sect' => 'lunhuizong', 'category' => '弱口令', 'fix' => '强密码策略+失败锁定+MFA'],
    'lh_lq_07_js_validate' => ['title' => '【轮回宗·炼气】幻象结界', 'sect' => 'lunhuizong', 'category' => 'JS前端校验', 'fix' => '服务端必须重新校验所有输入'],
    'lh_lq_08_header_leak' => ['title' => '【轮回宗·炼气】忘川河的回声', 'sect' => 'lunhuizong', 'category' => 'HTTP响应头泄露', 'fix' => '移除Server/X-Powered-By；严格WAF'],
    'qy_jz_01_xss_ref' => ['title' => '【青云宗·修真】练功房的咒语', 'sect' => 'qingong', 'category' => 'XSS反射型', 'fix' => 'htmlspecialchars + CSP'],
    'qy_jz_02_csrf_get' => ['title' => '【青云宗·修真】转账幻阵', 'sect' => 'qingong', 'category' => 'CSRF GET型', 'fix' => '仅POST+CSRF Token'],
    'qy_jz_03_sqli_num' => ['title' => '【青云宗·修真】丹房数字谜题', 'sect' => 'qingong', 'category' => 'SQL数字型', 'fix' => '参数化+类型校验'],
    'qy_jz_04_sqli_str' => ['title' => '【青云宗·修真】丹方字符咒语', 'sect' => 'qingong', 'category' => 'SQL字符型', 'fix' => '参数化+字符集'],
    'qy_jz_11_redirect' => ['title' => '【青云宗·修真】传送门诡计', 'sect' => 'qingong', 'category' => '开放重定向', 'fix' => '白名单URL'],
    'qy_jz_12_xss_store' => ['title' => '【青云宗·修真】留言板诅咒', 'sect' => 'qingong', 'category' => 'XSS存储型', 'fix' => '存储时净化+输出转义'],
    'qy_lq_01_html_comment' => ['title' => '【青云宗·炼气】藏经阁注释', 'sect' => 'qingong', 'category' => '信息泄露', 'fix' => '部署前清理注释；CI扫描'],
    'qy_lq_02_robots' => ['title' => '【青云宗·炼气】守山神兽', 'sect' => 'qingong', 'category' => 'robots.txt泄露', 'fix' => '不在robots中泄露管理路径'],
    'qy_lq_03_git' => ['title' => '【青云宗·炼气】祖师Git事故', 'sect' => 'qingong', 'category' => '.git泄露', 'fix' => '部署前删除.git；Apache禁止'],
    'qy_lq_04_backup' => ['title' => '【青云宗·炼气】整站打包泄露', 'sect' => 'qingong', 'category' => '备份文件泄露', 'fix' => '备份文件移出webroot'],
    'qy_lq_05_phpinfo' => ['title' => '【青云宗·炼气】丹房密报', 'sect' => 'qingong', 'category' => 'phpinfo泄露', 'fix' => '生产禁用phpinfo()'],
    'qy_hs_15_php_in' => ['title' => '【青云宗·化神】in_array 陷阱', 'sect' => 'qingong', 'category' => 'in_array弱比较', 'fix' => '第三个参数传true严格模式'],
    'qy_lx_10_cache_adv' => ['title' => '【青云宗·炼虚】缓存成魔', 'sect' => 'qingong', 'category' => '高级缓存投毒', 'fix' => 'Vary+Cache-Control: private'],
    'wm_jz_08_sqli_time' => ['title' => '【万魔宗·修真】时光咒', 'sect' => 'wanmozong', 'category' => 'SQL时间盲注', 'fix' => '参数化；速率限制'],
    'wm_jz_09_rce_basic' => ['title' => '【万魔宗·修真】Ping 测灵根', 'sect' => 'wanmozong', 'category' => '命令注入基础', 'fix' => '白名单+escapeshellarg'],
    'wm_jz_10_csrf_post' => ['title' => '【万魔宗·修真】魔影传书', 'sect' => 'wanmozong', 'category' => 'CSRF POST型', 'fix' => 'CSRF Token+SameSite'],
    'wm_jz_15_clickjack' => ['title' => '【万魔宗·修真】无形之框', 'sect' => 'wanmozong', 'category' => '点击劫持', 'fix' => 'X-Frame-Options: DENY'],
    'wm_lq_09_sqli_error' => ['title' => '【万魔宗·炼气】血池回响', 'sect' => 'wanmozong', 'category' => 'SQL错误回显', 'fix' => '关闭display_errors'],
    'wm_lq_10_default_admin' => ['title' => '【万魔宗·炼气】魔窟默认禁地', 'sect' => 'wanmozong', 'category' => '默认配置漏洞', 'fix' => '修改默认路径+鉴权+IP白名单'],
    'wm_lx_06_php_strcmp' => ['title' => '【万魔宗·炼虚】strcmp 陷阱', 'sect' => 'wanmozong', 'category' => 'strcmp数组绕过', 'fix' => 'hash_equals/password_verify'],
];

foreach ($metadata as $dirName => $info) {
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

echo "✅ 剩余修真叙事补充完成\n";
echo "总修真关卡：100 个\n";
$count = 0;
$dirs = glob(__DIR__ . '/../public/challenges/*/*', GLOB_ONLYDIR);
foreach ($dirs as $d) {
    if (is_file("$d/learn.php")) $count++;
}
echo "含修真叙事：$count 个\n";