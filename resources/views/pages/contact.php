<?php
/** @var string|null $success */
/** @var string|null $error */
?>

<section class="section">
    <div class="container info-hero">
        <span class="section-head__eyebrow">Contact</span>
        <h1>Got a story? We’d love to hear it.</h1>
        <p>Pitch an article, ask a question, or just say hi — we read every message.</p>
    </div>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
        <?php endif; ?>

        <div class="contact-grid">
            <form action="<?= url('/contact') ?>" method="post" class="card" novalidate>
                <div class="card__body">
                    <div class="form-stack">
                        <?= csrfField() ?>
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="name">Your name</label>
                                <input type="text" id="name" name="name" class="form-input" required>
                            </div>
                            <div class="form-field">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-input" required>
                        </div>
                        <div class="form-field">
                            <label for="message">Your message</label>
                            <textarea id="message" name="message" class="form-textarea" required placeholder="Tell us a bit about yourself and what’s on your mind…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card__footer">
                    <small class="text-muted">We reply within two business days.</small>
                    <button type="submit" class="btn">Send message <i class="ri-send-plane-line"></i></button>
                </div>
            </form>

            <aside>
                <div class="card">
                    <div class="card__body">
                        <h4 class="card__title mb-4">Reach us directly</h4>
                        <ul class="contact-info">
                            <li><i class="ri-mail-line"></i><div><strong>Email</strong><br>hello@example.com</div></li>
                            <li><i class="ri-phone-line"></i><div><strong>Phone</strong><br>+234 (0) 800 000 0000</div></li>
                            <li><i class="ri-map-pin-line"></i><div><strong>Office</strong><br>Lagos, Nigeria</div></li>
                            <li><i class="ri-time-line"></i><div><strong>Hours</strong><br>Mon–Fri, 9am – 6pm WAT</div></li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
