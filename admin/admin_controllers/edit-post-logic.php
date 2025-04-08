<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";

// Verificar si el usuario es administrador
if (!isset($_SESSION['idUser']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que el ID de la noticia esté presente
    if (!isset($_POST['idNoticia']) || !is_numeric($_POST['idNoticia'])) {
        $_SESSION['edit-post-error'] = "ID de noticia inválido.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
        exit();
    }

    // Obtener datos del formulario
    $idNoticia = $_POST['idNoticia'];
    $titulo = trim($_POST['title']);
    $fecha = $_POST['date'];
    $texto = trim($_POST['texto']);

    // Guardar datos en sesión en caso de error
    $_SESSION['edit-post-data'] = [
        'title' => $titulo,
        'date' => $fecha,
        'texto' => $texto
    ];

    // Validar campos obligatorios
    if (empty($titulo) || empty($fecha) || empty($texto)) {
        $_SESSION['edit-post-error'] = "Todos los campos son obligatorios.";
        header('Location: ' . BASE_URL . '/?url=admin/edit/edit-post&id=' . $idNoticia);
        exit();
    }

    try {
        global $pdo;

        // Obtener la noticia actual para verificar la imagen existente
        $query = "SELECT imagen FROM noticias WHERE idNoticia = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $idNoticia]);
        $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$noticia) {
            $_SESSION['edit-post-error'] = "La noticia no existe.";
            header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
            exit();
        }

        $image_name = $noticia['imagen']; // Mantener la imagen actual por defecto

        // Procesar la nueva imagen si se subió una
        if (!empty($_FILES['imagen']['name'])) {
            $image = $_FILES['imagen'];
            $image_name = uniqid() . '-' . basename($image['name']);
            $upload_path = __DIR__ . "/../../public/assets/images/" . $image_name;
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            // Validar tipo de archivo
            if (!in_array($image['type'], $allowed_types)) {
                $_SESSION['edit-post-error'] = "Solo se permiten imágenes JPEG, PNG o GIF.";
                header('Location: ' . BASE_URL . '/?url=admin/edit/edit-post&id=' . $idNoticia);
                exit();
            }

            // Validar tamaño de archivo
            if ($image['size'] > $max_size) {
                $_SESSION['edit-post-error'] = "La imagen es demasiado grande. Tamaño máximo: 2MB.";
                header('Location: ' . BASE_URL . '/?url=admin/edit/edit-post&id=' . $idNoticia);
                exit();
            }

            // Verificar permisos de escritura en la carpeta
            if (!is_writable(dirname($upload_path))) {
                $_SESSION['edit-post-error'] = "La carpeta de destino no tiene permisos de escritura.";
                header('Location: ' . BASE_URL . '/?url=admin/edit/edit-post&id=' . $idNoticia);
                exit();
            }

            // Mover el archivo subido
            if (!move_uploaded_file($image['tmp_name'], $upload_path)) {
                $_SESSION['edit-post-error'] = "Error al subir la imagen.";
                header('Location: ' . BASE_URL . '/?url=admin/edit/edit-post&id=' . $idNoticia);
                exit();
            }

            // Eliminar la imagen anterior si existe
            $old_image_path = __DIR__ . "/../../public/assets/images/" . $noticia['imagen'];
            if (file_exists($old_image_path) && $noticia['imagen'] !== $image_name) {
                unlink($old_image_path);
            }
        }

        // Actualizar la noticia en la base de datos
        $query = "UPDATE noticias SET titulo = :titulo, imagen = :imagen, texto = :texto, fecha = :fecha WHERE idNoticia = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':titulo' => $titulo,
            ':imagen' => $image_name,
            ':texto' => $texto,
            ':fecha' => $fecha,
            ':id' => $idNoticia
        ]);

        // Limpiar datos de sesión después de éxito
        unset($_SESSION['edit-post-data']);
        $_SESSION['edit-post-success'] = "Noticia actualizada exitosamente.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
        exit();
    } catch (PDOException $e) {
        $_SESSION['edit-post-error'] = "Error al actualizar la noticia: " . $e->getMessage();
        header('Location: ' . BASE_URL . '/?url=admin/edit/edit-post&id=' . $idNoticia);
        exit();
    }
} else {
    header('Location: ' . BASE_URL . '/?url=admin/manage-posts');
    exit();
}