<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Post;

final class BlogController
{
    public function index(): void
    {
        $posts      = (new Post())->getLatest(100);
        $categories = (new Category())->getAll();

        View::render('blog/index', [
            'title'      => 'Blog — ' . APP_NAME,
            'posts'      => $posts,
            'categories' => $categories,
            'activeLink' => 'blog',
        ]);
    }
}
