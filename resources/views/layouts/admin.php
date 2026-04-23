<?php
/** @var string $_content */
/** @var string $title */
/** @var string $activeLink */

use App\Core\View;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin — ' . APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
</head>
<body class="is-admin">

<button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
    <i class="ri-menu-line"></i>
</button>

<div class="dashboard" id="dashboardShell">
    <?php View::partial('partials/admin-sidebar', ['activeLink' => $activeLink ?? '']); ?>

    <div class="dashboard__main">
        <?= $_content ?>
    </div>
</div>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
