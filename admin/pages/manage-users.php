<?php
//session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";
include_once __DIR__ . "/../../public/templates/header.php";

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

try {
    global $pdo; // Usar la conexión PDO global definida en database.php

    // Obtener los datos de los usuarios
    $query = "
        SELECT users_data.idUser, users_data.name, users_data.last_name, users_data.email, users_login.usuario, users_login.rol 
        FROM users_data 
        JOIN users_login ON users_data.idUser = users_login.idUser
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $users = []; // En caso de error, inicializar como un array vacío
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
                    <h5>Add Noticia</h5></a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/?url=admin"><i class="uil uil-postcard"></i>
                    <h5>Manage Noticias</h5></a>
                </li>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) : ?>
                    <li>
                        <a href="<?= BASE_URL ?>/?url=admin/add-user"><i class="uil uil-user-plus"></i>
                        <h5>Add Usuario</h5></a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/?url=admin/manage-users" class="active"><i class="uil uil-users-alt"></i>
                        <h5>Manage Usuarios</h5></a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/?url=admin/add-cita"><i class="uil uil-edit"></i>
                        <h5>Add Cita</h5></a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/?url=admin/manage-citas"><i class="uil uil-list-ul"></i>
                        <h5>Manage Citas</h5></a>
                    </li>
                <?php endif; ?>
            </aside>

            <main>
                <h2 style="color: #d3d1d1;">Manage Usuarios</h2>

                <!-- Mostrar mensajes de SweetAlert -->
                <?php if (isset($_SESSION['add-user-success'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: '<?= addslashes($_SESSION['add-user-success']) ?>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php unset($_SESSION['add-user-success']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['edit-user-success'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: '<?= addslashes($_SESSION['edit-user-success']) ?>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php unset($_SESSION['edit-user-success']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['delete-user-success'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: '<?= addslashes($_SESSION['delete-user-success']) ?>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php unset($_SESSION['delete-user-success']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['add-user-error']) || isset($_SESSION['edit-user-error']) || isset($_SESSION['delete-user-error'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: '<?= addslashes($_SESSION['add-user-error'] ?? $_SESSION['edit-user-error'] ?? $_SESSION['delete-user-error']) ?>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php 
                        unset($_SESSION['add-user-error']);
                        unset($_SESSION['edit-user-error']);
                        unset($_SESSION['delete-user-error']);
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])) : ?>
                    <div class="alert alert-danger">
                        <p><?= $_SESSION['error'] ?></p>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <!--<th>Email</th>-->
                            <th>Editar</th>
                            <th>Borrar</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)) : ?>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= htmlspecialchars($user['usuario']) ?></td>
                                    <!--<td><?= htmlspecialchars($user['email']) ?></td>--->
                                    <td>
                                        <a href="<?= BASE_URL ?>/?url=admin/edit-user&id=<?= $user['idUser'] ?>" class="btn btn-warning btn-sm w-40 rounded">Editar</a>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-user-btn w-40 rounded" 
                                                data-id="<?= $user['idUser'] ?>" 
                                                data-name="<?= htmlspecialchars($user['name'] . ' ' . $user['last_name']) ?>">
                                            Borrar
                                        </button>
                                    </td>
                                    <td><?= ($user['rol'] === 'admin') ? 'Si' : 'No' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center">Usuario no encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mostrar SweetAlert si hay un mensaje de éxito
        <?php if (isset($_SESSION['add-user-success'])) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $_SESSION['add-user-success'] ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['add-user-success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['edit-user-success'])) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $_SESSION['edit-user-success'] ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['edit-user-success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['delete-user-success'])) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $_SESSION['delete-user-success'] ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['delete-user-success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['delete-user-error'])) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= $_SESSION['delete-user-error'] ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['delete-user-error']); ?>
        <?php endif; ?>

        // Añadir SweetAlert para confirmación de eliminación
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-user-btn');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: `Estás seguro de eliminar al usuario ${name}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `<?= BASE_URL ?>/?url=admin/delete-user&id=${id}`;
                        }
                    });
                });
            });
        });
    </script>

<?php
include_once __DIR__ . "/../../public/templates/footer.php";
?>