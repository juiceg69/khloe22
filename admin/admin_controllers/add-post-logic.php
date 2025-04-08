<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['idUser']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['add-post-error'] = "Debes iniciar sesión como administrador para agregar una noticia.";
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos estén presentes
    if (empty($_POST['title']) || empty($_POST['date']) || empty($_POST['texto']) || empty($_FILES['imagen']['name'])) {
        $_SESSION['add-post-error'] = "Todos los campos son obligatorios.";
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ' . BASE_URL . '/?url=admin/add-post');
        exit();
    }

    // Obtener datos del formulario
    $title = trim($_POST['title']);
    $date = $_POST['date'];
    $texto = trim($_POST['texto']);
    $idUser = $_SESSION['idUser'];

    // Validar que idUser no sea null (por seguridad adicional)
    if (empty($idUser)) {
        $_SESSION['add-post-error'] = "Error: No se pudo identificar al usuario. Por favor, inicia sesión nuevamente.";
        header('Location: ' . BASE_URL . '/?url=login');
        exit();
    }

    // Validar y procesar la imagen
    $image = $_FILES['imagen'];
    $image_name = uniqid() . '-' . basename($image['name']);
    $upload_path = __DIR__ . "/../../public/assets/images/" . $image_name;
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    // Validar tipo de archivo
    if (!in_array($image['type'], $allowed_types)) {
        $_SESSION['add-post-error'] = "Solo se permiten imágenes JPG, JPEG, PNG o GIF.";
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ' . BASE_URL . '/?url=admin/add-post');
        exit();
    }

    // Validar tamaño de archivo
    if ($image['size'] > $max_size) {
        $_SESSION['add-post-error'] = "La imagen es demasiado grande. Tamaño máximo: 2MB.";
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ' . BASE_URL . '/?url=admin/add-post');
        exit();
    }

    // Verificar permisos de escritura en la carpeta
    if (!is_writable(dirname($upload_path))) {
        $_SESSION['add-post-error'] = "La carpeta de destino no tiene permisos de escritura.";
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ' . BASE_URL . '/?url=admin/add-post');
        exit();
    }

    // Mover el archivo subido
    if (!move_uploaded_file($image['tmp_name'], $upload_path)) {
        $_SESSION['add-post-error'] = "Error al subir la imagen.";
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ' . BASE_URL . '/?url=admin/add-post');
        exit();
    }

    // Insertar noticia en la base de datos
    try {
        global $pdo;
        $query = "INSERT INTO noticias (idUser, titulo, imagen, texto, fecha) VALUES (:idUser, :titulo, :imagen, :texto, :fecha)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':idUser' => $idUser,
            ':titulo' => $title,
            ':imagen' => $image_name,
            ':texto' => $texto,
            ':fecha' => $date
        ]);

        // Limpiar datos de sesión después de éxito
        unset($_SESSION['add-post-data']);
        $_SESSION['add-post-success'] = "Noticia agregada exitosamente.";
        // Redirigir a ?url=blog
        header('Location: ' . BASE_URL . '/?url=blog');
        exit();
    } catch (PDOException $e) {
        $_SESSION['add-post-error'] = "Error al agregar la noticia: " . $e->getMessage();
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ' . BASE_URL . '/?url=admin/add-post');
        exit();
    }
} else {
    header('Location: ' . BASE_URL . '/?url=admin/add-post');
    exit();
}