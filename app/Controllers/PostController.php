<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Post;

final class PostController
{
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect(url('/'));
        }

        $post = (new Post())->getById($id);
        if (!$post) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Post not found']);
            return;
        }

        $related = (new Post())->getByCategory((int) $post['category_id']);
        // Filter out the current post and keep at most 3
        $related = array_values(array_filter($related, fn ($p) => (int) $p['id'] !== (int) $post['id']));
        $related = array_slice($related, 0, 3);

        View::render('post/show', [
            'title'      => $post['title'] . ' — ' . APP_NAME,
            'post'       => $post,
            'related'    => $related,
            'categories' => (new Category())->getAll(),
            'activeLink' => 'blog',
        ]);
    }
}
