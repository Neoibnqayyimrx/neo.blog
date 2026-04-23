<?php
/**
 * @var int   $totalPosts
 * @var int   $totalCategories
 * @var int   $totalUsers
 * @var array $featured
 * @var array $recentPosts
 * @var array $posts
 * @var array $categories
 */
?>
<div class="admin-topbar">
    <div>
        <div class="admin-topbar__crumb">
            <a href="<?= url('/admin') ?>">Admin</a> <i class="ri-arrow-right-s-line"></i> <span>Dashboard</span>
        </div>
        <div class="admin-topbar__title"><h1>Dashboard</h1></div>
        <div class="admin-topbar__sub">An overview of your content and community.</div>
    </div>
    <div class="admin-topbar__actions">
        <a href="<?= url('/') ?>" class="btn btn--secondary btn--sm">
            <i class="ri-external-link-line"></i> View site
        </a>
        <a href="<?= url('/admin/posts/create') ?>" class="btn btn--sm">
            <i class="ri-add-line"></i> New post
        </a>
    </div>
</div>

<div class="admin-content">
    <div class="stats-grid">
        <div class="stat stat--primary">
            <div class="stat__icon"><i class="ri-article-line"></i></div>
            <div>
                <div class="stat__label">Total posts</div>
                <div class="stat__value"><?= (int) $totalPosts ?></div>
                <div class="stat__hint">Published stories</div>
            </div>
        </div>
        <div class="stat stat--success">
            <div class="stat__icon"><i class="ri-folder-2-line"></i></div>
            <div>
                <div class="stat__label">Categories</div>
                <div class="stat__value"><?= (int) $totalCategories ?></div>
                <div class="stat__hint">Organised topics</div>
            </div>
        </div>
        <div class="stat stat--warning">
            <div class="stat__icon"><i class="ri-star-line"></i></div>
            <div>
                <div class="stat__label">Featured</div>
                <div class="stat__value"><?= $featured ? 1 : 0 ?></div>
                <div class="stat__hint">Pinned on homepage</div>
            </div>
        </div>
        <div class="stat stat--danger">
            <div class="stat__icon"><i class="ri-team-line"></i></div>
            <div>
                <div class="stat__label">Users</div>
                <div class="stat__value"><?= (int) $totalUsers ?></div>
                <div class="stat__hint">Registered accounts</div>
            </div>
        </div>
    </div>

    <div class="admin-grid">
        <div class="card">
            <div class="card__header">
                <div>
                    <h3 class="card__title">Recent posts</h3>
                    <div class="card__subtitle">Your latest five stories</div>
                </div>
                <a href="<?= url('/admin/posts') ?>" class="btn btn--ghost btn--sm">View all</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPosts)): ?>
                            <tr><td colspan="5" class="table__empty">No posts yet.</td></tr>
                        <?php else: foreach ($recentPosts as $post): ?>
                            <tr>
                                <td>
                                    <div class="table__thumb">
                                        <img src="<?= thumbnail_url($post['thumbnail']) ?>" alt="">
                                    </div>
                                </td>
                                <td class="table__title"><?= e(excerpt($post['title'], 60)) ?></td>
                                <td><span class="badge badge--primary"><?= e($post['category_title']) ?></span></td>
                                <td class="table__muted"><?= e(formatDate($post['date_time'], 'M d, Y')) ?></td>
                                <td class="text-end">
                                    <a href="<?= url('/admin/posts/edit?id=' . (int) $post['id']) ?>"
                                       class="table__action table__action--edit" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card__header">
                <div>
                    <h3 class="card__title">Categories</h3>
                    <div class="card__subtitle">Post counts per category</div>
                </div>
                <a href="<?= url('/admin/categories') ?>" class="btn btn--ghost btn--sm">Manage</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Category</th><th>Posts</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="2" class="table__empty">No categories yet.</td></tr>
                        <?php else: foreach ($categories as $c): ?>
                            <tr>
                                <td><span class="color-dot"></span> <?= e($c['title']) ?></td>
                                <td class="table__muted"><?= (int) $c['post_count'] ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
