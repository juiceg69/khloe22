<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//session_start();
require_once __DIR__ . "/../config/database.php";

if (!isset($_SESSION['idUser'])) {
    header('Location: ' . BASE_URL . '/?url=login'); // Usar enrutamiento
    exit();
}

$user_id = $_SESSION['idUser'];

// Función para validar fechas en formato YYYY-MM-DD
function isValidDate($date) {
    $format = 'Y-m-d';
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar los datos, pero sin FILTER_SANITIZE_FULL_SPECIAL_CHARS para evitar que escape caracteres que queremos validar
    $name = trim($_POST['name'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $phone = trim($_POST['phone'] ?? '');
    $date_birth = $_POST['date_birth'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Validaciones
    if (empty($name) || !preg_match("/^[a-zA-Z\s'-ñÑáéíóúÁÉÍÓÚ]+$/", $name)) {
        $_SESSION['error'] = "El nombre solo puede contener letras, espacios, apóstrofes, guiones y acentos.";
    } elseif (strlen($name) > 50) {
        $_SESSION['error'] = "El nombre no puede exceder los 50 caracteres.";
    } elseif (empty($lastname) || !preg_match("/^[a-zA-Z\s'-ñÑáéíóúÁÉÍÓÚ]+$/", $lastname)) {
        $_SESSION['error'] = "El apellido solo puede contener letras, espacios, apóstrofes, guiones y acentos.";
    } elseif (strlen($lastname) > 50) {
        $_SESSION['error'] = "El apellido no puede exceder los 50 caracteres.";
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Por favor ingrese un email válido.";
    } elseif (empty($phone) || !preg_match('/^[0-9\s()-]{9,15}$/', $phone)) {
        $_SESSION['error'] = "El teléfono debe contener solo números, espacios, guiones o paréntesis, y tener entre 9 y 15 caracteres.";
    } elseif (empty($date_birth) || !isValidDate($date_birth)) {
        $_SESSION['error'] = "Por favor seleccione una fecha de nacimiento válida en formato YYYY-MM-DD.";
    } elseif (strtotime($date_birth) >= strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = "La fecha de nacimiento no puede ser en el futuro.";
    } elseif (date('Y', strtotime($date_birth)) < 1900) {
        $_SESSION['error'] = "La fecha de nacimiento no puede ser anterior a 1900.";
    } elseif (empty($address) || !preg_match('/^[a-zA-Z0-9\s.,-ñÑáéíóúÁÉÍÓÚ]+$/', $address)) {
        $_SESSION['error'] = "La dirección solo puede contener letras, números, espacios, puntos, comas, guiones y acentos.";
    } elseif (strlen($address) > 100) {
        $_SESSION['error'] = "La dirección no puede exceder los 100 caracteres.";
    } elseif (empty($gender) || !in_array($gender, ['male', 'female', 'other'])) {
        $_SESSION['error'] = "Por favor seleccione un género válido.";
    } elseif ($password && (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $_POST['password']) || strlen($_POST['password']) < 6)) {
        $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.";
    } else {
        try {
            global $pdo; // Usar $pdo global

            // Verificar si el email ya está en uso por otro usuario
            $stmt = $pdo->prepare("SELECT idUser FROM users_data WHERE email = :email AND idUser != :idUser");
            $stmt->execute([':email' => $email, ':idUser' => $user_id]);
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = "El email ya está en uso por otro usuario.";
            } else {
                // Actualizar los datos
                if ($password) {
                    $stmt = $pdo->prepare("UPDATE users_data SET name = :name, last_name = :lastname, email = :email, phone = :phone, date_birth = :date_birth, address = :address, gender = :gender WHERE idUser = :idUser");
                    $stmt->execute([
                        ':name' => $name,
                        ':lastname' => $lastname,
                        ':email' => $email,
                        ':phone' => $phone,
                        ':date_birth' => $date_birth,
                        ':address' => $address,
                        ':gender' => $gender,
                        ':idUser' => $user_id
                    ]);

                    $stmt = $pdo->prepare("UPDATE users_login SET password = :password WHERE idUser = :idUser");
                    $stmt->execute([':password' => $password, ':idUser' => $user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users_data SET name = :name, last_name = :lastname, email = :email, phone = :phone, date_birth = :date_birth, address = :address, gender = :gender WHERE idUser = :idUser");
                    $stmt->execute([
                        ':name' => $name,
                        ':lastname' => $lastname,
                        ':email' => $email,
                        ':phone' => $phone,
                        ':date_birth' => $date_birth,
                        ':address' => $address,
                        ':gender' => $gender,
                        ':idUser' => $user_id
                    ]);
                }

                $_SESSION['success'] = "Perfil actualizado correctamente.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al actualizar el perfil: " . $e->getMessage();
        }
    }

    header('Location: ' . BASE_URL . '/?url=profile'); // Usar enrutamiento
    exit();
}