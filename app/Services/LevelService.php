<?php
declare(strict_types=1);

namespace XiuXian\Services;

use XiuXian\Models\User;
use XiuXian\Models\Progress;
use XiuXian\Core\Database;

/**
 * 修真境界系统
 *
 * 晋升规则：**通关当前境界的全部关卡**方可晋升下一境界。
 * 修为（realm_exp/积分）只作为展示与货币，不参与境界判定——
 * 上一级的关卡未修完，就不能进入下一个级别。
 */
class LevelService
{
    /** 境界顺序 */
    public const REALM_ORDER = [
        'liqi', 'zhuji', 'jindan', 'yuanying',
        'huashen', 'lianxu', 'heti', 'dacheng',
    ];

    /** 境界显示名称 */
    public const REALM_NAMES = [
        'liqi'    => '炼气期',
        'zhuji'   => '筑基期',
        'jindan'  => '金丹期',
        'yuanying'=> '元婴期',
        'huashen' => '化神期',
        'lianxu'  => '炼虚期',
        'heti'    => '合体期',
        'dacheng' => '大乘期',
    ];

    /** 境界称号（晋升时获得） */
    public const TITLES = [
        'zhuji'    => '筑基道人',
        'jindan'   => '金丹真人',
        'yuanying' => '元婴老祖',
        'huashen'  => '化神尊者',
        'lianxu'   => '炼虚大能',
        'heti'     => '合体圣君',
        'dacheng'  => '大乘天尊',
    ];

    /** @var Database|null 惰性连接（仅本类使用） */
    private static ?Database $db = null;

    private static function db(): Database
    {
        return self::$db ??= db();
    }

    /**
     * 用户在某境界的通关进度（以实际通关记录为准）
     *
     * @return array{done:int, total:int, percent:int, next:?string}
     */
    public static function realmProgress(int $userId, string $realm): array
    {
        $total = (int) self::db()->fetchScalar(
            'SELECT COUNT(*) FROM challenges WHERE realm = ? AND enabled = 1',
            [$realm]
        );
        $done = (int) self::db()->fetchScalar(
            'SELECT COUNT(*) FROM progress p JOIN challenges c ON c.id = p.challenge_id
             WHERE p.user_id = ? AND p.status = ? AND c.realm = ?',
            [$userId, 'completed', $realm]
        );
        $next = self::nextRealm($realm);
        $percent = $total > 0 ? (int) round($done / $total * 100) : 0;
        if ($next === null) {
            $percent = 100; // 已是最高境界
        }
        return ['done' => $done, 'total' => $total, 'percent' => $percent, 'next' => $next];
    }

    /**
     * 完成关卡后调用：增加点数；当前境界全部通关则晋升
     *
     * @return array{points:int, promoted:bool, old_realm:string, new_realm:string, title:?string}
     */
    public static function onChallengeCompleted(int $userId, int $points): array
    {
        $user = User::find($userId);
        if (!$user) return ['points' => 0, 'promoted' => false, 'old_realm' => '', 'new_realm' => '', 'title' => null];

        // 增加点数
        User::addPoints($userId, $points);

        $user = User::find($userId);
        $oldRealm = $user['realm_level'];

        // 是否已是大乘
        $currentIdx = array_search($oldRealm, self::REALM_ORDER, true);
        if ($currentIdx === false || $currentIdx === count(self::REALM_ORDER) - 1) {
            return [
                'points'    => $points,
                'promoted'  => false,
                'old_realm' => $oldRealm,
                'new_realm' => $oldRealm,
                'title'     => null,
            ];
        }

        // 晋升判定：当前境界关卡全部通关
        $progress = self::realmProgress($userId, $oldRealm);
        if ($progress['total'] > 0 && $progress['done'] >= $progress['total']) {
            $nextRealm = self::REALM_ORDER[$currentIdx + 1];
            $title = self::TITLES[$nextRealm] ?? null;
            User::promote($userId, $nextRealm);
            // 授予称号
            if ($title && !$user['title']) {
                User::updateProfile($userId, ['title' => $title]);
            }
            return [
                'points'    => $points,
                'promoted'  => true,
                'old_realm' => $oldRealm,
                'new_realm' => $nextRealm,
                'title'     => $title,
            ];
        }

        return [
            'points'    => $points,
            'promoted'  => false,
            'old_realm' => $oldRealm,
            'new_realm' => $oldRealm,
            'title'     => null,
        ];
    }

    /**
     * 下一境界
     */
    public static function nextRealm(string $currentRealm): ?string
    {
        $idx = array_search($currentRealm, self::REALM_ORDER, true);
        if ($idx === false || $idx >= count(self::REALM_ORDER) - 1) {
            return null;
        }
        return self::REALM_ORDER[$idx + 1];
    }
}
