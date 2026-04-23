<?php
/**
 * @var array $post
 * @var array $categories
 * @var string|null $success
 * @var string|null $error
 */
?>
<div class="admin-topbar">
    <div>
        <div class="admin-topbar__crumb">
            <a href="<?= url('/admin') ?>">Admin</a> <i class="ri-arrow-right-s-line"></i>
            <a href="<?= url('/admin/posts') ?>">Posts</a> <i class="ri-arrow-right-s-line"></i>
            <span>Edit</span>
        </div>
        <div class="admin-topbar__title"><h1>Edit post</h1></div>
        <div class="admin-topbar__sub">#<?= (int) $post['id'] ?> · last edited <?= e(timeAgo($post['date_time'])) ?></div>
    </div>
    <div class="admin-topbar__actions">
        <a href="<?= url('/post?id=' . (int) $post['id']) ?>" class="btn btn--secondary btn--sm" target="_blank" rel="noopener">
            <i class="ri-external-link-line"></i> Preview
        </a>
        <a href="<?= url('/admin/posts') ?>" class="btn btn--ghost btn--sm">Back to list</a>
    </div>
</div>

<div class="admin-content">
    <?php if ($success): ?>
        <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
    <?php endif; ?>

    <form action="<?= url('/admin/posts/update') ?>" method="post" enctype="multipart/form-data"
          class="admin-form-card" novalidate>
        <?= csrfField() ?>
        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">

        <div class="admin-form-card__body">
            <div class="form-stack">
                <div class="form-field">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-input"
                           value="<?= e($post['title']) ?>" required>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int) $c['id'] ?>"
                                    <?= (int) $c['id'] === (int) $post['category_id'] ? 'selected' : '' ?>>
                                    <?= e($c['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Current thumbnail</label>
                        <div class="form-preview form-preview--wide">
                            <img src="<?= thumbnail_url($post['thumbnail']) ?>" alt="">
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <label for="thumbnail">Replace thumbnail <small>(optional)</small></label>
                    <input type="file" id="thumbnail" name="thumbnail" class="form-file"
                           accept="image/jpeg,image/png,image/webp,image/gif">
                </div>

                <div class="form-field">
                    <label for="body">Body</label>
                    <textarea id="body" name="body" class="form-textarea" style="min-height: 18rem;" required><?= e($post['body']) ?></textarea>
                </div>

                <label class="form-check">
                    <input type="checkbox" name="is_featured" value="1" <?= (int) $post['is_featured'] === 1 ? 'checked' : '' ?>>
                    <span>Feature this post on the homepage.</span>
                </label>
            </div>
        </div>
        <div class="admin-form-card__footer">
            <a href="<?= url('/admin/posts') ?>" class="btn btn--secondary">Cancel</a>
            <button type="submit" class="btn">
                <i class="ri-save-line"></i> Save changes
            </button>
        </div>
    </form>
</div>
