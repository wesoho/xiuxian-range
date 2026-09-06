<?php
declare(strict_types=1);

namespace XiuXian\Controllers;

use XiuXian\Models\Challenge;
use XiuXian\Models\Progress;
use XiuXian\Services\ChallengeService;
use XiuXian\Services\EggService;
use XiuXian\Services\LevelService;

/**
 * 关卡相关控制器
 */
class ChallengeController
{
    /**
     * 关卡地图（按境界）
     */
    public function map(?string $realm = null): void
    {
        $realms = LevelService::REALM_ORDER;
        $currentRealm = $realm ?: (auth()->user()['realm_level'] ?? 'liqi');

        $challenges = Challenge::byRealm($currentRealm);

        // 标记每个关卡的用户状态
        // 规则：有进度记录用记录值；无记录时境界内首关默认开放，
        //       其余关卡在前一关通关后顺序解锁；长老（管理员）全境开放。
        if ($user = auth()->user()) {
            $isAdmin = auth()->isAdmin();
            $progressMap = [];
            foreach (Progress::listByUser($user['id']) as $p) {
                $progressMap[$p['challenge_id']] = $p;
            }
            $prevCompleted = true; // 境界内第一关无需前置
            foreach ($challenges as &$c) {
                $rowStatus = $progressMap[$c['id']]['status'] ?? null;
                if ($isAdmin) {
                    $c['user_status'] = $rowStatus === 'completed' ? 'completed' : 'unlocked';
                } elseif ($rowStatus !== null) {
                    $c['user_status'] = $rowStatus;
                } else {
                    $c['user_status'] = $prevCompleted ? 'unlocked' : 'locked';
                }
                $c['user_completed'] = $rowStatus === 'completed';
                $prevCompleted = ($rowStatus === 'completed');
            }
            unset($c);
        } else {
            foreach ($challenges as &$c) {
                $c['user_status'] = 'locked';
                $c['user_completed'] = false;
            }
            unset($c);
        }

        view('challenge.map', [
            'user'         => auth()->user(),
            'realms'       => $realms,
            'currentRealm' => $currentRealm,
            'challenges'   => $challenges,
            // 彩蛋：境界地图的隐秘角落（?tianji=1）
            'tianjiHidden' => isset($_GET['tianji']),
        ]);
    }

    /**
     * 关卡详情 / 三阶段学习入口
     */
    public function show(string $id): void
    {
        $user = auth()->user();
        if (!$user) {
            flash('error', '请先登录');
            redirect(url('/login') . '?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        }

        $challenge = Challenge::find($id);
        if (!$challenge || !$challenge['enabled']) {
            not_found('关卡不存在');
        }

        $detail = ChallengeService::detail($user['id'], $id);

        // 学习路径（修真叙事）
        view('challenge.show', [
            'user'      => $user,
            'challenge' => $detail,
            'phase'     => $_GET['phase'] ?? 'learn', // learn/fight/review
        ]);
    }

    /**
     * 学习阶段：阅读理论
     */
    public function learn(string $id): void
    {
        $user = auth()->user();
        if (!$user) {
            redirect('/login');
        }
        $challenge = Challenge::find($id);
        if (!$challenge) not_found();

        view('challenge.learn', [
            'user'      => $user,
            'challenge' => $challenge,
        ]);
    }

    /**
     * 实战阶段：进入靶场（重定向到关卡目录）
     */
    public function fight(string $id): void
    {
        $user = auth()->user();
        if (!$user) {
            redirect('/login');
        }
        $challenge = Challenge::find($id);
        if (!$challenge) not_found();

        // 标记进入试炼
        Progress::start($user['id'], $id);
        logger()->challenge($user['id'], $id, 'open_challenge');

        // 重定向到关卡目录
        $challengeDir = self::challengeDir($id);
        redirect("/challenges/{$challengeDir}/");
    }

    /**
     * 提交 Flag（API）
     */
    public function submitFlag(): void
    {
        if (!auth()->check()) {
            json_fail('请先登录', 401, null, 401);
        }
        $user = auth()->user();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_fail('方法不允许', 405, null, 405);
        }

        // CSRF
        if (!validate_csrf()) {
            json_fail('CSRF Token 错误', 419, null, 419);
        }

        $challengeId = $_POST['challenge_id'] ?? '';
        $submittedFlag = trim($_POST['flag'] ?? '');

        if (!$challengeId || !$submittedFlag) {
            json_fail('参数错误');
        }

        // 彩蛋口令识别（彩蛋不分关卡归属，优先于解锁门禁）：
        // 误把彩蛋口令投进关卡 Flag 框时，自动收录并明确告知「这是支线，不是本关答案」
        if (EggService::isEggSecret($submittedFlag)) {
            $claim = EggService::claimSecret((int) $user['id'], $submittedFlag);
            json_fail(
                '🎁 这是一枚【彩蛋口令】（支线任务，不影响本关通关）——已为你收录。'
                . '本关的 Flag 是另一个，请按题目指引到对应位置寻找（通常形如 flag{随机字符串}）。',
                2,
                ['egg' => true, 'egg_name' => $claim['egg']['name'] ?? '', 'egg_detail' => $claim['message']],
                200
            );
        }

        // 解锁门禁（长老豁免）：与关卡地图的顺序解锁规则一致
        if (!auth()->isAdmin() && !ChallengeService::isUnlocked((int) $user['id'], $challengeId)) {
            json_fail('此关尚未解锁，请先通过前面的试炼', 403, null, 403);
        }

        $result = ChallengeService::submitFlag((int) $user['id'], $challengeId, $submittedFlag);

        if ($result['success']) {
            json_ok($result, $result['message']);
        }
        json_fail($result['message']);
    }

    /**
     * 查看源码（教学功能）
     */
    public function viewSource(string $id): void
    {
        $user = auth()->user();
        if (!$user) {
            redirect('/login');
        }
        $challenge = Challenge::find($id);
        if (!$challenge || !$challenge['source_viewable']) not_found();

        $challengeDir = self::challengeDir($id);
        $vulnerableFile = config('paths.public') . "/challenges/{$challengeDir}/vulnerable.php";
        $secureFile = config('paths.public') . "/challenges/{$challengeDir}/secure.php";

        view('challenge.source', [
            'user'           => $user,
            'challenge'      => $challenge,
            'vulnerableCode' => is_file($vulnerableFile) ? file_get_contents($vulnerableFile) : '// 关卡源码待补充',
            'secureCode'     => is_file($secureFile) ? file_get_contents($secureFile) : '// 安全版本待补充',
        ]);
    }

    /**
     * 复盘 / Writeup
     * （渲染统一由 show 的悟道页签承担，此处仅做跳转）
     */
    public function review(string $id): void
    {
        $user = auth()->user();
        if (!$user) {
            redirect('/login');
        }
        if (!Challenge::find($id)) not_found();

        redirect("/challenge/{$id}?phase=review");
    }

    /**
     * 保存 Writeup
     */
    public function saveWriteup(string $id): void
    {
        $user = auth()->user();
        if (!$user) json_fail('请先登录', 401, null, 401);

        if (!validate_csrf()) json_fail('CSRF Token 错误', 419, null, 419);

        $writeup = trim($_POST['writeup'] ?? '');
        Progress::saveWriteup((int) $user['id'], $id, $writeup);
        json_ok(null, 'Writeup 已保存');
    }

    /**
     * 获取提示（消耗积分）
     */
    public function getHint(): void
    {
        $user = auth()->user();
        if (!$user) json_fail('请先登录', 401, null, 401);

        if (!validate_csrf()) json_fail('CSRF Token 错误', 419, null, 419);

        $challengeId = $_POST['challenge_id'] ?? '';
        $hintId = (int) ($_POST['hint_id'] ?? 0);

        if (!$challengeId || !$hintId) json_fail('参数错误');

        // 解锁门禁（长老豁免）：锁定关卡的提示不可购买
        if (!auth()->isAdmin() && !ChallengeService::isUnlocked((int) $user['id'], $challengeId)) {
            json_fail('此关尚未解锁，请先通过前面的试炼', 403, null, 403);
        }

        $hint = db()->fetchOne(
            'SELECT * FROM hints WHERE id = ? AND challenge_id = ?',
            [$hintId, $challengeId]
        );
        if (!$hint) json_fail('提示不存在');

        // 幂等：已解锁过的提示直接返回内容，不再扣分
        $progress = Progress::get((int) $user['id'], $challengeId);
        $used = $progress ? (json_decode($progress['hints_used'] ?? '[]', true) ?: []) : [];
        if (in_array($hintId, array_map('intval', $used), true)) {
            $balance = (int) db()->fetchScalar('SELECT total_points FROM users WHERE id = ?', [(int) $user['id']]);
            json_ok(['content' => $hint['content'], 'balance' => $balance], '提示已解锁');
        }

        // 弱提示免费；其余原子扣分（余额不足时影响行数为 0）
        if ((int) $hint['level'] > 1) {
            $cost = (int) ($hint['point_cost'] ?? 0);
            if ($cost > 0) {
                $affected = db()->execute(
                    'UPDATE users SET total_points = total_points - ? WHERE id = ? AND total_points >= ?',
                    [$cost, (int) $user['id'], $cost]
                )->rowCount();
                if (!$affected) {
                    json_fail("积分不足（需要 $cost 点）");
                }
            }
        }

        Progress::recordHintUsed((int) $user['id'], $challengeId, $hintId);
        logger()->challenge($user['id'], $challengeId, 'view_hint', ['hint_id' => $hintId, 'level' => $hint['level']]);

        $balance = (int) db()->fetchScalar('SELECT total_points FROM users WHERE id = ?', [(int) $user['id']]);
        json_ok(['content' => $hint['content'], 'balance' => $balance], '提示已解锁');
    }

    /**
     * 关卡编号 -> 宗门目录/关卡目录（相对 public/challenges/）
     * 例如 QY-LQ-01 -> qingong/qy_lq_01_html_comment
     * 目录名带有语义后缀，需按编号前缀在文件系统中匹配
     */
    private static function challengeDir(string $id): string
    {
        $prefix = strtolower(str_replace('-', '_', $id));
        // 仅允许安全字符参与 glob 匹配，防止模式注入与路径穿越
        if (!preg_match('/^[a-z0-9_]{2,32}$/', $prefix)) {
            return preg_replace('/[^a-z0-9_]/', '', $prefix);
        }
        $base = config('paths.public') . '/challenges/';
        foreach (glob($base . '*/' . $prefix . '*', GLOB_ONLYDIR) as $dir) {
            $rel = str_replace('\\', '/', substr($dir, strlen($base)));
            if ($rel !== '') {
                return $rel;
            }
        }
        return $prefix; // 兜底：按编号直映
    }
}