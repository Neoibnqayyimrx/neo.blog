<?php
/**
 * router.php — for use with the PHP built-in web server.
 *
 * Run:
 *     php -S 127.0.0.1:8000 -t public router.php
 *
 * This script first serves static files out of /public verbatim, and forwards
 * everything else to the front controller. Using a router script here means
 * developers can test the project without installing Apache.
 */

declare(strict_types=1);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

// Serve existing static files directly.
$staticPath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && is_file($staticPath)) {
    return false; // let the built-in server serve the file
}

// Otherwise fall through to the front controller.
require __DIR__ . '/public/index.php';
