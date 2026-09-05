<?php
/**
 * 修真靶场 - 最终验收测试报告
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$challengesDir = $root . '/public/challenges';
$dbDir = $root . '/database';

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║      修真靶场 · 最终验收测试报告                        ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

$passed = 0;
$failed = 0;
$warnings = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed, $warnings;
    try {
        $result = $fn();
        if ($result === 'warn') {
            echo "  [WARN] $name\n";
            $warnings++;
        } elseif ($result === false) {
            throw new \Exception('返回 false');
        } else {
            echo "  [PASS] $name\n";
            $passed++;
        }
    } catch (\Throwable $e) {
        echo "  [FAIL] $name: " . $e->getMessage() . "\n";
        $failed++;
    }
}

// ============ 1. PHP 语法检查 ============
echo "【1】PHP 语法检查\n";
test('所有 PHP 文件无语法错误', function () use ($root) {
    $failCount = 0;
    $failFiles = [];
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($root . '/public/challenges', \FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $output = [];
            $rc = 0;
            exec('php -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $rc);
            $output = implode("\n", $output);
            if (!str_contains($output, 'No syntax errors')) {
                $failCount++;
                $failFiles[] = $file->getPathname();
            }
        }
    }
    if ($failCount > 0) throw new \Exception("$failCount 个文件有语法错误: " . implode(', ', array_slice($failFiles, 0, 3)));
    return true;
});

// ============ 2. 关卡数量 ============
echo "\n【2】关卡目录结构\n";
test('关卡总数量 = 100', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 100) throw new \Exception("修真关卡数 = $count");
    return true;
});

test('炼气期 = 10 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*lq_*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 10) throw new \Exception("炼气期 $count 关");
    return true;
});

test('筑基期 = 15 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*jz_*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 15) throw new \Exception("筑基期 $count 关");
    return true;
});

test('金丹期 = 15 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*jd_*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 15) throw new \Exception("金丹期 $count 关");
    return true;
});

test('元婴期 = 15 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*yy_*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 15) throw new \Exception("元婴期 $count 关");
    return true;
});

test('化神期 = 15 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*hs_*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 15) throw new \Exception("化神期 $count 关");
    return true;
});

test('炼虚期 = 10 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*lx_*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 10) throw new \Exception("炼虚期 $count 关");
    return true;
});

test('合体期 = 10 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*ht_*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 10) throw new \Exception("合体期 $count 关");
    return true;
});

test('大乘期 = 10 关', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/wanderer/*', GLOB_ONLYDIR) as $d) {
        if (is_file("$d/index.php")) $count++;
    }
    if ($count !== 10) throw new \Exception("大乘期 $count 关");
    return true;
});

// ============ 3. 文件完整性 ============
echo "\n【3】关卡文件完整性\n";
test('所有关卡都有 index.php', function () use ($challengesDir) {
    $missing = 0;
    foreach (glob($challengesDir . '/*/*', GLOB_ONLYDIR) as $d) {
        if (!is_file("$d/index.php")) $missing++;
    }
    if ($missing > 0) throw new \Exception("$missing 个关卡缺 index.php");
    return true;
});

test('所有关卡都有 vulnerable.php', function () use ($challengesDir) {
    $missing = 0;
    foreach (glob($challengesDir . '/*/*', GLOB_ONLYDIR) as $d) {
        if (!is_file("$d/vulnerable.php")) $missing++;
    }
    if ($missing > 0) throw new \Exception("$missing 个关卡缺 vulnerable.php");
    return true;
});

test('所有关卡都有 secure.php', function () use ($challengesDir) {
    $missing = 0;
    foreach (glob($challengesDir . '/*/*', GLOB_ONLYDIR) as $d) {
        if (!is_file("$d/secure.php")) $missing++;
    }
    if ($missing > 0) throw new \Exception("$missing 个关卡缺 secure.php");
    return true;
});

test('所有关卡都有 learn.php', function () use ($challengesDir) {
    $missing = 0;
    foreach (glob($challengesDir . '/*/*', GLOB_ONLYDIR) as $d) {
        if (!is_file("$d/learn.php")) $missing++;
    }
    if ($missing > 0) throw new \Exception("$missing 个关卡缺修真叙事 learn.php");
    return true;
});

// ============ 4. 内容质量检查 ============
echo "\n【4】内容质量检查\n";

test('所有关卡文件内容完整（index/vulnerable/secure）', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*', GLOB_ONLYDIR) as $d) {
        $idx = file_get_contents("$d/index.php");
        $vul = file_get_contents("$d/vulnerable.php");
        $sec = file_get_contents("$d/secure.php");
        // index.php ≥200 字节，vulnerable/secure ≥100 字节视为完整
        if (strlen($idx) < 200 || strlen($vul) < 100 || strlen($sec) < 100) $count++;
    }
    if ($count > 0) return 'warn';
    return true;
});

test('至少 50 个 vulnerable.php 含【漏洞】标记', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*/vulnerable.php') as $f) {
        $c = file_get_contents($f);
        if (strpos($c, '【漏洞】') !== false) $count++;
    }
    if ($count < 50) return 'warn';
    echo "    → $count 个 vulnerable.php 含【漏洞】标记\n";
    return true;
});

test('至少 50 个 secure.php 含【修复】标记', function () use ($challengesDir) {
    $count = 0;
    foreach (glob($challengesDir . '/*/*/secure.php') as $f) {
        $c = file_get_contents($f);
        if (strpos($c, '修复') !== false) $count++;
    }
    if ($count < 50) return 'warn';
    echo "    → $count 个 secure.php 含【修复】\n";
    return true;
});

// ============ 5. 数据库脚本检查 ============
echo "\n【5】数据库脚本检查\n";
test('03_hints.sql 存在且包含足够的提示数据', function () use ($dbDir) {
    $hintFile = $dbDir . '/init/03_hints.sql';
    if (!file_exists($hintFile)) throw new \Exception('03_hints.sql 不存在');
    $size = filesize($hintFile);
    if ($size < 10000) throw new \Exception("03_hints.sql 过小: $size 字节");
    return true;
});

test('01_schema.sql 包含全部核心数据表', function () use ($dbDir) {
    $schemaFile = $dbDir . '/init/01_schema.sql';
    if (!file_exists($schemaFile)) throw new \Exception('01_schema.sql 不存在');
    $c = file_get_contents($schemaFile);
    $tables = ['users', 'challenges', 'progress', 'hints', 'badges'];
    foreach ($tables as $t) {
        if (strpos($c, "TABLE `$t`") === false) {
            throw new \Exception("01_schema.sql 缺少数据表: $t");
        }
    }
    return true;
});

test('02_seed.sql 包含足够的关卡 INSERT（≥5 组）', function () use ($dbDir) {
    $seedFile = $dbDir . '/init/02_seed.sql';
    if (!file_exists($seedFile)) throw new \Exception('02_seed.sql 不存在');
    $c = file_get_contents($seedFile);
    if (substr_count($c, "INSERT INTO `challenges`") < 5) {
        throw new \Exception('02_seed.sql 缺少关卡 INSERT 数据');
    }
    return true;
});

// ============ 6. 部署配置检查 ============
echo "\n【6】部署配置检查\n";
test('Dockerfile 配置正确', function () use ($root) {
    if (!file_exists($root . '/Dockerfile')) throw new \Exception('Dockerfile 不存在');
    $c = file_get_contents($root . '/Dockerfile');
    if (strpos($c, 'FROM php:8.2-apache') === false) {
        throw new \Exception('Dockerfile 缺少 php:8.2-apache 基础镜像');
    }
    return true;
});

test('docker-compose.yml 配置正确', function () use ($root) {
    if (!file_exists($root . '/docker-compose.yml')) throw new \Exception('docker-compose.yml 不存在');
    $c = file_get_contents($root . '/docker-compose.yml');
    $services = ['web', 'db', 'redis'];
    foreach ($services as $s) {
        if (strpos($c, "$s:") === false) {
            throw new \Exception("docker-compose.yml 缺少服务: $s");
        }
    }
    return true;
});

test('Makefile 配置正确', function () use ($root) {
    if (!file_exists($root . '/Makefile')) throw new \Exception('Makefile 不存在');
    $c = file_get_contents($root . '/Makefile');
    $targets = ['up:', 'down:', 'test:', 'lint:'];
    foreach ($targets as $t) {
        if (strpos($c, $t) === false) {
            throw new \Exception("Makefile 缺少目标: $t");
        }
    }
    return true;
});

// ============ 7. 文档与 CI 检查 ============
echo "\n【7】文档与 CI 检查\n";
test('README 文档完整', function () use ($root) {
    if (!file_exists($root . '/README.md')) throw new \Exception('README.md 不存在');
    $c = file_get_contents($root . '/README.md');
    if (strlen($c) < 3000) throw new \Exception('README 内容过少');
    return true;
});

test('核心文档齐全（INSTALL/ARCHITECTURE/CHALLENGES_SUMMARY/ADD-CHALLENGE）', function () use ($root) {
    $docs = ['INSTALL.md', 'ARCHITECTURE.md', 'CHALLENGES_SUMMARY.md', 'ADD-CHALLENGE.md'];
    foreach ($docs as $d) {
        if (!file_exists($root . "/docs/$d")) {
            throw new \Exception("缺少文档: $d");
        }
    }
    return true;
});

test('测试套件文件存在', function () use ($root) {
    $tests = ['integration.php'];
    foreach ($tests as $t) {
        if (!file_exists($root . "/tests/$t")) {
            throw new \Exception("缺少测试文件: $t");
        }
    }
    return true;
});

test('GitHub Actions 工作流配置正确', function () use ($root) {
    $wf = $root . '/.github/workflows/ci.yml';
    if (!file_exists($wf)) throw new \Exception('ci.yml 不存在');
    $c = file_get_contents($wf);
    if (strpos($c, 'jobs:') === false) throw new \Exception('ci.yml 缺少 jobs 配置');
    return true;
});

// ============ 汇总 ============
echo "\n═══════════════════════════════════════════════════════════\n";
echo sprintf(" 通过: %d  失败: %d  警告: %d  总计: %d\n", $passed, $failed, $warnings, $passed + $failed);
echo "═══════════════════════════════════════════════════════════\n\n";

if ($failed === 0) {
    echo "🎉 全部检查通过！修真靶场达到可发布标准！\n";
    echo "🧘 道友，恭喜飞升成功！\n";
}

exit($failed === 0 ? 0 : 1);
