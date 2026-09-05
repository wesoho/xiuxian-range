<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * ============================================================
 * 修真网络安全靶场 - QY-LQ-01 藏经阁的注释
 * 修真叙事：藏经阁的网页源码中意外泄露了隐藏信息
 * 漏洞类型：信息泄露 - HTML 注释
 * 难度：L1 入门
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>藏经阁 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🏯 藏经阁 · 入门心法</h2>
        <p class="text-muted">你刚拜入青云宗，掌门让你整理典籍。浏览此页，看看能否找到隐藏的信息。</p>

        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 掌门远游前在藏经阁网页源码中留下了对新弟子的寄语。源码注释常被用于开发调试，但若忘记清理，会泄露密钥、备注、todo 列表等敏感信息。
        </div>

        <hr style="border-color: rgba(212,175,55,0.3);">

        <h4 class="text-gold mt-4">📜 藏经阁心法列表</h4>
        <ul>
            <li>《青云心法·卷一》</li>
            <li>《练功要诀》</li>
            <li>《符文入门》</li>
            <li>《山门规矩》</li>
        </ul>

        <!--
            ===========================================
            TODO (开发笔记 - 不应出现在生产环境)
            ===========================================
            掌门寄语：新弟子若能发现此注释，说明你心细如发。

            恭喜你！这一关的 Flag 是：

            <?= xxr_challenge_flag() ?>

            提示：在浏览器中按 Ctrl+U（或右键 → 查看页面源代码）查看源码。
            ===========================================
        -->

        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> 在浏览器中按 <kbd>Ctrl+U</kbd> 查看页面源代码。
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/QY-LQ-01" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>

    <!--
        ==================================================
        （藏经阁最底层的书架上，压着一张没人翻动的字条）
        ==================================================
        既然你把源码翻到了最底下，说明第一条注释根本满足不了你。

        天机残卷 · 翻书虫的嘉奖：
        口令：<?= xxr_egg_secret('egg_bookworm') ?>

        —— 复制口令，到 /tianji 天机阁兑换，可获【📖 翻书虫】印记。
        ==================================================
    -->
</body>
</html>