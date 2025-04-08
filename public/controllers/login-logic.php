<?php
// /public/controllers/login-logic.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Obtener y sanitizar datos del formulario
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Array para almacenar todos los errores
    $errors = [];

    // Validar el campo username
    if (empty($username)) {
        $errors['username'] = "El usuario es obligatorio.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "El usuario solo puede contener letras, números y guiones bajos.";
    } elseif (strlen($username) > 30) {
        $errors['username'] = "El usuario no puede exceder los 30 caracteres.";
    }

    // Validar el campo password
    if (empty($password)) {
        $errors['password'] = "La contraseña es obligatoria.";
    }

    // Si no hay errores de validación, proceder con la autenticación
    if (empty($errors)) {
        try {
            // Usar la conexión PDO global definida en database.php
            global $pdo;

            // Buscar al usuario en la base de datos (is_admin ahora está en users_login)
            $query = "SELECT idUser, usuario, password, is_admin 
                      FROM users_login 
                      WHERE usuario = :username";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verificar la contraseña
                if (password_verify($password, $user['password'])) {
                    // Establecer variables de sesión
                    $_SESSION['idUser'] = $user['idUser'];
                    $_SESSION['username'] = $user['usuario'];
                    $_SESSION['is_admin'] = $user['is_admin'];

                    // Establecer mensaje de éxito
                    $_SESSION['login-success'] = ((int)$user['is_admin'] === 1) 
                        ? "You have successfully logged in as Admin!"
                        : "You have successfully logged in as user!";

                    // Redirigir según el rol
                    if ((int)$user['is_admin'] === 1) {
                        header('Location: ' . BASE_URL . '/?url=admin');
                    } else {
                        header('Location: ' . BASE_URL . '/'); // Redirección al index para usuarios
                    }
                    exit();
                } else {
                    $errors['password'] = "Contraseña incorrecta.";
                }
            } else {
                $errors['username'] = "Usuario no encontrado.";
            }
        } catch (PDOException $e) {
            $errors['database'] = "Error de base de datos: " . $e->getMessage();
        }
    }

    // Guardar datos del formulario y errores en la sesión
    $_SESSION['signin-data'] = ['username' => $username];
    $_SESSION['signin-errors'] = $errors;

    // Redirigir al formulario de login en caso de error
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
} else {
    $_SESSION['signin-errors'] = ['method' => "Método no permitido. Usa el formulario para iniciar sesión."];
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}