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

// Obtener el ID de la cita desde la URL
$cita_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cita_id <= 0) {
    $_SESSION['edit-cita-error'] = "Invalid cita ID.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
    exit();
}

// Obtener los datos de la cita
try {
    global $pdo;
    $query = "SELECT * FROM citas WHERE idCita = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $cita_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['edit-cita-error'] = "Cita not found.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['edit-cita-error'] = "Database error: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
    exit();
}

// Obtener la lista de usuarios registrados para el desplegable
try {
    $query = "SELECT idUser, name, last_name FROM users_data ORDER BY name, last_name";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['edit-cita-error'] = "Database error: " . $e->getMessage();
    $users = [];
}
?>

<section class="form-signin w-100 m-auto mt-5 mb-5">
    <div class="form-container">
        <a href="<?= BASE_URL ?>/?url=admin" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">Editar Cita</h2>

        <form method="POST" action="<?= BASE_URL ?>/?url=admin/edit-cita-logic&id=<?= $cita_id ?>" enctype="multipart/form-data">
            <div class="form-floating mb-2">
                <select name="idUser" class="form-select custom-select" id="floatingUserId" required>
                    <option value="">Seleccionar Usuario</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['idUser']) ?>" 
                                <?= $user['idUser'] == $cita['idUser'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['name'] . ' ' . $user['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="floatingUserId">Usuario</label>
            </div>

            <div class="form-floating mb-2">
                <input name="fecha_cita" type="date" class="form-control w-100" id="floatingDate" 
                       value="<?= htmlspecialchars($cita['fecha_cita']) ?>" required>
                <label for="floatingDate">Fecha </label>
            </div>

            <div class="form-floating mb-2">
                <textarea 
                    name="motivo_cita" 
                    id="motivo_cita" 
                    class="form-control w-100" 
                    placeholder="Reason for the cita..." 
                    style="height: 200px; background: #02264a; 
                    color: white; resize: vertical; border: 1px 
                    solid #0056b3; border-radius: 5px;" 
                    required
                ><?= htmlspecialchars($cita['motivo_cita']) ?></textarea>
                <label for="motivo_cita">Razón de la cita</label>
            </div>

            <div class="my-2">
                Volver al <a href="<?= BASE_URL ?>/?url=admin/manage-citas" class="text-success fw-bold">Manage Citas</a>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Update Cita</button>
            <p class="mt-3 mb-3 text-muted text-center">© <?php echo date("Y"); ?></p>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['edit-cita-success'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?= addslashes($_SESSION['edit-cita-success']) ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['edit-cita-success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['edit-cita-error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= addslashes($_SESSION['edit-cita-error']) ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['edit-cita-error']); ?>
        <?php endif; ?>
    });
</script>

<?php include_once __DIR__ . "/../../public/templates/footer.php"; ?>