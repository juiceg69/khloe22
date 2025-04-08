<?php
//session_start(); // Iniciar sesión

// Habilitar visualización de errores para depuración (puedes desactivarlo en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirigir si el usuario no está logueado
if (!isset($_SESSION['idUser'])) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

require_once __DIR__ . "/../config/database.php"; // Reutilizar $pdo
global $pdo;

// Función para validar fechas en formato YYYY-MM-DD
function isValidDate($date) {
    $format = 'Y-m-d';
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

// Agregar una nueva cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cita'])) {
    $fecha_cita = filter_var($_POST['fecha_cita'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $motivo_cita = filter_var($_POST['motivo_cita'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id = $_SESSION['idUser'];

    // Validaciones adicionales
    if (empty($fecha_cita)) {
        $_SESSION['error'] = "Por favor, ingresa la fecha de la cita.";
    } elseif (!isValidDate($fecha_cita)) {
        $_SESSION['error'] = "La fecha de la cita debe tener el formato YYYY-MM-DD y ser válida.";
    } elseif (strtotime($fecha_cita) < strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = "No puedes agendar citas en fechas pasadas.";
    } elseif (empty($motivo_cita)) {
        $_SESSION['error'] = "Por favor, ingresa el motivo de la cita.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s.,\'?-ñÑáéíóúÁÉÍÓÚ]+$/', $motivo_cita)) {
        $_SESSION['error'] = "El motivo de la cita solo puede contener letras, números, espacios, comas, puntos, apóstrofes, signos de interrogación, guiones y acentos.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO citas (idUser, fecha_cita, motivo_cita) VALUES (:idUser, :fecha_cita, :motivo_cita)");
            $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_cita', $fecha_cita, PDO::PARAM_STR);
            $stmt->bindParam(':motivo_cita', $motivo_cita, PDO::PARAM_STR);
            $stmt->execute();

            $_SESSION['success'] = "Cita agregada exitosamente.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al agregar la cita: " . $e->getMessage();
        }
    }

    header('Location: ' . BASE_URL . '/?url=appointments');
    exit();
}

// Eliminar una cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cita'])) {
    $idCita = filter_var($_POST['idCita'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = $_SESSION['idUser'];

    try {
        // Verificar si la cita existe y pertenece al usuario
        $stmt = $pdo->prepare("SELECT fecha_cita FROM citas WHERE idCita = :idCita AND idUser = :idUser");
        $stmt->bindParam(':idCita', $idCita, PDO::PARAM_INT);
        $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cita) {
            // Verificar si la cita es pasada
            if (strtotime($cita['fecha_cita']) < strtotime(date('Y-m-d'))) {
                $_SESSION['error'] = "No puedes eliminar citas pasadas.";
            } else {
                // Eliminar la cita
                $stmt = $pdo->prepare("DELETE FROM citas WHERE idCita = :idCita AND idUser = :idUser");
                $stmt->bindParam(':idCita', $idCita, PDO::PARAM_INT);
                $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                $_SESSION['success'] = "Cita eliminada exitosamente.";
            }
        } else {
            $_SESSION['error'] = "Cita no encontrada o no tienes permiso para eliminarla.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al eliminar la cita: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . '/?url=appointments');
    exit();
}

// Editar una cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_cita'])) {
    $idCita = filter_var($_POST['idCita'], FILTER_SANITIZE_NUMBER_INT);
    $fecha_cita = filter_var($_POST['fecha_cita'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $motivo_cita = filter_var($_POST['motivo_cita'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id = $_SESSION['idUser'];

    // Validaciones adicionales
    if (empty($fecha_cita)) {
        $_SESSION['error'] = "Por favor, ingresa la fecha de la cita.";
    } elseif (!isValidDate($fecha_cita)) {
        $_SESSION['error'] = "La fecha de la cita debe tener el formato YYYY-MM-DD y ser válida.";
    } elseif (strtotime($fecha_cita) < strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = "No puedes agendar citas en fechas pasadas.";
    } elseif (empty($motivo_cita)) {
        $_SESSION['error'] = "Por favor, ingresa el motivo de la cita.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s.,\'?-ñÑáéíóúÁÉÍÓÚ]+$/', $motivo_cita)) {
        $_SESSION['error'] = "El motivo de la cita solo puede contener letras, números, espacios, comas, puntos, apóstrofes, signos de interrogación, guiones y acentos.";
    } else {
        try {
            // Verificar si la cita existe y pertenece al usuario
            $stmt = $pdo->prepare("SELECT fecha_cita FROM citas WHERE idCita = :idCita AND idUser = :idUser");
            $stmt->bindParam(':idCita', $idCita, PDO::PARAM_INT);
            $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $cita = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cita) {
                // Verificar si la cita es pasada
                if (strtotime($cita['fecha_cita']) < strtotime(date('Y-m-d'))) {
                    $_SESSION['error'] = "No puedes editar citas pasadas.";
                } else {
                    // Actualizar la cita
                    $stmt = $pdo->prepare("UPDATE citas SET fecha_cita = :fecha_cita, motivo_cita = :motivo_cita WHERE idCita = :idCita AND idUser = :idUser");
                    $stmt->bindParam(':fecha_cita', $fecha_cita, PDO::PARAM_STR);
                    $stmt->bindParam(':motivo_cita', $motivo_cita, PDO::PARAM_STR);
                    $stmt->bindParam(':idCita', $idCita, PDO::PARAM_INT);
                    $stmt->bindParam(':idUser', $user_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $_SESSION['success'] = "Cita actualizada exitosamente.";
                }
            } else {
                $_SESSION['error'] = "Cita no encontrada o no tienes permiso para editarla.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al actualizar la cita: " . $e->getMessage();
        }
    }

    header('Location: ' . BASE_URL . '/?url=appointments');
    exit();
}