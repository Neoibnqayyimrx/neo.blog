<?php
/**
 * @var array $user
 * @var string|null $success
 * @var string|null $error
 */
?>
<div class="admin-topbar">
    <div>
        <div class="admin-topbar__crumb">
            <a href="<?= url('/admin') ?>">Admin</a> <i class="ri-arrow-right-s-line"></i>
            <a href="<?= url('/admin/users') ?>">Users</a> <i class="ri-arrow-right-s-line"></i>
            <span>Edit</span>
        </div>
        <div class="admin-topbar__title"><h1>Edit user</h1></div>
        <div class="admin-topbar__sub">@<?= e($user['username']) ?> · joined <?= e(formatDate($user['created_at'], 'M d, Y')) ?></div>
    </div>
    <div class="admin-topbar__actions">
        <a href="<?= url('/admin/users') ?>" class="btn btn--ghost btn--sm">Back to list</a>
    </div>
</div>

<div class="admin-content">
    <?php if ($success): ?>
        <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
    <?php endif; ?>

    <form action="<?= url('/admin/users/update') ?>" method="post" enctype="multipart/form-data"
          class="admin-form-card" novalidate>
        <?= csrfField() ?>
        <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">

        <div class="admin-form-card__body">
            <div class="form-stack">
                <div class="form-grid">
                    <div class="form-field">
                        <label>Current avatar</label>
                        <div class="form-preview">
                            <img src="<?= avatar_url($user['avatar']) ?>" alt="">
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="avatar">Replace avatar <small>(optional)</small></label>
                        <input type="file" id="avatar" name="avatar" class="form-file"
                               accept="image/jpeg,image/png,image/webp,image/gif">
                        <small>Must be a JPG, PNG, WebP or GIF under 2 MB.</small>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="firstname">First name</label>
                        <input type="text" id="firstname" name="firstname" class="form-input"
                               value="<?= e($user['firstname']) ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="lastname">Last name</label>
                        <input type="text" id="lastname" name="lastname" class="form-input"
                               value="<?= e($user['lastname']) ?>" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-input"
                               value="<?= e($user['username']) ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-input"
                               value="<?= e($user['email']) ?>" required>
                    </div>
                </div>

                <div class="form-field">
                    <label for="new_password">New password <small>(leave blank to keep current)</small></label>
                    <input type="password" id="new_password" name="new_password" class="form-input"
                           autocomplete="new-password" minlength="8">
                    <small>At least 8 characters when setting a new one.</small>
                </div>
            </div>
        </div>
        <div class="admin-form-card__footer">
            <a href="<?= url('/admin/users') ?>" class="btn btn--secondary">Cancel</a>
            <button type="submit" class="btn">
                <i class="ri-save-line"></i> Save changes
            </button>
        </div>
    </form>
</div>
