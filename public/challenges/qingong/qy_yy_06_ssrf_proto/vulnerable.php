<?php
/**
 * QY-YY-06 vulnerable.php - 漏洞演示
 * 分类：ssrf_protocol
 *
 * ⚠️ 教学用代码，故意存在漏洞
 */

require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';

// 【漏洞】非 HTTP 协议利用：dict:// / gopher:// 打内网 Redis（未授权）
$url = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>万法归宗·SSRF 协议利用</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>📜 万法归宗</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 上一关你发现了宗门内网的方位。这一关的元神可以借用"万法"（各种协议）出窍——
            HTTP 之外，<code>dict://</code> 与 <code>gopher://</code> 才是打内网缓存的重锤。
        </div>
        <form method="GET">
            <input type="text" name="url" class="form-control" placeholder="dict://172.72.23.27:6379/info" autocomplete="off">
            <button class="xxr-btn xxr-btn-primary mt-2">出窍</button>
        </form>
        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        if ($url !== '') {
            $__sim = xxr_internal_network($url, xxr_challenge_flag());
            if ($__sim !== null) {
                // 模拟内网：Redis 未授权访问的响应
                echo htmlspecialchars($__sim, ENT_QUOTES, 'UTF-8');
            } else {
                try {
                    echo htmlspecialchars((string) file_get_contents($url));
                } catch (\Throwable $e) {
                    echo '请求失败：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                }
            }
        } else {
            echo '<p class="text-muted">以 ?url= 参数提交 dict:// / gopher:// 协议地址进行试炼。</p>';
        }
        ?>
        </pre>
        <div class="xxr-narrative mt-3">
            <strong>🧪 提示：</strong> 万魔宗·赤炎缓存没有设防（未授权）。
            <code>dict://172.72.23.27:6379/info</code> 先探一探，
            再用 <code>dict://172.72.23.27:6379/get:secret</code> 把缓存里的东西取出来。
        </div>
    </div>
<?php xxr_flag_reveal('ssrf'); ?>
</body>
</html>
