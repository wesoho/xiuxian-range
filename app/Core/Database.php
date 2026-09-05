<?php
declare(strict_types=1);

namespace XiuXian\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * 数据库封装（基于 PDO）
 *
 * 平台自身代码**必须**使用参数化查询（prepare/execute）。
 * 关卡内的漏洞演示代码可以故意使用非参数化查询以演示漏洞。
 */
class Database
{
    private ?PDO $pdo = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 获取 PDO 连接（懒加载）
     */
    public function pdo(): PDO
    {
        if ($this->pdo === null) {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $driver = $this->config['driver'] ?? 'mysql';

            if ($driver === 'sqlite') {
                // 本地开发 / 测试模式：DB_DATABASE 为 SQLite 数据库文件路径
                $file = $this->config['database'];
                if ($file !== ':memory:' && !is_file($file)) {
                    $dir = dirname($file);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    touch($file);
                }
                $this->pdo = new PDO('sqlite:' . $file, null, null, $options);
                $this->pdo->exec('PRAGMA foreign_keys = ON');
                $this->pdo->exec('PRAGMA busy_timeout = 5000');
                return $this->pdo;
            }

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['port'] ?? 3306,
                $this->config['database'],
                $this->config['charset'] ?? 'utf8mb4'
            );

            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";

            try {
                $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
            } catch (PDOException $e) {
                if (config('app.debug')) {
                    throw $e;
                }
                // 非调试模式下不泄露 DSN/异常细节
                abort(503, '数据库连接失败，请稍后再试');
            }
        }
        return $this->pdo;
    }

    /**
     * 准备并执行 SQL（参数化）
     */
    public function execute(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * 查询单行
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->execute($sql, $params)->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * 查询多行
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->execute($sql, $params)->fetchAll();
    }

    /**
     * 查询单个标量值
     */
    public function fetchScalar(string $sql, array $params = []): mixed
    {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * 插入并返回 lastInsertId
     */
    public function insert(string $sql, array $params = []): string
    {
        $this->execute($sql, $params);
        return $this->pdo()->lastInsertId();
    }

    /**
     * 启动事务
     */
    public function beginTransaction(): void
    {
        $this->pdo()->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit(): void
    {
        $this->pdo()->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback(): void
    {
        if ($this->pdo()->inTransaction()) {
            $this->pdo()->rollBack();
        }
    }
}