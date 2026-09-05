<?php
declare(strict_types=1);

namespace XiuXian\Services;

use XiuXian\Core\Database;

/**
 * 趣味玩法服务：天机阁求签 / 斗法台答题 / 悬赏令 / 万宝楼
 *
 * 说明：
 *  - 所有「每日」玩法以本地日期 date('Y-m-d') 为键，同一玩家当天结果确定。
 *  - SQL 均为 MySQL / SQLite 通用写法（日期比较用 PHP 生成的 'Y-m-d 00:00:00' 字符串）。
 *  - 奖励均为小额积分，不影响排行榜公正性。
 */
class FunService
{
    /** 斗法台每日题数 / 及格线 */
    public const QUIZ_COUNT = 10;
    public const QUIZ_PASS = 8;
    public const QUIZ_REWARD = 10;

    // ============================================================
    // 天机阁 · 每日求签
    // ============================================================

    /** 签池：key / 签文 / 灵石奖励 */
    private const FORTUNES = [
        ['yi_xss',       '今日宜：反射一下（反射型 XSS 练习）。忌：直连内网。', 1],
        ['yi_union',     '今日宜：让查询顺便多查一张表。忌：相信一切输入。', 1],
        ['yi_upload',    '今日宜：上传一枚白名单外的「图片」。忌：黑名单。', 2],
        ['yi_read',      '今日宜：读源码。忌：只看提示不思考。', 0],
        ['yi_burp',      '今日宜：开 Burp 抓个包。忌：手速过快误删环境。', 0],
        ['ji_rush',      '今日忌：跳过习道直接闯关。宜：把剧情读完。', 0],
        ['yi_jwt',       '今日宜：解开一枚符文的三段玄机。忌：信任 alg 字段。', 2],
        ['yi_sleep',     '今日宜：早点休息。道心稳，攻才利。', 0],
        ['yi_sqlmap',    '今日宜：手工注入一关，胜过跑十遍工具。忌：无脑 --batch。', 1],
        ['yi_404',       '今日宜：在迷路诗里找找藏头。忌：看见 404 就退。', 0],
        ['yi_log',       '今日宜：看看页脚有没有灵兽路过。忌：久坐不动。', 0],
        ['yi_writeup',   '今日宜：给通关的关卡写一篇悟道笔记。', 2],
        ['yi_crypto',    '今日宜：把 ECB 加密的图摆出来吓一吓同门。忌：MD5 存密码。', 1],
        ['yi_idor',      '今日宜：把 URL 里的 id 改一改。忌：越权看别人订单。', 1],
        ['yi_robots',    '今日宜：遵守 robots.txt。也宜：看看它都写了什么。', 0],
        ['yi_konami',    '今日宜：在键盘上敲一段古老的顺序。', 0],
        ['yi_srce',      '今日宜：右键查看网页源代码。忌：只看渲染结果。', 0],
        ['yi_cors',      '今日宜：检查一眼 CORS 响应头。忌：通配符配凭证。', 1],
        ['yi_ssr',       '今日宜：让服务器替你发一个请求。忌：file:// 乱读。', 2],
        ['yi_help',      '今日宜：给同门讲一道题。教是最好的学。', 3],
        ['yi_biguan',    '今日宜：闭关。把一个境界的关卡全打通。', 3],
        ['yi_lucky',     '今日大吉：天机不可泄露，灵石已自动入账。', 10],
        ['yi_php',       '今日宜：留意弱比较的陷阱。忌：== 一把梭。', 1],
        ['yi_zero',      '今日宜：从 0e 开头的哈希里找朋友。', 1],
    ];

    private static function db(): Database
    {
        static $db = null;
        return $db ??= db();
    }

    /**
     * 今日日期键
     *
     * 以「数据库时钟」为准（与 completed_at / earned_at 等列的 CURRENT_TIMESTAMP
     * 同源），避免 SQLite / 容器 UTC 存储与 PHP 本地日期错位 8 小时，
     * 导致每日悬赏/求签的完成判定失效。
     * 副作用：每日刷新点为数据库时区的零点（UTC 部署下即北京时间 08:00）。
     */
    private static function today(): string
    {
        $dbNow = (string) self::db()->fetchScalar('SELECT CURRENT_TIMESTAMP');
        return substr($dbNow, 0, 10);
    }

    /** 今日零点（与 today() 同源，字符串比较，兼容 MySQL/SQLite） */
    private static function todayStart(): string
    {
        return self::today() . ' 00:00:00';
    }

    /** 确定性随机挑选（同一 seed 结果稳定） */
    private static function seededPick(array $items, int $count, string $seed): array
    {
        $keys = array_keys($items);
        mt_srand(crc32($seed));
        // Fisher-Yates
        for ($i = count($keys) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$keys[$i], $keys[$j]] = [$keys[$j], $keys[$i]];
        }
        mt_srand(); // 恢复随机状态
        $picked = array_slice($keys, 0, min($count, count($keys)));
        return array_map(fn($k) => $items[$k], $picked);
    }

    /**
     * 今日求签状态（未求返回 null）
     */
    public static function todayFortune(int $userId): ?array
    {
        $row = self::db()->fetchOne(
            'SELECT * FROM fortune_draws WHERE user_id = ? AND draw_date = ?',
            [$userId, self::today()]
        );
        if (!$row) {
            return null;
        }
        foreach (self::FORTUNES as $f) {
            if ($f[0] === $row['fortune_key']) {
                return ['key' => $f[0], 'text' => $f[1], 'points' => (int) $row['reward_points']];
            }
        }
        return ['key' => $row['fortune_key'], 'text' => '（此签已随缘消散）', 'points' => (int) $row['reward_points']];
    }

    /**
     * 求签（每日一次，幂等）
     */
    public static function drawFortune(int $userId): array
    {
        $existing = self::todayFortune($userId);
        if ($existing) {
            return ['ok' => false, 'message' => '今日已求过签了，明日再来。', 'fortune' => $existing];
        }

        $seed = self::today() . '|' . $userId;
        $pick = self::seededPick(self::FORTUNES, 1, $seed)[0];
        [$key, $text, $points] = $pick;

        // 大吉签额外加一点运气波动，避免人人同分
        if ($points > 0) {
            mt_srand(crc32($seed . '#r'));
            $points = max(0, $points + mt_rand(-1, 1));
            mt_srand();
        }

        try {
            self::db()->execute(
                'INSERT INTO fortune_draws (user_id, draw_date, fortune_key, reward_points) VALUES (?, ?, ?, ?)',
                [$userId, self::today(), $key, $points]
            );
        } catch (\PDOException $e) {
            // 双击竞态：另一请求已写入今日签
            return ['ok' => false, 'message' => '今日已求过签了，明日再来。', 'fortune' => self::todayFortune($userId)];
        }
        if ($points > 0) {
            \XiuXian\Models\User::addPoints($userId, $points);
        }
        return ['ok' => true, 'message' => $points > 0 ? "签文显现，{$points} 枚灵石入账！" : '签文显现。', 'fortune' => ['key' => $key, 'text' => $text, 'points' => $points]];
    }

    // ============================================================
    // 斗法台 · 每日答题
    // ============================================================

    /**
     * 今日十题（全场同题，便于同门切磋）
     */
    public static function todayQuiz(): array
    {
        $rows = self::db()->fetchAll(
            'SELECT id, category, question, options, answer_idx, explanation FROM quiz_questions'
        );
        if (!$rows) {
            return [];
        }
        return self::seededPick($rows, self::QUIZ_COUNT, 'quiz|' . self::today());
    }

    /**
     * 今日答题状态
     */
    public static function todayQuizAttempt(int $userId): ?array
    {
        $row = self::db()->fetchOne(
            'SELECT * FROM quiz_attempts WHERE user_id = ? AND quiz_date = ?',
            [$userId, self::today()]
        );
        return $row ?: null;
    }

    /**
     * 连胜天数（连续得分达标的天数，含今日；以数据库时钟回溯）
     */
    public static function quizStreak(int $userId): int
    {
        $base = strtotime(self::today());
        $streak = 0;
        for ($i = 0; $i < 60; $i++) {
            $day = date('Y-m-d', $base - $i * 86400);
            $score = self::db()->fetchScalar(
                'SELECT score FROM quiz_attempts WHERE user_id = ? AND quiz_date = ?',
                [$userId, $day]
            );
            if ($score !== null && (int) $score >= self::QUIZ_PASS) {
                $streak++;
            } else {
                break;
            }
        }
        return $streak;
    }

    /**
     * 提交答卷
     *
     * @param array $answers question_id => option_idx
     */
    public static function submitQuiz(int $userId, array $answers): array
    {
        if (self::todayQuizAttempt($userId)) {
            return ['ok' => false, 'message' => '今日已斗过法，明日再来。'];
        }

        $questions = self::todayQuiz();
        if (!$questions) {
            return ['ok' => false, 'message' => '题库暂空，请先导入种子数据。'];
        }

        $detail = [];
        $score = 0;
        foreach ($questions as $q) {
            $pick = $answers[$q['id']] ?? null;
            $correct = $pick !== null && (int) $pick === (int) $q['answer_idx'];
            if ($correct) {
                $score++;
            }
            $opts = json_decode($q['options'], true) ?: [];
            $detail[] = [
                'id'          => (int) $q['id'],
                'category'    => $q['category'],
                'question'    => $q['question'],
                'pick'        => $pick !== null ? ((int) $pick >= 0 && (int) $pick < count($opts) ? $opts[(int) $pick] : null) : null,
                'correct'     => $correct,
                'answer'      => $opts[(int) $q['answer_idx']] ?? null,
                'explanation' => $q['explanation'],
            ];
        }

        $earned = $score >= self::QUIZ_PASS ? self::QUIZ_REWARD : 0;
        try {
            self::db()->execute(
                'INSERT INTO quiz_attempts (user_id, quiz_date, score, points_earned) VALUES (?, ?, ?, ?)',
                [$userId, self::today(), $score, $earned]
            );
        } catch (\PDOException $e) {
            // 双击竞态：另一请求已交卷
            return ['ok' => false, 'message' => '今日已斗过法，明日再来。'];
        }
        if ($earned > 0) {
            \XiuXian\Models\User::addPoints($userId, $earned);
        }

        $streak = self::quizStreak($userId);
        $msg = $earned > 0
            ? "⚔️ 斗法 {$score}/" . count($questions) . " 胜！灵石 +{$earned}" . ($streak > 1 ? "，连胜 {$streak} 天！" : '。')
            : "斗法 {$score}/" . count($questions) . "，未及及格线（" . self::QUIZ_PASS . "），明日再战。";

        return ['ok' => true, 'message' => $msg, 'score' => $score, 'total' => count($questions), 'earned' => $earned, 'streak' => $streak, 'detail' => $detail];
    }

    // ============================================================
    // 悬赏令 · 每日任务
    // ============================================================

    /** 悬赏池 */
    private const BOUNTIES = [
        ['solve_1',    '初战告捷', '今日通关任意 1 关', 10],
        ['solve_2',    '乘胜追击', '今日通关任意 2 关', 20],
        ['explore_3',  '踏遍山门', '今日进入 3 个不同关卡的试炼', 5],
        ['hint_1',     '翻看锦囊', '今日解锁 1 次关卡提示', 5],
        ['cross_sect', '游历他宗', '今日通关一道非本宗门关卡', 15],
        ['egg_1',      '寻幽探胜', '今日兑换任意一枚彩蛋口令', 10],
    ];

    /**
     * 今日悬赏（3 条，确定性）
     */
    public static function todayBounties(int $userId): array
    {
        $picked = self::seededPick(self::BOUNTIES, 3, 'bounty|' . self::today() . '|' . $userId);
        $claimed = self::db()->fetchAll(
            "SELECT bounty_key FROM user_bounties WHERE user_id = ? AND bounty_date = ?",
            [$userId, self::today()]
        );
        $claimedMap = array_column($claimed, null, 'bounty_key');

        $result = [];
        foreach ($picked as [$key, $name, $desc, $points]) {
            $result[] = [
                'key'     => $key,
                'name'    => $name,
                'desc'    => $desc,
                'points'  => $points,
                'done'    => self::bountyDone($userId, $key),
                'claimed' => isset($claimedMap[$key]),
            ];
        }
        return $result;
    }

    /** 悬赏进度判定 */
    private static function bountyDone(int $userId, string $key): bool
    {
        $uid = $userId;
        $start = self::todayStart();
        switch ($key) {
            case 'solve_1':
                return self::completedToday($uid) >= 1;
            case 'solve_2':
                return self::completedToday($uid) >= 2;
            case 'explore_3':
                return (int) self::db()->fetchScalar(
                    "SELECT COUNT(DISTINCT challenge_id) FROM challenge_logs WHERE user_id = ? AND action = 'open_challenge' AND created_at >= ?",
                    [$uid, $start]
                ) >= 3;
            case 'hint_1':
                return (int) self::db()->fetchScalar(
                    "SELECT COUNT(*) FROM challenge_logs WHERE user_id = ? AND action = 'view_hint' AND created_at >= ?",
                    [$uid, $start]
                ) >= 1;
            case 'cross_sect':
                $sect = auth()->user()['sect'] ?? '';
                return (int) self::db()->fetchScalar(
                    'SELECT COUNT(*) FROM progress p JOIN challenges c ON c.id = p.challenge_id
                     WHERE p.user_id = ? AND p.status = ? AND p.completed_at >= ? AND c.sect != ?',
                    [$uid, 'completed', $start, $sect]
                ) >= 1;
            case 'egg_1':
                return (int) self::db()->fetchScalar(
                    'SELECT COUNT(*) FROM user_easter_eggs WHERE user_id = ? AND earned_at >= ?',
                    [$uid, $start]
                ) >= 1;
        }
        return false;
    }

    private static function completedToday(int $userId): int
    {
        return (int) self::db()->fetchScalar(
            'SELECT COUNT(*) FROM progress WHERE user_id = ? AND status = ? AND completed_at >= ?',
            [$userId, 'completed', self::todayStart()]
        );
    }

    /**
     * 领取悬赏奖励
     */
    public static function claimBounty(int $userId, string $key): array
    {
        $valid = null;
        foreach (self::BOUNTIES as $b) {
            if ($b[0] === $key) {
                $valid = $b;
            }
        }
        if (!$valid) {
            return ['ok' => false, 'message' => '查无此悬赏。'];
        }
        // 只能领取今日榜单上的悬赏
        $todayKeys = array_column(self::todayBounties($userId), 'key');
        if (!in_array($key, $todayKeys, true)) {
            return ['ok' => false, 'message' => '此悬赏不在今日榜单上。'];
        }
        if (!self::bountyDone($userId, $key)) {
            return ['ok' => false, 'message' => '悬赏尚未完成，急不得。'];
        }
        try {
            self::db()->execute(
                'INSERT INTO user_bounties (user_id, bounty_date, bounty_key) VALUES (?, ?, ?)',
                [$userId, self::today(), $key]
            );
        } catch (\PDOException $e) {
            return ['ok' => false, 'message' => '此悬赏已领取过。'];
        }
        $points = (int) $valid[3];
        \XiuXian\Models\User::addPoints($userId, $points);
        return ['ok' => true, 'message' => "📜 悬赏「{$valid[1]}」达成，灵石 +{$points}！", 'points' => $points];
    }

    // ============================================================
    // 万宝楼 · 装扮坊市
    // ============================================================

    /**
     * 商品列表（含持有状态）
     */
    public static function shopList(int $userId): array
    {
        $items = self::db()->fetchAll(
            "SELECT * FROM cosmetics WHERE is_active = 1 AND type = 'title' AND price > 0 ORDER BY price ASC"
        );
        $owned = array_column(
            self::db()->fetchAll('SELECT cosmetic_code, equipped FROM user_cosmetics WHERE user_id = ?', [$userId]),
            null,
            'cosmetic_code'
        );
        foreach ($items as &$it) {
            $it['owned'] = isset($owned[$it['code']]);
            $it['equipped'] = (bool) ($owned[$it['code']]['equipped'] ?? false);
        }
        unset($it);
        return $items;
    }

    /**
     * 购买装扮
     */
    public static function buyCosmetic(int $userId, string $code): array
    {
        $item = self::db()->fetchOne(
            "SELECT * FROM cosmetics WHERE code = ? AND is_active = 1 AND type = 'title' AND price > 0",
            [$code]
        );
        if (!$item) {
            return ['ok' => false, 'message' => '万宝楼查无此物。'];
        }
        $owned = self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_cosmetics WHERE user_id = ? AND cosmetic_code = ?',
            [$userId, $code]
        );
        if ((int) $owned > 0) {
            return ['ok' => false, 'message' => '此物已在囊中，不必重复购买。'];
        }
        // 原子扣费：余额不足时影响行数为 0
        $affected = self::db()->execute(
            'UPDATE users SET total_points = total_points - ? WHERE id = ? AND total_points >= ?',
            [(int) $item['price'], $userId, (int) $item['price']]
        )->rowCount();
        if (!$affected) {
            return ['ok' => false, 'message' => "灵石不足（需 {$item['price']} 点），先去闯关攒一攒。"];
        }
        try {
            self::db()->execute(
                'INSERT INTO user_cosmetics (user_id, cosmetic_code) VALUES (?, ?)',
                [$userId, $code]
            );
        } catch (\PDOException $e) {
            return ['ok' => false, 'message' => '此物已在囊中，不必重复购买。'];
        }
        return ['ok' => true, 'message' => "🎉 已购入「{$item['icon']} {$item['name']}」，可在万宝楼装备。"];
    }

    /**
     * 装备/卸下头衔
     */
    public static function equipCosmetic(int $userId, string $code): array
    {
        $item = self::db()->fetchOne(
            "SELECT * FROM cosmetics WHERE code = ? AND type = 'title'",
            [$code]
        );
        if (!$item) {
            return ['ok' => false, 'message' => '万宝楼查无此物。'];
        }
        $owned = self::db()->fetchScalar(
            'SELECT COUNT(*) FROM user_cosmetics WHERE user_id = ? AND cosmetic_code = ?',
            [$userId, $code]
        );
        if ((int) $owned === 0) {
            return ['ok' => false, 'message' => '尚未购得此物。'];
        }
        $isEquipped = (bool) self::db()->fetchScalar(
            'SELECT equipped FROM user_cosmetics WHERE user_id = ? AND cosmetic_code = ?',
            [$userId, $code]
        );

        // 统一卸下同类，再按需装备
        self::db()->execute(
            "UPDATE user_cosmetics SET equipped = 0 WHERE user_id = ? AND cosmetic_code IN (SELECT code FROM cosmetics WHERE type = 'title')",
            [$userId]
        );
        if ($isEquipped) {
            \XiuXian\Models\User::updateProfile($userId, ['title' => null]);
            return ['ok' => true, 'message' => '已卸下头衔。', 'equipped' => false];
        }
        self::db()->execute(
            'UPDATE user_cosmetics SET equipped = 1 WHERE user_id = ? AND cosmetic_code = ?',
            [$userId, $code]
        );
        \XiuXian\Models\User::updateProfile($userId, ['title' => $item['icon'] . ' ' . $item['name']]);
        return ['ok' => true, 'message' => "已装备头衔「{$item['icon']} {$item['name']}」。", 'equipped' => true];
    }
}
