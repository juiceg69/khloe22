<?php
//session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";
include __DIR__ . "/../../public/templates/header.php";

// Control de acceso: Solo administradores pueden agregar usuarios
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: ' . BASE_URL . '/?url=admin');
    exit();
}

// Recuperar datos del formulario si hubo un error
$name = $_SESSION['add-user-data']['name'] ?? '';
$lastname = $_SESSION['add-user-data']['lastname'] ?? '';
$usuario = $_SESSION['add-user-data']['usuario'] ?? '';
$email = $_SESSION['add-user-data']['email'] ?? '';
$phone = $_SESSION['add-user-data']['phone'] ?? '';
$date_birth = $_SESSION['add-user-data']['date_birth'] ?? '';
$address = $_SESSION['add-user-data']['address'] ?? '';
$gender = $_SESSION['add-user-data']['gender'] ?? '';
$createPassword = $_SESSION['add-user-data']['password'] ?? '';
$confirmPassword = $_SESSION['add-user-data']['confirmPassword'] ?? '';
$userrole = $_SESSION['add-user-data']['category'] ?? '';

// Recuperar errores si existen
$errors = $_SESSION['add-user-errors'] ?? [];

// Eliminar datos de sesión después de usarlos
unset($_SESSION['add-user-data']);
unset($_SESSION['add-user-errors']);
?>

<section class="form-signin w-100 m-auto mt-5 mb-5">
    <div class="form-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/?url=admin" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">Add Usuario</h2>

        <!-- Mostrar mensaje de éxito con SweetAlert -->
        <?php if (isset($_SESSION['add-user-success'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: <?= json_encode($_SESSION['add-user-success']) ?>,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    });
                });
            </script>
            <?php unset($_SESSION['add-user-success']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/?url=admin/add-user-logic" enctype="multipart/form-data">
            <div class="form-floating mb-2">
                <input name="name" type="text" class="form-control w-100 <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="floatingName" placeholder="Nombre" 
                    value="<?= htmlspecialchars($name) ?>" required>    
                <label for="floatingName">Nombre</label>
                <?php if (isset($errors['name'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <input name="lastname" type="text" class="form-control w-100 <?= isset($errors['lastname']) ? 'is-invalid' : '' ?>" id="floatingLastname" placeholder="Apellidos" 
                    value="<?= htmlspecialchars($lastname) ?>" required>
                <label for="floatingLastname">Apellidos</label>
                <?php if (isset($errors['lastname'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['lastname']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <input name="email" type="email" class="form-control w-100 <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="floatingEmail" placeholder="Correo Electrónico" 
                    value="<?= htmlspecialchars($email) ?>" required>
                <label for="floatingEmail">Email</label>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <input name="phone" type="tel" class="form-control w-100 <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="floatingPhone" placeholder="Teléfono" 
                    value="<?= htmlspecialchars($phone) ?>" required>
                <label for="floatingPhone">Teléfono</label>
                <?php if (isset($errors['phone'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['phone']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <input name="date_birth" type="date" class="form-control w-100 <?= isset($errors['date_birth']) ? 'is-invalid' : '' ?>" id="floatingDateBirth" 
                    value="<?= htmlspecialchars($date_birth) ?>" required>
                <label for="floatingDateBirth">Fecha de Nacimiento</label>    
                <?php if (isset($errors['date_birth'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['date_birth']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <input name="address" type="text" class="form-control w-100 <?= isset($errors['address']) ? 'is-invalid' : '' ?>" id="floatingAddress" placeholder="Dirección" 
                    value="<?= htmlspecialchars($address) ?>" required>
                <label for="floatingAddress">Dirección</label>
                <?php if (isset($errors['address'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['address']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <select name="gender" class="form-select custom-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" id="floatingGender" required>
                    <option value="">Seleccione sexo</option>
                    <option value="male" <?= ($gender == 'male') ? 'selected' : '' ?>>Masculino</option>
                    <option value="female" <?= ($gender == 'female') ? 'selected' : '' ?>>Femenino</option>
                    <option value="other" <?= ($gender == 'other') ? 'selected' : '' ?>>Otro</option>
                </select>
                <label for="floatingGender">Sexo</label>
                <?php if (isset($errors['gender'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['gender']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <input name="usuario" type="text" class="form-control w-100 <?= isset($errors['usuario']) ? 'is-invalid' : '' ?>" id="floatingUsername" placeholder="Usuario" 
                    value="<?= htmlspecialchars($usuario) ?>" required>
                <label for="floatingUsername">Nombre de Usuario</label>
                <?php if (isset($errors['usuario'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['usuario']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2 position-relative">
                <input name="password" type="password" class="form-control w-100 <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="floatingPassword" placeholder="Contraseña"
                    value="<?= htmlspecialchars($createPassword) ?>" required>
                <label for="floatingPassword">Contraseña</label>
                <span class="input-icon" onclick="togglePasswordVisibility('floatingPassword', this)">
                    <i class="fas fa-eye dark-icon" id="passwordIcon"></i>
                </span>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2 position-relative">
                <input name="confirmPassword" type="password" class="form-control w-100 <?= isset($errors['confirmPassword']) ? 'is-invalid' : '' ?>" id="floatingRetypePassword" placeholder="Confirmar Contraseña" 
                    value="<?= htmlspecialchars($confirmPassword) ?>" required>
                <label for="floatingRetypePassword">Confirmar Contraseña</label>
                <span class="input-icon" onclick="togglePasswordVisibility('floatingRetypePassword', this)">
                    <i class="fas fa-eye dark-icon" id="confirmPasswordIcon"></i>
                </span>
                <?php if (isset($errors['confirmPassword'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['confirmPassword']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-floating mb-2">
                <select name="category" class="form-select custom-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" id="floatingCategory" required>
                    <option value="">Seleccione rol de usuario</option>
                    <option value="0" <?= ($userrole == '0') ? 'selected' : '' ?>>Usuario</option>
                    <option value="1" <?= ($userrole == '1') ? 'selected' : '' ?>>Administrador</option>
                </select>
                <label for="floatingCategory">Rol de usuario</label>
                <?php if (isset($errors['category'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['category']) ?></span>
                <?php endif; ?>
            </div>
            <div class="my-2">
                Volver al <a href="<?= BASE_URL ?>/?url=admin" class="text-success fw-bold">Panel de Administración</a>
            </div>
            <button class="btn btn-primary w-100 py-2" name="submit" type="submit">Agregar Usuario</button>
            <p class="mt-3 mb-3 text-muted text-center">© <?php echo date("Y"); ?></p>
        </form>
    </div>
</section>

<!-- Estilos para los mensajes de error -->
<style>
    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
        display: block;
    }
    .form-control.is-invalid {
        border-color: red;
    }
    .form-select.is-invalid {
        border-color: red;
    }
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.js"></script>
<?php include_once __DIR__ . "/../../public/templates/footer.php"; ?>