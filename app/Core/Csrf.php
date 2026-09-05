<?php
declare(strict_types=1);

namespace XiuXian\Core;

/**
 * CSRF Token 管理
 */
class Csrf
{
    private const SESSION_KEY = '_csrf_token';
    private const SESSION_TIME_KEY = '_csrf_token_time';
    private const TOKEN_LIFETIME = 7200; // 2 小时

    /**
     * 获取当前 Token（不存在则生成）
     */
    public static function token(): string
    {
        $sess = session();
        $existing = $sess->get(self::SESSION_KEY);
        $created  = $sess->get(self::SESSION_TIME_KEY, 0);

        if (!$existing || (time() - $created) > self::TOKEN_LIFETIME) {
            $existing = bin2hex(random_bytes(32));
            $sess->set(self::SESSION_KEY, $existing);
            $sess->set(self::SESSION_TIME_KEY, time());
        }
        return $existing;
    }

    /**
     * 校验 Token
     */
    public static function validate(?string $token = null): bool
    {
        if ($token === null) {
            $token = $_POST['_token'] ?? $_GET['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }
        if (!$token) return false;

        $sess = session();
        $expected = $sess->get(self::SESSION_KEY);
        $created  = $sess->get(self::SESSION_TIME_KEY, 0);

        if (!$expected || (time() - $created) > self::TOKEN_LIFETIME) {
            return false;
        }
        return hash_equals($expected, $token);
    }

    /**
     * 校验失败时终止请求
     */
    public static function verifyOrFail(): void
    {
        if (!self::validate()) {
            abort(419, 'CSRF Token 校验失败。请刷新页面后重试。');
        }
    }
}