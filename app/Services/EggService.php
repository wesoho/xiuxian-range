<?php
declare(strict_types=1);

namespace XiuXian\Services;

use XiuXian\Core\Database;

/**
 * 彩蛋系统服务
 *
 * 设计原则：
 *  - 彩蛋只授予徽章/称号/装扮等荣誉，不发放闯关积分，保证排行榜公平。
 *  - 口令型彩蛋：玩家在关卡/页面中找到 flag{egg_xxx} 口令，到天机阁兑换。
 *  - 行为型彩蛋：Konami 秘籍、灵兽、周年、飞升等，由事件直接授予。
 *  - 徽章记录（badges/user_badges）在首次授予时自动建档，兼容旧库。
 */
class EggService
{
    /** 天机残页总数（寻宝链环数） */
    public const SLIP_TOTAL = 5;

    /** @var Database|null */
    private static ?Database $db = null;

    private static function db(): Database
    {
        return self::$db ??= db();
    }

    /**
     * 全部在架彩蛋
     */
    public static function all(): array
    {
        return self::db()->fetchAll(
            'SELECT * FROM easter_eggs WHERE is_active = 1 ORDER BY tier DESC, code ASC'
        );
    }

    /**
     * 弟子已获得的彩蛋代号集合
     */
    public static function earnedCodes(int $userId): array
    {
        return array_column(
            self::db()->fetchAll('SELECT egg_code FROM user_easter_eggs WHERE user_id = ?', [$userId]),
            'egg_code'
        );
    }

    public static function has(int $userId, string $code): bool
    {
        return (bool) self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_easter_eggs WHERE user_id = ? AND egg_code = ?',
            [$userId, $code]
        );
    }

    public static function hasAny(int $userId): bool
    {
        return (bool) self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_easter_eggs WHERE user_id = ? LIMIT 1',
            [$userId]
        );
    }

    /**
     * 判断输入是否为某个彩蛋的口令（不计尝试次数、不受关卡门禁限制）
     *
     * 旧库未安装彩蛋表时静默返回 false，绝不影响主流程的 Flag 提交。
     */
    public static function isEggSecret(string $input): bool
    {
        $secret = trim($input);
        if ($secret === '') {
            return false;
        }
        try {
            return (bool) self::db()->fetchScalar(
                'SELECT COUNT(*) FROM easter_eggs WHERE secret = ? AND is_active = 1',
                [$secret]
            );
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 兑换口令（天机阁 / 秘境祭坛共用）
     *
     * @return array{ok:bool, message:string, egg:?array, slip:?int, mijing:bool}
     */
    public static function claimSecret(int $userId, string $input): array
    {
        $secret = trim($input);
        if ($secret === '') {
            return ['ok' => false, 'message' => '口令不能为空', 'egg' => null, 'slip' => null, 'mijing' => false];
        }

        // 限流：防止暴力枚举口令
        if (!rate_limit('egg_claim', 15, 60)) {
            return ['ok' => false, 'message' => '兑换太频繁，天机阁要歇一歇了', 'egg' => null, 'slip' => null, 'mijing' => false];
        }

        $egg = self::db()->fetchOne(
            'SELECT * FROM easter_eggs WHERE secret = ? AND is_active = 1',
            [$secret]
        );
        if (!$egg) {
            logger()->challenge($userId, 'easter_egg', 'egg_claim_fail', ['secret' => mb_substr($secret, 0, 50)]);
            return ['ok' => false, 'message' => '天机阁掌柜眯眼看了看：「这口令不对。」', 'egg' => null, 'slip' => null, 'mijing' => false];
        }

        $newly = self::grant($userId, $egg['code']);
        $slipNo = self::slipNumberOf($egg['code']);
        if ($newly && $slipNo !== null) {
            self::addSlip($userId, $slipNo);
        }

        $mijing = self::checkMijing($userId);

        if (!$newly) {
            return [
                'ok'      => true,
                'message' => '「这页你早已交给我了，道友。」（' . $egg['name'] . ' 已在收集册中）',
                'egg'     => $egg,
                'slip'    => null,
                'mijing'  => false,
            ];
        }

        logger()->challenge($userId, 'easter_egg', 'egg_claim_ok', ['egg' => $egg['code']]);
        $msg = '🎉 天机阁掌柜眼睛一亮：「好口令！」获得彩蛋【' . $egg['icon'] . ' ' . $egg['name'] . '】';
        if ($slipNo !== null) {
            $msg .= '，天机残页·' . self::CN_NUM[$slipNo] . ' 已收入囊中';
        }
        if ($mijing) {
            $msg .= '。五页已齐，秘境为你而开！';
        }
        return ['ok' => true, 'message' => $msg, 'egg' => $egg, 'slip' => $slipNo, 'mijing' => $mijing];
    }

    /**
     * 授予行为型彩蛋（幂等）
     *
     * @return bool 是否为首次获得
     */
    public static function award(int $userId, string $code): bool
    {
        return self::grant($userId, $code);
    }

    /**
     * 内部：授予彩蛋 + 同步徽章（幂等）
     */
    private static function grant(int $userId, string $code): bool
    {
        $exists = self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_easter_eggs WHERE user_id = ? AND egg_code = ?',
            [$userId, $code]
        );
        if ((int) $exists > 0) {
            return false;
        }
        self::db()->execute(
            'INSERT INTO user_easter_eggs (user_id, egg_code) VALUES (?, ?)',
            [$userId, $code]
        );
        self::awardBadge($userId, $code);
        return true;
    }

    /**
     * 授予徽章（幂等；徽章不存在时按彩蛋登记自动建档）
     */
    public static function awardBadge(int $userId, string $badgeCode): bool
    {
        $badge = self::db()->fetchOne('SELECT id FROM badges WHERE code = ?', [$badgeCode]);
        if (!$badge) {
            $egg = self::db()->fetchOne('SELECT name, icon, tier FROM easter_eggs WHERE code = ?', [$badgeCode]);
            self::db()->execute(
                'INSERT INTO badges (code, name, description, icon, tier) VALUES (?, ?, ?, ?, ?)',
                [
                    $badgeCode,
                    $egg['name'] ?? $badgeCode,
                    $egg['description'] ?? '隐藏彩蛋徽章',
                    $egg['icon'] ?? '🥚',
                    $egg['tier'] ?? 'bronze',
                ]
            );
            $badge = self::db()->fetchOne('SELECT id FROM badges WHERE code = ?', [$badgeCode]);
        }
        if (!$badge) {
            return false;
        }
        $linked = self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_badges WHERE user_id = ? AND badge_id = ?',
            [$userId, (int) $badge['id']]
        );
        if ((int) $linked > 0) {
            return false;
        }
        self::db()->execute(
            'INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)',
            [$userId, (int) $badge['id']]
        );
        return true;
    }

    /**
     * 授予装扮（成就解锁类，如飞升主题；幂等）
     */
    public static function grantCosmetic(int $userId, string $code): bool
    {
        $exists = self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_cosmetics WHERE user_id = ? AND cosmetic_code = ?',
            [$userId, $code]
        );
        if ((int) $exists > 0) {
            return false;
        }
        self::db()->execute(
            'INSERT INTO user_cosmetics (user_id, cosmetic_code) VALUES (?, ?)',
            [$userId, $code]
        );
        return true;
    }

    /** 彩蛋代号 -> 残页编号（非残页彩蛋返回 null） */
    public static function slipNumberOf(string $eggCode): ?int
    {
        if (!str_starts_with($eggCode, 'egg_slip_')) {
            return null;
        }
        $no = (int) substr($eggCode, strlen('egg_slip_'));
        return ($no >= 1 && $no <= self::SLIP_TOTAL) ? $no : null;
    }

    /**
     * 收入残页（幂等）
     */
    public static function addSlip(int $userId, int $no): bool
    {
        if ($no < 1 || $no > self::SLIP_TOTAL) {
            return false;
        }
        $exists = self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_slips WHERE user_id = ? AND slip_no = ?',
            [$userId, $no]
        );
        if ((int) $exists > 0) {
            return false;
        }
        self::db()->execute(
            'INSERT INTO user_slips (user_id, slip_no) VALUES (?, ?)',
            [$userId, $no]
        );
        return true;
    }

    /**
     * 已收集的残页编号
     */
    public static function slips(int $userId): array
    {
        return array_map(
            'intval',
            array_column(
                self::db()->fetchAll('SELECT slip_no FROM user_slips WHERE user_id = ? ORDER BY slip_no', [$userId]),
                'slip_no'
            )
        );
    }

    /**
     * 秘境判定：五页集齐 -> 授予【天机子】+ 大乘金光主题
     */
    public static function checkMijing(int $userId): bool
    {
        $count = (int) self::db()->fetchScalar('SELECT COUNT(*) FROM user_slips WHERE user_id = ?', [$userId]);
        if ($count < self::SLIP_TOTAL) {
            return false;
        }
        $newly = self::grant($userId, 'egg_tianji_master');
        if ($newly) {
            self::grantCosmetic($userId, 'theme_gold');
            logger()->challenge($userId, 'easter_egg', 'mijing_complete');
        }
        return true;
    }

    /**
     * 计数器自增（灵兽图鉴等），返回自增后的值
     * MySQL 与 SQLite 的 upsert 语法不同，这里用「先更新、失败再插入」的通用写法
     */
    public static function bump(int $userId, string $key): int
    {
        $affected = self::db()->execute(
            'UPDATE user_counters SET value = value + 1, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND counter_key = ?',
            [$userId, $key]
        )->rowCount();
        if (!$affected) {
            try {
                self::db()->execute(
                    'INSERT INTO user_counters (user_id, counter_key, value) VALUES (?, ?, 1)',
                    [$userId, $key]
                );
            } catch (\Throwable $e) {
                // 并发插入冲突：自增一次即可
                self::db()->execute(
                    'UPDATE user_counters SET value = value + 1 WHERE user_id = ? AND counter_key = ?',
                    [$userId, $key]
                );
            }
        }
        return self::counter($userId, $key);
    }

    /**
     * 读取计数器
     */
    public static function counter(int $userId, string $key): int
    {
        return (int) self::db()->fetchScalar(
            'SELECT value FROM user_counters WHERE user_id = ? AND counter_key = ?',
            [$userId, $key]
        );
    }

    /** 残页中文编号 */
    public const CN_NUM = [1 => '壹', 2 => '贰', 3 => '叁', 4 => '肆', 5 => '伍'];
}
