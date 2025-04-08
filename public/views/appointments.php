<?php
//session_start(); 
require_once __DIR__ . "/../config/database.php"; // Reutilizar $pdo
require_once __DIR__ . "/../templates/header.php";

// Configurar cabeceras para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idUser'])) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

global $pdo; // Usar $pdo global
$user_id = $_SESSION['idUser'];

try {
    // Obtener todas las citas del usuario actual (pasadas y futuras)
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE idUser = :idUser ORDER BY fecha_cita DESC");
    $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las citas: " . $e->getMessage());
}
?>

<section class="form-signin w-100 m-auto mb-5 mt-5">
    <div class="form-container" style="max-width: 600px;">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">Citas</h2>

        <!-- Formulario para agregar cita -->
        <form method="POST" action="<?= BASE_URL ?>/?url=appointments-logic">
            <div class="form-floating mb-2">
                <input type="date" class="form-control w-100" id="floatingFecha" name="fecha_cita" required>
                <label for="floatingFecha">Fecha</label>
            </div>
            <div class="form-floating mb-2">
                <textarea 
                    class="form-control w-100" 
                    id="floatingMotivo" 
                    name="motivo_cita" 
                    placeholder="Motivo" 
                    required 
                    rows="5" 
                    style="background: #02264a; color: white; width: 100%; height: 150px; resize: vertical; border: 1px solid #0056b3; border-radius: 5px;"
                ></textarea>
                <label for="floatingMotivo">Motivo</label>
            </div>
            <button type="submit" name="add_cita" class="btn btn-primary w-100 py-2 mb-3">Agregar Cita</button>
        </form>

        <!-- Lista de citas programadas -->
        <h2 class="h3 mb-3 fw-normal mt-4">Citas Programadas</h2>
        <div class="appointments-list">
            <?php if (empty($citas)): ?>
                <p class="text-center" style="color: #d3d1d1;">No hay citas programadas.</p>
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <div class="appointment-card mb-3 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1"><strong>Fecha:</strong> <?= htmlspecialchars($cita['fecha_cita']) ?></p>
                                <p class="mb-0"><strong>Motivo:</strong> <?= htmlspecialchars($cita['motivo_cita']) ?></p>
                            </div>
                            <div class="appointment-actions">
                                <?php
                                $isPast = strtotime($cita['fecha_cita']) < strtotime(date('Y-m-d'));
                                if ($isPast) {
                                    echo '<span class="past-appointment-notice">Cita Pasada</span>';
                                } else {
                                    $editUrl = BASE_URL . '/?url=edit/edit-appointment&id=' . $cita['idCita'];
                                    echo '<a href="' . $editUrl . '" class="btn btn-warning btn-sm me-2">Editar</a>';
                                    echo '<form method="POST" action="' . BASE_URL . '/?url=appointments-logic" style="display: inline;">';
                                    echo '<input type="hidden" name="idCita" value="' . $cita['idCita'] . '">';
                                    echo '<button type="button" class="btn btn-danger btn-sm delete-appointment-btn">Eliminar</button>';
                                    echo '</form>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SweetAlert2 para éxito o error -->
<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: <?= json_encode($_SESSION['success']) ?>,
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php elseif (isset($_SESSION['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($_SESSION['error']) ?>,
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Script para confirmación de eliminación con SweetAlert -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-appointment-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Evita cualquier acción predeterminada
                const form = this.closest('form'); // Obtiene el formulario más cercano
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Quieres eliminar esta cita?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Agregar el campo delete_cita al formulario antes de enviarlo
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'delete_cita';
                        input.value = '1';
                        form.appendChild(input);
                        form.submit(); // Envía el formulario
                    }
                });
            });
        });
    });
</script>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php require_once __DIR__ . "/../templates/footer.php"; ?>