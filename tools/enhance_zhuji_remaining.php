<?php
/**
 * 修真靶场 - 补全修真期缺失的轮回宗/万魔宗/部分青云宗关卡
 * 上一轮 generate_challenges_v2.php 漏掉了 修真期剩余9关
 */

declare(strict_types=1);

$root = dirname(__DIR__) . '/public/challenges';

// 修真期剩余缺失的关卡
$missing = [
    // 轮回宗
    'lh' => [
        'lh_jz_05_sqli_union',
        'lh_jz_06_sqli_error',
        'lh_jz_07_sqli_bool',
        'lh_jz_13_file_read',
        'lh_jz_14_upload_js',
    ],
    // 万魔宗
    'wm' => [
        'wm_jz_08_sqli_time',
        'wm_jz_09_rce_basic',
        'wm_jz_10_csrf_post',
        'wm_jz_15_clickjack',
    ],
];

foreach ($missing as $sect => $dirNames) {
    $sectDir = $sect === 'lh' ? 'lunhuizong' : 'wanmozong';
    foreach ($dirNames as $dirName) {
        $dir = "$root/$sectDir/$dirName";
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
            echo "创建：$dirName\n";
        }
    }
}

echo "\n✅ 修真期缺失目录检查完成\n";