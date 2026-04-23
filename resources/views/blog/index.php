<?php
/**
 * @var array $posts
 * @var array $categories
 */

use App\Core\View;
?>

<div class="blog-head container">
    <h1>The Blog</h1>
    <p>All the writing, unfiltered. Filter by category, search for a keyword, or just browse the latest.</p>
</div>

<div class="container">
    <form action="<?= url('/search') ?>" method="get" class="search-bar">
        <input type="search" name="q" class="form-input" placeholder="Search articles by title or keyword…" aria-label="Search">
        <button type="submit" class="btn">
            <i class="ri-search-line"></i> Search
        </button>
    </form>

    <?php View::partial('partials/categories-row', ['categories' => $categories]); ?>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <i class="ri-inbox-line"></i>
                <div class="empty-state__title">Nothing here yet</div>
                <div>Check back soon — new posts are published regularly.</div>
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
