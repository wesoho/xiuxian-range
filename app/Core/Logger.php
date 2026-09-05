<?php
declare(strict_types=1);

namespace XiuXian\Core;

/**
 * 日志记录器
 *
 * 按日期生成日志文件，支持日志级别。
 * 默认写到 storage/logs/xxr-YYYY-MM-DD.log
 */
class Logger
{
    private string $logDir;
    private string $channel;

    public function __construct(string $logDir, string $channel = 'xxr')
    {
        $this->logDir = rtrim($logDir, '/');
        $this->channel = $channel;
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0775, true);
        }
    }

    /**
     * 写入日志
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d');
        $file = "{$this->logDir}/{$this->channel}-{$date}.log";

        $ts = date('Y-m-d H:i:s');
        $ip = client_ip();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'cli';

        $line = "[$ts] [$level] [$ip] $message";
        if ($context) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        $line .= " | UA: $ua\n";

        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    public function debug(string $msg, array $ctx = []): void { $this->log('DEBUG',   $msg, $ctx); }
    public function info(string $msg, array $ctx = []): void  { $this->log('INFO',    $msg, $ctx); }
    public function warn(string $msg, array $ctx = []): void  { $this->log('WARN',    $msg, $ctx); }
    public function error(string $msg, array $ctx = []): void { $this->log('ERROR',   $msg, $ctx); }

    /**
     * 记录关卡行为
     *
     * 同时写入日志文件与 challenge_logs 表（后者供悬赏令等玩法统计进度）。
     * 数据库写入失败（旧库缺表等）静默忽略，文件日志不受影响。
     */
    public function challenge(int $userId, string $challengeId, string $action, array $detail = []): void
    {
        $this->log('CHALLENGE', "user=$userId challenge=$challengeId action=$action", $detail);

        if ($userId <= 0 || !function_exists('db')) {
            return;
        }
        try {
            db()->execute(
                'INSERT INTO challenge_logs (user_id, challenge_id, action, detail, ip, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    $userId,
                    $challengeId,
                    $action,
                    $detail ? json_encode($detail, JSON_UNESCAPED_UNICODE) : null,
                    client_ip(),
                    $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
                ]
            );
        } catch (\Throwable $e) {
            // 表不存在 / 数据库不可用时静默降级
        }
    }
}