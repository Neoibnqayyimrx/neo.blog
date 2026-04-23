<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div>
                <h4>NEO · BLOG</h4>
                <p>A modern publishing platform for crisp writing, thoughtful design, and honest conversations on software, craft and culture.</p>
                <div class="site-footer__socials" aria-label="Social links">
                    <a href="#" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                    <a href="#" aria-label="Twitter / X"><i class="ri-twitter-x-line"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="ri-linkedin-box-line"></i></a>
                    <a href="#" aria-label="GitHub"><i class="ri-github-line"></i></a>
                </div>
            </div>
            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="<?= url('/') ?>">Home</a></li>
                    <li><a href="<?= url('/blog') ?>">Blog</a></li>
                    <li><a href="<?= url('/about') ?>">About</a></li>
                    <li><a href="<?= url('/services') ?>">Services</a></li>
                    <li><a href="<?= url('/contact') ?>">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Terms of service</a></li>
                    <li><a href="#">Privacy policy</a></li>
                    <li><a href="#">Code of conduct</a></li>
                </ul>
            </div>
            <div>
                <h4>Get in touch</h4>
                <ul>
                    <li><a href="mailto:hello@example.com">hello@example.com</a></li>
                    <li>Lagos, Nigeria</li>
                </ul>
            </div>
        </div>
        <div class="site-footer__bottom">
            <span>© <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</span>
            <span>Crafted with care and careful CSS.</span>
        </div>
    </div>
</footer>
