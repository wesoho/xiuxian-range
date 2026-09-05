<?php
declare(strict_types=1);

namespace XiuXian\Controllers;

use XiuXian\Models\User;
use XiuXian\Services\EggService;
use XiuXian\Services\FunService;

/**
 * 趣味玩法控制器：天机阁 / 万宝楼 / 斗法台 / 悬赏令 / 秘境 / 飞升大典
 *
 * 彩蛋类端点注意：
 *  - /egg/whistle（点 logo 触发）对游客开放，仅点亮隐藏导航，不发任何奖励；
 *  - 其余发放类端点一律要求登录 + CSRF。
 */
class FunController
{
    // ============================================================
    // 天机阁（/tianji）
    // ============================================================

    public function tianji(): void
    {
        $user = $this->requireLogin('/tianji');
        $uid = (int) $user['id'];

        $earned = EggService::earnedCodes($uid);
        $eggMap = [];
        foreach (EggService::all() as $egg) {
            $eggMap[$egg['code']] = $egg;
        }

        view('fun.tianji', [
            'user'     => $user,
            'fortune'  => FunService::todayFortune($uid),
            'slips'    => EggService::slips($uid),
            'slipTotal'=> EggService::SLIP_TOTAL,
            'eggs'     => $eggMap,
            'earned'   => $earned,
            'crane'    => EggService::counter($uid, 'crane_caught'),
        ]);
    }

    /** 每日求签 */
    public function drawFortune(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $result = FunService::drawFortune((int) auth()->user()['id']);
        json_ok($result, $result['message']);
    }

    /** 彩蛋口令兑换（天机阁 / 秘境祭坛共用） */
    public function claimEgg(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $secret = trim($_POST['secret'] ?? '');
        $result = EggService::claimSecret((int) auth()->user()['id'], $secret);
        if ($result['ok']) {
            json_ok($result, $result['message']);
        }
        json_fail($result['message']);
    }

    /** Konami 秘籍（前端检测按键序列后上报） */
    public function claimKonami(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $uid = (int) auth()->user()['id'];
        $newly = EggService::award($uid, 'egg_konami');
        if ($newly) {
            logger()->challenge($uid, 'easter_egg', 'egg_konami');
            json_ok(['newly' => true, 'effect' => 'qi'], '🌩️ 上古禁术生效！灵气充盈周身，彩蛋【禁术·百晓生】已入册！');
        }
        json_ok(['newly' => false, 'effect' => 'qi'], '禁术再次生效，灵气依旧充盈。（彩蛋已在收集册中）');
    }

    /** 灵兽抓捕（页脚灵兽被点击） */
    public function claimCrane(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $uid = (int) auth()->user()['id'];
        $count = EggService::bump($uid, 'crane_caught');
        $newly = EggService::award($uid, 'egg_crane');
        $msg = $newly
            ? "🦢 灵鹤束手就擒！【灵兽饲养员】印记已入册（已捕获 {$count} 次）。"
            : "🦢 灵鹤抖了抖翅膀飞走了……但图鉴上又多了一笔（已捕获 {$count} 次）。";
        json_ok(['newly' => $newly, 'count' => $count], $msg);
    }

    /** 点 logo 九下：点亮天机阁隐藏导航（游客亦可，不发奖励） */
    public function whistle(): void
    {
        if (!rate_limit('egg_whistle', 12, 60)) {
            json_fail('山门都被你敲晕了，稍后再来。');
        }
        session()->set('tianji_revealed', true);
        json_ok(['revealed' => true], '✨ 敲门声惊动了天机阁，导航栏悄悄多了一个入口……');
    }

    // ============================================================
    // 万宝楼（/wanbaolou）
    // ============================================================

    public function shop(): void
    {
        $user = $this->requireLogin('/wanbaolou');
        view('fun.wanbaolou', [
            'user'  => $user,
            'items' => FunService::shopList((int) $user['id']),
        ]);
    }

    public function buy(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $result = FunService::buyCosmetic((int) auth()->user()['id'], trim($_POST['code'] ?? ''));
        $result['ok'] ? json_ok($result, $result['message']) : json_fail($result['message']);
    }

    public function equip(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $result = FunService::equipCosmetic((int) auth()->user()['id'], trim($_POST['code'] ?? ''));
        $result['ok'] ? json_ok($result, $result['message']) : json_fail($result['message']);
    }

    // ============================================================
    // 斗法台（/doufatai）
    // ============================================================

    public function quiz(): void
    {
        $user = $this->requireLogin('/doufatai');
        $uid = (int) $user['id'];
        $attempt = FunService::todayQuizAttempt($uid);

        // 出题（答题前隐藏答案与解析；已交卷时无妨，结果由前端渲染）
        $questions = FunService::todayQuiz();
        foreach ($questions as &$q) {
            unset($q['answer_idx'], $q['explanation']);
            $q['options'] = json_decode($q['options'], true) ?: [];
        }
        unset($q);

        view('fun.doufatai', [
            'user'      => $user,
            'questions' => $questions,
            'attempt'   => $attempt,
            'streak'    => FunService::quizStreak($uid),
        ]);
    }

    public function quizSubmit(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $rawAnswers = $_POST['answers'] ?? [];
        if (!is_array($rawAnswers)) {
            json_fail('答卷格式错误');
        }
        $answers = [];
        foreach ($rawAnswers as $qid => $opt) {
            if (ctype_digit((string) $qid) && is_numeric($opt)) {
                $answers[(int) $qid] = (int) $opt;
            }
        }
        $result = FunService::submitQuiz((int) auth()->user()['id'], $answers);
        $result['ok'] ? json_ok($result, $result['message']) : json_fail($result['message']);
    }

    // ============================================================
    // 悬赏令（/xuanshang）
    // ============================================================

    public function bounty(): void
    {
        $user = $this->requireLogin('/xuanshang');
        view('fun.xuanshang', [
            'user'     => $user,
            'bounties' => FunService::todayBounties((int) $user['id']),
        ]);
    }

    public function bountyClaim(): void
    {
        if (!$this->guardApi()) {
            return;
        }
        $result = FunService::claimBounty((int) auth()->user()['id'], trim($_POST['key'] ?? ''));
        $result['ok'] ? json_ok($result, $result['message']) : json_fail($result['message']);
    }

    // ============================================================
    // 秘境（/mijing）—— 寻宝链第五环
    // ============================================================

    public function mijing(): void
    {
        $user = auth()->user();
        if (!$user) {
            $this->requireLogin('/mijing');
        }
        $uid = (int) $user['id'];
        view('fun.mijing', [
            'user'      => $user,
            'slips'     => EggService::slips($uid),
            'slipTotal' => EggService::SLIP_TOTAL,
            'master'    => EggService::has($uid, 'egg_tianji_master'),
        ]);
    }

    // ============================================================
    // 飞升大典（/ascend）与谢幕（/dacheng-final）
    // ============================================================

    public function ascend(): void
    {
        $user = $this->requireLogin('/ascend');
        if (empty($user['ascended_at'])) {
            flash('error', '道友尚未渡劫，先去把余下的关卡通关吧！');
            redirect('/challenges');
        }
        $stats = db()->fetchOne(
            "SELECT COUNT(*) AS done FROM progress WHERE user_id = ? AND status = 'completed'",
            [(int) $user['id']]
        ) ?? ['done' => 0];

        view('fun.ascend', [
            'user'    => $user,
            'done'    => (int) $stats['done'],
            'rank'    => db()->fetchScalar(
                'SELECT COUNT(*) + 1 FROM users WHERE role = ? AND total_points > ?',
                ['user', (int) $user['total_points']]
            ),
        ]);
    }

    public function finalCredits(): void
    {
        $user = auth()->user();
        if (!$user) {
            $this->requireLogin('/dacheng-final');
        }
        $ascended = !empty($user['ascended_at']);
        $eggs = $ascended ? EggService::all() : [];
        view('fun.dacheng_final', [
            'user'      => $user,
            'ascended'  => $ascended,
            'eggs'      => $eggs,
            'earned'    => EggService::earnedCodes((int) $user['id']),
        ]);
    }

    // ============================================================
    // 内部工具
    // ============================================================

    /** 页面类登录门禁 */
    private function requireLogin(string $back): array
    {
        $user = auth()->user();
        if (!$user) {
            flash('error', '请先登录');
            redirect('/login?redirect=' . urlencode($back));
        }
        return $user;
    }

    /** API 类门禁：登录 + POST + CSRF */
    private function guardApi(): bool
    {
        if (!auth()->check()) {
            json_fail('请先登录', 1, null, 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_fail('方法不允许', 1, null, 405);
        }
        if (!validate_csrf()) {
            json_fail('CSRF Token 错误', 1, null, 419);
        }
        return true;
    }
}
