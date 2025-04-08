<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Control de acceso: Solo administradores pueden editar usuarios
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: ' . BASE_URL . '/?url=admin');
    exit();
}

require_once __DIR__ . "/../../public/config/database.php";

// Verificar si el formulario fue enviado
if (isset($_POST['submit'])) {
    // Sanitizar y recopilar datos del formulario
    $idUser = filter_var($_POST['idUser'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $usuario = filter_var($_POST['usuario'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $createPassword = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $date_birth = filter_var($_POST['date_birth'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userrole = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT); // 0 para usuario, 1 para admin

    // Validar campos
    if (!$idUser || !is_numeric($idUser)) {
        $_SESSION['edit-user'] = "ID de usuario no válido.";
    } elseif (!$name) {
        $_SESSION['edit-user'] = "Por favor, ingresa el nombre.";
    } elseif (!$lastname) {
        $_SESSION['edit-user'] = "Por favor, ingresa los apellidos.";
    } elseif (!$email) {
        $_SESSION['edit-user'] = "Por favor, ingresa un correo electrónico válido.";
    } elseif (!$usuario) {
        $_SESSION['edit-user'] = "Por favor, ingresa un nombre de usuario.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
        $_SESSION['edit-user'] = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } elseif ($createPassword && strlen($createPassword) < 6) {
        $_SESSION['edit-user'] = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($createPassword && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $createPassword)) {
        $_SESSION['edit-user'] = "La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial.";
    } elseif ($createPassword && $createPassword !== $confirmPassword) {
        $_SESSION['edit-user'] = "Las contraseñas no coinciden.";
    } elseif (!$phone) {
        $_SESSION['edit-user'] = "Por favor, ingresa un número de teléfono.";
    } elseif (!$date_birth) {
        $_SESSION['edit-user'] = "Por favor, ingresa la fecha de nacimiento.";
    } elseif (!$address) {
        $_SESSION['edit-user'] = "Por favor, ingresa la dirección.";
    } elseif (!$gender) {
        $_SESSION['edit-user'] = "Por favor, selecciona el sexo.";
    } elseif ($userrole === "") {
        $_SESSION['edit-user'] = "Por favor, selecciona el rol del usuario.";
    } else {
        try {
            global $pdo; // Usar la conexión PDO global

            // Verificar si el usuario o email ya existen (excluyendo el usuario actual)
            $stmt = $pdo->prepare("
                SELECT usuario FROM users_login WHERE usuario = :usuario AND idUser != :idUser
                UNION 
                SELECT email FROM users_data WHERE email = :email AND idUser != :idUser
            ");
            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['edit-user'] = "El nombre de usuario o el correo electrónico ya existen.";
            } else {
                // Actualizar los datos en la tabla `users_data`
                $update_user_query = $pdo->prepare("
                    UPDATE users_data 
                    SET name = :name, last_name = :last_name, email = :email, phone = :phone, 
                        date_birth = :date_birth, address = :address, gender = :gender, is_admin = :is_admin 
                    WHERE idUser = :idUser
                ");
                $update_user_query->bindParam(':name', $name, PDO::PARAM_STR);
                $update_user_query->bindParam(':last_name', $lastname, PDO::PARAM_STR);
                $update_user_query->bindParam(':email', $email, PDO::PARAM_STR);
                $update_user_query->bindParam(':phone', $phone, PDO::PARAM_STR);
                $update_user_query->bindParam(':date_birth', $date_birth, PDO::PARAM_STR);
                $update_user_query->bindParam(':address', $address, PDO::PARAM_STR);
                $update_user_query->bindParam(':gender', $gender, PDO::PARAM_STR);
                $update_user_query->bindParam(':is_admin', $userrole, PDO::PARAM_INT);
                $update_user_query->bindParam(':idUser', $idUser, PDO::PARAM_INT);

                if ($update_user_query->execute()) {
                    // Actualizar las credenciales en la tabla `users_login`
                    $update_login_query = $pdo->prepare("
                        UPDATE users_login 
                        SET usuario = :usuario, rol = :rol 
                        WHERE idUser = :idUser
                    ");
                    $update_login_query->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                    $update_login_query->bindValue(':rol', ($userrole == 1) ? 'admin' : 'user', PDO::PARAM_STR);
                    $update_login_query->bindParam(':idUser', $idUser, PDO::PARAM_INT);

                    // Si se proporcionó una nueva contraseña, actualizarla
                    if ($createPassword) {
                        $hashed_password = password_hash($createPassword, PASSWORD_DEFAULT);
                        $update_login_query = $pdo->prepare("
                            UPDATE users_login 
                            SET usuario = :usuario, password = :password, rol = :rol 
                            WHERE idUser = :idUser
                        ");
                        $update_login_query->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                        $update_login_query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                        $update_login_query->bindValue(':rol', ($userrole == 1) ? 'admin' : 'user', PDO::PARAM_STR);
                        $update_login_query->bindParam(':idUser', $idUser, PDO::PARAM_INT);
                    }

                    if ($update_login_query->execute()) {
                        $_SESSION['edit-user-success'] = "Usuario $name $lastname actualizado exitosamente.";
                        header('Location: ' . BASE_URL . '/?url=admin/manage-users');
                        exit();
                    } else {
                        $_SESSION['edit-user'] = "Error al actualizar el usuario. Por favor, intenta de nuevo.";
                    }
                } else {
                    $_SESSION['edit-user'] = "Error al actualizar el usuario. Por favor, intenta de nuevo.";
                }
            }
        } catch (PDOException $e) {
            $_SESSION['edit-user'] = "Error de base de datos: " . $e->getMessage();
        }
    }

    // Guardar datos del formulario en la sesión para mostrarlos en caso de error
    $_SESSION['edit-user-data'] = $_POST;

    // Redirigir de vuelta a la página de edición si hay algún problema
    header('Location: ' . BASE_URL . '/?url=admin/edit-user&id=' . $idUser);
    exit();
} else {
    header('Location: ' . BASE_URL . '/?url=admin/manage-users');
    exit();
}