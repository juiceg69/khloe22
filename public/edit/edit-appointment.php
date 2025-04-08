<?php
//session_start();
require_once __DIR__ . "/../config/database.php"; // Ajustar ruta
require_once __DIR__ . "/../templates/header.php"; // Ajustar ruta

// Verificar si el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID de cita no proporcionado. Redirigiendo a citas...";
    header('Location: ' . BASE_URL . '/?url=appointments');
    exit();
}

$idCita = $_GET['id'];
$user_id = $_SESSION['idUser'];

try {
    global $pdo; // Usar $pdo global
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE idCita = :idCita AND idUser = :idUser");
    $stmt->bindParam(':idCita', $idCita, PDO::PARAM_INT);
    $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        $_SESSION['error'] = "Cita no encontrada o no pertenece al usuario. Redirigiendo a citas...";
        header('Location: ' . BASE_URL . '/?url=appointments');
        exit();
    }

    // Verificar si la cita es pasada
    $isPast = strtotime($cita['fecha_cita']) < strtotime(date('Y-m-d'));
    if ($isPast) {
        $_SESSION['error'] = "No puedes editar citas pasadas.";
        header('Location: ' . BASE_URL . '/?url=appointments');
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener la cita: " . $e->getMessage());
}
?>

<section class="form-signin w-100 m-auto mb-5 mt-5">
    <div class="form-container" style="max-width: 600px;">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">Editar Cita</h2>

        <!-- Formulario para editar la cita -->
        <form method="POST" action="<?= BASE_URL ?>/?url=appointments-logic" id="editCitaForm">
            <input type="hidden" name="idCita" value="<?= $cita['idCita'] ?>">
            <div class="form-floating mb-2">
                <input type="date" class="form-control w-100" id="floatingFecha" name="fecha_cita" value="<?= htmlspecialchars($cita['fecha_cita']) ?>" required>
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
                    style="background: #02264a; color: white; width: 100%; height: 150px; resize: vertical; border: 2px solid #0056b3; border-radius: 5px;"
                ><?= htmlspecialchars($cita['motivo_cita']) ?></textarea>
                <label for="floatingMotivo">Motivo</label>
            </div> 
            <button type="submit" name="edit_cita" class="btn btn-primary w-100 py-2 mb-3">Actualizar Cita</button>
        </form>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Validación para evitar símbolos no deseados en el motivo
    document.getElementById('floatingMotivo').addEventListener('input', function(e) {
        const regex = /[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,!?]/g; // Permitir letras, números, acentos, ñ, espacios y algunos signos básicos
        this.value = this.value.replace(regex, '');
    });
</script>
<?php require_once __DIR__ . "/../templates/footer.php"; ?>