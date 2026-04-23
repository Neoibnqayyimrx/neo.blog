<?php
/**
 * @var array $category
 * @var array $posts
 * @var array $categories
 */

use App\Core\View;
?>

<div class="blog-head container">
    <span class="section-head__eyebrow">Category</span>
    <h1><?= e($category['title']) ?></h1>
    <p><?= count($posts) ?> article<?= count($posts) === 1 ? '' : 's' ?> in this category.</p>
</div>

<div class="container">
    <?php View::partial('partials/categories-row', [
        'categories'       => $categories,
        'activeCategoryId' => (int) $category['id'],
    ]); ?>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <i class="ri-folder-line"></i>
                <div class="empty-state__title">No posts in this category yet</div>
                <div>Browse <a href="<?= url('/blog') ?>">all articles</a> instead.</div>
            </div>
        <?php else: ?>
            <div class="post-grid">
                <?php foreach ($posts as $post): ?>
                    <?php View::partial('partials/post-card', ['post' => $post]); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
