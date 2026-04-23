<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database
 *
 * Thin PDO wrapper with the same public API as the original Database class
 * (fetchAll/fetchOne/execute/insert) so existing models keep working.
 *
 * Supports two drivers:
 *   - mysql  (matches docker-compose.yml — default in production)
 *   - sqlite (zero-config local run, great for quick demos)
 *
 * All queries go through prepared statements with positional `?` placeholders.
 * The legacy mysqli "types" string is accepted for backwards compatibility and
 * simply ignored (PDO auto-detects types).
 */
final class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        try {
            if (DB_DRIVER === 'sqlite') {
                $this->pdo = new PDO('sqlite:' . DB_SQLITE_PATH);
                $this->pdo->exec('PRAGMA foreign_keys = ON');
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_PORT,
                    DB_NAME,
                    DB_CHARSET
                );
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            }
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /** Fetch all rows as associative arrays. */
    public function fetchAll(string $sql, string $types = '', mixed ...$params): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /** Fetch a single row, or null if not found. */
    public function fetchOne(string $sql, string $types = '', mixed ...$params): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Execute an INSERT / UPDATE / DELETE; returns affected row count. */
    public function execute(string $sql, string $types = '', mixed ...$params): int
    {
        return $this->run($sql, $params)->rowCount();
    }

    /** Execute an INSERT and return the new row id. */
    public function insert(string $sql, string $types = '', mixed ...$params): int
    {
        $this->run($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }

    /** Run raw SQL (no bindings). Useful during install/migrate. */
    public function exec(string $sql): int
    {
        return (int) $this->pdo->exec($sql);
    }

    // ── Internals ────────────────────────────────────────────────────────────

    private function run(string $sql, array $params): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        // PDO expects 1-based positional binds; ->execute(array) handles it.
        $stmt->execute($params);
        return $stmt;
    }

    // Prevent cloning / unserialization of the singleton.
    private function __clone() {}
    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize a singleton.');
    }
}
