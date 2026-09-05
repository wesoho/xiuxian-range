<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-LQ-10 魔窟的默认禁地 - 默认管理路径暴露
 */
$path = $_GET['path'] ?? '';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>魔窟 · 默认禁地</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🚪 魔窟的默认禁地</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 魔窟深处有一个默认开放的禁地，所有闯入者皆可长驱直入。
        </div>

        <div class="alert alert-info">
            <strong>💡 习道提示：</strong> 尝试访问 <a href="/admin/" class="text-gold">/admin/</a> 默认管理路径。
        </div>

        <h4>📚 默认配置漏洞</h4>
        <ul>
            <li><code>/admin/</code>：通用管理后台</li>
            <li><code>/manager/</code>：旧版 Tomcat 管理</li>
            <li><code>/actuator/</code>：Spring Boot Actuator</li>
            <li><code>/swagger-ui.html</code>：API 文档</li>
            <li><code>/.env</code>：环境变量</li>
        </ul>

        <div class="bg-dark-translucent p-3 rounded">
            <p>本关演示：直接访问 <a href="/admin/" class="text-gold">/admin/</a> 即可进入"魔窟禁地"。</p>
            <p>修真靶场的管理后台有独立鉴权，但教学演示中默认开放供学习。</p>
            <hr style="border-color: rgba(212,175,55,0.2);">
            <p>本关 Flag: <code class="xxr-mono"><?= xxr_challenge_flag() ?></code></p>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/WM-LQ-10" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>