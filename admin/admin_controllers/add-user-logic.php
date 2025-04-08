<?php
// /admin/admin_controllers/add-user-logic.php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Control de acceso: Solo administradores pueden agregar usuarios
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: ' . BASE_URL . '/?url=admin');
    exit();
}

require_once __DIR__ . "/../../public/config/database.php"; // Usar la conexión existente

// Función para validar fechas en formato YYYY-MM-DD
function isValidDate($date) {
    $format = 'Y-m-d';
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

// Verificar si el formulario fue enviado
if (isset($_POST['submit'])) {
    // Sanitizar y recopilar datos del formulario
    $name = trim($_POST['name'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $usuario = trim($_POST['usuario'] ?? '');
    $createPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $date_birth = trim($_POST['date_birth'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $userrole = $_POST['category'] ?? ''; // 0 para usuario, 1 para admin

    // Array para almacenar todos los errores
    $errors = [];

    // Validar campos (mismas reglas que signup-logic.php)
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

    if (empty($usuario) || !preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
        $errors['usuario'] = "El usuario solo puede contener letras, números y guiones bajos.";
    }
    if (strlen($usuario) > 30) {
        $errors['usuario'] = "El usuario no puede exceder los 30 caracteres.";
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

    if (empty($date_birth) || !isValidDate($date_birth)) {
        $errors['date_birth'] = "Por favor seleccione una fecha de nacimiento válida en formato YYYY-MM-DD.";
    } elseif (strtotime($date_birth) >= strtotime(date('Y-m-d'))) {
        $errors['date_birth'] = "La fecha de nacimiento no puede ser en el futuro.";
    } elseif (date('Y', strtotime($date_birth)) < 1900) {
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

    if ($userrole === '' || !in_array($userrole, ['0', '1'])) {
        $errors['category'] = "Por favor seleccione un rol válido.";
    }

    // Si no hay errores, proceder con la inserción
    if (empty($errors)) {
        // Hash de la contraseña
        $hashed_password = password_hash($createPassword, PASSWORD_DEFAULT);

        try {
            global $pdo; // Usar la conexión PDO global definida en database.php

            // Verificar si el usuario o email ya existen
            $stmt = $pdo->prepare("
                SELECT usuario FROM users_login WHERE usuario = :usuario 
                UNION 
                SELECT email FROM users_data WHERE email = :email
            ");
            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $errors['usuario'] = "El usuario o email ya existe.";
            } else {
                // Insertar los datos en la tabla `users_data` (sin is_admin)
                $insert_user_query = $pdo->prepare("
                    INSERT INTO users_data (name, last_name, email, phone, date_birth, address, gender) 
                    VALUES (:name, :last_name, :email, :phone, :date_birth, :address, :gender)
                ");
                $insert_user_query->bindParam(':name', $name, PDO::PARAM_STR);
                $insert_user_query->bindParam(':last_name', $lastname, PDO::PARAM_STR);
                $insert_user_query->bindParam(':email', $email, PDO::PARAM_STR);
                $insert_user_query->bindParam(':phone', $phone, PDO::PARAM_STR);
                $insert_user_query->bindParam(':date_birth', $date_birth, PDO::PARAM_STR);
                $insert_user_query->bindParam(':address', $address, PDO::PARAM_STR);
                $insert_user_query->bindParam(':gender', $gender, PDO::PARAM_STR);

                if ($insert_user_query->execute()) {
                    // Obtener el ID del usuario recién insertado
                    $user_id = $pdo->lastInsertId();

                    // Insertar las credenciales en la tabla `users_login` (con is_admin)
                    $insert_login_query = $pdo->prepare("
                        INSERT INTO users_login (idUser, usuario, password, rol, is_admin) 
                        VALUES (:idUser, :usuario, :password, :rol, :is_admin)
                    ");
                    $insert_login_query->bindParam(':idUser', $user_id, PDO::PARAM_INT);
                    $insert_login_query->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                    $insert_login_query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $insert_login_query->bindValue(':rol', ($userrole == 1) ? 'admin' : 'user', PDO::PARAM_STR);
                    $insert_login_query->bindParam(':is_admin', $userrole, PDO::PARAM_INT);

                    if ($insert_login_query->execute()) {
                        // Éxito
                        $_SESSION['add-user-success'] = "Nuevo usuario $name $lastname agregado exitosamente.";
                        header('Location: ' . BASE_URL . '/?url=admin/manage-users');
                        exit();
                    } else {
                        $errors['database'] = "Error al agregar el usuario. Por favor, intenta de nuevo.";
                    }
                } else {
                    $errors['database'] = "Error al agregar el usuario. Por favor, intenta de nuevo.";
                }
            }
        } catch (PDOException $e) {
            $errors['database'] = "Error de base de datos: " . $e->getMessage();
        }
    }

    // Guardar datos del formulario y errores en la sesión
    $_SESSION['add-user-data'] = $_POST;
    $_SESSION['add-user-errors'] = $errors;

    // Redirigir de vuelta a la página de agregar usuario si hay algún problema
    header('Location: ' . BASE_URL . '/?url=admin/add-user');
    exit();
} else {
    header('Location: ' . BASE_URL . '/?url=admin/add-user');
    exit();
}