<?php
/**
 * 修真靶场 · 端到端测试（v2 - 健壮版）
 *
 * 使用 PHP 手动建表，避开 SQL 语法转换陷阱
 */

declare(strict_types=1);

// 1. SQLite 数据库
$dbFile = __DIR__ . '/xxr_test.db';
if (file_exists($dbFile)) unlink($dbFile);

$pdo = new PDO("sqlite:{$dbFile}", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// 2. 手动建表（确保兼容）
$pdo->exec("CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE,
    password_hash TEXT NOT NULL,
    sect TEXT DEFAULT 'wanderer',
    realm_level TEXT DEFAULT 'liqi',
    realm_exp INTEGER DEFAULT 0,
    total_points INTEGER DEFAULT 0,
    title TEXT,
    role TEXT DEFAULT 'user',
    avatar TEXT,
    bio TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    last_login_at TEXT,
    last_login_ip TEXT
)");

$pdo->exec("CREATE TABLE challenges (
    id TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    sect TEXT NOT NULL,
    realm TEXT NOT NULL,
    difficulty INTEGER DEFAULT 1,
    category TEXT NOT NULL,
    narrative TEXT,
    description TEXT,
    learn_content TEXT,
    flag TEXT NOT NULL,
    points INTEGER DEFAULT 10,
    order_num INTEGER DEFAULT 0,
    prerequisites TEXT,
    source_viewable INTEGER DEFAULT 1,
    enabled INTEGER DEFAULT 1,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE progress (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    challenge_id TEXT NOT NULL,
    status TEXT DEFAULT 'locked',
    hints_used TEXT,
    attempts INTEGER DEFAULT 0,
    time_spent INTEGER DEFAULT 0,
    completed_at TEXT,
    points_earned INTEGER DEFAULT 0,
    writeup TEXT,
    started_at TEXT,
    UNIQUE(user_id, challenge_id)
)");

$pdo->exec("CREATE TABLE hints (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    challenge_id TEXT NOT NULL,
    level INTEGER NOT NULL,
    content TEXT NOT NULL,
    point_cost INTEGER DEFAULT 0,
    order_num INTEGER DEFAULT 0
)");

$pdo->exec("CREATE TABLE badges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    description TEXT,
    icon TEXT,
    realm TEXT,
    tier TEXT DEFAULT 'bronze',
    condition TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE user_badges (
    user_id INTEGER NOT NULL,
    badge_id INTEGER NOT NULL,
    earned_at TEXT DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, badge_id)
)");

$pdo->exec("CREATE TABLE writeups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    challenge_id TEXT NOT NULL,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    is_public INTEGER DEFAULT 0,
    likes INTEGER DEFAULT 0,
    views INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE challenge_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    challenge_id TEXT,
    action TEXT NOT NULL,
    detail TEXT,
    ip TEXT,
    user_agent TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE demo_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    email TEXT,
    role TEXT DEFAULT 'user',
    balance REAL DEFAULT 0,
    phone TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE settings (
    key TEXT PRIMARY KEY,
    value TEXT,
    description TEXT,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

// 3. 导入修真关卡元数据（SQLite 原生导入，正确处理 '' 转义与多行 VALUES）
$seed = file_get_contents(__DIR__ . '/../database/seeds/02_challenges.sql');

/**
 * 按 SQL 语义切分语句：仅在字符串（含 '' 与反斜杠转义）之外的 ';' 处切分
 */
function splitSqlStatements(string $sql): array
{
    $stmts = [];
    $cur = '';
    $len = strlen($sql);
    $i = 0;
    while ($i < $len) {
        $ch = $sql[$i];
        if ($ch === "'") {
            $cur .= $ch;
            $i++;
            while ($i < $len) {
                if ($sql[$i] === '\\' && $i + 1 < $len) {
                    $cur .= $sql[$i] . $sql[$i + 1];
                    $i += 2;
                    continue;
                }
                if ($sql[$i] === "'") {
                    if ($i + 1 < $len && $sql[$i + 1] === "'") {
                        $cur .= "''";
                        $i += 2;
                        continue;
                    }
                    $cur .= "'";
                    $i++;
                    break;
                }
                $cur .= $sql[$i];
                $i++;
            }
            continue;
        }
        if ($ch === ';') {
            $stmts[] = $cur;
            $cur = '';
            $i++;
            continue;
        }
        $cur .= $ch;
        $i++;
    }
    if (trim($cur) !== '') $stmts[] = $cur;
    return $stmts;
}

$totalInserted = 0;
foreach (splitSqlStatements($seed) as $stmt) {
    // 去掉注释行
    $lines = array_filter(explode("\n", $stmt), fn($l) => !str_starts_with(ltrim($l), '--'));
    $stmt = trim(implode("\n", $lines));
    if ($stmt === '' || !str_contains($stmt, 'INSERT INTO')) continue;
    // MySQL 专属语句跳过；反引号 → 双引号（SQLite 标识符引用）
    if (str_starts_with($stmt, 'SET NAMES')) continue;
    $stmt = str_replace('`', '"', $stmt);
    try {
        $totalInserted += $pdo->exec($stmt);
    } catch (PDOException $e) {
        echo "⚠️ 导入失败: " . $e->getMessage() . "\n";
    }
}
echo "✅ 修真关卡数据插入：{$totalInserted} 条\n\n";

// 4. 测试框架
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║          修真靶场 · 修真测试套件 v1.0                      ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

$passed = 0;
$failed = 0;
$warnings = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    try {
        $fn();
        echo "  [PASS] {$name}\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "  [FAIL] {$name}: " . $e->getMessage() . "\n";
        $failed++;
    }
}

function assert_true(bool $cond, string $msg = ''): void
{
    if (!$cond) throw new \Exception($msg ?: '断言失败');
}

function assert_eq($expected, $actual, string $msg = ''): void
{
    if ($expected !== $actual) {
        throw new \Exception($msg ?: "期望 " . var_export($expected, true) . ", 实际 " . var_export($actual, true));
    }
}

function warn(string $msg): void
{
    global $warnings;
    echo "  [WARN] {$msg}\n";
    $warnings++;
}

// ============ 测试用例 ============

echo "[1] 修真靶场数据库初始化\n";
test('数据库连接', function () use ($pdo) {
    $pdo->query('SELECT 1');
});

test('修真 users 表存在', function () use ($pdo) {
    $pdo->query('SELECT * FROM users LIMIT 1');
});

test('修真 challenges 表存在', function () use ($pdo) {
    $pdo->query('SELECT * FROM challenges LIMIT 1');
});

test('修真关卡数 = 100', function () use ($pdo) {
    $count = $pdo->query('SELECT COUNT(*) FROM challenges')->fetchColumn();
    assert_eq(100, $count);
});

echo "\n[2] 修真境界覆盖\n";
test('炼气期 10 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='liqi'")->fetchColumn();
    assert_eq(10, $count);
});
test('筑基期 15 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='zhuji'")->fetchColumn();
    assert_eq(15, $count);
});
test('金丹期 15 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='jindan'")->fetchColumn();
    assert_eq(15, $count);
});
test('元婴期 15 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='yuanying'")->fetchColumn();
    assert_eq(15, $count);
});
test('化神期 15 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='huashen'")->fetchColumn();
    assert_eq(15, $count);
});
test('炼虚期 10 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='lianxu'")->fetchColumn();
    assert_eq(10, $count);
});
test('合体期 10 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='heti'")->fetchColumn();
    assert_eq(10, $count);
});
test('大乘期 10 关', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE realm='dacheng'")->fetchColumn();
    assert_eq(10, $count);
});

echo "\n[3] Flag 一致性\n";
test('所有 Flag 非空', function () use ($pdo) {
    $empty = $pdo->query("SELECT COUNT(*) FROM challenges WHERE flag='' OR flag IS NULL")->fetchColumn();
    assert_eq(0, $empty);
});

test('所有 Flag 唯一', function () use ($pdo) {
    $dup = $pdo->query("SELECT flag, COUNT(*) c FROM challenges GROUP BY flag HAVING c>1")->fetchAll();
    assert_eq(0, count($dup));
});

test('所有 Flag 以 flag{ 开头', function () use ($pdo) {
    $bad = $pdo->query("SELECT COUNT(*) FROM challenges WHERE flag NOT LIKE 'flag{%}'")->fetchColumn();
    assert_eq(0, $bad);
});

echo "\n[4] 修真文件系统\n";
test('每个关卡目录都有 index.php', function () {
    $rows = $GLOBALS['pdo']->query("SELECT id FROM challenges WHERE enabled=1")->fetchAll(PDO::FETCH_COLUMN);
    $missing = [];
    foreach ($rows as $id) {
        $dirName = strtolower(str_replace('-', '_', $id));
        $found = glob(__DIR__ . "/../public/challenges/*/{$dirName}*");
        if (empty($found)) {
            $missing[] = $id;
        }
    }
    if ($missing) {
        throw new \Exception('缺失修真关卡文件: ' . implode(', ', array_slice($missing, 0, 5)));
    }
});

test('修真关卡数 = 文件系统关卡数', function () use ($pdo) {
    $dbCount = $pdo->query("SELECT COUNT(*) FROM challenges")->fetchColumn();
    $fileCount = 0;
    foreach (glob(__DIR__ . '/../public/challenges/*') as $sectDir) {
        foreach (glob($sectDir . '/*') as $chDir) {
            if (is_dir($chDir) && !empty(glob($chDir . '/index.php'))) {
                $fileCount++;
            }
        }
    }
    assert_eq($dbCount, $fileCount);
});

echo "\n[5] 修真关卡修真叙事质量\n";
test('炼气期10关全部有修真叙事', function () {
    $liqi_ids = ['QY-LQ-01','QY-LQ-02','QY-LQ-03','QY-LQ-04','QY-LQ-05',
                  'LH-LQ-06','LH-LQ-07','LH-LQ-08',
                  'WM-LQ-09','WM-LQ-10'];
    foreach ($liqi_ids as $id) {
        $dirName = strtolower(str_replace('-', '_', $id));
        $found = glob(__DIR__ . "/../public/challenges/*/{$dirName}*");
        if (empty($found)) continue;
        $content = file_get_contents($found[0] . '/index.php');
        if (!str_contains($content, '📖') && !str_contains($content, '剧情')) {
            throw new \Exception("关卡 {$id} 修真叙事缺失");
        }
    }
});

test('每个 vulnerable.php 都包含【漏洞】标记', function () {
    $noVul = [];
    foreach (glob(__DIR__ . '/../public/challenges/*/*/vulnerable.php') as $f) {
        if (!str_contains(file_get_contents($f), '漏洞') && !str_contains(file_get_contents($f), 'Vulnerability')) {
            $noVul[] = $f;
        }
    }
    if ($noVul) throw new \Exception('缺失漏洞标记: ' . basename(dirname($noVul[0])));
});

test('每个 secure.php 都包含【修复/安全】标记', function () {
    $noFix = [];
    foreach (glob(__DIR__ . '/../public/challenges/*/*/secure.php') as $f) {
        $content = file_get_contents($f);
        if (!str_contains($content, '修复') && !str_contains($content, '安全')) {
            $noFix[] = $f;
        }
    }
    if ($noFix) throw new \Exception('缺失修复标记: ' . basename(dirname($noFix[0])));
});

echo "\n[6] 修真关卡修真宗门分类\n";
test('青云宗关卡存在', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE sect='qiingong'")->fetchColumn();
    assert_true($count > 30, "青云宗关卡数：{$count}");
});
test('轮回宗关卡存在', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE sect='lunhuizong'")->fetchColumn();
    assert_true($count > 25, "轮回宗关卡数：{$count}");
});
test('万魔宗关卡存在', function () use ($pdo) {
    $count = $pdo->query("SELECT COUNT(*) FROM challenges WHERE sect='wanmozong'")->fetchColumn();
    assert_true($count > 25, "万魔宗关卡数：{$count}");
});

echo "\n[7] 修真演示用户表\n";
test('demo_users 表存在（关卡内部需要）', function () use ($pdo) {
    $pdo->query('SELECT * FROM demo_users LIMIT 1');
});

// ============ 总结 ============
echo "\n╔═══════════════════════════════════════════════════════════╗\n";
echo sprintf("║  通过: %-3d  失败: %-3d  警告: %-3d                      ║\n", $passed, $failed, $warnings);
echo "╚═══════════════════════════════════════════════════════════╝\n";

exit($failed > 0 ? 1 : 0);