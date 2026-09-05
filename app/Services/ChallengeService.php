<?php
declare(strict_types=1);

namespace XiuXian\Services;

use XiuXian\Models\Challenge;
use XiuXian\Models\Progress;
use XiuXian\Models\User;
use XiuXian\Core\Database;
use XiuXian\Core\Logger;
use XiuXian\Services\EggService;

/**
 * 关卡业务逻辑
 */
class ChallengeService
{
    /**
     * 校验 Flag 并完成关卡
     *
     * @return array{success:bool, message:string, points?:int, promoted?:array}
     */
    public static function submitFlag(int $userId, string $challengeId, string $submittedFlag): array
    {
        $challenge = Challenge::find($challengeId);
        if (!$challenge || !$challenge['enabled']) {
            return ['success' => false, 'message' => '关卡不存在或已下架'];
        }

        // 限流
        if (!rate_limit('submit_flag', 30, 60)) {
            return ['success' => false, 'message' => '提交过于频繁，请稍后再试'];
        }

        // 标记为试炼中
        Progress::start($userId, $challengeId);

        if (!Challenge::verifyFlag($challengeId, $submittedFlag)) {
            logger()->challenge($userId, $challengeId, 'submit_flag_fail');
            return ['success' => false, 'message' => 'Flag 不正确，再仔细琢磨琢磨！'];
        }

        // 已通关？
        $existing = Progress::get($userId, $challengeId);
        if ($existing && $existing['status'] === 'completed') {
            // 幂等补判飞升：若上次通关响应丢失且恰为最后一关，重复提交时可补触发
            if (self::checkAscension($userId)) {
                return [
                    'success'  => true,
                    'message'  => '⚡ 天劫已至！百关全数打通，速去 /ascend 渡劫飞升！',
                    'points'   => 0,
                    'ascended' => true,
                    'next'     => null,
                ];
            }
            return ['success' => false, 'message' => '此关已通关，请勿重复提交'];
        }

        $points = (int) $challenge['points'];
        Progress::complete($userId, $challengeId, $points);

        // 触发晋升判定
        $promotion = LevelService::onChallengeCompleted($userId, $points);

        logger()->challenge($userId, $challengeId, 'submit_flag_ok', [
            'points'   => $points,
            'promoted' => $promotion['promoted'] ?? false,
        ]);

        // 解锁下一关（同境界）
        self::unlockNextInRealm($userId, $challenge['realm'], (int) $challenge['order_num']);

        // 飞升判定：全部启用关卡均已通关
        $ascended = self::checkAscension($userId);

        return [
            'success'  => true,
            'message'  => ($promotion['promoted'] ?? false)
                ? '🎉 通关成功！境界提升至 ' . render_realm($promotion['new_realm']) . '！'
                : '🎉 通关成功！获得 ' . $points . ' 修真点数！',
            'points'   => $points,
            'promoted' => $promotion,
            'ascended' => $ascended,
            'next'     => self::nextChallenge($challenge['realm'], (int) $challenge['order_num']),
        ];
    }

    /**
     * 飞升判定：启用关卡全部通关时，标记飞升 + 授予【道祖亲临】与金光主题
     *
     * @return bool 本次是否触发飞升
     */
    private static function checkAscension(int $userId): bool
    {
        try {
            $total = (int) db()->fetchScalar('SELECT COUNT(*) FROM challenges WHERE enabled = 1');
            if ($total <= 0) {
                return false;
            }
            $done = (int) db()->fetchScalar(
                "SELECT COUNT(*) FROM progress WHERE user_id = ? AND status = 'completed'
                 AND challenge_id IN (SELECT id FROM challenges WHERE enabled = 1)",
                [$userId]
            );
            if ($done < $total) {
                return false;
            }
            $user = User::find($userId);
            if (!$user || !empty($user['ascended_at'])) {
                return false; // 已飞升过，天劫只渡一次
            }
            User::markAscended($userId);
            EggService::award($userId, 'egg_daozi');
            EggService::grantCosmetic($userId, 'theme_gold');
            logger()->challenge($userId, 'ascension', 'ascended', ['total' => $total]);
            return true;
        } catch (\Throwable $e) {
            // 旧库未安装彩蛋系统（缺 ascended_at 列 / 彩蛋表）时静默降级，不影响通关
            logger()->error('checkAscension failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 推荐下一关：同境界下一关；境界打通则推进到下一境界首关
     *
     * @return array{id:string,title:string}|null
     */
    public static function nextChallenge(string $realm, int $currentOrder): ?array
    {
        $next = db()->fetchOne(
            'SELECT id, title FROM challenges WHERE realm = ? AND order_num > ? AND enabled = 1 ORDER BY order_num ASC LIMIT 1',
            [$realm, $currentOrder]
        );
        if ($next) {
            return $next;
        }

        $realmOrder = LevelService::REALM_ORDER;
        $idx = array_search($realm, $realmOrder, true);
        if ($idx === false || !isset($realmOrder[$idx + 1])) {
            return null; // 全部境界已打通
        }
        return db()->fetchOne(
            'SELECT id, title FROM challenges WHERE realm = ? AND enabled = 1 ORDER BY order_num ASC LIMIT 1',
            [$realmOrder[$idx + 1]]
        );
    }

    /**
     * 解锁下一关（同境界）
     */
    private static function unlockNextInRealm(int $userId, string $realm, int $currentOrder): void
    {
        $rows = db()->fetchAll(
            'SELECT id FROM challenges WHERE realm = ? AND order_num = ? AND enabled = 1 LIMIT 1',
            [$realm, $currentOrder + 1]
        );
        if ($rows) {
            Progress::unlock($userId, $rows[0]['id']);
        }
    }

    /**
     * 判定当前关卡状态
     */
    public static function status(int $userId, string $challengeId): string
    {
        $row = Progress::get($userId, $challengeId);
        return $row['status'] ?? 'locked';
    }

    /**
     * 关卡是否对用户解锁
     *
     * 规则与关卡地图一致：已有非 locked 进度记录、境界内首关、
     * 或境界内前一关已通关，即视为解锁。
     */
    public static function isUnlocked(int $userId, string $challengeId): bool
    {
        $challenge = Challenge::find($challengeId);
        if (!$challenge) return false;

        $row = Progress::get($userId, $challengeId);
        if ($row && $row['status'] !== 'locked') return true;

        $prev = db()->fetchOne(
            'SELECT id FROM challenges WHERE realm = ? AND order_num < ? AND enabled = 1 ORDER BY order_num DESC LIMIT 1',
            [$challenge['realm'], (int) $challenge['order_num']]
        );
        if (!$prev) return true; // 境界首关默认开放

        $prevRow = Progress::get($userId, $prev['id']);
        return $prevRow !== null && $prevRow['status'] === 'completed';
    }

    /**
     * 获取关卡详情（含用户进度）
     */
    public static function detail(int $userId, string $challengeId): ?array
    {
        $challenge = Challenge::find($challengeId);
        if (!$challenge) return null;
        $challenge['progress'] = Progress::get($userId, $challengeId);
        $challenge['hints'] = Challenge::hints($challengeId);
        $challenge['hints_used'] = $challenge['progress']
            ? (json_decode($challenge['progress']['hints_used'] ?? '[]', true) ?: [])
            : [];
        return $challenge;
    }

    /**
     * 关卡尝试次数（用于统计分析）
     */
    public static function incrementAttempts(int $userId, string $challengeId): void
    {
        db()->execute(
            'UPDATE progress SET attempts = attempts + 1 WHERE user_id = ? AND challenge_id = ?',
            [$userId, $challengeId]
        );
    }
}