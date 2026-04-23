<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

/**
 * Static info pages (About / Services). Kept here instead of as flat PHP
 * files so routes remain the single source of truth.
 */
final class PageController
{
    public function about(): void
    {
        View::render('pages/about', [
            'title'      => 'About — ' . APP_NAME,
            'activeLink' => 'about',
        ]);
    }

    public function services(): void
    {
        View::render('pages/services', [
            'title'      => 'Services — ' . APP_NAME,
            'activeLink' => 'services',
        ]);
    }
}
