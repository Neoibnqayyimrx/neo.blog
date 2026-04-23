<?php
/** @var string $_content */
/** @var string $title */
/** @var string|null $activeLink */

use App\Core\Auth;
use App\Core\View;

$activeLink = $activeLink ?? '';
$isActive   = fn (string $name) => $activeLink === $name ? 'is-active' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
</head>
<body>
<header class="site-nav" id="siteNav">
    <div class="container site-nav__inner">
        <a href="<?= url('/') ?>" class="site-nav__brand">
            NEO<span class="dot">·</span>BLOG
        </a>

        <nav class="site-nav__links" aria-label="Primary">
            <a href="<?= url('/') ?>"        class="<?= $isActive('home') ?>">Home</a>
            <a href="<?= url('/blog') ?>"    class="<?= $isActive('blog') ?>">Blog</a>
            <a href="<?= url('/about') ?>"   class="<?= $isActive('about') ?>">About</a>
            <a href="<?= url('/services') ?>"class="<?= $isActive('services') ?>">Services</a>
            <a href="<?= url('/contact') ?>" class="<?= $isActive('contact') ?>">Contact</a>
        </nav>

        <div class="site-nav__actions">
            <?php if (Auth::isLoggedIn()): $u = Auth::user(); ?>
                <div class="site-nav__profile" tabindex="0">
                    <button type="button" class="site-nav__profile-btn" aria-haspopup="menu">
                        <span class="avatar avatar--sm">
                            <img src="<?= avatar_url($u['avatar'] ?? '') ?>" alt="">
                        </span>
                        <span class="md-hide" style="padding-right: .5rem; font-size: .875rem; font-weight:500;">
                            <?= e($u['firstname'] ?? 'Account') ?>
                        </span>
                    </button>
                    <div class="site-nav__profile-menu" role="menu">
                        <?php if (Auth::isAdmin()): ?>
                            <a href="<?= url('/admin') ?>"><i class="ri-dashboard-3-line"></i> Dashboard</a>
                        <?php endif; ?>
                        <a href="<?= url('/blog') ?>"><i class="ri-book-read-line"></i> My Reading</a>
                        <form action="<?= url('/logout') ?>" method="post" style="margin:0;">
                            <?= csrfField() ?>
                            <button type="submit" class="site-nav__profile-menu-item" style="display:flex; gap:.5rem; align-items:center; padding:.5rem .75rem; width:100%; text-align:left; color: var(--color-danger); font-size: var(--fs-sm); border-radius: var(--radius-sm);">
                                <i class="ri-logout-box-r-line"></i> Sign out
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= url('/signin') ?>" class="btn btn--ghost btn--sm md-hide">Sign in</a>
                <a href="<?= url('/signup') ?>" class="btn btn--sm">Get started</a>
            <?php endif; ?>

            <button type="button" class="site-nav__toggle" id="siteNavToggle" aria-label="Toggle menu" aria-controls="siteNav">
                <i class="ri-menu-line"></i>
            </button>
        </div>
    </div>
</header>

<main id="main">
<?= $_content ?>
</main>

<?php View::partial('partials/site-footer'); ?>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
