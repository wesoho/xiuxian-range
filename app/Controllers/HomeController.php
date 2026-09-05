<?php
declare(strict_types=1);

namespace XiuXian\Controllers;

use XiuXian\Models\Challenge;
use XiuXian\Models\Progress;
use XiuXian\Models\User;
use XiuXian\Services\LevelService;

/**
 * 首页 / 主页
 */
class HomeController
{
    /**
     * 首页（修真境界地图 + 三大宗门总览）
     */
    public function index(): void
    {
        $user = auth()->user();
        $stats = [
            'total_challenges' => Challenge::totalCount(),
            'total_users'      => User::totalCount(),
            'realm_name'       => $user ? render_realm($user['realm_level']) : '散修',
        ];

        // 修真境界地图数据
        $realms = [];
        foreach (LevelService::REALM_ORDER as $r) {
            $count = Challenge::totalCount($r);
            $realms[] = [
                'code'     => $r,
                'name'     => LevelService::REALM_NAMES[$r],
                'count'    => $count,
                'is_current' => $user && ($user['realm_level'] === $r),
            ];
        }

        view('home.index', [
            'user'    => $user,
            'stats'   => $stats,
            'realms'  => $realms,
            'announcement' => db()->fetchScalar("SELECT value FROM settings WHERE `key` = 'announcement'") ?: '',
            // 彩蛋：山门之上，云雾深处（?dao=1）
            'daoHidden' => isset($_GET['dao']),
        ]);
    }

    /**
     * 关于页
     */
    public function about(): void
    {
        view('home.about', ['user' => auth()->user()]);
    }
}