<?php
require_once __DIR__ . '/../session/session.php'; // Inicia la sesión

// Limpiar todas las variables de sesión
$_SESSION = [];

// Si se usa una cookie de sesión, eliminarla
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio con un parámetro temporal
header('Location: ' . BASE_URL . '/?logout=success');
exit();
