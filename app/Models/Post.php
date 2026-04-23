<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Post model — all SQL for the posts table.
 */
final class Post
{
    private Database $db;

    private const BASE_SELECT = "
        SELECT
            posts.id,
            posts.title,
            posts.body,
            posts.date_time,
            posts.thumbnail,
            posts.is_featured,
            categories.title AS category_title,
            categories.id    AS category_id,
            users.id         AS author_id,
            users.avatar,
            users.firstname,
            users.lastname,
            users.username
        FROM posts
        INNER JOIN categories ON posts.category_id = categories.id
        INNER JOIN users      ON posts.author_id   = users.id
    ";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Featured post, or null. */
    public function getFeatured(): ?array
    {
        return $this->db->fetchOne(self::BASE_SELECT . " WHERE posts.is_featured = 1 LIMIT 1");
    }

    /** Latest $limit posts. */
    public function getLatest(int $limit = 6): array
    {
        return $this->db->fetchAll(
            self::BASE_SELECT . " ORDER BY posts.date_time DESC LIMIT ?",
            '', $limit
        );
    }

    /** All posts by category. */
    public function getByCategory(int $categoryId): array
    {
        return $this->db->fetchAll(
            self::BASE_SELECT . " WHERE posts.category_id = ? ORDER BY posts.date_time DESC",
            '', $categoryId
        );
    }

    /** Single post, or null. */
    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            self::BASE_SELECT . " WHERE posts.id = ?",
            '', $id
        );
    }

    /** LIKE search on title + body. */
    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        return $this->db->fetchAll(
            self::BASE_SELECT . " WHERE posts.title LIKE ? OR posts.body LIKE ? ORDER BY posts.date_time DESC",
            '', $like, $like
        );
    }

    public function count(): int
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS c FROM posts");
        return (int) ($row['c'] ?? 0);
    }

    /** Clear the featured flag for all other posts. */
    public function clearFeaturedExcept(int $keepId = 0): void
    {
        $this->db->execute(
            "UPDATE posts SET is_featured = 0 WHERE is_featured = 1 AND id != ?",
            '', $keepId
        );
    }

    public function create(
        string $title,
        string $body,
        string $thumbnail,
        int    $categoryId,
        int    $authorId,
        bool   $isFeatured = false
    ): int {
        return $this->db->insert(
            "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured, date_time)
             VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)",
            '',
            $title, $body, $thumbnail, $categoryId, $authorId, (int) $isFeatured
        );
    }

    public function update(
        int    $id,
        string $title,
        string $body,
        int    $categoryId,
        bool   $isFeatured
    ): int {
        return $this->db->execute(
            "UPDATE posts
                SET title = ?, body = ?, category_id = ?, is_featured = ?
              WHERE id = ?",
            '',
            $title, $body, $categoryId, (int) $isFeatured, $id
        );
    }

    public function updateThumbnail(int $id, string $thumbnail): int
    {
        return $this->db->execute(
            "UPDATE posts SET thumbnail = ? WHERE id = ?",
            '', $thumbnail, $id
        );
    }

    public function delete(int $id): int
    {
        return $this->db->execute("DELETE FROM posts WHERE id = ?", '', $id);
    }
}
