<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../public/config/database.php";

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

try {
    global $pdo;

    // Verificar si la cita existe
    $query = "SELECT idCita FROM citas WHERE idCita = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $cita_id]);
    if (!$stmt->fetch()) {
        $_SESSION['delete-cita-error'] = "Cita not found.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
        exit();
    }

    // Eliminar la cita
    $query = "DELETE FROM citas WHERE idCita = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $cita_id]);

    // Confirmar que la eliminación fue exitosa
    if ($stmt->rowCount() > 0) {
        $_SESSION['delete-cita-success'] = "Cita successfully deleted.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
        exit();
    } else {
        $_SESSION['delete-cita-error'] = "The cita was not deleted. Please try again.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['delete-cita-error'] = "Error deleting cita: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
    exit();
}