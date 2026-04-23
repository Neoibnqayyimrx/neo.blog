<?php
/**
 * Application configuration.
 *
 * Values are read from environment variables (or .env), with sensible defaults
 * for local development. Never commit real credentials to version control.
 */

declare(strict_types=1);

// ── Load .env file if it exists ───────────────────────────────────────────────
$envFile = dirname(__DIR__) . '/.env';
if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($key === '') continue;
        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

/** Read an environment variable with a fallback. */
function env(string $key, string|int|bool|null $default = null): string|int|bool|null
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    $lower = strtolower($value);
    return match ($lower) {
        'true'  => true,
        'false' => false,
        'null'  => null,
        default => $value,
    };
}

// ── Environment ───────────────────────────────────────────────────────────────
define('APP_ENV',   env('APP_ENV', 'production'));
define('APP_NAME',  env('APP_NAME', 'NEOIBNQAYYIM Blog'));
define('APP_DEBUG', APP_ENV === 'development');

// ── Base URL path (leading + trailing slash) ─────────────────────────────────
// Detects whether the project is served from a subfolder so links still work.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath   = '/';
if ($scriptName !== '') {
    // e.g. "/index.php" or "/subdir/public/index.php"
    $scriptDir = str_replace('\\', '/', dirname($scriptName));
    $scriptDir = rtrim(preg_replace('#/public$#', '', $scriptDir), '/');
    $basePath  = ($scriptDir === '' || $scriptDir === '.') ? '/' : $scriptDir . '/';
}
define('ROOT_URL', $basePath);

// ── Database ──────────────────────────────────────────────────────────────────
// DB_DRIVER can be 'mysql' (default, matches docker-compose) or 'sqlite'.
define('DB_DRIVER',  env('DB_DRIVER',  'mysql'));
define('DB_HOST',    env('DB_HOST',    'localhost'));
define('DB_PORT',    (int) env('DB_PORT', 3306));
define('DB_NAME',    env('DB_NAME',    'php_blog'));
define('DB_USER',    env('DB_USER',    'root'));
define('DB_PASS',    env('DB_PASS',    ''));
define('DB_CHARSET', 'utf8mb4');
define('DB_SQLITE_PATH', dirname(__DIR__) . '/storage/database.sqlite');

// ── Paths ─────────────────────────────────────────────────────────────────────
define('BASE_PATH',     dirname(__DIR__));
define('APP_PATH',      BASE_PATH . '/app');
define('VIEW_PATH',     BASE_PATH . '/resources/views');
define('PUBLIC_PATH',   BASE_PATH . '/public');
define('UPLOAD_DIR',    PUBLIC_PATH . '/uploads/');
define('UPLOAD_URL',    ROOT_URL . 'uploads/');
define('LOG_PATH',      BASE_PATH . '/storage/logs');

// ── File uploads ─────────────────────────────────────────────────────────────
define('MAX_UPLOAD_SIZE', (int) env('MAX_UPLOAD_SIZE', 2_097_152)); // 2 MB
define('ALLOWED_MIME',    ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// ── Time ──────────────────────────────────────────────────────────────────────
define('APP_TIMEZONE', env('APP_TIMEZONE', 'UTC'));

// ── Session ──────────────────────────────────────────────────────────────────
define('SESSION_LIFETIME', (int) env('SESSION_LIFETIME', 7200)); // 2 hours
