<?php
/**
 * 修真网络安全靶场 - 安全相关辅助函数
 *
 * 注意：以下函数**仅供平台自身代码使用**，
 * 关卡内部（演示漏洞代码）禁止调用。
 */

if (!function_exists('safe_sql')) {
    /**
     * PDO 参数化绑定辅助（更优雅的写法）
     * 用法：safe_sql("SELECT * FROM users WHERE id = ?", [1])
     */
    function safe_sql(string $sql, array $params = []): \PDOStatement
    {
        $stmt = db()->pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

if (!function_exists('safe_query')) {
    /**
     * 安全查询一行
     */
    function safe_query(string $sql, array $params = []): ?array
    {
        $row = safe_sql($sql, $params)->fetch();
        return $row ?: null;
    }
}

if (!function_exists('safe_query_all')) {
    /**
     * 安全查询多行
     */
    function safe_query_all(string $sql, array $params = []): array
    {
        return safe_sql($sql, $params)->fetchAll();
    }
}

if (!function_exists('safe_execute')) {
    /**
     * 安全执行（INSERT/UPDATE/DELETE）
     */
    function safe_execute(string $sql, array $params = []): int
    {
        return safe_sql($sql, $params)->rowCount();
    }
}

if (!function_exists('safe_insert')) {
    /**
     * 安全插入并返回 lastInsertId
     */
    function safe_insert(string $sql, array $params = []): string
    {
        $stmt = safe_sql($sql, $params);
        return db()->pdo()->lastInsertId();
    }
}

if (!function_exists('h')) {
    /**
     * 安全 HTML 输出（短别名）
     */
    function h(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('validate_csrf')) {
    /**
     * 校验 CSRF Token
     */
    function validate_csrf(?string $token = null): bool
    {
        return \XiuXian\Core\Csrf::validate($token);
    }
}

if (!function_exists('rate_limit')) {
    /**
     * 简易限流（基于 Session 的滑动窗口）
     *
     * @param string $action 操作名称（如 login, submit_flag）
     * @param int    $maxAttempts 时间窗口内最大次数
     * @param int    $window      时间窗口（秒）
     */
    function rate_limit(string $action, int $maxAttempts = 10, int $window = 60): bool
    {
        $key = "rate_limit_$action";
        $now = time();
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'start' => $now];

        if ($now - $attempts['start'] > $window) {
            $attempts = ['count' => 1, 'start' => $now];
        } else {
            $attempts['count']++;
        }

        $_SESSION[$key] = $attempts;

        return $attempts['count'] <= $maxAttempts;
    }
}

if (!function_exists('ip')) {
    /**
     * 获取客户端 IP（考虑反向代理）
     */
    function ip(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        ];
        foreach ($headers as $h) {
            if (!empty($_SERVER[$h])) {
                $candidate = trim(explode(',', $_SERVER[$h])[0]);
                // 校验为合法 IP 才采信，防止伪造头污染日志
                if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                    return $candidate;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

if (!function_exists('client_ip')) { function client_ip(): string { return ip(); } }