<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * LH-LQ-08 忘川河的回声 - HTTP 响应头信息泄露
 */

// 【漏洞】添加了敏感信息到响应头
header('X-Powered-By: PHP/8.2.0-QiuXianEdition');
header('Server: QiXian-WebServer/1.0');
header('X-Internal-Token: ' . base64_encode('demo-secret-token-' . date('Y-m-d')));
header('X-Debug-Info: debug-mode-enabled=true');
header('X-Flag-Header: ' . xxr_challenge_flag());  // 演示用

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>忘川河 · 响应头泄露</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🌊 忘川河的回声</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 忘川河会反射一切，河面（HTTP响应头）下藏着秘密。
        </div>

        <div class="alert alert-info">
            <strong>💡 习道提示：</strong> 使用浏览器 <kbd>F12</kbd> 打开开发者工具，切换到 <strong>Network</strong> 标签，刷新页面查看 Response Headers。
        </div>

        <h4>📚 HTTP 响应头泄露的危害</h4>
        <ul>
            <li><code>Server</code>：泄露 Web 服务器版本，可被针对性攻击</li>
            <li><code>X-Powered-By</code>：泄露语言/框架版本</li>
            <li><code>X-Debug-Info</code>：调试模式泄露</li>
            <li>自定义头部可能泄露 token、密钥、调试信息</li>
        </ul>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5>🔍 实战演示：</h5>
            <p>查看本页面响应头，特别是 X-Flag-Header 字段。</p>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/LH-LQ-08" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>