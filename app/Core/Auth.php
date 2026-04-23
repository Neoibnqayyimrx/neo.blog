<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Auth
 *
 * Centralises session-based authentication and flash / old-input helpers.
 * Pages/controllers must go through this class instead of touching $_SESSION
 * directly, so the contract (and its security properties) stays in one place.
 */
final class Auth
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && (($_SESSION['user_is_admin'] ?? false) === true);
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /** Cache-and-return the current user row, or null. */
    public static function user(): ?array
    {
        if (!self::isLoggedIn()) return null;
        static $cached = null;
        if ($cached === null || ($cached['id'] ?? null) !== self::userId()) {
            $cached = (new User())->getById((int) self::userId());
        }
        return $cached;
    }

    /** Redirect to the sign-in page if the visitor is not logged in. */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            redirect(url('/signin'));
        }
    }

    /** Redirect to the homepage if the visitor is not an admin. */
    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            redirect(url('/'));
        }
    }

    /** Create the session on successful login. */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']       = (int) $user['id'];
        $_SESSION['user_is_admin'] = ((int) ($user['is_admin'] ?? 0)) === 1;
    }

    /** Destroy the session on logout. */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path']     ?? '/',
                $params['domain']   ?? '',
                (bool)($params['secure']   ?? false),
                (bool)($params['httponly'] ?? false),
            );
        }
        session_destroy();
    }

    // ── Flash messages ────────────────────────────────────────────────────────

    public static function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /** Read a flash (once) and clear it. */
    public static function getFlash(string $key): ?string
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    // ── Old input (repopulate forms after validation errors) ─────────────────

    public static function setOldInput(array $data): void
    {
        unset($data['password'], $data['createpassword'], $data['confirmpassword'], $data['new_password']);
        $_SESSION['old_input'] = $data;
    }

    public static function getOldInput(string $field, string $default = ''): string
    {
        $value = $_SESSION['old_input'][$field] ?? $default;
        unset($_SESSION['old_input'][$field]);
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
