<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

// Reutilizamos la conexión de database.php
require_once __DIR__ . "/../../public/config/database.php";
include_once __DIR__ . "/../../public/templates/header.php";

try {
    global $pdo;

    // Obtener las citas de la base de datos con el nombre del usuario
    $query = "SELECT citas.*, users_data.name, users_data.last_name 
              FROM citas 
              JOIN users_data ON citas.idUser = users_data.idUser 
              ORDER BY citas.fecha_cita";
    $stmt = $pdo->query($query);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejar errores de la base de datos
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $citas = []; // En caso de error, inicializar como un array vacío
}
?>

<section class="dashboard">
    <div class="container dashboard__container">
        <!------ SIDE BAR BTN --->
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
                    <a href="<?= BASE_URL ?>/?url=admin"><i class="uil uil-postcard"></i>
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
                    <a href="<?= BASE_URL ?>/?url=admin/manage-citas" class="active"><i class="uil uil-list-ul"></i>
                    <h5>Manage Citas</h5>
                    </a>
                </li>
            </ul>
        </aside>
        <main>
            <h2 style="color: #d3d1d1;">Manage Citas</h2>

            <!-- Mostrar mensajes de SweetAlert -->
            <?php if (isset($_SESSION['add-cita-success'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '<?= addslashes($_SESSION['add-cita-success']) ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
                <?php unset($_SESSION['add-cita-success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['edit-cita-success'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '<?= addslashes($_SESSION['edit-cita-success']) ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
                <?php unset($_SESSION['edit-cita-success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['delete-cita-success'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '<?= addslashes($_SESSION['delete-cita-success']) ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
                <?php unset($_SESSION['delete-cita-success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['add-cita-error']) || isset($_SESSION['edit-cita-error']) || isset($_SESSION['delete-cita-error'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '<?= addslashes($_SESSION['add-cita-error'] ?? $_SESSION['edit-cita-error'] ?? $_SESSION['delete-cita-error']) ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
                <?php 
                    unset($_SESSION['add-cita-error']);
                    unset($_SESSION['edit-cita-error']);
                    unset($_SESSION['delete-cita-error']);
                ?>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Razón</th>
                        <th>Editar</th>
                        <th>Borrar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($citas)) : ?>
                        <?php foreach ($citas as $cita) : ?>
                            <tr>
                                <td><?= htmlspecialchars($cita['name'] . ' ' . $cita['last_name']) ?></td>
                                <td><?= htmlspecialchars($cita['fecha_cita']) ?></td>
                                <td><?= htmlspecialchars($cita['motivo_cita']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/?url=admin/edit-cita&id=<?= $cita['idCita'] ?>" class="btn btn-warning btn-sm w-30 rounded">Editar</a>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm w-30 rounded delete-cita-btn" 
                                            data-id="<?= $cita['idCita'] ?>" 
                                            data-user="<?= htmlspecialchars($cita['name'] . ' ' . $cita['last_name']) ?>" 
                                            data-date="<?= htmlspecialchars($cita['fecha_cita']) ?>">
                                        Borrar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">No citas encontradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-cita-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const user = this.getAttribute('data-user');
            const date = this.getAttribute('data-date');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Estás seguro de eliminar la cita creada por ${user} para el ${date}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= BASE_URL ?>/?url=admin/delete-cita&id=${id}`;
                }
            });
        });
    });
});
</script>

<?php
include_once __DIR__ . "/../../public/templates/footer.php";
?>