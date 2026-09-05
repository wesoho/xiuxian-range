<?php
declare(strict_types=1);

namespace XiuXian\Models;

use XiuXian\Core\Database;

/**
 * 关卡模型
 */
class Challenge
{
    /**
     * 通过 ID 查询
     */
    public static function find(string $id): ?array
    {
        return db()->fetchOne(
            'SELECT * FROM challenges WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    /**
     * 查询多个关卡
     */
    public static function findMany(array $ids): array
    {
        if (!$ids) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        return db()->fetchAll(
            "SELECT * FROM challenges WHERE id IN ($placeholders) ORDER BY order_num",
            $ids
        );
    }

    /**
     * 按境界查询
     */
    public static function byRealm(string $realm): array
    {
        return db()->fetchAll(
            'SELECT * FROM challenges WHERE realm = ? AND enabled = 1 ORDER BY order_num',
            [$realm]
        );
    }

    /**
     * 按宗门查询
     */
    public static function bySect(string $sect): array
    {
        return db()->fetchAll(
            'SELECT * FROM challenges WHERE sect = ? AND enabled = 1 ORDER BY order_num',
            [$sect]
        );
    }

    /**
     * 全部关卡（带可选过滤）
     */
    public static function all(?string $realm = null, ?string $sect = null): array
    {
        $sql = 'SELECT * FROM challenges WHERE enabled = 1';
        $params = [];
        if ($realm) { $sql .= ' AND realm = ?'; $params[] = $realm; }
        if ($sect)   { $sql .= ' AND sect = ?';   $params[] = $sect; }
        $sql .= ' ORDER BY order_num';
        return db()->fetchAll($sql, $params);
    }

    /**
     * 总数
     */
    public static function totalCount(?string $realm = null): int
    {
        $sql = 'SELECT COUNT(*) FROM challenges WHERE enabled = 1';
        $params = [];
        if ($realm) { $sql .= ' AND realm = ?'; $params[] = $realm; }
        return (int) db()->fetchScalar($sql, $params);
    }

    /**
     * 校验用户提交的 Flag
     */
    public static function verifyFlag(string $challengeId, string $submittedFlag): bool
    {
        $row = db()->fetchOne(
            'SELECT flag FROM challenges WHERE id = ? AND enabled = 1',
            [$challengeId]
        );
        if (!$row) return false;
        return hash_equals($row['flag'], $submittedFlag);
    }

    /**
     * 获取关卡的提示
     */
    public static function hints(string $challengeId): array
    {
        return db()->fetchAll(
            'SELECT id, level, content, point_cost FROM hints
             WHERE challenge_id = ? ORDER BY level, order_num',
            [$challengeId]
        );
    }

    /**
     * 启用/禁用关卡
     */
    public static function setEnabled(string $id, bool $enabled): void
    {
        db()->execute('UPDATE challenges SET enabled = ? WHERE id = ?', [(int) $enabled, $id]);
    }

    /**
     * 更新关卡元数据（管理员）
     */
    public static function update(string $id, array $data): void
    {
        $allowed = ['title', 'description', 'narrative', 'learn_content', 'category', 'difficulty', 'points', 'enabled', 'source_viewable', 'flag'];
        $sets = [];
        $params = [];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!$sets) return;
        $params[] = $id;
        db()->execute('UPDATE challenges SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }
}