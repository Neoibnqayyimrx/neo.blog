<?php
/**
 * @var array|null $featured
 * @var array      $posts
 * @var array      $categories
 */

use App\Core\View;
?>

<section class="hero">
    <div class="container hero__inner">
        <div>
            <span class="hero__badge"><i class="ri-sparkling-line"></i> Writing that pays attention</span>
            <h1>Thoughtful stories on software, culture and craft.</h1>
            <p>
                NEO · BLOG is a modern publishing platform built for writers who care about typography, images, and the ideas behind their posts.
                Dive into featured essays, or browse the latest from our contributors.
            </p>
            <div class="hero__actions">
                <a href="<?= url('/blog') ?>" class="btn btn--lg">
                    Read the blog <i class="ri-arrow-right-line"></i>
                </a>
                <a href="<?= url('/about') ?>" class="btn btn--outline btn--lg">Learn more</a>
            </div>
        </div>

        <?php if ($featured): ?>
            <a href="<?= url('/post?id=' . (int) $featured['id']) ?>" class="hero__featured-card">
                <div class="hero__featured-thumb">
                    <img src="<?= thumbnail_url($featured['thumbnail']) ?>" alt="<?= e($featured['title']) ?>">
                </div>
                <div class="hero__featured-body">
                    <span class="badge"><?= e($featured['category_title']) ?></span>
                    <h3><?= e($featured['title']) ?></h3>
                    <p><?= e(excerpt(strip_tags($featured['body']), 140)) ?></p>
                    <div class="hero__featured-meta">
                        <span class="avatar avatar--xs">
                            <img src="<?= avatar_url($featured['avatar']) ?>" alt="">
                        </span>
                        <span><?= e($featured['firstname'] . ' ' . $featured['lastname']) ?></span>
                        <span>·</span>
                        <span><?= e(timeAgo($featured['date_time'])) ?></span>
                    </div>
                </div>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="section-head__eyebrow">Latest</span>
                <h2>Fresh from the blog</h2>
                <p>Our most recent stories across every topic we cover — updated whenever an author publishes.</p>
            </div>
            <a href="<?= url('/blog') ?>" class="btn btn--ghost btn--sm">
                All articles <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <?php View::partial('partials/categories-row', ['categories' => $categories]); ?>

        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <i class="ri-inbox-line"></i>
                <div class="empty-state__title">No posts yet</div>
                <div>Sign in as an admin to publish the first story.</div>
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
