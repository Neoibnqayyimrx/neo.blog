<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUsernameOrEmail(string $identifier): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1",
            '', $identifier, $identifier
        );
    }

    public function existsByUsernameOrEmail(string $username, string $email, int $excludeId = 0): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ? LIMIT 1",
            '', $username, $email, $excludeId
        );
        return $row !== null;
    }

    public function usernameTaken(string $username, int $excludeId = 0): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1",
            '', $username, $excludeId
        );
        return $row !== null;
    }

    public function emailTaken(string $email, int $excludeId = 0): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1",
            '', $email, $excludeId
        );
        return $row !== null;
    }

    public function create(
        string $firstname,
        string $lastname,
        string $username,
        string $email,
        string $plainPassword,
        string $avatar
    ): int {
        $hashed = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->db->insert(
            "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin)
             VALUES (?, ?, ?, ?, ?, ?, 0)",
            '',
            $firstname, $lastname, $username, $email, $hashed, $avatar
        );
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT id, firstname, lastname, username, email, avatar, is_admin, created_at
               FROM users
              ORDER BY created_at DESC"
        );
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT id, firstname, lastname, username, email, avatar, is_admin, created_at
               FROM users WHERE id = ?",
            '', $id
        );
    }

    public function count(): int
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS c FROM users");
        return (int) ($row['c'] ?? 0);
    }

    public function delete(int $id): int
    {
        return $this->db->execute("DELETE FROM users WHERE id = ?", '', $id);
    }

    public function setAdmin(int $id, bool $isAdmin): int
    {
        return $this->db->execute(
            "UPDATE users SET is_admin = ? WHERE id = ?",
            '', (int) $isAdmin, $id
        );
    }

    public function updateProfile(
        int    $id,
        string $firstname,
        string $lastname,
        string $username,
        string $email,
        string $avatar
    ): int {
        return $this->db->execute(
            "UPDATE users
                SET firstname = ?, lastname = ?, username = ?, email = ?, avatar = ?
              WHERE id = ?",
            '',
            $firstname, $lastname, $username, $email, $avatar, $id
        );
    }

    public function updatePassword(int $id, string $plainPassword): int
    {
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->db->execute(
            "UPDATE users SET password = ? WHERE id = ?",
            '', $hash, $id
        );
    }

    public function updateAvatar(int $id, string $avatar): int
    {
        return $this->db->execute(
            "UPDATE users SET avatar = ? WHERE id = ?",
            '', $avatar, $id
        );
    }
}
