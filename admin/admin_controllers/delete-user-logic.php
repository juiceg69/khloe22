<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

// Obtener el ID del usuario desde la URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    $_SESSION['delete-user-error'] = "Invalid user ID.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-users');
    exit();
}

// No permitir que un administrador se elimine a sí mismo
if ($user_id === (int)$_SESSION['idUser']) {
    $_SESSION['delete-user-error'] = "You cannot delete your own account.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-users');
    exit();
}

// Eliminar el usuario
try {
    global $pdo;

    // Eliminar de users_login
    $query = "DELETE FROM users_login WHERE idUser = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Eliminar de users_data
    $query = "DELETE FROM users_data WHERE idUser = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['delete-user-success'] = "User deleted successfully.";
} catch (PDOException $e) {
    $_SESSION['delete-user-error'] = "Database error: " . $e->getMessage();
}

header('Location: ' . BASE_URL . '/?url=admin/manage-users');
exit();