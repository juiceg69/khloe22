<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//session_start();
require_once __DIR__ . "/../../public/config/database.php";

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
    exit();
}

// Obtener el ID de la cita desde la URL
$cita_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cita_id <= 0) {
    $_SESSION['edit-cita-error'] = "Invalid cita ID.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
    exit();
}

$idUser = $_POST['idUser'] ?? '';
$fecha_cita = $_POST['fecha_cita'] ?? '';
$motivo_cita = $_POST['motivo_cita'] ?? '';

if (empty($idUser) || empty($fecha_cita) || empty($motivo_cita)) {
    $_SESSION['edit-cita-error'] = "All fields are required.";
    header('Location: ' . BASE_URL . '/?url=admin/edit-cita&id=' . $cita_id);
    exit();
}

try {
    global $pdo;

    // Verificar si el usuario existe
    $query = "SELECT idUser FROM users_data WHERE idUser = :idUser";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':idUser' => $idUser]);
    if (!$stmt->fetch()) {
        $_SESSION['edit-cita-error'] = "The selected user does not exist.";
        header('Location: ' . BASE_URL . '/?url=admin/edit-cita&id=' . $cita_id);
        exit();
    }

    // Verificar si la cita existe
    $query = "SELECT idCita FROM citas WHERE idCita = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $cita_id]);
    if (!$stmt->fetch()) {
        $_SESSION['edit-cita-error'] = "Cita not found.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
        exit();
    }

    // Actualizar la cita
    $query = "UPDATE citas SET idUser = :idUser, fecha_cita = :fecha_cita, motivo_cita = :motivo_cita WHERE idCita = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':idUser' => $idUser,
        ':fecha_cita' => $fecha_cita,
        ':motivo_cita' => $motivo_cita,
        ':id' => $cita_id
    ]);

    // Confirmar que la actualización fue exitosa
    if ($stmt->rowCount() >= 0) { // rowCount puede ser 0 si no hay cambios
        $_SESSION['edit-cita-success'] = "Cita successfully updated.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas');
        exit();
    } else {
        $_SESSION['edit-cita-error'] = "The cita was not updated. Please try again.";
        header('Location: ' . BASE_URL . '/?url=admin/edit-cita&id=' . $cita_id);
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['edit-cita-error'] = "Error updating cita: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/?url=admin/edit-cita&id=' . $cita_id);
    exit();
}