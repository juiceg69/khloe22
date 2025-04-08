<?php
// /public/controllers/signup-logic.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/constants.php";

global $pdo;

// Función para validar fechas en formato YYYY-MM-DD
function isValidDate($date) {
    $format = 'Y-m-d';
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize and trim inputs
    $name = trim($_POST['name'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $username = trim($_POST['username'] ?? '');
    $createPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $dateBirth = trim($_POST['date_birth'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    // Array para almacenar todos los errores
    $errors = [];

    // Server-side validation
    if (empty($name) || !preg_match("/^[a-zA-Z\s'-ñÑáéíóúÁÉÍÓÚ]+$/", $name)) {
        $errors['name'] = "El nombre solo puede contener letras, espacios, apóstrofes, guiones y acentos.";
    }
    if (strlen($name) > 50) {
        $errors['name'] = "El nombre no puede exceder los 50 caracteres.";
    }

    if (empty($lastname) || !preg_match("/^[a-zA-Z\s'-ñÑáéíóúÁÉÍÓÚ]+$/", $lastname)) {
        $errors['lastname'] = "El apellido solo puede contener letras, espacios, apóstrofes, guiones y acentos.";
    }
    if (strlen($lastname) > 50) {
        $errors['lastname'] = "El apellido no puede exceder los 50 caracteres.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Por favor ingrese un email válido.";
    }

    if (empty($username) || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "El usuario solo puede contener letras, números y guiones bajos.";
    }
    if (strlen($username) > 30) {
        $errors['username'] = "El usuario no puede exceder los 30 caracteres.";
    }

    if (empty($createPassword) || strlen($createPassword) < 6 || 
        !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $createPassword)) {
        $errors['password'] = "La contraseña debe tener al menos 6 caracteres, incluyendo mayúscula, minúscula, número y carácter especial.";
    } elseif ($createPassword !== $confirmPassword) {
        $errors['confirmPassword'] = "Las contraseñas no coinciden.";
    }

    if (empty($phone) || !preg_match('/^[0-9\s()-]{9,15}$/', $phone)) {
        $errors['phone'] = "El teléfono debe contener solo números, espacios, guiones o paréntesis, y tener entre 9 y 15 caracteres.";
    }

    if (empty($dateBirth) || !isValidDate($dateBirth)) {
        $errors['date_birth'] = "Por favor seleccione una fecha de nacimiento válida en formato YYYY-MM-DD.";
    } elseif (strtotime($dateBirth) >= strtotime(date('Y-m-d'))) {
        $errors['date_birth'] = "La fecha de nacimiento no puede ser en el futuro.";
    } elseif (date('Y', strtotime($dateBirth)) < 1900) {
        $errors['date_birth'] = "La fecha de nacimiento no puede ser anterior a 1900.";
    }

    if (empty($address) || !preg_match('/^[a-zA-Z0-9\s.,-ñÑáéíóúÁÉÍÓÚ]+$/', $address)) {
        $errors['address'] = "La dirección solo puede contener letras, números, espacios, puntos, comas, guiones y acentos.";
    }
    if (strlen($address) > 100) {
        $errors['address'] = "La dirección no puede exceder los 100 caracteres.";
    }

    if (empty($gender) || !in_array($gender, ['male', 'female', 'other'])) {
        $errors['gender'] = "Por favor seleccione un género válido.";
    }

    if (!isset($_POST['terms'])) {
        $errors['terms'] = "Debe aceptar los términos y condiciones.";
    }

    // Si no hay errores, proceder con el registro
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM users_data 
                WHERE email = :email
                UNION
                SELECT COUNT(*) 
                FROM users_login 
                WHERE usuario = :username
            ");
            $stmt->execute([':email' => $email, ':username' => $username]);
            $counts = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (array_sum($counts) > 0) {
                $errors['email'] = "El usuario o email ya existe.";
            } else {
                $hashed_password = password_hash($createPassword, PASSWORD_DEFAULT);
                
                $pdo->beginTransaction();
                
                // Insertar en users_data (sin is_admin)
                $insert_user_query = $pdo->prepare("
                    INSERT INTO users_data (name, last_name, email, phone, date_birth, address, gender) 
                    VALUES (:name, :last_name, :email, :phone, :date_birth, :address, :gender)
                ");
                $insert_user_query->execute([
                    ':name' => $name,
                    ':last_name' => $lastname,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':date_birth' => $dateBirth,
                    ':address' => $address,
                    ':gender' => $gender
                ]);

                $user_id = $pdo->lastInsertId();
                // Insertar en users_login (con is_admin)
                $insert_login_query = $pdo->prepare("
                    INSERT INTO users_login (idUser, usuario, password, rol, is_admin) 
                    VALUES (:idUser, :usuario, :password, :rol, :is_admin)
                ");
                $insert_login_query->execute([
                    ':idUser' => $user_id,
                    ':usuario' => $username,
                    ':password' => $hashed_password,
                    ':rol' => 'user',
                    ':is_admin' => 0
                ]);

                $pdo->commit();
                
                $_SESSION['signup-success'] = "Registro exitoso. Por favor inicia sesión.";
                header('Location: ' . BASE_URL . '/?url=signup');
                exit();
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['database'] = "Error en la base de datos: " . $e->getMessage();
            error_log("PDO Exception: " . $e->getMessage());
        }
    }

    // Guardar los datos del formulario y los errores en la sesión
    $_SESSION['signup-data'] = $_POST;
    $_SESSION['signup-errors'] = $errors;
    header('Location: ' . BASE_URL . '/?url=signup');
    exit();
}

header('Location: ' . BASE_URL . '/?url=signup');
exit();