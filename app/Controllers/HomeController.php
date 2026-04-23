<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Post;

final class HomeController
{
    public function index(): void
    {
        $postModel     = new Post();
        $categoryModel = new Category();

        View::render('home', [
            'title'      => APP_NAME,
            'featured'   => $postModel->getFeatured(),
            'posts'      => $postModel->getLatest(6),
            'categories' => $categoryModel->getAll(),
            'activeLink' => 'home',
        ]);
    }
}
