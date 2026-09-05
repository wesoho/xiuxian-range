<?php
/**
 * 修真靶场 - 本地开发 SQLite 数据库初始化
 *
 * 用途：在无 Docker / MySQL 的本机环境快速拉起可玩靶场。
 * 用法：php tools/init_sqlite_dev.php [数据库文件路径]
 * 默认：storage/xxr_dev.db（.env 中 DB_CONNECTION=sqlite + DB_DATABASE 指向此文件）
 *
 * 数据来源：database/init/01_schema.sql 对应的 SQLite 结构（与 tests/e2e.php 保持一致）、
 *          database/init/02_seed.sql（用户/徽章/演示用户/设置/100 关元数据）、
 *          database/init/03_hints.sql（三级提示）。
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$dbFile = $argv[1] ?? $root . '/storage/xxr_dev.db';

if (is_file($dbFile)) {
    unlink($dbFile);
    echo "已删除旧库: $dbFile\n";
}
$dir = dirname($dbFile);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$pdo = new PDO('sqlite:' . $dbFile, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec('PRAGMA foreign_keys = ON');

// ============ 1. 建表（SQLite 版，与 MySQL schema 字段对齐） ============
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
    ascended_at TEXT,
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

// ============ 1.5 彩蛋系统 / 趣味玩法（与 006_easter_eggs.sql 对齐） ============
$pdo->exec("CREATE TABLE easter_eggs (
    code TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
    hint TEXT,
    icon TEXT DEFAULT '🥚',
    secret TEXT UNIQUE,
    tier TEXT DEFAULT 'bronze',
    is_active INTEGER DEFAULT 1
)");
$pdo->exec("CREATE TABLE user_easter_eggs (
    user_id INTEGER NOT NULL,
    egg_code TEXT NOT NULL,
    earned_at TEXT DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, egg_code)
)");
$pdo->exec("CREATE TABLE user_slips (
    user_id INTEGER NOT NULL,
    slip_no INTEGER NOT NULL,
    earned_at TEXT DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, slip_no)
)");
$pdo->exec("CREATE TABLE fortune_draws (
    user_id INTEGER NOT NULL,
    draw_date TEXT NOT NULL,
    fortune_key TEXT NOT NULL,
    reward_points INTEGER DEFAULT 0,
    PRIMARY KEY (user_id, draw_date)
)");
$pdo->exec("CREATE TABLE cosmetics (
    code TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    type TEXT DEFAULT 'title',
    price INTEGER DEFAULT 0,
    icon TEXT DEFAULT '🎫',
    description TEXT,
    is_active INTEGER DEFAULT 1
)");
$pdo->exec("CREATE TABLE user_cosmetics (
    user_id INTEGER NOT NULL,
    cosmetic_code TEXT NOT NULL,
    equipped INTEGER DEFAULT 0,
    acquired_at TEXT DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, cosmetic_code)
)");
$pdo->exec("CREATE TABLE quiz_questions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT DEFAULT '综合',
    question TEXT NOT NULL,
    options TEXT NOT NULL,
    answer_idx INTEGER DEFAULT 0,
    explanation TEXT
)");
$pdo->exec("CREATE TABLE quiz_attempts (
    user_id INTEGER NOT NULL,
    quiz_date TEXT NOT NULL,
    score INTEGER DEFAULT 0,
    points_earned INTEGER DEFAULT 0,
    PRIMARY KEY (user_id, quiz_date)
)");
$pdo->exec("CREATE TABLE user_bounties (
    user_id INTEGER NOT NULL,
    bounty_date TEXT NOT NULL,
    bounty_key TEXT NOT NULL,
    claimed_at TEXT DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, bounty_date, bounty_key)
)");
$pdo->exec("CREATE TABLE user_counters (
    user_id INTEGER NOT NULL,
    counter_key TEXT NOT NULL,
    value INTEGER DEFAULT 0,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, counter_key)
)");
// 关卡彩蛋：丹房暗格《宗门秘史》（QY-JZ-03 UNION 注入彩蛋）
$pdo->exec("CREATE TABLE secret_manual (
    title TEXT PRIMARY KEY,
    content TEXT
)");

echo "✅ 数据表创建完成（21 张，含彩蛋系统）\n";

// ============ 2. 导入种子数据（SQL 语义切分，支持 '' 转义与多行 VALUES） ============

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

function importSeedFile(PDO $pdo, string $file): int
{
    if (!is_file($file)) {
        echo "⚠️ 跳过不存在的文件: $file\n";
        return 0;
    }
    $sql = file_get_contents($file);
    $rows = 0;
    foreach (splitSqlStatements($sql) as $stmt) {
        $lines = array_filter(explode("\n", $stmt), fn($l) => !str_starts_with(ltrim($l), '--'));
        $stmt = trim(implode("\n", $lines));
        if ($stmt === '' || !str_contains($stmt, 'INSERT INTO')) continue;
        if (str_starts_with($stmt, 'SET NAMES')) continue;
        $stmt = str_replace('`', '"', $stmt);
        try {
            $rows += $pdo->exec($stmt);
        } catch (PDOException $e) {
            echo "⚠️ 导入失败: " . substr($stmt, 0, 60) . "... → " . $e->getMessage() . "\n";
        }
    }
    return $rows;
}

$seedRows = importSeedFile($pdo, $root . '/database/init/02_seed.sql');
$hintRows = importSeedFile($pdo, $root . '/database/init/03_hints.sql');
$eggRows  = importSeedFile($pdo, $root . '/database/init/04_eggs.sql');
echo "✅ 种子数据导入: 02_seed.sql {$seedRows} 行, 03_hints.sql {$hintRows} 行, 04_eggs.sql {$eggRows} 行\n";

// ============ 2.5 关卡 Flag 随机化（防猜测 / 防仓库泄露） ============
// 关卡页面通过 app/bootstrap_challenge.php 的 xxr_challenge_flag() 动态渲染，
// 数据库中的随机值是唯一事实来源。
$challengeIds = $pdo->query('SELECT id FROM challenges WHERE enabled = 1')->fetchAll(PDO::FETCH_COLUMN);
$stmtFlag = $pdo->prepare('UPDATE challenges SET flag = ? WHERE id = ?');
foreach ($challengeIds as $cid) {
    $stmtFlag->execute(['flag{' . bin2hex(random_bytes(8)) . '}', $cid]);
}
echo "✅ 关卡 Flag 已随机化（" . count($challengeIds) . " 个，16 位随机 hex）\n";

// ============ 2.6 彩蛋口令随机化（防猜测；揭示点经 xxr_egg_secret() 动态渲染） ============
$eggCodes = $pdo->query('SELECT code FROM easter_eggs WHERE secret IS NOT NULL')->fetchAll(PDO::FETCH_COLUMN);
$stmtEgg = $pdo->prepare('UPDATE easter_eggs SET secret = ? WHERE code = ?');
$newSectSecret = '';
foreach ($eggCodes as $eggCode) {
    $newSecret = 'flag{egg_' . bin2hex(random_bytes(6)) . '}';
    $stmtEgg->execute([$newSecret, $eggCode]);
    if ($eggCode === 'egg_sect_secret') {
        $newSectSecret = $newSecret;
    }
}
// 同步丹房暗格《宗门秘史》中的口令（QY-JZ-03 UNION 注入彩蛋）
if ($newSectSecret !== '') {
    $pdo->exec("UPDATE secret_manual SET content = REPLACE(content, 'flag{egg_sect_manual}', " . $pdo->quote($newSectSecret) . ")");
}
echo "✅ 彩蛋口令已随机化（" . count($eggCodes) . " 个）\n";

// ============ 3. 校验 ============
$challenges = (int) $pdo->query('SELECT COUNT(*) FROM challenges')->fetchColumn();
$users = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$hints = (int) $pdo->query('SELECT COUNT(*) FROM hints')->fetchColumn();
$badges = (int) $pdo->query('SELECT COUNT(*) FROM badges')->fetchColumn();
$realms = $pdo->query("SELECT realm, COUNT(*) c FROM challenges GROUP BY realm ORDER BY c DESC")->fetchAll();

echo "\n数据库: $dbFile (" . round(filesize($dbFile) / 1024) . " KB)\n";
echo "  关卡: $challenges | 用户: $users | 提示: $hints | 徽章: $badges\n";
echo "  境界分布: ";
foreach ($realms as $r) {
    echo $r['realm'] . '=' . $r['c'] . ' ';
}
echo "\n\n🎉 本地开发库初始化完成！
⚠️ 注意：init 会清空全部进度。若服务正在运行，请重启：先 Ctrl+C 停掉，再重新执行 php -S 127.0.0.1:8686 -t public server.php\n";
echo "启动: php -S 127.0.0.1:8080 -t public server.php\n";
