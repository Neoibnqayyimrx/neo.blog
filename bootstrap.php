<?php
/**
 * bootstrap.php — single entry point for application bootstrap.
 *
 * Loaded by the front controller. Sets up config, error handling, session,
 * the PSR-style class autoloader for app/, and opens the DB connection.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Core/helpers.php';

// ── PSR-4-ish autoloader for the App\ namespace ───────────────────────────────
spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class, 'App\\')) return;
    $relative = str_replace('\\', '/', substr($class, 4));
    $file     = APP_PATH . '/' . $relative . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// ── Error reporting ───────────────────────────────────────────────────────────
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors',     '1');
    if (!is_dir(LOG_PATH)) {
        @mkdir(LOG_PATH, 0755, true);
    }
    ini_set('error_log',      LOG_PATH . '/error.log');
    error_reporting(E_ALL);
}

// ── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);

// ── Session ───────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => ROOT_URL,
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('BLOG_SESSION');
    session_start();
}

// ── DB connection (lazily retrievable via Database::getInstance()) ────────────
\App\Core\Database::getInstance();
