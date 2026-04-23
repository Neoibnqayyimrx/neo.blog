<?php
/**
 * @var string $term
 * @var array  $posts
 * @var array  $categories
 */

use App\Core\View;
?>

<div class="blog-head container">
    <span class="section-head__eyebrow">Search</span>
    <h1>Results for “<?= e($term) ?>”</h1>
    <p>
        <?= count($posts) ?> article<?= count($posts) === 1 ? '' : 's' ?> found.
        <a href="<?= url('/blog') ?>">Reset search</a>.
    </p>
</div>

<div class="container">
    <form action="<?= url('/search') ?>" method="get" class="search-bar">
        <input type="search" name="q" value="<?= e($term) ?>" class="form-input" aria-label="Search">
        <button type="submit" class="btn"><i class="ri-search-line"></i> Search</button>
    </form>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <i class="ri-search-eye-line"></i>
                <div class="empty-state__title">No matches found</div>
                <div>Try a different keyword or browse categories on the blog.</div>
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
