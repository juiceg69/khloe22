<?php
//session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../templates/header.php";

// Obtener las noticias usando la función reutilizable (sin filtro de administrador ni búsqueda)
try {
    global $pdo;
    $noticias = getNoticias($pdo); // Sin término de búsqueda, sin filtro de admin
} catch (Exception $e) {
    echo "Error al obtener las noticias: " . $e->getMessage();
    $noticias = [];
}
?>

<!-- Encabezado de la sección de noticias -->
<section class="posts">
    <div class="container">
        <h2 style="text-align: center; font-family: 'Times New Roman', Times, serif; font-size: 2.5rem; font-weight: 600; color: var(--color-accent); margin-bottom: 2rem;">
            Últimas Noticias
        </h2>
        <!-- Mostrar mensajes de SweetAlert -->
        <?php if (isset($_SESSION['add-post-success'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '<?= addslashes($_SESSION['add-post-success']) ?>',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
            <?php unset($_SESSION['add-post-success']); ?>
        <?php endif; ?>

        <div class="posts__container">
            <?php if (!empty($noticias)): ?>
                <?php foreach ($noticias as $noticia): ?>
                    <article class="post">
                        <div class="post__thumbnail post__thumbnail--dynamic">
                            <img src="<?= BASE_URL ?>/public/assets/images/<?= htmlspecialchars($noticia['imagen']) ?>" 
                                 alt="<?= htmlspecialchars($noticia['titulo']) ?>"
                                 class="dynamic-post-image"
                                 onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder.jpg';">
                        </div>
                        <div class="post__info post__info--dynamic">
                            <h3 class="post__title">
                                <a href="<?= BASE_URL ?>/?url=post&id=<?= $noticia['idNoticia'] ?>"><?= htmlspecialchars($noticia['titulo']) ?></a>
                            </h3>
                            <p class="post__body">
                                <?= substr(htmlspecialchars($noticia['texto']), 0, 150) ?>...
                            </p>
                            <div class="post__author">
                                <div class="post__author-info">
                                    <h5>By: <?= htmlspecialchars($noticia['name']) ?></h5>
                                    <small><?= date('F j, Y', strtotime($noticia['fecha'])) ?></small>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay noticias disponibles.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include_once __DIR__ . "/../templates/footer.php"; ?>