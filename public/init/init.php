<?php
// /public/init/init.php

// Incluir archivos necesarios
require_once __DIR__ . '/../session/session.php'; // Correcto: sube un nivel a /public/ y entra a session/
require_once __DIR__ . '/../config/constants.php'; // Correcto: sube un nivel a /public/ y entra a config/
require_once __DIR__ . '/../config/database.php'; // Correcto: sube un nivel a /public/ y entra a config/

// Configuración de errores
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

global $pdo;

// Obtener la ruta solicitada
$page_name = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

// Si no hay ruta, cargar la página principal (index.php)
if (empty($page_name)) {
    require_once __DIR__ . '/../../index.php'; // Correcto: sube dos niveles a /www/
    exit;
}

// Limpiar la ruta
$page_name = preg_replace('/\.php$/', '', $page_name);
$page_name = preg_replace('/[^a-zA-Z0-9\-\/]/', '', $page_name);

// Separar la ruta en partes
$path_parts = explode('/', $page_name);
$file_name = array_pop($path_parts);
$subdirectory = implode('/', $path_parts);

// Definir rutas base
$views_path = __DIR__ . '/../views/'; // Correcto: /public/views/
$controllers_path = __DIR__ . '/../controllers/'; // Correcto: /public/controllers/
$admin_pages_path = __DIR__ . '/../../admin/pages/'; // Correcto: /admin/pages/
$admin_controllers_path = __DIR__ . '/../../admin/admin_controllers/'; // Correcto: /admin/admin_controllers/
$admin_edit_path = __DIR__ . '/../../admin/edit/'; // Correcto: /admin/edit/
$admin_delete_path = __DIR__ . '/../../admin/delete/'; // Correcto: /admin/delete/

// Definir la lógica de enrutamiento
$base_path = $views_path; // Por defecto, intentamos cargar una vista
$filename = null;

// Rutas que requieren controladores (lógica)
$controller_routes = [
    'signup-logic' => [$controllers_path, 'signup-logic'],
    'login-logic' => [$controllers_path, 'login-logic'],
    'profile-logic' => [$controllers_path, 'profile-logic'],
    'appointments-logic' => [$controllers_path, 'appointments-logic'],
    'admin/add-post-logic' => [$admin_controllers_path, 'add-post-logic'],
    'admin/add-user-logic' => [$admin_controllers_path, 'add-user-logic'],
    'admin/add-cita-logic' => [$admin_controllers_path, 'add-cita-logic'],
    'admin/edit-post-logic' => [$admin_controllers_path, 'edit-post-logic'],
    'admin/edit-user-logic' => [$admin_controllers_path, 'edit-user-logic'],
    'admin/edit-cita-logic' => [$admin_controllers_path, 'edit-cita-logic'],
    'admin/delete-user-logic' => [$admin_controllers_path, 'delete-user-logic'],
    'admin/delete-cita-logic' => [$admin_controllers_path, 'delete-cita-logic'],
];

// Rutas que requieren vistas o páginas de admin
$view_routes = [
    'signup' => [$views_path, 'signup'],
    'login' => [$views_path, 'login'],
    'blog' => [$views_path, 'blog'],
    'post' => [$views_path, 'post'],
    'profile' => [$views_path, 'profile'],
    'appointments' => [$views_path, 'appointments'],
    'logout' => [$views_path, 'logout'],
    'edit/edit-appointment' => [$views_path . '../edit/', 'edit-appointment'],
    'admin' => [$admin_pages_path, 'dashboard'],
    'admin/manage-posts' => [$admin_pages_path, 'dashboard'],
    'admin/add-post' => [$admin_pages_path, 'add-post'],
    'admin/add-user' => [$admin_pages_path, 'add-user'],
    'admin/add-cita' => [$admin_pages_path, 'add-cita'],
    'admin/manage-users' => [$admin_pages_path, 'manage-users'],
    'admin/manage-citas' => [$admin_pages_path, 'manage-citas'],
    'admin/edit/edit-post' => [$admin_edit_path, 'edit-post'],
    'admin/delete/delete-post' => [$admin_delete_path, 'delete-post'],
    'admin/edit/edit-user' => [$admin_edit_path, 'edit-user'],
    'admin/delete/delete-user' => [$admin_delete_path, 'delete-user'],
    'admin/edit/edit-cita' => [$admin_edit_path, 'edit-cita'],
    'admin/delete/delete-cita' => [$admin_delete_path, 'delete-cita'],
];

// Redirecciones
if ($page_name === 'admin/edit-post') {
    header('Location: ' . BASE_URL . '/?url=admin/edit/edit-post&id=' . ($_GET['id'] ?? ''));
    exit;
} elseif ($page_name === 'admin/delete-post') {
    header('Location: ' . BASE_URL . '/?url=admin/delete/delete-post&id=' . ($_GET['id'] ?? ''));
    exit;
}

// Determinar si la ruta es un controlador o una vista
if (isset($controller_routes[$page_name])) {
    $base_path = $controller_routes[$page_name][0];
    $file_name = $controller_routes[$page_name][1];
} elseif (isset($view_routes[$page_name])) {
    $base_path = $view_routes[$page_name][0];
    $file_name = $view_routes[$page_name][1];
} elseif (!empty($subdirectory)) {
    // Manejar subdirectorios genéricos
    if ($subdirectory === 'edit') {
        $base_path = $views_path . '../edit/';
    } elseif ($subdirectory === 'admin') {
        $base_path = $admin_pages_path;
    } elseif ($subdirectory === 'admin/edit') {
        $base_path = $admin_edit_path;
    } elseif ($subdirectory === 'admin/delete') {
        $base_path = $admin_delete_path;
    } else {
        $base_path = $views_path . $subdirectory . '/';
    }
}

if (empty($file_name)) {
    $file_name = 'index';
}

// Construir la ruta del archivo
$filename = $base_path . $file_name . '.php';

// Verificar si el archivo existe y cargarlo
if (file_exists($filename) && is_readable($filename)) {
    require_once $filename;
} else {
    $error_filename = $views_path . '404.php';
    if (file_exists($error_filename) && is_readable($error_filename)) {
        require_once $error_filename;
    } else {
        http_response_code(404);
        echo "Error 404: Página no encontrada. Ruta buscada: $filename";
    }
}