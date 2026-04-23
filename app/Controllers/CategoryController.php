<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Post;

final class CategoryController
{
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect(url('/'));
        }

        $categoryModel = new Category();
        $category      = $categoryModel->getById($id);
        if (!$category) {
            redirect(url('/'));
        }

        View::render('category/show', [
            'title'      => $category['title'] . ' — ' . APP_NAME,
            'category'   => $category,
            'posts'      => (new Post())->getByCategory($id),
            'categories' => $categoryModel->getAll(),
            'activeLink' => 'blog',
        ]);
    }
}
