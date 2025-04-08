<?php
//session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../templates/header.php";

// Obtener el ID de la noticia desde la URL
$idNoticia = $_GET['id'] ?? null;
if (!$idNoticia || !is_numeric($idNoticia)) {
    header('Location: ' . BASE_URL . '/?url=blog');
    exit();
}

// Obtener la noticia usando la función reutilizable
try {
    global $pdo;
    $post = getNoticiaById($pdo, $idNoticia);
    if (!$post) {
        header('Location: ' . BASE_URL . '/?url=blog');
        exit();
    }
} catch (Exception $e) {
    die("Error al obtener la noticia: " . $e->getMessage());
}
?>

<!-- SINGLE POST -->
<section class="singlepost">
    <div class="container singlepost__container">
        <div class="post__thumbnail post__thumbnail--dynamic">
            <img src="<?= BASE_URL ?>/public/assets/images/<?= htmlspecialchars($post['imagen']) ?>" 
                 alt="<?= htmlspecialchars($post['titulo']) ?>"
                 class="dynamic-post-image"
                 onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder.jpg';">
        </div>
        <div class="post__info post__info--dynamic">
            <h2 class="post__title">
                <?= htmlspecialchars($post['titulo']) ?>
            </h2>
            <div class="post__author">
                <!--<div class="post__author-avatar">
                    <img src="<?= BASE_URL ?>/public/assets/images/avatar-default.jpg" alt="avatar">
                </div>-->
                <div class="post__author-info">
                    <h5>By: <?= htmlspecialchars($post['name'] . ' ' . ($post['last_name'] ?? '')) ?></h5>
                    <small><?= date('F j, Y - H:i', strtotime($post['fecha'])) ?></small>
                </div>
            </div>
            <p class="post__body">
                <?= nl2br(htmlspecialchars($post['texto'])) ?>
            </p>
            <div class="d-flex justify-content-center mt-3">
                <a href="<?= BASE_URL ?>/?url=blog" class="btn btn-primary">Volver a Noticias</a>
            </div>
        </div>
    </div>
</section>
<!-- END OF SINGLE POST -->

<?php
include_once __DIR__ . "/../templates/footer.php";
?>