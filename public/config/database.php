<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/constants.php'; 

try {
    // Conectar a MySQL y seleccionar la base de datos
    global $pdo; // Declaramos $pdo como variable global
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    //echo "✅ Conexión a la base de datos '" . DB_NAME . "' establecida correctamente.\n";

} catch (PDOException $e) {
    die("❌ Error al conectar a MySQL: " . $e->getMessage());
}

// Función para obtener noticias (reutilizable)
function getNoticias($pdo, $search_term = '', $is_admin_filter = false) {
    try {
        // Consulta base para obtener noticias con el nombre del autor
        $sql = "
            SELECT n.idNoticia, n.titulo, n.imagen, n.texto, n.fecha, u.name 
            FROM noticias n 
            JOIN users_data u ON n.idUser = u.idUser 
            WHERE 1=1
        ";

        // Agregar filtro de búsqueda si existe
        if (!empty($search_term)) {
            $sql .= " AND (n.titulo LIKE :search OR n.texto LIKE :search)";
        }

        // Agregar filtro para usuarios no administradores si se especifica
        if ($is_admin_filter) {
            $sql .= " AND u.is_admin = 0";
        }

        $sql .= " ORDER BY n.fecha DESC";

        $stmt = $pdo->prepare($sql);
        if (!empty($search_term)) {
            $stmt->execute([':search' => "%$search_term%"]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al obtener las noticias: " . $e->getMessage());
    }
}

// Función para obtener una noticia específica por ID
function getNoticiaById($pdo, $idNoticia) {
    try {
        $sql = "
            SELECT n.idNoticia, n.titulo, n.imagen, n.texto, n.fecha, u.name, u.last_name 
            FROM noticias n 
            LEFT JOIN users_data u ON n.idUser = u.idUser 
            WHERE n.idNoticia = :id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $idNoticia]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al obtener la noticia: " . $e->getMessage());
    }
}
?>