<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * 修真靶场 - 辅助函数单元测试
 */
final class FunctionsTest extends TestCase
{
    /**
     * 测试 htmlspecialchars 别名 e()
     */
    public function testEscapeFunction(): void
    {
        $this->assertSame('&lt;script&gt;alert(1)&lt;/script&gt;', e('<script>alert(1)</script>'));
        $this->assertSame('&quot;hello&quot', e('"hello"'));
        $this->assertSame('你好&lt;b&gt;世界&lt;/b&gt;', e('你好<b>世界</b>'));
    }

    /**
     * 测试 uuid() 生成
     */
    public function testUuidGeneration(): void
    {
        $uuid1 = uuid();
        $uuid2 = uuid();

        // UUID v4 格式
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid1
        );

        // 每次生成都不同
        $this->assertNotEquals($uuid1, $uuid2);
    }

    /**
     * 测试 now() 返回当前时间
     */
    public function testNowFunction(): void
    {
        $now = now();
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $now);
    }

    /**
     * 测试 client_ip() 兜底
     */
    public function testClientIpFallback(): void
    {
        unset($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_X_REAL_IP'], $_SERVER['HTTP_CF_CONNECTING_IP']);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->assertSame('127.0.0.1', client_ip());
    }

    /**
     * 测试 e() 处理 null/array
     */
    public function testEscapeNull(): void
    {
        $this->assertSame('', e(null));
        $this->assertSame('Array', e(['x']));
    }

    /**
     * 测试境界渲染
     */
    public function testRenderRealm(): void
    {
        $this->assertStringContainsString('炼气', render_realm('liqi'));
        $this->assertStringContainsString('大乘', render_realm('dacheng'));
        $this->assertSame('unknown', render_realm('unknown'));
    }

    /**
     * 测试宗门渲染
     */
    public function testRenderSect(): void
    {
        $this->assertStringContainsString('青云', render_sect('qiingong'));
        $this->assertStringContainsString('万魔', render_sect('wanmozong'));
        $this->assertStringContainsString('轮回', render_sect('lunhuizong'));
        $this->assertSame('wanderer', render_sect('wanderer'));
    }

    /**
     * 测试难度星标
     */
    public function testRenderDifficulty(): void
    {
        $this->assertSame('⭐☆☆☆☆', render_difficulty(1));
        $this->assertSame('⭐⭐⭐⭐⭐', render_difficulty(5));
        $this->assertSame('☆☆☆☆☆', render_difficulty(0));
    }
}