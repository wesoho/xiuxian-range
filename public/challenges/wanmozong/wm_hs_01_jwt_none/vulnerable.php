<?php
// JWT alg=none 攻击
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>无相法印·JWT</title>
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2>🔓 无相法印</h2>
        <p>JWT Token 鉴权</p>
        <div id="result"></div>
    </div>
    <script>
    async function check() {
        const token = prompt('输入 JWT Token（试 alg=none）');
        const res = await fetch('/api/check-jwt', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({token})
        });
        document.getElementById('result').innerHTML = await res.text();
    }
    check();
    </script>
    <?php
    // 后端验证逻辑（教学演示）
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $token = $input['token'] ?? '';
        $parts = explode('.', $token);
        if (count($parts) === 3) {
            $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
            // 【漏洞】未验证 alg
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            echo "用户：{$payload['user']}, 角色：{$payload['role']}";
        }
    }
    ?>
<?php require_once __DIR__ . '/../../../../app/bootstrap_challenge.php'; xxr_flag_reveal('jwt'); ?>
</body>
</html>