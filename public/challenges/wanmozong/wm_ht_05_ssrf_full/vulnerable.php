<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
// 【漏洞】综合 SSRF：SSRF 作跳板，横向打模拟内网（借鉴国光 SSRF-Labs 编排）
// 内网拓扑见 xxr_internal_network()：22=命令执行 23=SQL注入 27=Redis未授权
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>炼魂殿·SSRF 内网横向</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🔮 炼魂殿 · 元神入内网</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 炼魂殿的「引魂幡」可以把元神送进宗门内网。此前你在元婴期已经学会了
            <code>file://</code> 读方位、<code>dict://</code> 探缓存——现在，用它们向内网纵深推进。
        </div>

        <div class="row g-3 my-3">
            <div class="col-md-4">
                <div class="xxr-egg-card p-3 h-100">
                    <h6 class="text-gold">🎯 第一步 · 内网发现</h6>
                    <p class="small text-muted mb-0">从炼魂殿的 SSRF 入口读 <code>file:///etc/hosts</code>，摸清内网三席的方位。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="xxr-egg-card p-3 h-100">
                    <h6 class="text-gold">⚔️ 第二步 · 横向拿权</h6>
                    <p class="small text-muted mb-0"><code>172.72.23.22</code> 灵脉控制台存在命令执行，
                    <code>?cmd=</code> 直接发号施令——配置文件里有本关的 Flag。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="xxr-egg-card p-3 h-100">
                    <h6 class="text-gold">📜 第三步 · 顺手牵羊</h6>
                    <p class="small text-muted mb-0"><code>172.72.23.23</code> 轮回殿数据库有注入，
                    <code>172.72.23.27</code> 赤炎缓存未授权——都藏着宗门秘闻。</p>
                </div>
            </div>
        </div>

        <form method="GET">
            <div class="input-group">
                <input type="text" name="url" class="form-control" placeholder="file:///etc/hosts 或 http://172.72.23.22/shell.php?cmd=whoami" autocomplete="off">
                <button class="xxr-btn xxr-btn-primary">元神出窍</button>
            </div>
        </form>

        <pre class="bg-dark-translucent p-3 mt-3">
        <?php
        $url = $_GET['url'] ?? '';
        if ($url !== '') {
            // 【漏洞】未校验协议与目标 —— SSRF 直通内网
            $__sim = xxr_internal_network($url, xxr_challenge_flag());
            if ($__sim !== null) {
                echo htmlspecialchars($__sim, ENT_QUOTES, 'UTF-8');
            } else {
                try {
                    echo htmlspecialchars((string) @file_get_contents($url));
                } catch (\Throwable $e) {
                    echo '请求失败：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                }
            }
        }
        ?>
        </pre>

        <div class="xxr-narrative mt-3">
            <strong>💡 攻击链提示：</strong>
            <code>file:///etc/hosts</code>（发现）
            → <code>http://172.72.23.22/shell.php?cmd=cat%20/var/www/qingong/config.php</code>（命令执行拿 Flag）
            → <code>http://172.72.23.23/?id=-1</code>（SQL 注入看秘闻）
            → <code>dict://172.72.23.27:6379/get:secret</code>（Redis 未授权）
        </div>
    </div>
<?php xxr_flag_reveal('ssrf'); ?>
</body>
</html>
