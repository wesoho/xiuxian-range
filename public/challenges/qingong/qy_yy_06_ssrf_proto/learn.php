<?php
/**
 * QY-YY-06 【青云宗·元婴】万法归宗（SSRF 协议利用）
 * 习道：dict:// / gopher:// 打内网 Redis 未授权 / 防御心法
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·元婴】万法归宗 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【青云宗·元婴】万法归宗（SSRF 协议利用）</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 元神出窍既可探方位，亦可借"万法"破阵——HTTP 之外的协议，才是内网重锤。
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 漏洞原理</h5>
            <p class="text-muted">SSRF 的威力取决于<strong>支持的协议</strong>：
            <code>dict://</code> 可向内网服务发送指令（探活、字典协议）；<code>gopher://</code> 更可
            <strong>构造任意 TCP 报文</strong>——把 Redis / MySQL / SMTP 的原生命令封装进去，
            等于让服务器替你与内网服务"对话"。<code>file://</code> 则用于读本地文件。</p>
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">⚔️ 本关攻击链（试炼指引）</h5>
            <ol class="text-muted mb-0">
                <li><code>dict://172.72.23.27:6379/info</code> —— 探测赤炎缓存（Redis <strong>未授权访问</strong>）</li>
                <li><code>dict://172.72.23.27:6379/get:secret</code> —— 读取缓存中的 <code>secret</code> 键，本关 Flag 就在里面</li>
                <li>进阶（gopher）：构造 <code>*1\r\n$4\r\nINFO\r\n</code> 形式的 RESP 报文直发 Redis——真实环境可写计划任务 / SSH 公钥 getshell</li>
            </ol>
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 安全修真心法</h5>
            <ul class="text-muted mb-0">
                <li>只允许 <code>http(s)</code> 协议，<strong>禁用 gopher/dict/file</strong> 等 wrapper</li>
                <li>内网缓存务必设置 <strong>requirepass</strong>，并绑定内网网卡、禁用外网来源</li>
                <li>Redis 以低权限用户运行、禁用 <code>CONFIG SET dir</code> 等危险命令（rename）</li>
                <li>SSRF 防御同上一关：白名单 + 解析 IP 校验</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/QY-YY-06" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>
