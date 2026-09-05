<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use XiuXian\Core\Csrf;

/**
 * CSRF Token 单元测试
 */
final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        // 重置 Session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        @session_start();
        $_SESSION = [];
    }

    public function testTokenGeneration(): void
    {
        $token = Csrf::token();
        $this->assertNotEmpty($token);
        $this->assertSame(64, strlen($token), 'Token 应为 64 字符（32字节 hex）');
    }

    public function testTokenConsistency(): void
    {
        $token1 = Csrf::token();
        $token2 = Csrf::token();
        $this->assertSame($token1, $token2, '同一会话内 token 应保持一致');
    }

    public function testValidateCorrectToken(): void
    {
        $token = Csrf::token();
        $this->assertTrue(Csrf::validate($token));
    }

    public function testValidateWrongToken(): void
    {
        Csrf::token();
        $this->assertFalse(Csrf::validate('wrong-token'));
    }

    public function testValidateEmptyToken(): void
    {
        Csrf::token();
        $this->assertFalse(Csrf::validate(''));
        $this->assertFalse(Csrf::validate(null));
    }

    public function testTokenExpiry(): void
    {
        $token = Csrf::token();

        // 模拟过期（设置创建时间为很久以前）
        $_SESSION['_csrf_token_time'] = time() - 10000;

        $this->assertFalse(Csrf::validate($token), '过期 token 应验证失败');
    }
}