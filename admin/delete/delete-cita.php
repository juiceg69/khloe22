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

// Obtener el ID de la cita desde la URL
$cita_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cita_id <= 0) {
    $_SESSION['delete-cita-error'] = "Invalid cita ID.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
    exit();
}

// Obtener los datos de la cita
try {
    global $pdo;
    $query = "SELECT citas.*, users_data.name, users_data.last_name 
              FROM citas 
              JOIN users_data ON citas.idUser = users_data.idUser 
              WHERE idCita = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $cita_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['delete-cita-error'] = "Cita not found.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['delete-cita-error'] = "Database error: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
    exit();
}

// Redirigir directamente al controlador de eliminación
header('Location: ' . BASE_URL . '/?url=admin/delete-cita-logic&id=' . $cita_id);
exit();