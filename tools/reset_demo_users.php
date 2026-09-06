<?php
$pdo = new PDO('sqlite:' . dirname(__DIR__) . '/storage/xxr_dev.db');
$uid = (int) $pdo->query("SELECT id FROM users WHERE username='lunhui'")->fetchColumn();
if ($uid) {
    foreach (['user_badges', 'user_easter_eggs', 'user_cosmetics', 'user_counters',
              'user_slips', 'user_bounties', 'fortune_draws', 'quiz_attempts',
              'challenge_logs', 'progress'] as $t) {
        $pdo->prepare("DELETE FROM {$t} WHERE user_id=?")->execute([$uid]);
    }
    $pdo->prepare("UPDATE users SET ascended_at=NULL, realm_exp=0 WHERE id=?")->execute([$uid]);
    echo "lunhui 干净", PHP_EOL;
}
echo "admin 飞升状态保留: ", $pdo->query("SELECT ascended_at FROM users WHERE username='admin'")->fetchColumn() ?: 'NULL', PHP_EOL;
