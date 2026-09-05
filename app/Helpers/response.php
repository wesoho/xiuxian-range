<?php
/**
 * 修真网络安全靶场 - HTTP 响应辅助
 */

if (!function_exists('json_response')) {
    /**
     * JSON 响应
     */
    function json_response(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('json_ok')) {
    /**
     * JSON 成功响应
     */
    function json_ok(mixed $data = null, string $message = 'ok'): void
    {
        json_response([
            'code'    => 0,
            'message' => $message,
            'data'    => $data,
        ]);
    }
}

if (!function_exists('json_fail')) {
    /**
     * JSON 失败响应
     */
    function json_fail(string $message = 'fail', int $code = 1, mixed $data = null, int $httpStatus = 400): void
    {
        json_response([
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], $httpStatus);
    }
}

if (!function_exists('view')) {
    /**
     * 渲染视图
     *
     * @param string               $name 视图名（如 'home.index'）
     * @param array<string,mixed>  $data 数据
     */
    function view(string $name, array $data = []): void
    {
        $view = new \XiuXian\Core\View(config('paths.views'));
        $view->render($name, $data);
    }
}

if (!function_exists('not_found')) {
    /**
     * 404 响应
     *
     * 始终渲染完整 404 视图（迷路诗彩蛋藏于其中，生产环境同样需要）；
     * 视图渲染失败时降级为一行文本。
     */
    function not_found(string $message = '页面未找到'): void
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        try {
            view('errors.404', ['message' => $message]);
        } catch (\Throwable $e) {
            echo '<h1>404 - 道友迷路了</h1><p>' . e($message) . '</p>';
        }
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * 抛出 HTTP 错误
     */
    function abort(int $status, string $message = ''): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        $view = "errors.$status";
        if (is_file(view_path($view))) {
            view($view, ['status' => $status, 'message' => $message]);
        } else {
            echo "<h1>$status</h1><p>" . e($message) . '</p>';
        }
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * 返回上一页
     */
    function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}