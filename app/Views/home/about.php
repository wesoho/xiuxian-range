<?php
/** @var ?array $user */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>关于 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <h2 class="text-gold text-center mb-4">📖 关于修真靶场</h2>

        <div class="bg-dark-translucent p-4 rounded mb-4">
            <h4 class="text-gold">⚔️ 修真靶场是什么？</h4>
            <p>修真网络安全靶场（XiuXian Range）是一个<strong>面向个人和团队</strong>的网络安全教学平台，以中国<strong>修真文化</strong>为载体，将传统网络安全漏洞学习包装成"修真炼道"之旅。</p>

            <h4 class="text-gold mt-4">🗺️ 修真境界</h4>
            <ul>
                <li><strong>🥉 炼气期（10关）</strong>：入门安全意识，信息泄露、弱口令、HTTP 头等基础</li>
                <li><strong>🥉 筑基期（15关）</strong>：XSS、CSRF、SQL 注入基础、命令注入基础</li>
                <li><strong>🥈 金丹期（15关）</strong>：过滤绕过、盲注、文件包含、文件上传</li>
                <li><strong>🥈 元婴期（15关）</strong>：XXE、SSRF、反序列化、越权、支付漏洞</li>
                <li><strong>🥇 化神期（15关）</strong>：JWT、OAuth、CORS、密码学、PHP 弱类型</li>
                <li><strong>🥇 炼虚期（10关）</strong>：综合 RCE、SQLi GetShell、PHP 解析漏洞</li>
                <li><strong>💎 合体期（10关）</strong>：剧情综合挑战</li>
                <li><strong>💎 大乘期（10关）</strong>：终极跨宗门渗透、真实 CVE 复现</li>
            </ul>

            <h4 class="text-gold mt-4">📚 三阶段学习路径</h4>
            <p>每个关卡都遵循"<strong>习道 → 试炼 → 悟道</strong>"的三阶段流程：</p>
            <ul>
                <li><strong>习道</strong>：阅读剧情背景、漏洞原理、攻击思路</li>
                <li><strong>试炼</strong>：进入真实靶场环境实战，提交 Flag 通关</li>
                <li><strong>悟道</strong>：通关后查看 Writeup、源码对比、根因分析</li>
            </ul>

            <h4 class="text-gold mt-4">🛠️ 技术栈</h4>
            <ul>
                <li>原生 PHP 8.2（无框架，代码透明可审计）</li>
                <li>MySQL 8.0 + Redis 7</li>
                <li>Apache 2.4 + mod_rewrite</li>
                <li>Bootstrap 5 + 自研修真风格主题</li>
                <li>Docker + Docker Compose 一键启动</li>
            </ul>

            <h4 class="text-gold mt-4">⚠️ 免责声明</h4>
            <p>本平台<strong>仅供网络安全学习与研究使用</strong>。请勿用于任何非法用途。学员应严格遵守《中华人民共和国网络安全法》及相关法律法规。</p>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>