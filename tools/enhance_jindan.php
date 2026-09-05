<?php
/**
 * 修真靶场 - 金丹期15关修真叙事增强
 */

declare(strict_types=1);

$root = dirname(__DIR__) . '/public/challenges';

$metadata = [
    'qy_jd_01_xss_filter' => ['title' => '【青云宗·金丹】金光的过滤', 'sect' => 'qingong', 'narrative' => '金丹期的咒语会过滤一些关键字，但你可以用编码绕过。', 'category' => 'XSS HTML实体编码绕过', 'fix' => '使用htmlspecialchars() + ENT_QUOTES；CSP头禁止内联脚本；白名单过滤'],
    'qy_jd_02_xss_bypass' => ['title' => '【青云宗·金丹】咒语变形', 'sect' => 'qingong', 'narrative' => '金丹真人会过滤 script 等关键字，你需要变形绕过。', 'category' => 'XSS关键字过滤绕过', 'fix' => '使用DOMPurify；CSP头限制脚本；输出转义'],
    'qy_jd_03_csrf_token' => ['title' => '【青云宗·金丹】令牌之谜', 'sect' => 'qingong', 'narrative' => 'CSRF Token 可以被预测或泄露。', 'category' => 'CSRF Token可预测', 'fix' => '使用密码学安全随机数生成Token；HMAC签名；SameSite Cookie'],
    'qy_jd_09_rce_space' => ['title' => '【青云宗·金丹】空间的缝隙', 'sect' => 'qingong', 'narrative' => '青云宗过滤了空格，你可以利用其他字符代替。', 'category' => '命令注入空格过滤绕过', 'fix' => '白名单命令参数；PHP禁用危险函数（escapeshellarg + escapeshellcmd）'],
    'qy_jd_10_rce_filter' => ['title' => '【青云宗·金丹】禁咒搜寻', 'sect' => 'qingong', 'narrative' => '金丹期过滤了 cat 等关键字。', 'category' => '命令注入关键字过滤绕过', 'fix' => '避免使用shell命令处理用户输入；用专用API替代'],
    'qy_jd_15_upload_img' => ['title' => '【青云宗·金丹】金身绘像', 'sect' => 'qingong', 'narrative' => '青云宗只接受图片，但实际上可以藏入 PHP 代码。', 'category' => '文件上传图片马', 'fix' => 'GD重新生成图片（剥离PHP代码）；白名单MIME；.htaccess禁止脚本执行'],
    'lh_jd_04_sqli_stack' => ['title' => '【轮回宗·金丹】轮回双咒', 'sect' => 'lunhuizong', 'narrative' => '轮回宗允许同时执行多个咒语（堆叠查询）。', 'category' => 'SQL堆叠注入', 'fix' => 'PDO禁用multi_query；最小权限数据库账户；WAF拦截分号'],
    'lh_jd_05_sqli_gbk' => ['title' => '【轮回宗·金丹】宽字节迷阵', 'sect' => 'lunhuizong', 'narrative' => '轮回宗使用 GBK 编码，引号会被吞掉。', 'category' => 'SQL注入宽字节绕过', 'fix' => '使用UTF-8字符集；用addslashes前先检查字符集；推荐参数化'],
    'lh_jd_06_sqli_second' => ['title' => '【轮回宗·金丹】二次重生', 'sect' => 'lunhuizong', 'narrative' => '轮回宗会让你重生于第二次注册时（二次注入）。', 'category' => 'SQL二次注入', 'fix' => '存储时转义；查询时再次转义；最佳方案参数化'],
    'lh_jd_11_lfi_basic' => ['title' => '【轮回宗·金丹】轮回之眼', 'sect' => 'lunhuizong', 'narrative' => '轮回宗的眼睛能看到任何文件路径。', 'category' => 'LFI目录穿越', 'fix' => '白名单文件包含；realpath规范化；open_basedir限制'],
    'lh_jd_12_lfi_filter' => ['title' => '【轮回宗·金丹】PHP之源', 'sect' => 'lunhuizong', 'narrative' => '轮回宗用 PHP 伪协议读取源码。', 'category' => 'LFI php://filter', 'fix' => '禁用allow_url_include；白名单；删除危险php://包装器'],
    'wm_jd_07_sqli_filter' => ['title' => '【万魔宗·金丹】禁咒过滤', 'sect' => 'wanmozong', 'narrative' => '万魔宗过滤了 union/select 等关键字。', 'category' => 'SQL关键字过滤绕过', 'fix' => '不依赖黑名单；使用参数化查询；WAF深度检测'],
    'wm_jd_08_sqli_waf' => ['title' => '【万魔宗·金丹】护山结界', 'sect' => 'wanmozong', 'narrative' => '万魔宗的山门有护山大阵（WAF）阻挡入侵。', 'category' => 'SQL注入WAF绕过', 'fix' => 'WAF需配合后端校验；不能仅依赖WAF；使用机器学习检测异常'],
    'wm_jd_13_upload_mime' => ['title' => '【万魔宗·金丹】灵识伪装', 'sect' => 'wanmozong', 'narrative' => '万魔宗灵识伪装术：上传时只检查 MIME 类型。', 'category' => '文件上传MIME绕过', 'fix' => '使用mime_content_type()而非客户端Content-Type；白名单；重命名文件'],
    'wm_jd_14_upload_ext' => ['title' => '【万魔宗·金丹】禁咒文件', 'sect' => 'wanmozong', 'narrative' => '黑名单过滤可被特殊后缀绕过。', 'category' => '文件上传黑名单绕过', 'fix' => '使用白名单而非黑名单；Apache配置禁止脚本执行；随机文件名'],
    // 缺失的：qy_jd_15_upload_img 已添加
];

foreach ($metadata as $dirName => $info) {
    $dir = "$root/{$info['sect']}/{$dirName}";

    if (!is_dir($dir)) continue;

    $titleEsc = addslashes($info['title']);
    $narrativeEsc = addslashes($info['narrative']);
    $category = $info['category'];
    $fix = $info['fix'];
    $parts = explode('_', $dirName);
    $id = strtoupper($parts[0] . '-' . $parts[1] . '-' . $parts[2]);

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

echo "✅ 金丹期修真叙事增强完成\n";