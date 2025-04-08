<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start(); // Ya está iniciada en index.php, no es necesario repetir
$current_url = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
require_once __DIR__ . '/../config/database.php';

function normalizeUrl($url) {
    $url = trim($url, '/');
    $url = strtolower($url);
    return $url ?: '';
}

$current_url = normalizeUrl($current_url);

// Obtener la URL actual desde ?url=
$current_page = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

// Definir las URLs de cada sección según el rol usando ?url=
$nav_items = [
    'visitor' => [
        'Index' => BASE_URL,
        'Noticias' => BASE_URL . '/?url=blog',
        'Sign Up' => BASE_URL . '/?url=signup',
        'Log In' => BASE_URL . '/?url=login'
    ],
    'user' => [
        'Index' => BASE_URL,
        'Noticias' => BASE_URL . '/?url=blog',
        'Citaciones' => BASE_URL . '/?url=appointments',
        'Perfil' => BASE_URL . '/?url=profile',
        'Log Out' => BASE_URL . '/?url=logout'
    ],
    'admin' => [
        'Index' => BASE_URL,
        'Perfil' => BASE_URL . '/?url=profile',
        'Noticias' => BASE_URL . '/?url=blog',
        'Usuarios admin' => BASE_URL . '/?url=admin/manage-users',
        'Citas admin' => BASE_URL . '/?url=admin/manage-citas',
        'Noticias admin' => BASE_URL . '/?url=admin',
        'Log Out' => BASE_URL . '/?url=logout'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP & MySQL Blog App with Admin Panel</title>

    <!-- Favicon -->
    <link rel="icon" type="favicon/ico" href="<?= IMAGES_PATH ?>/favicon.ico">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?= FONTAWESOME_5_CSS ?>">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= BOOTSTRAP_ICONS_CSS ?>">  

    <!-- UNICONS CSS -->
    <link rel="stylesheet" href="<?= UNICONS_CSS ?>">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="<?= SWEETALERT_CSS ?>">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="<?= BOOTSTRAP_5_CSS ?>">

    <!-- GOOGLE FONT (MONTSERRAT) -->
    <link href="<?= MONTSERRAT_FONT ?>" rel="stylesheet">

    <!-- CUSTOM STYLES -->
    <link rel="stylesheet" href="<?= CSS_PATH ?>/style.css">
</head>
<body>
    <nav>
        <div class="container nav__container">
            <a href="<?= BASE_URL ?>" class="nav__logo d-flex align-items-center">
                <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid" style="max-width: 70px; height: auto;">
            </a>
            <ul class="nav__items">
                <?php
                // Determinar el rol del usuario
                if (!isset($_SESSION['idUser'])) {
                    $role = 'visitor';
                } elseif ($_SESSION['is_admin'] == 0) {
                    $role = 'user';
                } else {
                    $role = 'admin';
                }

                // Generar los ítems de navegación según el rol
                foreach ($nav_items[$role] as $label => $url) {
                    // Extraer el valor de ?url= desde la URL del enlace
                    $url_param = str_replace(BASE_URL . '/?url=', '', $url);
                    $url_param = str_replace(BASE_URL, '', $url_param); // Para manejar la raíz (BASE_URL)

                    // Determinar si el enlace está activo
                    $is_active = ($label === 'Index' && $current_page === '' && $url === BASE_URL) ||
                                 ($current_page === $url_param && $url !== BASE_URL) ? 'active' : '';

                    // Agregar ícono solo a "Index", y estilos especiales para "Sign Up", "Log In" y "Log Out"
                    if ($label === 'Index') {
                        echo "<li class='$is_active'>
                            <a href='$url' class='nav-link'><i class='fas fa-home'></i> $label</a>
                        </li>";
                    } elseif ($label === 'Sign Up') {
                        echo "<li>
                            <a class='nav-link fw-bold custom-btn signup-btn btn btn-primary' href='$url'>
                                <i class='fas fa-user-plus'></i> $label
                            </a>
                        </li>";
                    } elseif ($label === 'Log In') {
                        echo "<li>
                            <a class='nav-link fw-bold custom-btn login-btn btn btn-success' href='$url'>
                                <i class='fas fa-sign-in-alt'></i> $label
                            </a>
                        </li>";
                    } elseif ($label === 'Log Out' || $label === 'Cerrar Sesión') {
                        echo "<li>
                            <a class='nav-link fw-bold custom-btn logout-btn btn btn-danger' href='$url'>
                                <i class='fas fa-sign-out-alt'></i> $label
                            </a>
                        </li>";
                    } else {
                        echo "<li class='$is_active'><a href='$url' class='nav-link'>$label</a></li>";
                    }
                }
                ?>
            </ul>
            <button id="toggle__nav-btn"><i class="uil uil-bars"></i></button>
        </div>
    </nav>

    <?php if (isset($_SESSION['login-success'])) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?= addslashes($_SESSION['login-success']) ?>',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['login-success']); ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success') : ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'You have successfully logged out!',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    <?php endif; ?>

    <!----- END OF NAV ----->