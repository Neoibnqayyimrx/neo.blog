<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Models\Category;
use App\Models\Post;

final class PostController
{
    public function index(): void
    {
        Auth::requireAdmin();

        View::render('admin/posts/index', [
            'title'      => 'All posts — Admin',
            'activeLink' => 'posts',
            'posts'      => (new Post())->getLatest(100),
            'success'    => Auth::getFlash('admin-success'),
            'error'      => Auth::getFlash('admin-error'),
        ], layout: 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();

        View::render('admin/posts/create', [
            'title'      => 'Add post — Admin',
            'activeLink' => 'add-post',
            'categories' => (new Category())->getAll(),
            'success'    => Auth::getFlash('admin-success'),
            'error'      => Auth::getFlash('admin-error'),
        ], layout: 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $title      = post('title');
        $body       = post('body');
        $categoryId = (int) post('category_id');
        $isFeatured = isset($_POST['is_featured']);
        $thumbnail  = $_FILES['thumbnail'] ?? null;

        $errors = [];
        if ($title === '')               $errors[] = 'Post title is required.';
        if ($body === '')                $errors[] = 'Post body is required.';
        if ($categoryId <= 0)            $errors[] = 'Please select a valid category.';
        if (empty($thumbnail['name']))   $errors[] = 'A thumbnail image is required.';

        if ($errors) {
            Auth::setFlash('admin-error', implode(' ', $errors));
            Auth::setOldInput($_POST);
            redirect(url('/admin/posts/create'));
        }

        try {
            $filename = handleImageUpload($thumbnail, UPLOAD_DIR);
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            Auth::setFlash('admin-error', $e->getMessage());
            Auth::setOldInput($_POST);
            redirect(url('/admin/posts/create'));
        }

        $postModel = new Post();
        if ($isFeatured) {
            $postModel->clearFeaturedExcept(0);
        }

        $newId = $postModel->create(
            $title, $body, $filename, $categoryId, (int) Auth::userId(), $isFeatured
        );
        if (!$newId) {
            Auth::setFlash('admin-error', 'Failed to create the post. Please try again.');
            redirect(url('/admin/posts/create'));
        }

        Auth::setFlash('admin-success', 'Post published successfully.');
        redirect(url('/admin/posts'));
    }

    public function edit(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) redirect(url('/admin/posts'));

        $post = (new Post())->getById($id);
        if (!$post) {
            Auth::setFlash('admin-error', 'Post not found.');
            redirect(url('/admin/posts'));
        }

        View::render('admin/posts/edit', [
            'title'      => 'Edit post — Admin',
            'activeLink' => 'posts',
            'post'       => $post,
            'categories' => (new Category())->getAll(),
            'success'    => Auth::getFlash('admin-success'),
            'error'      => Auth::getFlash('admin-error'),
        ], layout: 'admin');
    }

    public function update(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $postId     = (int) post('post_id');
        $title      = post('title');
        $body       = post('body');
        $categoryId = (int) post('category_id');
        $isFeatured = isset($_POST['is_featured']);
        $thumbnail  = $_FILES['thumbnail'] ?? null;

        if ($postId <= 0) redirect(url('/admin/posts'));
        $back = url('/admin/posts/edit') . '?id=' . $postId;

        $errors = [];
        if ($title === '')      $errors[] = 'Post title is required.';
        if ($body === '')       $errors[] = 'Post body is required.';
        if ($categoryId <= 0)   $errors[] = 'Please select a valid category.';
        if ($errors) {
            Auth::setFlash('admin-error', implode(' ', $errors));
            redirect($back);
        }

        $postModel = new Post();
        $existing  = $postModel->getById($postId);
        if (!$existing) {
            Auth::setFlash('admin-error', 'Post not found.');
            redirect(url('/admin/posts'));
        }

        // Optional thumbnail replacement.
        if (is_array($thumbnail) && !empty($thumbnail['name'])) {
            try {
                $newThumb = handleImageUpload($thumbnail, UPLOAD_DIR);
                if ($existing['thumbnail'] !== 'default-thumbnail.png') {
                    $old = UPLOAD_DIR . $existing['thumbnail'];
                    if (is_file($old)) @unlink($old);
                }
                $postModel->updateThumbnail($postId, $newThumb);
            } catch (\InvalidArgumentException | \RuntimeException $e) {
                Auth::setFlash('admin-error', $e->getMessage());
                redirect($back);
            }
        }

        if ($isFeatured) {
            $postModel->clearFeaturedExcept($postId);
        }

        $postModel->update($postId, $title, $body, $categoryId, $isFeatured);
        Auth::setFlash('admin-success', 'Post updated successfully.');
        redirect(url('/admin/posts'));
    }

    public function delete(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $id = (int) post('id');
        if ($id <= 0) redirect(url('/admin/posts'));

        $postModel = new Post();
        $post      = $postModel->getById($id);
        if (!$post) {
            Auth::setFlash('admin-error', 'Post not found.');
            redirect(url('/admin/posts'));
        }

        if (!empty($post['thumbnail']) && $post['thumbnail'] !== 'default-thumbnail.png') {
            $path = UPLOAD_DIR . $post['thumbnail'];
            if (is_file($path)) @unlink($path);
        }

        $postModel->delete($id);
        Auth::setFlash('admin-success', 'Post "' . $post['title'] . '" was deleted.');
        redirect(url('/admin/posts'));
    }
}
