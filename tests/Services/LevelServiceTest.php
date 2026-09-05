<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use XiuXian\Services\LevelService;

/**
 * 修真境界系统测试
 *
 * 晋升规则（v2）：以通关当前境界全部关卡为准，积分不参与境界判定。
 * realmProgress 依赖数据库，通关进度相关断言见 tests/integration.php。
 */
final class LevelServiceTest extends TestCase
{
    public function testRealmOrderIsCorrect(): void
    {
        $this->assertSame([
            'liqi', 'zhuji', 'jindan', 'yuanying',
            'huashen', 'lianxu', 'heti', 'dacheng'
        ], LevelService::REALM_ORDER);
    }

    public function testNextRealm(): void
    {
        $this->assertSame('zhuji', LevelService::nextRealm('liqi'));
        $this->assertSame('jindan', LevelService::nextRealm('zhuji'));
        $this->assertSame('dacheng', LevelService::nextRealm('heti'));
        $this->assertNull(LevelService::nextRealm('dacheng'), '大乘已到顶');
    }

    public function testNextRealmUnknown(): void
    {
        $this->assertNull(LevelService::nextRealm('unknown_realm'));
    }

    public function testRealmNamesMatchOrder(): void
    {
        foreach (LevelService::REALM_ORDER as $realm) {
            $this->assertArrayHasKey($realm, LevelService::REALM_NAMES);
            $this->assertNotEmpty(LevelService::REALM_NAMES[$realm]);
        }
    }

    public function testTitlesExist(): void
    {
        foreach (LevelService::REALM_ORDER as $idx => $realm) {
            if ($idx === 0) continue; // 炼气没有专属称号
            $this->assertArrayHasKey($realm, LevelService::TITLES);
            $this->assertNotEmpty(LevelService::TITLES[$realm]);
        }
    }
}
