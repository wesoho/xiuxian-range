<?php
/**
 * QY-HS-03 【青云宗·化神】kid 注入
 * 修真叙事：青云宗令牌中的 kid 字段可注入。
 * 漏洞类型：jwt_kid
 * 难度：L5
 * 宗门：qingong
 */

require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';

// 一枚门内样例符文（过期令牌，签名未校验），供演示与观察
$b64url = static function (string $raw): string {
    return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
};
$jwtHeader  = $b64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT', 'kid' => '/etc/keys/qingyun.pem']));
$jwtPayload = $b64url(json_encode([
    'sub'  => 'qingyun-inner-disciple',
    'role' => 'inner',
    'exp'  => 1700000000,
    'note' => '这枚符文早已过期，但掌门没舍得销毁——他说夹层里藏着一句要紧的话。',
    'egg'  => xxr_egg_secret('egg_rune_master'),
]));
$jwtSignature = $b64url('xiuxian-range-demo-signature-not-verified');
$sampleJwt = $jwtHeader . '.' . $jwtPayload . '.' . $jwtSignature;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>【青云宗·化神】kid 注入 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">【青云宗·化神】kid 注入</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 青云宗令牌中的 kid 字段可注入。
        </div>
        <p>JWT kid 注入。</p>

        <div class="xxr-narrative mt-3">
            <strong>🔐 符文样例（过期令牌）：</strong>
            <p class="mt-2 mb-1" style="font-family:monospace; word-break:break-all; color:#d4af37;"><?= htmlspecialchars($sampleJwt, ENT_QUOTES, 'UTF-8') ?></p>
            <p class="small text-muted mb-0">
                老弟子说：符文有三段，头尾验真伪，<strong>中间那一段</strong>从不加密——
                有人习惯往夹层里塞私货。用 base64 解一解中段，看看掌门塞了什么。
            </p>
        </div>

        <div class="alert alert-info mt-4">
            <strong>💡 习道提示：</strong> JWT kid 注入
            <hr>
            Flag 提交位置：<a href="/challenge/QY-HS-03" class="text-gold">返回关卡详情页</a> 提交。
        </div>
        <div class="text-center mt-4">
            <a href="/challenge/QY-HS-03" class="xxr-btn xxr-btn-secondary">← 返回关卡详情</a>
        </div>
    </div>
</body>
</html>
