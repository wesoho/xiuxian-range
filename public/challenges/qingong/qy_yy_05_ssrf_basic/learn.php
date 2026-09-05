<?php
/**
 * QY-YY-05 【青云宗·元婴】元神出窍（SSRF 基础）
 * 习道：SSRF 原理 / file:// 内网发现 / 防御心法
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·元婴】元神出窍 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【青云宗·元婴】元神出窍（SSRF 基础）</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 元神出窍替宗门拉取讯息，是绕过山门、直探内网的秘术——也是元婴期最凶险的一关。
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞原理</h5>
            <p class="text-muted">SSRF（服务端请求伪造）：服务端根据用户输入发起网络请求，却未校验协议与目标。
            攻击者可让<strong>服务器本身</strong>去访问它能到达的任何地址——内网服务、云元数据（169.254.169.254）、
            甚至用 <code>file://</code> 直接读取服务器本地文件。</p>
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">⚔️ 本关攻击链（试炼指引）</h5>
            <ol class="text-muted mb-0">
                <li><code>file:///etc/hosts</code> —— 读服务器 hosts，<strong>发现内网三席的方位</strong>（灵脉控制台 / 轮回殿数据库 / 赤炎缓存）</li>
                <li>Flag 会随成功的攻击尝试自动显现（试炼印记），复制到关卡详情页提交</li>
                <li>下一关（万法归宗）用 <code>dict://</code> 深入内网缓存</li>
            </ol>
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <ul class="text-muted mb-0">
                <li><strong>白名单</strong>目标域名与协议，只允许 <code>http(s)</code></li>
                <li><strong>禁止</strong> <code>file://</code>、<code>gopher://</code>、<code>dict://</code> 等危险协议</li>
                <li>解析域名后校验 IP，<strong>禁止内网网段</strong>与回环地址（防 DNS rebinding 需在请求时二次解析校验）</li>
                <li>云环境防护元数据接口（绑定内网网卡 + 强制 Token 头）</li>
                <li>错误信息与响应内容不要原样回显给用户</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/QY-YY-05" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>
