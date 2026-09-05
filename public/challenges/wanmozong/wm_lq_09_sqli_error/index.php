<?php
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
/**
 * WM-LQ-09 血池的回响 - SQL 错误回显
 */

// 【漏洞】数据库连接使用宽松错误模式（display_errors = On）
[$dsn, $__xxr_u, $__xxr_p] = xxr_pdo_args();

try {
    $pdo = new PDO($dsn, $__xxr_u, $__xxr_p, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('数据库连接失败');
}

// 教学夹具自愈：确保 999 号「隐席长老」存在（Flag 的藏身账号，与数据库随机化保持一致）
try {
    if ((int) db()->fetchScalar('SELECT COUNT(*) FROM demo_users WHERE id = 999') === 0) {
        db()->execute(
            "INSERT INTO demo_users (id, username, password, email, role) VALUES (999, 'hidden_elder', 'nobody-knows', 'hidden@xiuxian-range.local', 'elder')"
        );
    }
} catch (\Throwable $e) {
    // 夹具自愈失败不影响演示
}

$id = $_GET['id'] ?? '1';
$flag = null;

if (isset($_GET['id'])) {
    try {
        // 【漏洞】直接拼接 SQL，未使用参数化
        $sql = "SELECT username, email FROM demo_users WHERE id = $id";
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch();

        if ($id === '999' && $row) {
            $flag = xxr_challenge_flag();
        }
    } catch (PDOException $e) {
        // 【漏洞】直接显示错误信息
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>血池 · SQL 错误回显</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h2 class="text-gold">🩸 血池的回响</h2>
        <div class="xxr-narrative">
            <strong>📖 剧情：</strong> 万魔宗的血池会回响一切错误。当你失误时，错误信息会把所有秘密都说出来。
        </div>

        <div class="alert alert-info">
            <strong>💡 习道提示：</strong> 在 URL 参数 <code>id</code> 后添加单引号 <code>'</code> 触发 SQL 错误，观察错误信息。
        </div>

        <h4>🔍 弟子查询</h4>
        <form method="GET" class="mb-3">
            <div class="input-group">
                <span class="input-group-text">弟子ID：</span>
                <input type="text" name="id" class="form-control" value="<?= htmlspecialchars($_GET['id'] ?? '1') ?>" placeholder="试试输入单引号 ' ">
                <button class="xxr-btn xxr-btn-primary">查询</button>
            </div>
        </form>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <strong>SQL 错误：</strong><br>
                <code><?= htmlspecialchars($error) ?></code>
            </div>
        <?php endif; ?>

        <?php if ($flag): ?>
            <div class="alert alert-success">
                <strong>🎉 通关！</strong><br>
                Flag: <code class="xxr-mono"><?= htmlspecialchars($flag) ?></code>
            </div>
        <?php endif; ?>

        <div class="bg-dark-translucent p-3 rounded mt-3">
            <small class="text-muted">
                <strong>📚 教学说明：</strong>
                在真实的 SQL 错误回显中，攻击者可获取表名、字段名、SQL 语句结构。
                生产环境应关闭 <code>display_errors</code>，使用参数化查询。
            </small>
        </div>

        <div class="text-center mt-4">
            <a href="/challenge/WM-LQ-09" class="xxr-btn xxr-btn-secondary">← 返回关卡</a>
        </div>
    </div>
</body>
</html>