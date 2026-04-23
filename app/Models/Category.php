<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY title");
    }

    public function getAllWithCounts(): array
    {
        return $this->db->fetchAll(
            "SELECT categories.*, COUNT(posts.id) AS post_count
               FROM categories
               LEFT JOIN posts ON posts.category_id = categories.id
              GROUP BY categories.id
              ORDER BY categories.title"
        );
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM categories WHERE id = ?", '', $id);
    }

    public function count(): int
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS c FROM categories");
        return (int) ($row['c'] ?? 0);
    }

    public function postCount(int $categoryId): int
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM posts WHERE category_id = ?",
            '', $categoryId
        );
        return (int) ($row['c'] ?? 0);
    }

    public function titleExists(string $title, int $excludeId = 0): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM categories WHERE title = ? AND id != ? LIMIT 1",
            '', $title, $excludeId
        );
        return $row !== null;
    }

    public function create(string $title): int
    {
        return $this->db->insert(
            "INSERT INTO categories (title) VALUES (?)", '', $title
        );
    }

    public function update(int $id, string $title): int
    {
        return $this->db->execute(
            "UPDATE categories SET title = ? WHERE id = ?",
            '', $title, $id
        );
    }

    public function delete(int $id): int
    {
        return $this->db->execute("DELETE FROM categories WHERE id = ?", '', $id);
    }
}
