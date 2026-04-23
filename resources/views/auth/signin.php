<?php
/** @var string|null $error */
/** @var string|null $success */

use App\Core\Auth;
?>

<section class="auth">
    <div class="auth__card">
        <div class="auth__brand">
            <div class="auth__brand-logo">N</div>
            <h1 class="auth__title">Welcome back</h1>
            <p class="auth__subtitle">Sign in to continue to your account</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
        <?php endif; ?>

        <form action="<?= url('/signin') ?>" method="post" class="form-stack" novalidate>
            <?= csrfField() ?>

            <div class="form-field">
                <label for="username_email">Username or email</label>
                <input type="text" id="username_email" name="username_email"
                       class="form-input"
                       autocomplete="username"
                       value="<?= Auth::getOldInput('username_email') ?>" required>
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       class="form-input"
                       autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn--block btn--lg">
                Sign in <i class="ri-arrow-right-line"></i>
            </button>
        </form>

        <p class="form-footnote">
            Don’t have an account yet?
            <a href="<?= url('/signup') ?>">Create one</a>
        </p>
    </div>
</section>
