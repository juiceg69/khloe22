<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['idUser']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

// Obtener el ID de la noticia a editar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
    exit();
}

$idNoticia = $_GET['id'];

try {
    global $pdo;
    if (!$pdo) {
        die("The DataBase connection is not defined in the dataBase.php");
    }

    // Obtener la noticia de la base de datos usando la función reutilizable
    $noticia = getNoticiaById($pdo, $idNoticia);

    if (!$noticia) {
        header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
        exit();
    }
} catch (Exception $e) {
    die("Error getting the news: " . $e->getMessage());
}

// Recuperar datos del formulario si hubo un error
$title = $_SESSION['edit-post-data']['title'] ?? $noticia['titulo'];
$date = $_SESSION['edit-post-data']['date'] ?? $noticia['fecha'];
$texto = $_SESSION['edit-post-data']['texto'] ?? $noticia['texto'];

// Eliminar datos de sesión después de usarlos
unset($_SESSION['edit-post-data']);

include_once __DIR__ . "/../../public/templates/header.php";
?>

<section class="form-signin w-100 m-auto mt-5 mb-5">
    <div class="form-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/?url=admin" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">Editar Noticia</h2>

        <!-- Mostrar mensajes de error o éxito con SweetAlert -->
        <?php if (isset($_SESSION['edit-post-error'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '<?= addslashes($_SESSION['edit-post-error']) ?>',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
            <?php unset($_SESSION['edit-post-error']); ?>
        <?php elseif (isset($_SESSION['edit-post-success'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '<?= addslashes($_SESSION['edit-post-success']) ?>',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
            <?php unset($_SESSION['edit-post-success']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/?url=admin/edit-post-logic" enctype="multipart/form-data">
            <input type="hidden" name="idNoticia" value="<?= $noticia['idNoticia'] ?>">

            <!-- Título -->
            <div class="form-floating mb-2">
                <input name="title" type="text" class="form-control w-100" id="floatingTitle" placeholder="Título" 
                    value="<?= htmlspecialchars($title) ?>" required>
                <label for="floatingTitle">Título</label>
            </div>

            <!-- Fecha -->
            <div class="form-floating mb-2">
                <input name="date" type="date" class="form-control w-100" id="floatingDate" 
                    value="<?= htmlspecialchars($date) ?>" required>
                <label for="floatingDate">Fecha</label>
            </div>

            <!-- Contenido -->
            <div class="form-floating mb-2">
                <textarea 
                    style="color: #d3d1d1; background: #003366; border: 1px solid #007bff; width: 100%; height: 200px; resize: vertical;" 
                    name="texto" 
                    id="texto" 
                    class="form-control" 
                    placeholder="Texto de la noticia..." 
                    rows="10" 
                    required
                ><?= htmlspecialchars($texto) ?></textarea>
                <label for="texto">Texto de la noticia</label>
            </div>

            <!-- Imagen -->
            <div class="form_control mb-2 position-relative">
                <input 
                    style="opacity: 0; position: absolute; width: 100%; height: 100%; z-index: 1;" 
                    type="file" 
                    name="imagen" 
                    id="imagen" 
                    accept="image/*" 
                    class="form-control"
                >
                <div 
                    style="color: #d3d1d1; background: #0056b3; border: 1px solid #007bff; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 0.375rem; cursor: pointer;"
                >
                    <span 
                        class="btn btn-primary" 
                        style="padding: 4px 12px; font-size: 14px; line-height: 1.5; white-space: nowrap;"
                    >
                        Seleccionar archivo
                    </span>
                </div>
                <small class="text-muted d-block mt-1">Dejar vacío para mantener la imagen actual</small>
            </div>

            <!-- Enlace para volver y botón de envío -->
            <div class="my-2">
                Volver al <a href="<?= BASE_URL ?>/?url=admin" class="text-success fw-bold">Panel de Administración</a>
            </div>
            <button class="btn btn-primary w-100 py-2" name="submit" type="submit">Actualizar Noticia</button>
            <p class="mt-3 mb-3 text-muted text-center">© <?php echo date("Y"); ?></p>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<?php include_once __DIR__ . "/../../public/templates/footer.php"; ?>