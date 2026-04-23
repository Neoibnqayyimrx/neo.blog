<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Models\Category;

final class CategoryController
{
    public function index(): void
    {
        Auth::requireAdmin();

        View::render('admin/categories/index', [
            'title'      => 'Categories — Admin',
            'activeLink' => 'categories',
            'categories' => (new Category())->getAllWithCounts(),
            'success'    => Auth::getFlash('admin-success'),
            'error'      => Auth::getFlash('admin-error'),
        ], layout: 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $title = post('title');
        if ($title === '')               { $this->error('Category name is required.'); }
        if (strlen($title) > 100)        { $this->error('Category name must be 100 characters or fewer.'); }

        $categoryModel = new Category();
        if ($categoryModel->titleExists($title)) {
            $this->error('A category with that name already exists.');
        }

        $categoryModel->create($title);
        Auth::setFlash('admin-success', 'Category "' . $title . '" created.');
        redirect(url('/admin/categories'));
    }

    public function update(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $id    = (int) post('category_id');
        $title = post('title');

        if ($id <= 0)                    { redirect(url('/admin/categories')); }
        if ($title === '')               { $this->error('Category name is required.'); }
        if (strlen($title) > 100)        { $this->error('Category name must be 100 characters or fewer.'); }

        $categoryModel = new Category();
        if (!$categoryModel->getById($id)) {
            $this->error('Category not found.');
        }
        if ($categoryModel->titleExists($title, $id)) {
            $this->error('A category with that name already exists.');
        }

        $categoryModel->update($id, $title);
        Auth::setFlash('admin-success', 'Category updated to "' . $title . '".');
        redirect(url('/admin/categories'));
    }

    public function delete(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $id = (int) post('id');
        if ($id <= 0) redirect(url('/admin/categories'));

        $categoryModel = new Category();
        $category      = $categoryModel->getById($id);
        if (!$category) $this->error('Category not found.');

        if ($categoryModel->postCount($id) > 0) {
            $this->error('Cannot delete "' . $category['title'] . '" — reassign or delete its posts first.');
        }

        $categoryModel->delete($id);
        Auth::setFlash('admin-success', 'Category "' . $category['title'] . '" deleted.');
        redirect(url('/admin/categories'));
    }

    private function error(string $message): never
    {
        Auth::setFlash('admin-error', $message);
        redirect(url('/admin/categories'));
    }
}
