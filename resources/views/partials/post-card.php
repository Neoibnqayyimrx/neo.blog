<?php
/** @var array $post */
?>
<article class="post-card">
    <a href="<?= url('/post?id=' . (int) $post['id']) ?>" class="post-card__thumb">
        <img src="<?= thumbnail_url($post['thumbnail'] ?? '') ?>" alt="<?= e($post['title']) ?>">
    </a>
    <div class="post-card__body">
        <div class="post-card__meta">
            <a href="<?= url('/category?id=' . (int) $post['category_id']) ?>" class="badge badge--primary"><?= e($post['category_title']) ?></a>
            <span>·</span>
            <span><?= e(formatDate($post['date_time'], 'M d, Y')) ?></span>
        </div>
        <h3 class="post-card__title">
            <a href="<?= url('/post?id=' . (int) $post['id']) ?>"><?= e($post['title']) ?></a>
        </h3>
        <p class="post-card__excerpt"><?= e(excerpt(strip_tags($post['body']), 160)) ?></p>
    </div>
    <div class="post-card__footer">
        <div class="post-card__author">
            <span class="avatar avatar--xs">
                <img src="<?= avatar_url($post['avatar'] ?? '') ?>" alt="">
            </span>
            <span><?= e($post['firstname'] . ' ' . $post['lastname']) ?></span>
        </div>
        <span class="post-card__readtime"><?= readingTime($post['body']) ?> min read</span>
    </div>
</article>
