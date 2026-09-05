<?php
/**
 * 修真靶场 - 增强已有关卡
 *
 * 为每个关卡添加：
 *  - 更详细的修真叙事背景
 *  - 详细的安全修复建议（secure.php）
 *  - 完整的修真叙事界面（index.php）
 */

declare(strict_types=1);

$root = dirname(__DIR__) . '/public/challenges';

// 关卡元数据（修真叙事增强）
$metadata = [
    // 炼气期
    'QY-LQ-01' => ['title' => '【青云宗·炼气】藏经阁的注释', 'sect' => '青云宗', 'narrative' => '你刚拜入青云宗，掌门让你去藏经阁整理典籍。在翻阅一本《入门心法》时，你发现网页源码的HTML注释中似乎藏着什么...', 'category' => '信息泄露', 'fix' => '部署前移除所有HTML注释；CI/CD中加入注释扫描；敏感信息永远不要放在前端注释中'],
    'QY-LQ-02' => ['title' => '【青云宗·炼气】守山神兽的指引', 'sect' => '青云宗', 'narrative' => '守山神兽不让你过，但据说在它的指引下，访客能找到通过山门的小路。', 'category' => '信息泄露', 'fix' => '不要在robots.txt中泄露管理后台路径；使用身份验证保护敏感资源；WAF拦截扫描行为'],
    'QY-LQ-03' => ['title' => '【青云宗·炼气】祖师的Git事故', 'sect' => '青云宗', 'narrative' => '祖师爷曾把毕生所学放在一个版本控制系统中，但忘记清理。', 'category' => '信息泄露', 'fix' => '部署前删除.git目录；Apache/Nginx禁止访问点号开头的目录；CI/CD中检测.git存在'],
    'QY-LQ-04' => ['title' => '【青云宗·炼气】整站打包泄露', 'sect' => '青云宗', 'narrative' => '门派管理疏忽，把整站备份压缩包放到了webroot下，被弟子们发现了。', 'category' => '信息泄露', 'fix' => '备份文件存储在webroot之外；Apache配置禁止访问.zip/.bak/.sql；定期扫描泄露'],
    'QY-LQ-05' => ['title' => '【青云宗·炼气】丹房密报', 'sect' => '青云宗', 'narrative' => '丹房的某位师兄留下了一份详细的服务器配置单，意外地暴露在了公开路径。', 'category' => '信息泄露', 'fix' => '生产环境禁用phpinfo()；CI/CD中加入phpinfo检测；debug模式关闭'],
    'LH-LQ-06' => ['title' => '【轮回宗·炼气】最弱口令', 'sect' => '轮回宗', 'narrative' => '轮回殿门口有守卫，据说用最简单的口令就能通过。', 'category' => '弱口令', 'fix' => '强制密码策略（8+字符、复杂度）；登录失败锁定（5次/15分钟）；多因素认证'],
    'LH-LQ-07' => ['title' => '【轮回宗·炼气】幻象结界', 'sect' => '轮回宗', 'narrative' => '轮回宗设有幻象结界，所有验证都在前端完成，请绕过后端直接突破。', 'category' => '客户端校验', 'fix' => '服务端必须重新校验所有输入；前端校验仅作UX；使用WAF拦截异常请求'],
    'LH-LQ-08' => ['title' => '【轮回宗·炼气】忘川河的回声', 'sect' => '轮回宗', 'narrative' => '忘川河会反射一切，河面（HTTP响应头）下藏着秘密。', 'category' => '信息泄露', 'fix' => '生产环境移除Server/X-Powered-By头；不暴露内部信息；严格WAF规则'],
    'WM-LQ-09' => ['title' => '【万魔宗·炼气】血池的回响', 'sect' => '万魔宗', 'narrative' => '万魔宗的血池会回响一切错误。当你失误时，错误信息会把所有秘密都说出来。', 'category' => '信息泄露', 'fix' => '生产环境关闭display_errors；错误记录到日志；使用统一的错误页面'],
    'WM-LQ-10' => ['title' => '【万魔宗·炼气】魔窟的默认禁地', 'sect' => '万魔宗', 'narrative' => '魔窟深处有一个默认开放的禁地，所有闯入者皆可长驱直入。', 'category' => '默认配置', 'fix' => '修改默认管理路径；强制鉴权；IP白名单；定期安全审计'],
];

// 修真叙事增强文件
$sectMap = [
    '青云宗' => 'qingong',
    '轮回宗' => 'lunhuizong',
    '万魔宗' => 'wanmozong',
];

foreach ($metadata as $id => $info) {
    $dirName = strtolower(str_replace('-', '_', $id));
    $sectDir = $sectMap[$info['sect']];
    $dir = "$root/$sectDir/$dirName";

    if (!is_dir($dir)) continue;

    $titleEsc = addslashes($info['title']);
    $narrativeEsc = addslashes($info['narrative']);
    $category = $info['category'];
    $fix = $info['fix'];

    // 创建详细的修真叙事 PHP 文件（learn.php）
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

echo "✅ 修真叙事增强完成\n";