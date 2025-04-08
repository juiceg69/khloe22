<?php
require_once __DIR__ . '/../config/database.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Lista estática de publicaciones recientes (ajustada para reflejar posibles publicaciones en post.php)
$recent_posts = [
    ['title' => 'Exploring New Tech Horizons', 'url' => BASE_URL . '/?url=post#post1'],
    ['title' => 'Cultural Insights from Around the World', 'url' => BASE_URL . '/?url=post#post2'],
    ['title' => 'Travel Tips for 2025', 'url' => BASE_URL . '/?url=post#post3']
];
?>

<!---- FOOTER SECTION ---->
<footer>
    <div class="container footer__container">
        <!-- Logo y descripción -->
        <div class="footer__branding">
            <a href="<?= BASE_URL ?>" class="footer__logo">
                <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" style="max-width: 60px; height: auto;">
            </a>
            <p><i class="bi bi-newspaper"></i> Un blog sobre <span class="highlight">noticias</span>, tecnología y más. Mantente informado con nuestras publicaciones diarias.</p>
            <div class="footer__socials">
                <a href="https://www.facebook.com" target="_blank" class="social__link">
                    <i class="bi bi-facebook"></i> 
                </a>
                <a href="https://www.twitter.com" target="_blank" class="social__link">
                    <i class="bi bi-twitter"></i> 
                </a>
                <a href="https://www.instagram.com" target="_blank" class="social__link">
                    <i class="bi bi-instagram"></i> 
                </a>
                <a href="https://www.linkedin.com" target="_blank" class="social__link">
                    <i class="bi bi-linkedin"></i> 
                </a>
                <a href="https://www.youtube.com" target="_blank" class="social__link">
                    <i class="bi bi-youtube"></i> 
                </a>
            </div>
        </div>

        <div class="footer__columns">
            <article>
                <h4>Permalinks</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/">Home</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=blog">News</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=about">About</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=services">Services</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=contact">Contact</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=sitemap">Sitemap</a></li>
                </ul>
            </article>
            <article>
                <h4>Support</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/?url=support">Support</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=social">Social</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=faq">FAQ</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=terms">Terms</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=privacy">Privacy</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=cookies">Cookies Policy</a></li>
                </ul>
            </article>
            <article>
                <h4>Recent Posts</h4>
                <ul>
                    <?php foreach ($recent_posts as $post): ?>
                        <li><a href="<?= $post['url'] ?>"><?= htmlspecialchars($post['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <article>
                <h4>Contact Info</h4>
                <ul>
                    <li><a href="mailto:support@blog.com"><i class="bi bi-envelope"></i> support@blog.com</a></li>
                    <li><a href="tel:+1234567890"><i class="bi bi-telephone"></i> +1 (234) 567-890</a></li>
                    <li><a href="<?= BASE_URL ?>/?url=location"><i class="bi bi-geo-alt"></i> Our Location</a></li>
                    <li><span><i class="bi bi-clock"></i> Mon-Fri: 9 AM - 5 PM</span></li>
                </ul>
            </article>
        </div>

        <!-- Formulario de suscripción -->
        <div class="footer__newsletter">
            <h4>Subscribe to Our Newsletter</h4>
            <form action="<?= BASE_URL ?>/?url=subscribe" method="POST" class="newsletter__form" id="newsletterForm">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="footer__copyright">
        <p>© <?php echo date("Y"); ?> BLOG. All rights reserved.</p>
        <a href="#top" class="back-to-top">Back to Top <i class="bi bi-arrow-up"></i></a>
    </div>
</footer>

<!-- SweetAlert2 JS -->
<script src="<?= SWEETALERT_JS; ?>"></script>

<!------ BOOTSTRAP SCRIPTS ----->
<script src="<?= BOOTSTRAP_JS ?>"></script>

<!-- jQuery (opcional, solo si lo necesitas) -->
<script src="<?= JQUERY_JS ?>"></script>

<!-- Custom JS -->
<script src="<?= JS_PATH ?>/main.js"></script>

</body>
</html>