<?php
/**
 * WM-HT-05 【万魔宗·合体】炼魂殿·SSRF 内网横向
 * 习道：发现 → 横向拿权 → 纵深防御
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【万魔宗·合体】炼魂殿·SSRF 内网横向 · 修真心法</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">📖 【万魔宗·合体】炼魂殿 · SSRF 内网横向</h2>
        <div class="xxr-narrative">
            <strong>📜 剧情：</strong> 合体期的修士不再单点破阵——一个 SSRF 入口，就是通往整个内网的传送门。
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🔍 攻击编排（借鉴国光 SSRF-Labs）</h5>
            <ol class="text-muted mb-0">
                <li><strong>内网发现</strong>：<code>file:///etc/hosts</code> 读服务器本地映射，圈定内网资产（本靶场为 172.72.23.22 / .23 / .27 三席）</li>
                <li><strong>横向拿权</strong>：内网服务普遍"生在内网故信任内网"——灵脉控制台的命令执行接口没有鉴权，<code>?cmd=</code> 直接发号施令</li>
                <li><strong>顺手收集</strong>：轮回殿数据库注入读秘闻、赤炎缓存未授权读缓存——每台主机都是新的跳板</li>
                <li><strong>真实世界延伸</strong>：gopher 写 Redis 计划任务 / SSH 公钥、MySQL UDF 提权、云元数据密钥窃取</li>
            </ol>
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-3">
            <h5 class="text-gold">🛡️ 纵深防御心法</h5>
            <ul class="text-muted mb-0">
                <li><strong>入口侧</strong>：SSRF 防御三板斧——协议白名单、目标白名单、解析 IP 后校验内网段</li>
                <li><strong>内网侧</strong>：内网服务<strong>必须鉴权</strong>（默认关闭），不要因"在内网"而裸奔</li>
                <li><strong>网络侧</strong>：分区隔离（业务 / 缓存 / 数据库分网段），出口收敛，最小权限</li>
                <li><strong>监测侧</strong>：服务器发起的异常外联与内网扫描行为告警</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/WM-HT-05" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>
