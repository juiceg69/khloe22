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

// Obtener la lista de usuarios registrados para el desplegable
try {
    global $pdo;
    $query = "SELECT idUser, name, last_name FROM users_data ORDER BY name, last_name";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['add-cita-error'] = "Database error: " . $e->getMessage();
    $users = [];
}
?>

<section class="form-signin w-100 m-auto mt-5 mb-5">
    <div class="form-container">
        <a href="<?= BASE_URL ?>/?url=admin" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">Add Cita</h2>

        <form method="POST" action="<?= BASE_URL ?>/?url=admin/add-cita-logic" enctype="multipart/form-data">
            <div class="form-floating mb-2">
                <select name="idUser" class="form-select custom-select" id="floatingUserId" required>
                    <option value="">Select a user</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['idUser']) ?>">
                            <?= htmlspecialchars($user['name'] . ' ' . $user['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="floatingUserId">User</label>
            </div>

            <div class="form-floating mb-2">
                <input name="fecha_cita" type="date" class="form-control w-100" id="floatingDate" required>
                <label for="floatingDate">Fecha Cita</label>
            </div>

            <div class="form-floating mb-2">
                <textarea 
                    name="motivo_cita" 
                    id="motivo_cita" 
                    class="form-control w-100" 
                    placeholder="Reason for the cita..." 
                    style="height: 200px; background: #02264a; color: white; resize: vertical; border: 1px solid #0056b3; border-radius: 5px;" 
                    required 
                ></textarea>
                <label for="motivo_cita">Razón de la cita</label>
            </div>

                        <div class="my-2">
                Volver al <a href="<?= BASE_URL ?>/?url=admin" class="text-success fw-bold">Panel de Administración</a>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Add Cita</button>
            <p class="mt-3 mb-3 text-muted text-center">© <?php echo date("Y"); ?></p>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['add-cita-success'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?= addslashes($_SESSION['add-cita-success']) ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['add-cita-success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['add-cita-error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= addslashes($_SESSION['add-cita-error']) ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['add-cita-error']); ?>
        <?php endif; ?>
    });
</script>

<?php include_once __DIR__ . "/../../public/templates/footer.php"; ?>