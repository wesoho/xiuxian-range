<?php
/**
 * 【轮回宗·合体】轮回殿·认证篇
 * 修真叙事：轮回殿多重认证缺陷：会话固定 + JWT 攻击 + 密码重置漏洞。
你需要分别破解这三道防线，获取《轮回令》。
 * 漏洞类型：认证漏洞综合
 * 难度：终极挑战
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【轮回宗·合体】轮回殿·认证篇 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【轮回宗·合体】轮回殿·认证篇</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 轮回殿多重认证缺陷：会话固定 + JWT 攻击 + 密码重置漏洞。
你需要分别破解这三道防线，获取《轮回令》。
        </div>

        <div class="bg-dark-translucent p-4 rounded mt-4">
            <h5 class="text-gold">🎯 试炼目标</h5>
            <p>本关为<strong>修真综合挑战</strong>，需要综合运用多种漏洞技术。</p>
            <ul>
                <li>建立完整的<strong>攻击链思维</strong></li>
                <li>从信息收集到漏洞利用的<strong>渗透测试方法论</strong></li>
                <li>修真靶场鼓励学员在 <code>vulnerable.php</code> 中找到线索，在修真靶场其他关卡中找到技术</li>
            </ul>
        </div>

        <div class="alert alert-warning mt-4">
            <strong>🧠 高阶修真思路</strong>
            <ol>
                <li><strong>情报收集</strong>：robots.txt、.git泄露、源码备份</li>
                <li><strong>漏洞扫描</strong>：SQL注入、XSS、命令执行等</li>
                <li><strong>漏洞利用</strong>：获得初始立足点</li>
                <li><strong>权限提升</strong>：横向移动、提权</li>
                <li><strong>后渗透</strong>：获取核心数据（Flag）</li>
            </ol>
        </div>

        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong>
            <ul>
                <li>详细漏洞环境请参考修真靶场其他关卡</li>
                <li>本关是综合演练，重点在于<strong>思路</strong>而非单一漏洞</li>
            </ul>
            <hr>
            Flag: <code class="xxr-mono">flaglh_ht_04_auth_full_check_db</code>
            （具体 Flag 在数据库 challenges 表中）
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/LH-HT-04" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>