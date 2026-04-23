<?php
/**
 * @var array $post
 * @var array $related
 */

use App\Core\View;
?>

<section class="section">
    <div class="container post-show">
        <header class="post-show__header">
            <a href="<?= url('/category?id=' . (int) $post['category_id']) ?>" class="badge badge--primary">
                <?= e($post['category_title']) ?>
            </a>
            <h1><?= e($post['title']) ?></h1>
            <div class="post-show__meta">
                <span class="avatar avatar--sm">
                    <img src="<?= avatar_url($post['avatar']) ?>" alt="">
                </span>
                <span><?= e($post['firstname'] . ' ' . $post['lastname']) ?></span>
                <span class="dot"></span>
                <span><?= e(formatDate($post['date_time'], 'F j, Y')) ?></span>
                <span class="dot"></span>
                <span><?= readingTime($post['body']) ?> min read</span>
            </div>
        </header>

        <figure class="post-show__thumb">
            <img src="<?= thumbnail_url($post['thumbnail']) ?>" alt="<?= e($post['title']) ?>">
        </figure>

        <div class="post-show__body prose">
            <?= nl2br(e($post['body'])) ?>
        </div>

        <footer class="post-show__footer">
            <div class="post-show__author">
                <span class="avatar avatar--lg">
                    <img src="<?= avatar_url($post['avatar']) ?>" alt="">
                </span>
                <div>
                    <div class="post-show__author-name"><?= e($post['firstname'] . ' ' . $post['lastname']) ?></div>
                    <div class="post-show__author-role">Contributor · @<?= e($post['username']) ?></div>
                </div>
            </div>
            <a href="<?= url('/blog') ?>" class="btn btn--secondary btn--sm">
                <i class="ri-arrow-left-line"></i> Back to blog
            </a>
        </footer>

        <?php if (!empty($related)): ?>
            <div class="related">
                <div class="section-head">
                    <div>
                        <span class="section-head__eyebrow">Keep reading</span>
                        <h2>More in <?= e($post['category_title']) ?></h2>
                    </div>
                </div>
                <div class="post-grid">
                    <?php foreach ($related as $r): ?>
                        <?php View::partial('partials/post-card', ['post' => $r]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
