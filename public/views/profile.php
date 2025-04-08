<?php
// /public/views/profile.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start(); // Ya se maneja en header.php
require_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../templates/header.php";

if (!isset($_SESSION['idUser'])) {
    header('Location: ' . BASE_URL . '/?url=login'); // Usar enrutamiento
    exit();
}

$user_id = $_SESSION['idUser'];

try {
    global $pdo; // Usar $pdo global definido en database.php
    $stmt = $pdo->prepare("SELECT * FROM users_data WHERE idUser = :idUser");
    $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        die("No se encontraron datos para el usuario con ID: $user_id");
    }

    $stmt = $pdo->prepare("SELECT usuario, is_admin FROM users_login WHERE idUser = :idUser");
    $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_login = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_login) {
        die("No se encontró el nombre de usuario para el ID: $user_id");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del usuario: " . $e->getMessage());
}

$name = $_SESSION['profile-data']['name'] ?? $user_data['name'];
$lastname = $_SESSION['profile-data']['lastname'] ?? $user_data['last_name'];
$email = $_SESSION['profile-data']['email'] ?? $user_data['email'];
$phone = $_SESSION['profile-data']['phone'] ?? $user_data['phone'];
$date_birth = $_SESSION['profile-data']['date_birth'] ?? $user_data['date_birth'];
$address = $_SESSION['profile-data']['address'] ?? $user_data['address'];
$gender = $_SESSION['profile-data']['gender'] ?? $user_data['gender'];
$username = $user_login['usuario'];
$is_admin = $user_login['is_admin'];

unset($_SESSION['profile-data']);
?>

<section class="form-signin w-100 m-auto mb-5 mt-5">
    <div class="form-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">
            Perfil de <?= (int)$is_admin === 1 ? 'Administrador' : 'Usuario' ?>
        </h2>

        <form method="POST" action="<?= BASE_URL ?>/?url=profile-logic" id="profileForm">
            <!-- Campos del formulario -->
            <div class="form-floating mb-2">
                <input type="text" class="form-control w-100" id="floatingName" name="name" placeholder="Nombre" value="<?= htmlspecialchars($name) ?>" required>
                <label for="floatingName">Nombre</label>
            </div>
            <div class="form-floating mb-2">
                <input type="text" class="form-control w-100" id="floatingLastname" name="lastname" placeholder="Apellidos" value="<?= htmlspecialchars($lastname) ?>" required>
                <label for="floatingLastname">Apellidos</label>
            </div>
            <div class="form-floating mb-2">
                <input type="email" class="form-control w-100" id="floatingEmail" name="email" placeholder="Correo Electrónico" value="<?= htmlspecialchars($email) ?>" required>
                <label for="floatingEmail">Email</label>
            </div>
            <!-- Nombre de usuario (no editable) -->
            <div class="form-floating mb-2">
                <input type="text" class="form-control w-100" id="floatingUsername" value="<?= htmlspecialchars($username) ?>" disabled>
                <label for="floatingUsername">Nombre de Usuario</label>
            </div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control w-100" id="floatingPassword" name="password" placeholder="Nueva Contraseña">
                <label for="floatingPassword">Password (dejar en blanco para mantener la actual)</label>
            </div>
            <div class="form-floating mb-2">
                <input type="tel" class="form-control w-100" id="floatingPhone" name="phone" placeholder="Teléfono" value="<?= htmlspecialchars($phone) ?>" required>
                <label for="floatingPhone">Teléfono</label>
            </div>
            <div class="form-floating mb-2">
                <input type="date" class="form-control w-100" id="floatingDateBirth" name="date_birth" value="<?= htmlspecialchars($date_birth) ?>" required>
                <label for="floatingDateBirth">Fecha de Nacimiento</label>
            </div>
            <div class="form-floating mb-2">
                <input type="text" class="form-control w-100" id="floatingAddress" name="address" placeholder="Dirección" value="<?= htmlspecialchars($address) ?>">
                <label for="floatingAddress">Dirección</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-control w-100" id="floatingGender" name="gender">
                    <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Masculino</option>
                    <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Femenino</option>
                    <option value="other" <?= $gender === 'other' ? 'selected' : '' ?>>Otro</option>
                </select>
                <label for="floatingGender">Género</label>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit">Guardar Cambios</button>
        </form>
    </div>
</section>

<!-- SweetAlert2 para mensajes -->
<?php if (isset($_SESSION['success'])) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?= addslashes($_SESSION['success']) ?>',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php elseif (isset($_SESSION['error'])) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= addslashes($_SESSION['error']) ?>',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include_once __DIR__ . "/../templates/footer.php"; ?>