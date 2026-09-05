<?php
/**
 * QY-YY-05 vulnerable.php - 漏洞演示
 * 分类：ssrf_basic
 *
 * ⚠️ 教学用代码，故意存在漏洞
 */

require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';

// SSRF 基础：元神出窍，未限制协议与目标
$url = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>元神出窍·SSRF</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🌀 元神出窍</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 你的元神可以从这里出窍，替宗门去"拉取"任何地址的讯息——无论那是什么协议、什么方位。
        </div>
        <form method="GET">
            <input type="text" name="url" class="form-control" placeholder="file:///etc/hosts 或 http://……" autocomplete="off">
            <button class="xxr-btn xxr-btn-primary mt-2">拉取</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if ($url) {
            // 【漏洞】未限制协议与目标（file://、内网地址均可）
            $__sim = xxr_internal_network($url);
            $content = $__sim !== null ? $__sim : @file_get_contents($url);
            echo htmlspecialchars((string) $content);
        }
        ?>
        </pre>
        <div class="xxr-narrative mt-3">
            <strong>🧪 提示：</strong> 元神出窍不止能拉网页——试试 <code>file:///etc/hosts</code>，
            宗门内网的方位也许就写在里面。
        </div>
    </div>
<?php xxr_flag_reveal('ssrf'); ?>
</body>
</html>
