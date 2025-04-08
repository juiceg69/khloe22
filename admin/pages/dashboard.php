<?php
//session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

// Obtener las noticias usando la función reutilizable
try {
    global $pdo;
    $posts = getNoticias($pdo);
} catch (Exception $e) {
    die("Error al obtener las noticias: " . $e->getMessage());
}

// Incluir el header
include_once __DIR__ . "/../../public/templates/header.php";
?>

<section class="dashboard" style="margin-top: 20px;">
    <div class="container dashboard__container">
        <button id="show__sidebar-btn" class="sidebar__toggle">
            <i class="uil uil-angle-right-b"></i>
        </button>
        <button id="hide__sidebar-btn" class="sidebar__toggle" style="display: none;">
            <i class="uil uil-angle-left-b"></i>
        </button>
        <aside>
            <ul>
                <li>
                    <a href="<?= BASE_URL ?>/?url=admin/add-post"><i class="uil uil-pen"></i>
                    <h5>Add Noticia</h5>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/?url=admin/manage-posts" class="active"><i class="uil uil-postcard"></i>
                    <h5>Manage Noticias</h5>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/?url=admin/add-user"><i class="uil uil-user-plus"></i>
                    <h5>Add Usuario</h5>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/?url=admin/manage-users"><i class="uil uil-users-alt"></i>
                    <h5>Manage Usuarios</h5>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/?url=admin/add-cita"><i class="uil uil-edit"></i>
                    <h5>Add Cita</h5>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/?url=admin/manage-citas"><i class="uil uil-list-ul"></i>
                    <h5>Manage Citas</h5>
                    </a>
                </li>
            </ul>
        </aside>
        <main>
            <h2 style="color: #d3d1d1;">Manage Noticias</h2>
            
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
            <?php if (isset($_SESSION['edit-post-success'])): ?>
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
            <?php if (isset($_SESSION['delete-post-success'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '<?= addslashes($_SESSION['delete-post-success']) ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
                <?php unset($_SESSION['delete-post-success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['add-post-error']) || isset($_SESSION['edit-post-error']) || isset($_SESSION['delete-post-error'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '<?= addslashes($_SESSION['add-post-error'] ?? $_SESSION['edit-post-error'] ?? $_SESSION['delete-post-error']) ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
                <?php 
                    unset($_SESSION['add-post-error']);
                    unset($_SESSION['edit-post-error']);
                    unset($_SESSION['delete-post-error']);
                ?>
            <?php endif; ?>
            
            <!-- Contenido del dashboard -->
            <?php if (empty($posts)): ?>
                <p>Ninguna noticia encotrada.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Titulo</th>
                            <th>Fecha</th>
                            <th>Imagen</th>
                            <th>Author</th>
                            <th>Editar</th>
                            <th>Borrar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post) : ?>
                            <tr>
                                <td><?= htmlspecialchars($post['titulo']) ?></td>
                                <td><?= htmlspecialchars($post['fecha']) ?></td>
                                <td>
                                    <img src="<?= BASE_URL ?>/public/assets/images/<?= htmlspecialchars($post['imagen']) ?>" 
                                         alt="<?= htmlspecialchars($post['titulo']) ?>" 
                                         style="max-width: 100px; height: auto;">
                                </td>
                                <td><?= htmlspecialchars($post['name']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/?url=admin/edit-post&id=<?= $post['idNoticia'] ?>" 
                                       class="btn btn-warning btn-sm">Editar</a>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-post-btn" 
                                            data-id="<?= $post['idNoticia'] ?>" 
                                            data-author="<?= htmlspecialchars($post['name']) ?>" 
                                            data-date="<?= htmlspecialchars($post['fecha']) ?>">
                                        Borrar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-post-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const author = this.getAttribute('data-author');
            const date = this.getAttribute('data-date');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Estás seguro de eliminar esta noticia creada por: ${author} con fecha ${date}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= BASE_URL ?>/?url=admin/delete-post&id=${id}`;
                }
            });
        });
    });
});
</script>
<script src="<?= BASE_URL ?>/public/assets/js/dashboard.js"></script>

<?php
include_once __DIR__ . "/../../public/templates/footer.php";
?>