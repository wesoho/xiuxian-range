<?php
declare(strict_types=1);

namespace XiuXian\Models;

use XiuXian\Core\Database;

/**
 * 用户模型
 */
class User
{
    /**
     * 通过 ID 查询
     */
    public static function find(int $id): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM users WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    /**
     * 通过用户名查询
     */
    public static function findByUsername(string $username): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM users WHERE username = ? LIMIT 1',
            [$username]
        );
    }

    /**
     * 通过邮箱查询
     */
    public static function findByEmail(string $email): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [$email]
        );
    }

    /**
     * 通过用户名或邮箱查询
     */
    public static function findByUsernameOrEmail(string $usernameOrEmail): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1',
            [$usernameOrEmail, $usernameOrEmail]
        );
    }

    /**
     * 创建用户
     *
     * @return string 新用户 ID
     */
    public static function create(array $data): string
    {
        $sql = "INSERT INTO users (username, email, password_hash, sect, realm_level, title, bio)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return db()->insert($sql, [
            $data['username'],
            $data['email'] ?? null,
            $data['password_hash'],
            $data['sect'] ?? 'wanderer',
            $data['realm_level'] ?? 'liqi',
            $data['title'] ?? null,
            $data['bio'] ?? null,
        ]);
    }

    /**
     * 更新最后登录信息
     */
    public static function updateLastLogin(int $userId, string $ip): void
    {
        db()->execute(
            'UPDATE users SET last_login_at = CURRENT_TIMESTAMP, last_login_ip = ? WHERE id = ?',
            [$ip, $userId]
        );
    }

    /**
     * 增加修为（修真点数）
     */
    public static function addPoints(int $userId, int $points): void
    {
        db()->execute(
            'UPDATE users SET total_points = total_points + ?, realm_exp = realm_exp + ? WHERE id = ?',
            [$points, $points, $userId]
        );
    }

    /**
     * 晋升境界
     */
    public static function promote(int $userId, string $newRealm): bool
    {
        $rowCount = db()->execute(
            'UPDATE users SET realm_level = ?, realm_exp = 0 WHERE id = ?',
            [$newRealm, $userId]
        );
        return $rowCount > 0;
    }

    /**
     * 更新用户资料
     */
    public static function updateProfile(int $userId, array $data): void
    {
        $sets = [];
        $params = [];
        foreach (['title', 'bio', 'avatar', 'email'] as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!$sets) return;
        $params[] = $userId;
        db()->execute('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    /**
     * 修改密码
     */
    public static function changePassword(int $userId, string $newHash): void
    {
        db()->execute(
            'UPDATE users SET password_hash = ? WHERE id = ?',
            [$newHash, $userId]
        );
    }

    /**
     * 标记飞升（百关全通；幂等，仅记录首次时间）
     */
    public static function markAscended(int $userId): bool
    {
        $rowCount = db()->execute(
            'UPDATE users SET ascended_at = CURRENT_TIMESTAMP WHERE id = ? AND ascended_at IS NULL',
            [$userId]
        );
        return $rowCount > 0;
    }

    /**
     * 获取排行榜
     */
    public static function leaderboard(int $limit = 20, ?string $sect = null): array
    {
        $sql = 'SELECT id, username, sect, realm_level, total_points, title, avatar, last_login_at, ascended_at
                FROM users
                WHERE role = ?
                ORDER BY total_points DESC
                LIMIT ?';
        return db()->fetchAll($sql, ['user', $limit]);
    }

    /**
     * 最近飞升的弟子（用于全服横幅，$days 天内）
     */
    public static function recentAscension(int $days = 7): ?array
    {
        $since = date('Y-m-d H:i:s', time() - $days * 86400);
        return db()->fetchOne(
            'SELECT username, title, sect, ascended_at FROM users
             WHERE ascended_at IS NOT NULL AND ascended_at >= ?
             ORDER BY ascended_at DESC LIMIT 1',
            [$since]
        );
    }

    /**
     * 总用户数
     */
    public static function totalCount(): int
    {
        return (int) db()->fetchScalar('SELECT COUNT(*) FROM users');
    }

    /**
     * 校验用户名是否合法
     */
    public static function isValidUsername(string $username): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }
}