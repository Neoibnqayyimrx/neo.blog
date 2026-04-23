<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Post;

final class SearchController
{
    public function index(): void
    {
        $term = trim((string) ($_GET['q'] ?? $_GET['search'] ?? ''));
        if ($term === '') {
            redirect(url('/blog'));
        }

        View::render('blog/search', [
            'title'      => 'Search: ' . $term,
            'term'       => $term,
            'posts'      => (new Post())->search($term),
            'categories' => (new Category())->getAll(),
            'activeLink' => 'blog',
        ]);
    }
}
