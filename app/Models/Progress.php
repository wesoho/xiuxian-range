<?php
declare(strict_types=1);

namespace XiuXian\Models;

use XiuXian\Core\Database;

/**
 * 用户闯关进度
 */
class Progress
{
    /**
     * 获取用户对某关卡的进度
     */
    public static function get(int $userId, string $challengeId): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM progress WHERE user_id = ? AND challenge_id = ?',
            [$userId, $challengeId]
        );
    }

    /**
     * 获取用户全部关卡进度（带关卡信息）
     */
    public static function listByUser(int $userId): array
    {
        return db()->fetchAll(
            'SELECT p.*, c.title, c.sect, c.realm, c.difficulty, c.points, c.category
             FROM progress p
             LEFT JOIN challenges c ON c.id = p.challenge_id
             WHERE p.user_id = ?
             ORDER BY c.order_num',
            [$userId]
        );
    }

    /**
     * 获取用户已通关的关卡 ID
     */
    public static function completedIds(int $userId): array
    {
        $rows = db()->fetchAll(
            'SELECT challenge_id FROM progress WHERE user_id = ? AND status = ?',
            [$userId, 'completed']
        );
        return array_column($rows, 'challenge_id');
    }

    /**
     * 开始关卡（标记 in_progress）
     */
    public static function start(int $userId, string $challengeId): void
    {
        $existing = self::get($userId, $challengeId);
        if ($existing) {
            if ($existing['status'] === 'completed') return;
            db()->execute(
                'UPDATE progress SET status = ?, attempts = attempts + 1, started_at = CURRENT_TIMESTAMP WHERE user_id = ? AND challenge_id = ?',
                ['in_progress', $userId, $challengeId]
            );
        } else {
            db()->execute(
                'INSERT INTO progress (user_id, challenge_id, status, attempts, started_at) VALUES (?, ?, ?, 1, CURRENT_TIMESTAMP)',
                [$userId, $challengeId, 'in_progress']
            );
        }
    }

    /**
     * 完成关卡（提交 Flag 通过）
     */
    public static function complete(int $userId, string $challengeId, int $pointsEarned): bool
    {
        $existing = self::get($userId, $challengeId);
        if ($existing && $existing['status'] === 'completed') {
            return false; // 已通关
        }
        if ($existing) {
            db()->execute(
                'UPDATE progress
                 SET status = ?, completed_at = CURRENT_TIMESTAMP, points_earned = ?
                 WHERE user_id = ? AND challenge_id = ?',
                ['completed', $pointsEarned, $userId, $challengeId]
            );
        } else {
            db()->execute(
                'INSERT INTO progress (user_id, challenge_id, status, completed_at, points_earned, attempts, started_at)
                 VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, 1, CURRENT_TIMESTAMP)',
                [$userId, $challengeId, 'completed', $pointsEarned]
            );
        }
        return true;
    }

    /**
     * 标记解锁
     */
    public static function unlock(int $userId, string $challengeId): void
    {
        $existing = self::get($userId, $challengeId);
        if (!$existing) {
            db()->execute(
                'INSERT INTO progress (user_id, challenge_id, status) VALUES (?, ?, ?)',
                [$userId, $challengeId, 'unlocked']
            );
        } elseif ($existing['status'] === 'locked') {
            db()->execute(
                'UPDATE progress SET status = ? WHERE user_id = ? AND challenge_id = ?',
                ['unlocked', $userId, $challengeId]
            );
        }
    }

    /**
     * 记录使用提示
     */
    public static function recordHintUsed(int $userId, string $challengeId, int $hintId): void
    {
        $existing = self::get($userId, $challengeId);
        $hintsUsed = $existing ? (json_decode($existing['hints_used'] ?? '[]', true) ?: []) : [];
        if (!in_array($hintId, $hintsUsed)) {
            $hintsUsed[] = $hintId;
        }
        if ($existing) {
            db()->execute(
                'UPDATE progress SET hints_used = ? WHERE user_id = ? AND challenge_id = ?',
                [json_encode($hintsUsed), $userId, $challengeId]
            );
        } else {
            db()->execute(
                'INSERT INTO progress (user_id, challenge_id, status, hints_used) VALUES (?, ?, ?, ?)',
                [$userId, $challengeId, 'unlocked', json_encode($hintsUsed)]
            );
        }
    }

    /**
     * 记录 Writeup
     */
    public static function saveWriteup(int $userId, string $challengeId, string $writeup): void
    {
        db()->execute(
            'UPDATE progress SET writeup = ? WHERE user_id = ? AND challenge_id = ?',
            [$writeup, $userId, $challengeId]
        );
    }

    /**
     * 统计用户完成关卡数
     */
    public static function completedCount(int $userId): int
    {
        return (int) db()->fetchScalar(
            'SELECT COUNT(*) FROM progress WHERE user_id = ? AND status = ?',
            [$userId, 'completed']
        );
    }

    /**
     * 统计某关卡的总完成人数
     */
    public static function challengeCompletedCount(string $challengeId): int
    {
        return (int) db()->fetchScalar(
            'SELECT COUNT(*) FROM progress WHERE challenge_id = ? AND status = ?',
            [$challengeId, 'completed']
        );
    }
}