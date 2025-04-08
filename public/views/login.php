<?php
// /public/views/login.php
//session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/database.php";

// Recuperar datos del formulario si hubo un error previo
$username = isset($_SESSION['signin-data']['username']) ? $_SESSION['signin-data']['username'] : '';

// Recuperar errores si existen
$errors = $_SESSION['signin-errors'] ?? [];

// Limpiar datos de sesión después de usarlos
unset($_SESSION['signin-data']);
unset($_SESSION['signin-errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="icon" type="favicon/ico" href="<?= IMAGES_PATH ?>/favicon.ico">
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
            <!-- Logo -->
            <a href="<?= BASE_URL ?>" class="text-center mb-4 d-block">
                <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
            </a>
            <h2 class="h3 mb-3 fw-normal">Log In</h2>

            <!-- Formulario -->
            <form method="POST" action="<?= BASE_URL ?>/?url=login-logic">
                <div class="form-floating mb-2 position-relative has-icon-left">
                    <input 
                        type="text" 
                        class="form-control w-100 <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                        id="username" 
                        value="<?= htmlspecialchars($username) ?>" 
                        name="username" 
                        placeholder="Usuario o Email" 
                        required>
                    <span class="input-icon-left">
                        <i class="fas fa-user"></i>
                    </span>
                    <?php if (isset($errors['username'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['username']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2 position-relative has-icon-left">
                    <input 
                        type="password" 
                        class="form-control w-100 <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                        id="password" 
                        name="password" 
                        placeholder="Contraseña" 
                        required>
                    <span class="input-icon-left">
                        <i class="fas fa-lock lock-icon" id="lockIcon"></i>
                    </span>
                    <span class="input-icon" onclick="togglePasswordVisibility('password', this)">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </span>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if (isset($errors['database'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errors['database']) ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($errors['method'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errors['method']) ?>
                    </div>
                <?php endif; ?>
                <div class="my-2">
                    ¿No tienes una cuenta? 
                    <a href="<?= BASE_URL ?>/?url=signup" class="text-success fw-bold">¡Regístrate aquí!</a>
                </div>
                <button type="submit" name="submit" class="btn btn-primary w-100 py-2">Log In</button>
                <p class="mt-3 mb-3 text-muted text-center">© <?php echo date("Y"); ?></p>
            </form>
        </div>
    </section>

    <!-- Scripts -->
    <script src="<?= SWEETALERT_JS ?>"></script>
    <script src="<?= BOOTSTRAP_JS ?>"></script>
    <script src="<?= JS_PATH ?>/main.js"></script>
</body>
</html>




