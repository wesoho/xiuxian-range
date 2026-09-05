<?php
declare(strict_types=1);

namespace XiuXian\Controllers;

use XiuXian\Models\User;
use XiuXian\Models\Progress;

/**
 * 排行榜
 */
class LeaderboardController
{
    /**
     * 综合排行榜（按积分）
     */
    public function index(): void
    {
        $users = User::leaderboard(50);

        // 获取每个用户的通关数
        foreach ($users as &$u) {
            $u['completed_count'] = Progress::completedCount((int) $u['id']);
        }
        unset($u);

        view('leaderboard.index', [
            'user'         => auth()->user(),
            'users'        => $users,
            // 全服横幅：近期有人飞升时挂喜报
            'ascension'    => User::recentAscension(7),
        ]);
    }

    /**
     * 宗门榜
     */
    public function bySect(): void
    {
        $rows = db()->fetchAll("
            SELECT u.sect,
                   COUNT(DISTINCT u.id) as user_count,
                   SUM(CASE WHEN p.status='completed' THEN 1 ELSE 0 END) as completed_count,
                   SUM(u.total_points) as total_points
            FROM users u
            LEFT JOIN progress p ON p.user_id = u.id
            WHERE u.role = 'user'
            GROUP BY u.sect
            ORDER BY total_points DESC
        ");

        view('leaderboard.sect', [
            'user' => auth()->user(),
            'rows' => $rows,
        ]);
    }
}