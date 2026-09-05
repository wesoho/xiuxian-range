<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use XiuXian\Core\Router;

/**
 * 路由器单元测试
 *
 * 验证：
 *  - 静态路由优先于参数化路由
 *  - GET/POST 方法区分
 *  - 路径参数提取
 *  - 中间件执行
 *  - 404 处理
 */
final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function testStaticRouteMatches(): void
    {
        $matched = false;
        $this->router->get('/test/static', function () use (&$matched) {
            $matched = true;
        });

        $this->simulateRequest('GET', '/test/static');
        $this->router->dispatch();
        $this->assertTrue($matched);
    }

    public function testParamRouteMatches(): void
    {
        $captured = null;
        $this->router->get('/test/{id}', function ($params) use (&$captured) {
            $captured = $params['id'];
        });

        $this->simulateRequest('GET', '/test/123');
        $this->router->dispatch();
        $this->assertSame('123', $captured);
    }

    /**
     * 关键测试：静态路由应优先于参数化路由
     * 否则 /challenge/submit-flag 会被 /challenge/{id} 错误匹配
     */
    public function testStaticRoutePriority(): void
    {
        $staticHit = false;
        $paramHit = false;

        // 注册顺序：先参数，再静态（模拟真实场景）
        $this->router->get('/challenge/{id}', function ($params) use (&$paramHit) {
            $paramHit = true;
        });
        $this->router->get('/challenge/submit-flag', function () use (&$staticHit) {
            $staticHit = true;
        });

        // 访问静态路由
        $this->simulateRequest('GET', '/challenge/submit-flag');
        $this->router->dispatch();

        $this->assertTrue($staticHit, '静态路由应被命中');
        $this->assertFalse($paramHit, '参数化路由不应匹配');
    }

    public function testMethodNotAllowed(): void
    {
        $this->router->post('/login', function () {
            echo 'POST /login';
        });

        $this->simulateRequest('GET', '/login');

        $this->expectException(\Throwable::class);
        $this->router->dispatch();
    }

    public function test404OnNoMatch(): void
    {
        $this->simulateRequest('GET', '/nonexistent');
        $this->expectException(\Throwable::class);
        $this->router->dispatch();
    }

    public function testArrayHandler(): void
    {
        $handler = new class {
            public bool $called = false;
            public function index(array $params): void {
                $this->called = true;
            }
        };

        $this->router->get('/test', [$handler, 'index']);
        $this->simulateRequest('GET', '/test');
        $this->router->dispatch();

        $this->assertTrue($handler->called);
    }

    /**
     * 模拟 HTTP 请求
     */
    private function simulateRequest(string $method, string $uri): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;
    }
}