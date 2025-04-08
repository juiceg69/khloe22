<?php
require_once __DIR__ . '/public/session/session.php'; // Inicia la sesión
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir archivos de configuración (necesarios para IMAGES_PATH, BASE_URL y $pdo)
require_once 'public/config/constants.php';
require_once 'public/config/database.php';

// Incluir el enrutador solo para rutas secundarias
if (isset($_GET['url']) && !empty($_GET['url'])) {
    require_once './public/init/init.php';
    exit; // Salir después de que init.php maneje la ruta secundaria
}

// Manejar el parámetro ?logout=success y redirigir para limpiar la URL
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $_SESSION['logout-success'] = "Has cerrado sesión exitosamente.";
    header('Location: ' . BASE_URL . '/'); // Redirigir a la URL limpia
    exit();
}

// Incluir el encabezado
include_once 'public/templates/header.php';

// Mensaje de éxito para login
if (isset($_SESSION['login-success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: <?= json_encode($_SESSION['login-success']) ?>,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'animated fadeInDown'
                }
            });
        });
    </script>
    <?php unset($_SESSION['login-success']); ?>
<?php endif; ?>

<!-- Mensaje de éxito para logout -->
<?php if (isset($_SESSION['logout-success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: <?= json_encode($_SESSION['logout-success']) ?>,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'animated fadeInDown'
                }
            });
        });
    </script>
    <?php unset($_SESSION['logout-success']); ?>
<?php endif; ?>

<!-- Mensaje de error -->
<?php if (isset($_SESSION['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($_SESSION['error']) ?>,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'animated fadeInDown'
                }
            });
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Carousel -->
<div class="container-fluid mt-4 px-0">
    <div id="carouselExample" class="carousel slide shadow-sm" data-bs-ride="carousel" style="border: 2px solid #00C4B4; border-radius: 10px; overflow: hidden;">
        <div class="carousel-inner">
            <div class="carousel-item active" style="position: relative; height: 50vh; min-height: 300px; max-height: 70vh;">
                <img src="<?= IMAGES_PATH ?>/colored-face.jpg" class="d-block w-100 h-100" alt="Androide" style="object-fit: cover; object-position: center;">
                <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 0, 0, 0.5); padding: 15px; border-radius: 8px; bottom: 15px; left: 50%; transform: translateX(-50%); width: 80%; max-width: 600px;">
                    <h5 class="text-white mb-2" style="font-size: 1.5rem; font-weight: bold;">Colored</h5>
                    <p class="text-white" style="font-size: 1rem;">My slide caption text</p>
                </div>
            </div>
            <div class="carousel-item" style="position: relative; height: 50vh; min-height: 300px; max-height: 70vh;">
                <img src="<?= IMAGES_PATH ?>/cyberpunk.jpg" class="d-block w-100 h-100" alt="Cyber Punk Tokio" style="object-fit: cover; object-position: center;">
                <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 0, 0, 0.5); padding: 15px; border-radius: 8px; bottom: 15px; left: 50%; transform: translateX(-50%); width: 80%; max-width: 600px;">
                    <h5 class="text-white mb-2" style="font-size: 1.5rem; font-weight: bold;">Cyber Punk Tokio</h5>
                    <p class="text-white" style="font-size: 1rem;">My slide caption text</p>
                </div>
            </div>
            <div class="carousel-item" style="position: relative; height: 50vh; min-height: 300px; max-height: 70vh;">
                <img src="<?= IMAGES_PATH ?>/japan.jpg" class="d-block w-100 h-100" alt="Colored Eye" style="object-fit: cover; object-position: center;">
                <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 0, 0, 0.5); padding: 15px; border-radius: 8px; bottom: 15px; left: 50%; transform: translateX(-50%); width: 80%; max-width: 600px;">
                    <h5 class="text-white mb-2" style="font-size: 1.5rem; font-weight: bold;">Japan</h5>
                    <p class="text-white" style="font-size: 1rem;">My slide caption text</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev" style="background-color: #212529; border: 2px solid #00C4B4; width: 40px; height: 40px; border-radius: 50%; top: 50%; transform: translateY(-50%); left: 10px;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next" style="background-color: #212529; border: 2px solid #00C4B4; width: 40px; height: 40px; border-radius: 50%; top: 50%; transform: translateY(-50%); right: 10px;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
<!-- END SLIDERS -->

<!-- Sección estática: Featured Content -->
<section class="featured my-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Content</h2>
        <div class="posts__container">
            <article class="post">
                <div class="post__thumbnail">
                    <img src="<?= IMAGES_PATH ?>/atomic-bomb.jpg" alt="Tech Innovation">
                </div>
                <div class="post__info" style="color: #fff;">
                    <h3 class="post__title">Tech Innovation</h3>
                    <p class="post__body">Explore the latest technological innovations.</p>
                    <a href="<?= BASE_URL ?>/?url=blog" class="btn btn-outline-light">Learn More</a>
                </div>
            </article>
            <article class="post">
                <div class="post__thumbnail">
                    <img src="<?= IMAGES_PATH ?>/traditions.jpg" alt="Art & Culture">
                </div>
                <div class="post__info" style="color: #fff;">
                    <h3 class="post__title">Art & Culture</h3>
                    <p class="post__body">Dive into the world of art and culture.</p>
                    <a href="<?= BASE_URL ?>/?url=blog" class="btn btn-outline-light">Learn More</a>
                </div>
            </article>
            <article class="post">
                <div class="post__thumbnail">
                    <img src="<?= IMAGES_PATH ?>/japan.jpg" alt="Travel Destinations">
                </div>
                <div class="post__info" style="color: #fff;">
                    <h3 class="post__title">Travel Destinations</h3>
                    <p class="post__body">Discover unique travel destinations.</p>
                    <a href="<?= BASE_URL ?>/?url=blog" class="btn btn-outline-light">Learn More</a>
                </div>
            </article>
            <article class="post">
                <div class="post__thumbnail">
                    <img src="<?= IMAGES_PATH ?>/pop-art.jpg" alt="Pop Art Trends">
                </div>
                <div class="post__info" style="color: #fff;">
                    <h3 class="post__title">Pop Art Trends</h3>
                    <p class="post__body">The most vibrant trends in pop art.</p>
                    <a href="<?= BASE_URL ?>/?url=blog" class="btn btn-outline-light">Learn More</a>
                </div>
            </article>
            <article class="post">
                <div class="post__thumbnail">
                    <img src="<?= IMAGES_PATH ?>/futuristic.jpg" alt="Futuristic Visions">
                </div>
                <div class="post__info" style="color: #fff;">
                    <h3 class="post__title">Futuristic Visions</h3>
                    <p class="post__body">A glimpse into the future of technology.</p>
                    <a href="<?= BASE_URL ?>/?url=blog" class="btn btn-outline-light">Learn More</a>
                </div>
            </article>
            <article class="post">
                <div class="post__thumbnail">
                    <img src="<?= IMAGES_PATH ?>/gorilazz.jpeg" alt="Music & Vibes">
                </div>
                <div class="post__info" style="color: #fff;">
                    <h3 class="post__title">Music & Vibes</h3>
                    <p class="post__body">Explore unique rhythms and vibes.</p>
                    <a href="<?= BASE_URL ?>/?url=blog" class="btn btn-outline-light">Learn More</a>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="cta py-3 py-md-5" style="background-image: url('<?= IMAGES_PATH ?>/imrobot.jpg'); background-size: contain; background-position: center; background-repeat: no-repeat; color: #fff; text-align: center; min-height: 400px; display: flex; align-items: center; justify-content: center; background-color: #212529; position: relative;">
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-center text-md-start mb-4 mb-md-0">
                <img src="<?= IMAGES_PATH ?>/man-robot.jpg" alt="Man and Robot" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);">
            </div>
            <div class="col-12 col-md-6 text-center text-md-start">
                <h2 class="mb-3" style="font-size: 2.5rem; font-weight: bold; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">Join Our Community</h2>
                <p class="mb-4" style="font-size: 1.2rem; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);">Get the latest updates and exclusive content by signing up today!</p>
                <a href="<?= BASE_URL ?>/?url=signup" class="btn btn-outline-light btn-lg" style="padding: 10px 20px; font-size: 1.1rem;">Sign Up Now</a>
            </div>
        </div>
    </div>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #011f3c; opacity: 0.5; z-index: 0;"></div>
</section>

<!-- Sección estática: Sobre nosotros -->
<section class="about-us py-5" style="color: #fff;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 style="color: #fff;">About Us</h2>
                <p>We’re passionate about sharing stories, technology, and culture with you. Whether you’re here to explore news, art, or travel, we’ve got something for everyone.</p>
                <a href="<?= BASE_URL ?>/?url=about" class="btn btn-outline-light">Read More</a>
            </div>
            <div class="col-md-6">
                <img src="<?= IMAGES_PATH ?>/cyberpunk.jpg" class="img-fluid rounded" alt="About Us">
            </div>
        </div>
    </div>
</section>

<!-- Cargar SweetAlert2 y Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include_once 'public/templates/footer.php'; ?>