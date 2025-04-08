<?php
//session_start(); 
require_once __DIR__ . "/../config/constants.php";

// Recuperar datos del formulario si hubo un error previo
$name            = $_SESSION['signup-data']['name'] ?? '';
$lastname        = $_SESSION['signup-data']['lastname'] ?? '';
$username        = $_SESSION['signup-data']['username'] ?? '';
$email           = $_SESSION['signup-data']['email'] ?? '';
$createpassword  = $_SESSION['signup-data']['password'] ?? '';
$confirmpassword = $_SESSION['signup-data']['confirmpassword'] ?? '';
$phone           = $_SESSION['signup-data']['phone'] ?? '';
$gender          = $_SESSION['signup-data']['gender'] ?? '';
$address         = $_SESSION['signup-data']['address'] ?? '';
$date_birth      = $_SESSION['signup-data']['date_birth'] ?? '';

// Recuperar errores si existen
$errors = $_SESSION['signup-errors'] ?? [];

// Limpiar datos de sesión después de usarlos
unset($_SESSION['signup-data']);
unset($_SESSION['signup-errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <link rel="icon" type="image/x-icon" href="<?= IMAGES_PATH ?>/favicon.ico">
    <link rel="stylesheet" href="<?= FONTAWESOME_5_CSS ?>">
    <link rel="stylesheet" href="<?= BOOTSTRAP_ICONS_CSS ?>">  
    <link rel="stylesheet" href="<?= UNICONS_CSS ?>">
    <link rel="stylesheet" href="<?= SWEETALERT_CSS ?>">
    <link href="<?= BOOTSTRAP_4_CSS ?>" rel="stylesheet">
    <link href="<?= MONTSERRAT_FONT ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= CSS_PATH ?>/style.css">
</head>
<body>
    <section class="form-signin w-100 m-auto">
        <div class="form-container">
            <a href="<?= BASE_URL ?>" class="text-center mb-4 d-block">
                <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
            </a>
            <h2 class="h3 mb-3 fw-normal">Sign Up</h2>
            <form method="POST" action="<?= BASE_URL ?>/?url=signup-logic" id="signupForm" novalidate>
                <div class="form-floating mb-2">
                    <input name="name" type="text" value="<?= htmlspecialchars($name) ?>" class="form-control w-100 <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="floatingName" placeholder="Nombre" required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['name']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input name="lastname" type="text" value="<?= htmlspecialchars($lastname) ?>" class="form-control w-100 <?= isset($errors['lastname']) ? 'is-invalid' : '' ?>" id="floatingLastname" placeholder="Apellidos" required>
                    <?php if (isset($errors['lastname'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['lastname']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input name="email" type="email" value="<?= htmlspecialchars($email) ?>" class="form-control w-100 <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="floatingEmail" placeholder="name@example.com" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input name="username" type="text" value="<?= htmlspecialchars($username) ?>" class="form-control w-100 <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="floatingUsername" placeholder="Usuario" required>
                    <?php if (isset($errors['username'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['username']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2 position-relative">
                    <input name="password" type="password" value="<?= htmlspecialchars($createpassword) ?>" class="form-control w-100 <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="floatingPassword" placeholder="Contraseña" required>
                    <span class="input-icon" onclick="togglePasswordVisibility('floatingPassword', this)">
                        <i class="fas fa-eye dark-icon" id="passwordIcon"></i>
                    </span>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2 position-relative">
                    <input name="confirmPassword" type="password" value="<?= htmlspecialchars($confirmpassword) ?>" class="form-control w-100 <?= isset($errors['confirmPassword']) ? 'is-invalid' : '' ?>" id="floatingRetypePassword" placeholder="Confirmar Contraseña" required>
                    <span class="input-icon" onclick="togglePasswordVisibility('floatingRetypePassword', this)">
                        <i class="fas fa-eye dark-icon" id="retypePasswordIcon"></i>
                    </span>
                    <?php if (isset($errors['confirmPassword'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['confirmPassword']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input name="phone" type="tel" value="<?= htmlspecialchars($phone) ?>" class="form-control w-100 <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="floatingPhone" placeholder="Teléfono" required>
                    <?php if (isset($errors['phone'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['phone']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input name="date_birth" type="date" value="<?= htmlspecialchars($date_birth) ?>" class="form-control <?= isset($errors['date_birth']) ? 'is-invalid' : '' ?>" id="floatingBirthdate" required>
                    <?php if (isset($errors['date_birth'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['date_birth']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input name="address" type="text" value="<?= htmlspecialchars($address) ?>" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" id="floatingAddress" placeholder="Dirección" required>
                    <?php if (isset($errors['address'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['address']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <select name="gender" class="form-select custom-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" id="floatingGender" required>
                        <option value="">Seleccione sexo</option>
                        <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Masculino</option>
                        <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Femenino</option>
                        <option value="other" <?= $gender === 'other' ? 'selected' : '' ?>>Otros</option>
                    </select>
                    <?php if (isset($errors['gender'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['gender']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-check mb-3">
                    <input name="terms" type="checkbox" class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>" id="floatingTerms" required>
                    <label class="form-check-label" for="floatingTerms">Aceptar términos y condiciones</label>
                    <?php if (isset($errors['terms'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['terms']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="my-2">
                    ¿Ya tienes una cuenta? 
                    <a href="<?= BASE_URL ?>/?url=login" class="text-success fw-bold">¡Inicia sesión!</a>
                </div>
                <button class="btn btn-primary w-100 py-2" name="submit" type="submit" id="submitButton">Sign Up</button>
                <p class="mt-3 mb-3 text-muted text-center">© <?php echo date("Y"); ?></p>
            </form>
        </div>
    </section>

    <?php if (isset($_SESSION['signup-success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: <?= json_encode($_SESSION['signup-success']) ?>,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= BASE_URL ?>/?url=login';
                    }
                });
            });
        </script>
        <?php unset($_SESSION['signup-success']); ?>
    <?php endif; ?>

    <script src="<?= BOOTSTRAP_JS ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= JS_PATH ?>/main.js"></script>
    
</body>
</html>