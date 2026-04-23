<?php
/**
 * Front controller.
 *
 * Apache: mod_rewrite is configured in public/.htaccess to forward all
 * non-file requests here.
 *
 * PHP built-in server: use the router.php in the project root:
 *     php -S 127.0.0.1:8000 -t public router.php
 */

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$router = new \App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI']    ?? '/'
);
