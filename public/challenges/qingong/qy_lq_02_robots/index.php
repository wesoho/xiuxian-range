<?php
/**
 * ============================================================
 * QY-LQ-02 守山神兽的指引 - robots.txt 信息泄露
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>守山神兽 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🐉 守山神兽</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 守山神兽盘踞在山门前，看似凶猛，但据说它在指引访客找到通过山门的"小路"。聪明人会查阅山门的指引文件。
        </div>

        <div class="alert alert-info">
            <strong>💡 习道提示：</strong> robots.txt 是搜索引擎爬虫的第一道指令。访问 <a href="/robots.txt" class="text-gold">/robots.txt</a> 查看。
            <hr>
            <strong>如何过关：</strong> 本关 Flag 就在 /robots.txt 顶部的【本关答案】区块里（形如 <code>flag{随机字符串}</code>），
            复制后到 <a href="/challenge/QY-LQ-02" class="text-gold">关卡详情页</a> 提交即可。
            <small class="d-block mt-1 text-muted">另：robots.txt 里还有前人留下的「支线彩蛋」字条——与通关无关，有兴趣再去研究。</small>
        </div>

        <h4>📜 山门规则</h4>
        <ul>
            <li>入门弟子需持有掌门手令</li>
            <li>外门弟子可在 <code>/outer/</code> 区域活动</li>
            <li>内门弟子可入 <code>/inner/</code> 区域</li>
            <li>长老可入 <code>/admin/</code> 禁地</li>
        </ul>

        <div class="text-center mt-4">
            <a href="/challenge/QY-LQ-02" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>