<?php
/** @var string|null $error */

use App\Core\Auth;
?>

<section class="auth">
    <div class="auth__card auth__card--wide">
        <div class="auth__brand">
            <div class="auth__brand-logo">N</div>
            <h1 class="auth__title">Create your account</h1>
            <p class="auth__subtitle">It only takes a minute — and it’s free.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
        <?php endif; ?>

        <form action="<?= url('/signup') ?>" method="post" enctype="multipart/form-data" class="form-stack" novalidate>
            <?= csrfField() ?>

            <div class="form-grid">
                <div class="form-field">
                    <label for="firstname">First name</label>
                    <input type="text" id="firstname" name="firstname" class="form-input"
                           value="<?= Auth::getOldInput('firstname') ?>" required>
                </div>
                <div class="form-field">
                    <label for="lastname">Last name</label>
                    <input type="text" id="lastname" name="lastname" class="form-input"
                           value="<?= Auth::getOldInput('lastname') ?>" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input"
                           autocomplete="username"
                           value="<?= Auth::getOldInput('username') ?>" required>
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input"
                           autocomplete="email"
                           value="<?= Auth::getOldInput('email') ?>" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="createpassword">Password</label>
                    <input type="password" id="createpassword" name="createpassword"
                           class="form-input"
                           autocomplete="new-password" required>
                    <small>At least 8 characters.</small>
                </div>
                <div class="form-field">
                    <label for="confirmpassword">Confirm password</label>
                    <input type="password" id="confirmpassword" name="confirmpassword"
                           class="form-input"
                           autocomplete="new-password" required>
                </div>
            </div>

            <div class="form-field">
                <label for="avatar">Profile picture <small>(optional — JPG, PNG, WebP)</small></label>
                <input type="file" id="avatar" name="avatar" class="form-file"
                       accept="image/jpeg,image/png,image/webp,image/gif">
            </div>

            <button type="submit" class="btn btn--block btn--lg">
                Create account <i class="ri-arrow-right-line"></i>
            </button>
        </form>

        <p class="form-footnote">
            Already have an account?
            <a href="<?= url('/signin') ?>">Sign in</a>
        </p>
    </div>
</section>
