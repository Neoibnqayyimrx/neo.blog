<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;

final class DashboardController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $postModel     = new Post();
        $categoryModel = new Category();
        $userModel     = new User();

        $posts      = $postModel->getLatest(50);
        $categories = $categoryModel->getAllWithCounts();

        View::render('admin/dashboard', [
            'title'           => 'Dashboard — Admin',
            'activeLink'      => 'dashboard',
            'totalPosts'      => $postModel->count(),
            'totalCategories' => $categoryModel->count(),
            'totalUsers'      => $userModel->count(),
            'featured'        => $postModel->getFeatured(),
            'recentPosts'     => array_slice($posts, 0, 5),
            'posts'           => $posts,
            'categories'      => $categories,
        ], layout: 'admin');
    }
}
