<?php
/** @var ?array $user */
/** @var array $questions */
/** @var ?array $attempt */
/** @var int $streak */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>斗法台 · 修真靶场</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/xiuxian.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4" style="max-width: 860px;">
        <div class="text-center mb-4">
            <h1 class="text-gold">⚔️ 斗法台</h1>
            <p class="text-muted">每日十题 · 答对八题得灵石 · 全场同题，可与同门切磋</p>
            <?php if ($streak > 0): ?>
                <span class="badge bg-warning text-dark">🔥 连胜 <?= (int) $streak ?> 天</span>
            <?php endif; ?>
        </div>

        <?php if ($attempt): ?>
            <?php $detail = json_decode($attempt['score_detail'] ?? 'null', true); ?>
            <div class="xxr-egg-card p-4 text-center">
                <h3 class="text-gold"><?= (int) $attempt['score'] ?> / 10</h3>
                <p class="mb-2">
                    <?php if ((int) $attempt['points_earned'] > 0): ?>
                        🎉 今日斗法得胜，灵石 +<?= (int) $attempt['points_earned'] ?>
                    <?php else: ?>
                        未及及格线（8 胜），明日再战。
                    <?php endif; ?>
                </p>
                <p class="small text-muted mb-0">今日已交卷，明日请早。连胜 <?= (int) $streak ?> 天。</p>
            </div>
            <?php if (is_array($detail)): ?>
                <div class="mt-3">
                    <?php foreach ($detail as $i => $d): ?>
                        <div class="xxr-egg-card p-3 mb-2 <?= !empty($d['correct']) ? 'border-success' : '' ?>">
                            <strong><?= $i + 1 ?>. <?= e($d['question'] ?? '') ?></strong>
                            <span class="badge <?= !empty($d['correct']) ? 'bg-success' : 'bg-danger' ?> ms-1"><?= !empty($d['correct']) ? '对' : '错' ?></span>
                            <?php if (empty($d['correct'])): ?>
                                <p class="small mb-1 mt-2">你的答案：<?= e($d['pick'] ?? '（未作答）') ?> ｜ 正确答案：<span class="text-warning"><?= e($d['answer'] ?? '') ?></span></p>
                            <?php endif; ?>
                            <p class="small text-muted mb-0">💡 <?= e($d['explanation'] ?? '') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center small text-muted mt-3">本场答卷未保存逐题解析（旧数据）。</p>
            <?php endif; ?>
        <?php elseif (!$questions): ?>
            <div class="xxr-egg-card p-4 text-center text-muted">题库暂空，请长老先导入 04_eggs.sql 种子数据。</div>
        <?php else: ?>
            <form id="quizForm" onsubmit="return false;">
                <?php foreach ($questions as $idx => $q): ?>
                    <div class="xxr-egg-card p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong class="d-block mb-2"><?= $idx + 1 ?>. <?= e($q['question']) ?></strong>
                            <span class="badge bg-secondary flex-shrink-0 ms-2"><?= e($q['category']) ?></span>
                        </div>
                        <?php foreach (($q['options'] ?? []) as $oi => $optText): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q<?= (int) $q['id'] ?>" id="q<?= (int) $q['id'] ?>_<?= $oi ?>" value="<?= $oi ?>">
                                <label class="form-check-label small" for="q<?= (int) $q['id'] ?>_<?= $oi ?>"><?= e($optText) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <div class="text-center">
                    <button class="xxr-btn xxr-btn-primary px-5" id="btnSubmitQuiz">交卷！</button>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/xiuxian.js"></script>
    <script>
        document.getElementById('btnSubmitQuiz')?.addEventListener('click', function () {
            const fd = new FormData();
            const answers = {};
            let unanswered = 0;
            document.querySelectorAll('#quizForm input[type=radio]').forEach(function (r) {
                const key = r.name.replace(/^q/, '');
                if (r.checked) answers[key] = r.value; else if (!(key in answers)) answers[key] = null;
            });
            Object.keys(answers).forEach(function (k) { if (answers[k] === null) unanswered++; });
            if (unanswered > 0 && !confirm('还有 ' + unanswered + ' 题未作答，确定交卷？')) return;

            xxr.api('/doufatai/submit', answers).then(function (res) {
                if (res.code !== 0) return xxr.toast(res.message, 'error');
                // 揭榜：把题目区域替换为对题结果
                const form = document.getElementById('quizForm');
                let html = '<div class="xxr-egg-card p-4 text-center"><h3 class="text-gold">' + res.data.score + ' / ' + res.data.total + '</h3>';
                html += '<p class="text-warning">' + res.message + '</p></div>';
                html += '<div class="mt-3">';
                (res.data.detail || []).forEach(function (d, i) {
                    html += '<div class="xxr-egg-card p-3 mb-2 ' + (d.correct ? 'border-success' : '') + '">'
                        + '<strong>' + (i + 1) + '. ' + esc(d.question) + '</strong> <span class="badge ' + (d.correct ? 'bg-success' : 'bg-danger') + ' ms-1">' + (d.correct ? '对' : '错') + '</span>'
                        + (d.correct ? '' : '<p class="small mb-1 mt-2">你的答案：' + esc(d.pick ?? '（未作答）') + ' ｜ 正确答案：<span class="text-warning">' + esc(d.answer ?? '') + '</span></p>')
                        + '<p class="small text-muted mb-0">💡 ' + esc(d.explanation ?? '') + '</p>'
                        + '</div>';
                });
                html += '</div>';
                form.outerHTML = html;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                xxr.toast(res.message, res.data.earned > 0 ? 'success' : 'info');
            });
        });

        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }
    </script>
</body>
</html>
