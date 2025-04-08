<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['delete-post-error'] = "ID de noticia inválido.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
    exit();
}

$idNoticia = $_GET['id'];

try {
    global $pdo;
    if (!$pdo) {
        die("The DataBase connection is not defined in the dataBase.php");
    }

    // Verificar si la noticia existe
    $noticia = getNoticiaById($pdo, $idNoticia);

    if (!$noticia) {
        $_SESSION['delete-post-error'] = "La noticia no existe.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
        exit();
    }

    // Eliminar la imagen del servidor
    $image_path = __DIR__ . "/../../../public/assets/images/" . $noticia['imagen'];
    if (file_exists($image_path)) {
        if (!unlink($image_path)) {
            $_SESSION['delete-post-error'] = "Error al eliminar la imagen del servidor.";
            header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
            exit();
        }
    }

    // Eliminar la noticia de la base de datos
    $query = "DELETE FROM noticias WHERE idNoticia = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $idNoticia]);

    $_SESSION['delete-post-success'] = "Noticia eliminada exitosamente.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
    exit();
} catch (PDOException $e) {
    $_SESSION['delete-post-error'] = "Error al eliminar la noticia: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
    exit();
}