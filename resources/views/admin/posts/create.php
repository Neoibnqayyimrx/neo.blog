<?php
/**
 * @var array $categories
 * @var string|null $success
 * @var string|null $error
 */

use App\Core\Auth;
?>
<div class="admin-topbar">
    <div>
        <div class="admin-topbar__crumb">
            <a href="<?= url('/admin') ?>">Admin</a> <i class="ri-arrow-right-s-line"></i>
            <a href="<?= url('/admin/posts') ?>">Posts</a> <i class="ri-arrow-right-s-line"></i>
            <span>New</span>
        </div>
        <div class="admin-topbar__title"><h1>Add a new post</h1></div>
        <div class="admin-topbar__sub">Publish a new story to the blog.</div>
    </div>
    <div class="admin-topbar__actions">
        <a href="<?= url('/admin/posts') ?>" class="btn btn--secondary btn--sm">Back to list</a>
    </div>
</div>

<div class="admin-content">
    <?php if ($success): ?>
        <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
    <?php endif; ?>

    <form action="<?= url('/admin/posts/store') ?>" method="post" enctype="multipart/form-data"
          class="admin-form-card" novalidate>
        <?= csrfField() ?>
        <div class="admin-form-card__body">
            <div class="form-stack">
                <div class="form-field">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-input"
                           value="<?= Auth::getOldInput('title') ?>"
                           placeholder="Write a clear, descriptive headline…" required>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="" disabled selected>Select a category…</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int) $c['id'] ?>"><?= e($c['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="thumbnail">Thumbnail image</label>
                        <input type="file" id="thumbnail" name="thumbnail" class="form-file"
                               accept="image/jpeg,image/png,image/webp,image/gif" required>
                        <small>Recommended: 1200×630 · JPG/PNG/WebP up to 2 MB.</small>
                    </div>
                </div>

                <div class="form-field">
                    <label for="body">Body</label>
                    <textarea id="body" name="body" class="form-textarea" style="min-height: 16rem;"
                              placeholder="Write your post here. Line breaks are preserved." required><?= Auth::getOldInput('body') ?></textarea>
                </div>

                <label class="form-check">
                    <input type="checkbox" name="is_featured" value="1">
                    <span>Feature this post on the homepage (unsets any existing featured post).</span>
                </label>
            </div>
        </div>
        <div class="admin-form-card__footer">
            <a href="<?= url('/admin/posts') ?>" class="btn btn--secondary">Cancel</a>
            <button type="submit" class="btn">
                <i class="ri-send-plane-line"></i> Publish post
            </button>
        </div>
    </form>
</div>
