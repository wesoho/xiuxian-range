<?php
declare(strict_types=1);

namespace XiuXian\Core;

/**
 * 简易路由器
 *
 * 支持：
 *  - 静态路由（GET/POST）
 *  - 路径参数（{id}）
 *  - 路由中间件
 *  - 路由分组（前缀）
 */
class Router
{
    /** @var array<string, array<string, mixed>> 路由表 */
    private array $routes = [];

    /** @var array<string, callable> 全局中间件 */
    private array $middleware = [];

    /** @var string 当前路由分组前缀 */
    private string $prefix = '';

    /** @var array<string, callable> 当前分组中间件 */
    private array $groupMiddleware = [];

    /**
     * 注册 GET 路由
     */
    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * 注册 POST 路由
     */
    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * 注册任意方法路由
     */
    public function any(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('*', $path, $handler, $middleware);
    }

    /**
     * 路由分组
     */
    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $oldPrefix = $this->prefix;
        $oldMw = $this->groupMiddleware;
        $this->prefix .= $prefix;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);
        $callback($this);
        $this->prefix = $oldPrefix;
        $this->groupMiddleware = $oldMw;
    }

    /**
     * 添加全局中间件
     */
    public function middleware(string $name, callable $handler): void
    {
        $this->middleware[$name] = $handler;
    }

    /**
     * 注册路由
     */
    private function addRoute(string $method, string $path, callable|array $handler, array $mw): void
    {
        $fullPath = $this->prefix . $path;
        $mw = array_merge($this->groupMiddleware, $mw);

        $regex = $this->compilePath($fullPath);

        foreach (explode('|', $method === '*' ? 'GET|POST|PUT|DELETE|PATCH' : $method) as $m) {
            $this->routes[$m][] = [
                'path'       => $fullPath,
                'regex'      => $regex,
                'handler'    => $handler,
                'middleware' => $mw,
            ];
        }
    }

    /**
     * 编译路径为正则
     */
    private function compilePath(string $path): string
    {
        return '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path) . '$#';
    }

    /**
     * 分发请求
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $path   = '/' . trim($path, '/');

        $candidates = $this->routes[$method] ?? [];
        // 任何方法也匹配
        $candidates = array_merge($candidates, $this->routes['*'] ?? []);

        // 【Bug修复】静态路径（不含参数）应优先于参数化路径匹配
        // 否则 `/challenge/submit-flag` 会被 `/challenge/{id}` 错误匹配
        usort($candidates, function ($a, $b) {
            $aHasParam = str_contains($a['path'], '{');
            $bHasParam = str_contains($b['path'], '{');
            if ($aHasParam === $bHasParam) return 0;
            return $aHasParam ? 1 : -1; // 静态路由优先
        });

        foreach ($candidates as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                // 提取路径参数
                $params = array_filter($matches, fn($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY);

                // 执行中间件
                foreach ($route['middleware'] as $mw) {
                    $mwName = is_string($mw) ? $mw : null;
                    if ($mwName && isset($this->middleware[$mwName])) {
                        $result = ($this->middleware[$mwName])($this);
                        if ($result === false) {
                            return;
                        }
                    } elseif (is_callable($mw)) {
                        $result = $mw($this);
                        if ($result === false) {
                            return;
                        }
                    }
                }

                // 执行 handler
                $handler = $route['handler'];
                if (is_array($handler) && count($handler) === 2) {
                    [$class, $action] = $handler;
                    $instance = is_object($class) ? $class : new $class();
                    // 按路径中出现顺序展开路由参数（如 show(string $id)）
                    $instance->$action(...array_values($params));
                } elseif (is_callable($handler)) {
                    $handler(...array_values($params));
                }
                return;
            }
        }

        not_found('未匹配到路由：' . $path);
    }
}