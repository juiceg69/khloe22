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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/?url=admin/add-cita');
    exit();
}

$idUser = $_POST['idUser'] ?? '';
$fecha_cita = $_POST['fecha_cita'] ?? '';
$motivo_cita = $_POST['motivo_cita'] ?? '';

if (empty($idUser) || empty($fecha_cita) || empty($motivo_cita)) {
    $_SESSION['add-cita-error'] = "All fields are required.";
    header('Location: ' . BASE_URL . '/?url=admin/add-cita');
    exit();
}

try {
    global $pdo;

    // Verificar si el usuario existe
    $query = "SELECT idUser FROM users_data WHERE idUser = :idUser";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':idUser' => $idUser]);
    if (!$stmt->fetch()) {
        $_SESSION['add-cita-error'] = "The selected user does not exist.";
        header('Location: ' . BASE_URL . '/?url=admin/add-cita');
        exit();
    }

    // Insertar la cita
    $query = "INSERT INTO citas (idUser, fecha_cita, motivo_cita) VALUES (:idUser, :fecha_cita, :motivo_cita)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':idUser' => $idUser,
        ':fecha_cita' => $fecha_cita,
        ':motivo_cita' => $motivo_cita
    ]);

    // Confirmar que la inserción fue exitosa
    if ($stmt->rowCount() > 0) {
        $_SESSION['add-cita-success'] = "Cita successfully added.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-citas'); // Redirige a manage-citas.php
        exit();
    } else {
        $_SESSION['add-cita-error'] = "The cita was not inserted. Please try again.";
        header('Location: ' . BASE_URL . '/?url=admin/add-cita');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['add-cita-error'] = "Error adding cita: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/?url=admin/add-cita');
    exit();
}