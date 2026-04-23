<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View
 *
 * Minimal template renderer. Views receive their data as variables extracted
 * from the $data array, and a layout file wraps the view content.
 *
 * Usage:
 *     View::render('blog/index', ['posts' => $posts], layout: 'site');
 */
final class View
{
    public static function render(string $view, array $data = [], string $layout = 'site'): void
    {
        $data['title']    = $data['title']    ?? APP_NAME;
        $data['flash']    = $data['flash']    ?? null;
        $data['_content'] = self::renderPartial($view, $data);

        if ($layout === '' || $layout === null) {
            echo $data['_content'];
            return;
        }

        $layoutPath = VIEW_PATH . "/layouts/{$layout}.php";
        if (!is_file($layoutPath)) {
            throw new \RuntimeException("Layout not found: $layoutPath");
        }

        (static function (array $data, string $layoutPath): void {
            extract($data, EXTR_SKIP);
            require $layoutPath;
        })($data, $layoutPath);
    }

    public static function renderPartial(string $view, array $data = []): string
    {
        $path = VIEW_PATH . "/{$view}.php";
        if (!is_file($path)) {
            throw new \RuntimeException("View not found: $path");
        }

        return (static function (array $data, string $path): string {
            extract($data, EXTR_SKIP);
            ob_start();
            require $path;
            return (string) ob_get_clean();
        })($data, $path);
    }

    /** Include a partial with its own scope (used inside layout files). */
    public static function partial(string $view, array $data = []): void
    {
        echo self::renderPartial($view, $data);
    }
}
