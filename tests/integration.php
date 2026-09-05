<?php
/**
 * 修真靶场 · 集成测试套件
 *
 * 使用 SQLite 内存数据库 + PHP 模拟 HTTP 请求，测试修真靶场全流程
 */

declare(strict_types=1);

// ============ 1. 修真靶场模式初始化 ============

// 覆盖 config 使用 SQLite
require dirname(__DIR__) . '/app/Helpers/functions.php';

// 重写 config 函数
function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $config = [
            'app' => [
                'name'    => '修真靶场测试',
                'env'     => 'testing',
                'debug'   => true,
                'key'     => 'test',
                'url'     => 'http://localhost',
                'timezone'=> 'Asia/Shanghai',
            ],
            'db' => [
                'host'     => 'sqlite',
                'database' => ':memory:',
                'username' => '',
                'password' => '',
                'charset'  => 'utf8mb4',
            ],
            'redis' => ['host' => 'localhost', 'port' => 6379],
            'session' => ['lifetime' => 7200, 'path' => sys_get_temp_dir() . '/xxr_session'],
            'paths' => [
                'base'      => dirname(__DIR__),
                'app'       => dirname(__DIR__) . '/app',
                'public'    => dirname(__DIR__) . '/public',
                'storage'   => dirname(__DIR__) . '/storage',
                'logs'      => dirname(__DIR__) . '/storage/logs',
                'cache'     => dirname(__DIR__) . '/storage/cache',
                'challenges'=> dirname(__DIR__) . '/challenges',
                'views'     => dirname(__DIR__) . '/app/Views',
            ],
        ];
    }
    $parts = explode('.', $key);
    $value = $config;
    foreach ($parts as $p) {
        if (!is_array($value) || !array_key_exists($p, $value)) return $default;
        $value = $value[$p];
    }
    return $value;
}

require dirname(__DIR__) . '/app/Helpers/security.php';
require dirname(__DIR__) . '/app/Helpers/response.php';

spl_autoload_register(function ($class) {
    $prefix = 'XiuXian\\';
    if (!str_starts_with($class, $prefix)) return;
    $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) require $file;
});

// ============ 2. SQLite 修补 ============

// 使用 SQLite 替换 MySQL
class SqliteDatabase extends \XiuXian\Core\Database
{
    public function pdo(): PDO
    {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = new PDO('sqlite::memory:', null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $this->initSchema($pdo);
        }
        return $pdo;
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        if (stripos($sql, 'LIMIT') === false && stripos($sql, 'SELECT') !== false) {
            $sql .= ' LIMIT 1';
        }
        return parent::fetchOne($sql, $params);
    }

    private function initSchema(PDO $pdo): void
    {
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
            created_at TEXT DEFAULT (datetime('now')),
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
            created_at TEXT DEFAULT (datetime('now'))
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
            created_at TEXT DEFAULT (datetime('now'))
        )");

        $pdo->exec("CREATE TABLE user_badges (
            user_id INTEGER NOT NULL,
            badge_id INTEGER NOT NULL,
            earned_at TEXT DEFAULT (datetime('now')),
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
            created_at TEXT DEFAULT (datetime('now')),
            updated_at TEXT DEFAULT (datetime('now'))
        )");

        $pdo->exec("CREATE TABLE challenge_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            challenge_id TEXT,
            action TEXT NOT NULL,
            detail TEXT,
            ip TEXT,
            user_agent TEXT,
            created_at TEXT DEFAULT (datetime('now'))
        )");

        $pdo->exec("CREATE TABLE demo_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            password TEXT NOT NULL,
            email TEXT,
            role TEXT DEFAULT 'user',
            balance REAL DEFAULT 0,
            phone TEXT,
            created_at TEXT DEFAULT (datetime('now'))
        )");

        $pdo->exec("CREATE TABLE settings (
            key TEXT PRIMARY KEY,
            value TEXT,
            description TEXT,
            updated_at TEXT DEFAULT (datetime('now'))
        )");

        // 插入演示数据
        $pdo->exec("INSERT INTO demo_users (username, password, email, role, balance) VALUES
            ('admin', 'admin', 'admin@example.com', 'admin', 10000),
            ('user1', '123456', 'user1@example.com', 'user', 100),
            ('zhangsan', '123456', 'zhangsan@example.com', 'user', 1000),
            ('lisi', '123456', 'lisi@example.com', 'user', 500)");

        // 插入修真境界关卡元数据（精简版）
        $stmt = $pdo->prepare("INSERT INTO challenges (id, title, sect, realm, difficulty, category, narrative, description, flag, points, order_num, enabled)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $challenges = [
            ['QY-LQ-01', '【青云宗·炼气】藏经阁的注释', 'qiingong', 'liqi', 1, 'info_leak',
             '藏经阁的网页源码中意外泄露了隐藏信息', 'HTML 注释泄露', 'flag{html_comment_leak_01}', 10, 1, 1],
            ['QY-JZ-03', '【青云宗·筑基】丹房的数字谜题', 'qiingong', 'zhuji', 2, 'sqli_numeric',
             '丹房有座石碑会显示丹药品级', '数字型 SQL 注入', 'flag{sqli_numeric_or_13}', 15, 13, 1],
            ['LH-LQ-06', '【轮回宗·炼气】最弱口令', 'lunhuizong', 'liqi', 1, 'weak_password',
             '轮回殿门口有守卫', '弱口令登录', 'flag{weak_default_password_06}', 10, 6, 1],
            ['WM-LQ-09', '【万魔宗·炼气】血池的回响', 'wanmozong', 'liqi', 1, 'sqli_error',
             '血池会回响错误', 'SQL 错误回显', 'flag{sql_error_disclosure_09}', 10, 9, 1],
        ];
        foreach ($challenges as $c) {
            $stmt->execute($c);
        }

        // 插入提示
        $stmt = $pdo->prepare("INSERT INTO hints (challenge_id, level, content, point_cost) VALUES (?, ?, ?, ?)");
        $stmt->execute(['QY-LQ-01', 1, '查看页面源代码，留意 HTML 注释标记', 0]);
        $stmt->execute(['QY-LQ-01', 2, '在浏览器中按 Ctrl+U 查看页面源代码', 2]);
        $stmt->execute(['QY-LQ-01', 3, '直接查看源码即可找到 Flag', 0]);

        // 插入设置
        $pdo->exec("INSERT INTO settings (key, value, description) VALUES
            ('site_name', '修真靶场测试', '站点名称'),
            ('announcement', '修真靶场集成测试', '公告')");
    }
}

function db(): SqliteDatabase
{
    static $db = null;
    return $db ??= new SqliteDatabase(config('db'));
}

// 修补 User 模型避免 ENUM 报错
require_once dirname(__DIR__) . '/app/Models/User.php';

// ============ 3. 测试运行 ============

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║          修真靶场 · 集成测试套件 v1.0                      ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

$passed = 0;
$failed = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    try {
        $fn();
        echo "  ✅ $name\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "  ❌ $name：" . $e->getMessage() . "\n";
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

// ====== 测试 1：数据库连接与表创建 ======
echo "【1】数据库初始化...\n";
test('数据库连接成功', function () {
    $db = db();
    $stmt = $db->pdo()->query('SELECT 1');
    assert_eq(1, $stmt->fetchColumn());
});

test('users 表可查询', function () {
    $db = db();
    $row = $db->fetchOne('SELECT * FROM demo_users WHERE username = ?', ['admin']);
    assert_eq('admin', $row['username']);
});

test('challenges 表已有修真境界关卡', function () {
    $count = db()->fetchScalar('SELECT COUNT(*) FROM challenges');
    assert_true($count >= 4, "challenges 表至少 4 条记录");
});

// ====== 测试 2：用户注册流程 ======
echo "\n【2】用户注册...\n";
test('创建新用户', function () {
    $stmt = db()->pdo()->prepare(
        "INSERT INTO users (username, password_hash, sect, realm_level, title) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute(['test_user', password_hash('pass123', PASSWORD_DEFAULT), 'qiingong', 'liqi', '炼气小修']);
    assert_true($stmt->rowCount() > 0);
});

test('查询新用户', function () {
    $user = db()->fetchOne('SELECT * FROM users WHERE username = ?', ['test_user']);
    assert_eq('test_user', $user['username']);
    assert_eq('qiingong', $user['sect']);
});

// ====== 测试 3：密码校验 ======
echo "\n【3】密码校验...\n";
test('密码哈希可被验证', function () {
    $user = db()->fetchOne('SELECT * FROM users WHERE username = ?', ['test_user']);
    assert_true(password_verify('pass123', $user['password_hash']));
    assert_true(!password_verify('wrong', $user['password_hash']));
});

// ====== 测试 4：关卡 Flag 验证 ======
echo "\n【4】Flag 验证...\n";
test('QY-LQ-01 Flag 正确', function () {
    $row = db()->fetchOne('SELECT * FROM challenges WHERE id = ?', ['QY-LQ-01']);
    assert_eq('flag{html_comment_leak_01}', $row['flag']);
});

test('QY-JZ-03 Flag 正确', function () {
    $row = db()->fetchOne('SELECT * FROM challenges WHERE id = ?', ['QY-JZ-03']);
    assert_eq('flag{sqli_numeric_or_13}', $row['flag']);
});

// ====== 测试 5：修真境界系统 ======
echo "\n【5】修真境界系统...\n";
test('炼气期下一境界是筑基', function () {
    assert_eq('zhuji', \XiuXian\Services\LevelService::nextRealm('liqi'));
});

test('大乘期无下一境界', function () {
    assert_eq(null, \XiuXian\Services\LevelService::nextRealm('dacheng'));
});

test('境界晋升以通关全部关卡为准', function () {
    db()->execute("INSERT INTO users (username, password_hash, sect, realm_level) VALUES ('realmuser', 'x', 'qiingong', 'liqi')");
    $uid = (int) db()->fetchScalar("SELECT id FROM users WHERE username = 'realmuser'");

    // 只通关 2/3 炼气关 → 不满进度
    foreach (['QY-LQ-01', 'LH-LQ-06'] as $cid) {
        db()->execute("INSERT INTO progress (user_id, challenge_id, status) VALUES ($uid, '$cid', 'completed')");
    }
    $p = \XiuXian\Services\LevelService::realmProgress($uid, 'liqi');
    assert_eq(2, $p['done']);
    assert_eq(3, $p['total']);
    if ($p['percent'] >= 100) throw new Exception('未通关全部关卡不应满进度');

    // 通关全部 → 100%
    db()->execute("INSERT INTO progress (user_id, challenge_id, status) VALUES ($uid, 'WM-LQ-09', 'completed')");
    $p = \XiuXian\Services\LevelService::realmProgress($uid, 'liqi');
    assert_eq(3, $p['done']);
    assert_eq(100, $p['percent']);
});

// ====== 测试 6：提示数据 ======
echo "\n【6】三级提示...\n";
test('QY-LQ-01 有 3 级提示', function () {
    $hints = db()->fetchAll('SELECT * FROM hints WHERE challenge_id = ?', ['QY-LQ-01']);
    assert_eq(3, count($hints));
});

// ====== 测试 7：进度表 ======
echo "\n【7】关卡进度...\n";
test('记录用户进度', function () {
    $userId = db()->fetchScalar('SELECT id FROM users WHERE username = ?', ['test_user']);
    db()->execute(
        'INSERT INTO progress (user_id, challenge_id, status, completed_at, points_earned) VALUES (?, ?, ?, ?, ?)',
        [$userId, 'QY-LQ-01', 'completed', date('Y-m-d H:i:s'), 10]
    );
    $progress = db()->fetchOne('SELECT * FROM progress WHERE user_id = ? AND challenge_id = ?', [$userId, 'QY-LQ-01']);
    assert_eq('completed', $progress['status']);
});

test('同一用户同一关卡只能有一条进度（unique约束）', function () {
    $userId = db()->fetchScalar('SELECT id FROM users WHERE username = ?', ['test_user']);
    try {
        db()->execute(
            'INSERT INTO progress (user_id, challenge_id, status) VALUES (?, ?, ?)',
            [$userId, 'QY-LQ-01', 'completed']
        );
        throw new \Exception('unique约束未生效');
    } catch (\PDOException $e) {
        assert_true(str_contains($e->getMessage(), 'UNIQUE') || str_contains($e->getMessage(), 'constraint'));
    }
});

// ====== 测试 8：CSRF Token ======
echo "\n【8】CSRF Token...\n";
test('Token 生成稳定', function () {
    $_SESSION = [];
    $t1 = \XiuXian\Core\Csrf::token();
    $t2 = \XiuXian\Core\Csrf::token();
    assert_eq($t1, $t2);
    assert_eq(64, strlen($t1));
});

// ====== 测试 9：路由优先级 ======
echo "\n【9】路由优先级...\n";
test('静态路由优先于参数化路由', function () {
    $router = new \XiuXian\Core\Router();
    $captured = null;
    $router->get('/challenge/{id}', function ($params) use (&$captured) {
        $captured = 'param:' . ($params['id'] ?? '');
    });
    $router->post('/challenge/submit-flag', function () use (&$captured) {
        $captured = 'static';
    });

    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/challenge/submit-flag';
    ob_start();
    try { $router->dispatch(); } catch (\Throwable $e) { ob_end_clean(); }
    ob_end_clean();
    assert_eq('static', $captured);
});

// ====== 总结 ======
echo "\n╔═══════════════════════════════════════════════════════════╗\n";
echo sprintf("║  通过: %-3d  失败: %-3d  总计: %-3d                    ║\n", $passed, $failed, $passed + $failed);
echo "╚═══════════════════════════════════════════════════════════╝\n";

exit($failed > 0 ? 1 : 0);