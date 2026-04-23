<?php
/**
 * @var array $posts
 * @var string|null $success
 * @var string|null $error
 */
?>
<div class="admin-topbar">
    <div>
        <div class="admin-topbar__crumb">
            <a href="<?= url('/admin') ?>">Admin</a> <i class="ri-arrow-right-s-line"></i> <span>Posts</span>
        </div>
        <div class="admin-topbar__title"><h1>All posts</h1></div>
        <div class="admin-topbar__sub">Create, edit or remove published stories.</div>
    </div>
    <div class="admin-topbar__actions">
        <a href="<?= url('/admin/posts/create') ?>" class="btn btn--sm">
            <i class="ri-add-line"></i> New post
        </a>
    </div>
</div>

<div class="admin-content">
    <?php if ($success): ?>
        <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
    <?php endif; ?>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th></th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr><td colspan="8" class="table__empty">No posts yet — <a href="<?= url('/admin/posts/create') ?>">create the first one</a>.</td></tr>
                    <?php else: foreach ($posts as $post): ?>
                        <tr>
                            <td class="table__id">#<?= (int) $post['id'] ?></td>
                            <td>
                                <div class="table__thumb"><img src="<?= thumbnail_url($post['thumbnail']) ?>" alt=""></div>
                            </td>
                            <td class="table__title"><?= e(excerpt($post['title'], 80)) ?></td>
                            <td><span class="badge badge--primary"><?= e($post['category_title']) ?></span></td>
                            <td class="table__muted"><?= e($post['firstname'] . ' ' . $post['lastname']) ?></td>
                            <td class="table__muted"><?= e(formatDate($post['date_time'], 'M d, Y')) ?></td>
                            <td>
                                <?php if ((int) $post['is_featured'] === 1): ?>
                                    <span class="badge badge--warning"><i class="ri-star-fill"></i> Featured</span>
                                <?php else: ?>
                                    <span class="badge">Published</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table__actions">
                                    <a href="<?= url('/post?id=' . (int) $post['id']) ?>"
                                       class="table__action" title="View" target="_blank" rel="noopener">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="<?= url('/admin/posts/edit?id=' . (int) $post['id']) ?>"
                                       class="table__action table__action--edit" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <form action="<?= url('/admin/posts/delete') ?>" method="post"
                                          onsubmit="return confirm('Delete this post? This cannot be undone.');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                                        <button type="submit" class="table__action table__action--danger" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
