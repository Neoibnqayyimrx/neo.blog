<?php
/** @var string $activeLink */

use App\Core\Auth;

$user = Auth::user();
$is   = fn (string $name) => ($activeLink ?? '') === $name ? 'is-active' : '';
?>
<aside class="sidebar" aria-label="Admin navigation">
    <div class="sidebar__brand">
        <span class="sidebar__brand-logo">N</span>
        <div>
            <div class="sidebar__brand-text">NEO · BLOG</div>
            <div class="sidebar__brand-sub">Admin panel</div>
        </div>
    </div>

    <div class="sidebar__profile">
        <span class="avatar avatar--sm">
            <img src="<?= avatar_url($user['avatar'] ?? '') ?>" alt="">
        </span>
        <div class="sidebar__profile-meta">
            <span class="sidebar__profile-name"><?= e(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?></span>
            <span class="sidebar__profile-role"><?= Auth::isAdmin() ? 'Administrator' : 'Editor' ?></span>
        </div>
    </div>

    <nav class="sidebar__nav" aria-label="Admin">
        <div class="sidebar__group">Main</div>
        <a href="<?= url('/admin') ?>" class="sidebar__link <?= $is('dashboard') ?>">
            <i class="ri-dashboard-3-line"></i> <span>Dashboard</span>
        </a>

        <div class="sidebar__group">Content</div>
        <a href="<?= url('/admin/posts') ?>" class="sidebar__link <?= $is('posts') ?>">
            <i class="ri-article-line"></i> <span>All posts</span>
        </a>
        <a href="<?= url('/admin/posts/create') ?>" class="sidebar__link <?= $is('add-post') ?>">
            <i class="ri-add-box-line"></i> <span>Add post</span>
        </a>
        <a href="<?= url('/admin/categories') ?>" class="sidebar__link <?= $is('categories') ?>">
            <i class="ri-folder-2-line"></i> <span>Categories</span>
        </a>
        <a href="<?= url('/admin/users') ?>" class="sidebar__link <?= $is('users') ?>">
            <i class="ri-team-line"></i> <span>Users</span>
        </a>

        <div class="sidebar__group">Site</div>
        <a href="<?= url('/') ?>" class="sidebar__link">
            <i class="ri-external-link-line"></i> <span>View blog</span>
        </a>
    </nav>

    <div class="sidebar__logout">
        <form action="<?= url('/logout') ?>" method="post" style="margin:0;">
            <?= csrfField() ?>
            <button type="submit" class="sidebar__link" style="width:100%; background:none; border:0; text-align:left; color: rgba(255,255,255,0.72);">
                <i class="ri-logout-box-r-line"></i> <span>Sign out</span>
            </button>
        </form>
    </div>
</aside>
