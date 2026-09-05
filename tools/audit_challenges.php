<?php
/**
 * 修真靶场 - 关卡可通性审计
 *
 * 逐关检查：
 *   1. Flag 揭示路径：动态渲染 / 残留旧种子值 / 完全未揭示
 *   2. 目录可达性（fight 重定向的目标目录是否存在）
 *   3. 解锁链完整性（每境界 order_num 是否从 1 连续编号）
 *   4. Flag 唯一性 / 静态残留扫描
 *
 * 用法：php tools/audit_challenges.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/app/bootstrap_challenge.php';

$db = db();
$problems = [];

// ------------------------------------------------------------
// 1. 旧种子 Flag 映射（从历史测试库读取，用于定位「残留旧值」）
// ------------------------------------------------------------
$oldFlags = [];
foreach ([$root . '/tests/xxr_test.db', $root . '/xxr_test.db'] as $tf) {
    if (is_file($tf)) {
        try {
            $t = new PDO('sqlite:' . $tf, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            foreach ($t->query('SELECT id, flag FROM challenges') as $r) {
                $oldFlags[$r['id']] = $r['flag'];
            }
            if ($oldFlags) {
                break;
            }
        } catch (Throwable $e) {
            // 换下一个候选库
        }
    }
}
echo '旧种子 Flag 映射: ' . count($oldFlags) . " 条\n";

// ------------------------------------------------------------
// 2. 关卡清单与结构检查
// ------------------------------------------------------------
$challenges = $db->fetchAll('SELECT id, flag, realm, sect, order_num, enabled FROM challenges ORDER BY realm, order_num');
echo '数据库关卡: ' . count($challenges) . " 个\n";

// Flag 唯一性
$flagSeen = [];
foreach ($challenges as $c) {
    if (isset($flagSeen[$c['flag']])) {
        $problems[] = "[FLAG重复] {$c['id']} 与 {$flagSeen[$c['flag']]} 共用同一 Flag";
    }
    $flagSeen[$c['flag']] = $c['id'];
}

// order_num 连续性（每境界 1..N 连续，否则解锁链断裂）
$realmOrders = [];
foreach ($challenges as $c) {
    $realmOrders[$c['realm']][] = (int) $c['order_num'];
}
foreach ($realmOrders as $realm => $orders) {
    sort($orders);
    // order_num 为全服连续编号（炼气 1-10、筑基 11-25……），
    // 解锁链只要求「境界内连续」，不要求从 1 开始
    $expect = range($orders[0], $orders[0] + count($orders) - 1);
    if ($orders !== $expect) {
        $problems[] = '[解锁链断裂] 境界 ' . $realm . ' order_num 不连续: ' . implode(',', $orders);
    }
}

// ------------------------------------------------------------
// 3. 逐关目录扫描（Flag 揭示路径）
// ------------------------------------------------------------
$report = [];
foreach ($challenges as $c) {
    $id = $c['id'];
    $prefix = strtolower(str_replace('-', '_', $id));
    $dirs = glob($root . '/public/challenges/*/' . $prefix . '*', GLOB_ONLYDIR);
    $dir = $dirs[0] ?? null;

    if (!$dir) {
        $report[$id] = 'NO-DIR';
        $problems[] = "[无试炼目录] {$id}：fight 将 404，无法进入试炼";
        continue;
    }

    // 递归收集目录内所有文件内容（.git 等压缩内容 grep 不到明文，属已知盲区）
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    $hasDynamic = false;
    $hasOldFlag = false;
    $staticFlags = [];
    $fileCount = 0;
    foreach ($rii as $file) {
        if (!$file->isFile() || $file->getSize() > 512 * 1024) {
            continue;
        }
        $content = @file_get_contents($file->getPathname());
        if ($content === false) {
            continue;
        }
        $fileCount++;
        if (str_contains($content, 'xxr_challenge_flag') || str_contains($content, 'xxr_flag_reveal')) {
            $hasDynamic = true;
        }
        $old = $oldFlags[$id] ?? null;
        if ($old && str_contains($content, $old)) {
            $hasOldFlag = true;
        }
        if (preg_match_all('/flag\{([a-z0-9_]{4,40})\}/i', $content, $m)) {
            foreach ($m[1] as $name) {
                if (!str_starts_with($name, 'egg_')) {
                    $staticFlags[] = 'flag{' . $name . '}';
                }
            }
        }
    }

    if ($hasOldFlag) {
        $report[$id] = 'STALE';
        $problems[] = "[残留旧Flag] {$id}：页面仍展示旧种子值（现库为 {$c['flag']}），无法通关";
    } elseif ($hasDynamic) {
        $report[$id] = 'DYNAMIC';
    } elseif ($staticFlags) {
        $report[$id] = 'STATIC?';
        $problems[] = "[静态Flag待核] {$id}：含 " . implode(',', array_unique($staticFlags)) . '，但无动态渲染';
    } else {
        $report[$id] = 'NO-REVEAL';
    }
}

// ------------------------------------------------------------
// 4. 汇总
// ------------------------------------------------------------
$dist = array_count_values($report);
echo "\n==== 揭示路径分布 ====\n";
foreach (['DYNAMIC' => '动态渲染（可通关）', 'NO-REVEAL' => '未发现揭示路径（需人工核查）', 'STALE' => '残留旧值（不可通关）', 'STATIC?' => '静态Flag待核', 'NO-DIR' => '无目录'] as $k => $label) {
    echo str_pad($k, 10), ' = ', ($dist[$k] ?? 0), "  ({$label})\n";
}

echo "\n==== NO-REVEAL / STATIC? 关卡清单 ====\n";
foreach ($report as $id => $status) {
    if (in_array($status, ['NO-REVEAL', 'STATIC?'], true)) {
        echo str_pad($status, 10), "  {$id}\n";
    }
}

echo "\n==== 结构问题 ====\n";
echo $problems ? implode("\n", $problems) . "\n" : "无\n";
