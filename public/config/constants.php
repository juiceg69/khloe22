<?php
/***************************************************************************************
 * CONSTANTES DEL PROYECTO KHLOE
 * 
 * Este archivo define las constantes globales utilizadas en el proyecto Khloe.
 * Las constantes están organizadas en secciones para facilitar su comprensión y uso.
 ***************************************************************************************/

/***************************************************************************************
 * RUTAS DEL PROYECTO
 * 
 * Estas constantes definen las rutas base y específicas del proyecto, tanto para el
 * frontend como para el backend (panel de administración).
 ***************************************************************************************/
// Determinar el protocolo (http o https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

// Obtener el host (por ejemplo, localhost o un dominio)
$host = $_SERVER['HTTP_HOST'];

// Obtener el path de la carpeta del proyecto (por ejemplo, /khloe)
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

// Definir BASE_URL dinámicamente
define('BASE_URL', "$protocol://$host$path"); // Raíz del proyecto (URL base del sitio, definida dinámicamente)

//define('BASE_URL', 'http://lovestoblog.space');
define('PUBLIC_PATH', __DIR__ . '/../../public'); // Ruta absoluta al directorio público
define('ADMIN_PATH', __DIR__ . '/../../admin'); // Ruta absoluta al directorio de administración
define('ADMIN_URL', BASE_URL . '/admin/pages'); // URL base del panel de administración
define('DASHBOARD_URL', ADMIN_URL . '/dashboard'); // URL del dashboard de administración

/***************************************************************************************
 * RECURSOS EXTERNOS (CDN)
 * 
 * Estas constantes definen las URLs de los recursos externos (CSS y JS) cargados a través
 * de CDN. Requieren conexión a internet para funcionar.
 ***************************************************************************************/
define('BOOTSTRAP_ICONS_CSS', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css'); // Bootstrap Icons CSS
define('BOOTSTRAP_4_CSS', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'); // Bootstrap 4 CSS
define('BOOTSTRAP_5_CSS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'); // Bootstrap 5 CSS
define('BOOTSTRAP_JS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'); // Bootstrap 5 JS (incluye Popper.js)
//define('FONTAWESOME_5_CSS', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'); // Font Awesome 5 CSS
define('FONTAWESOME_5_CSS', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
define('UNICONS_CSS', "https://unicons.iconscout.com/release/v4.0.8/css/line.css"); // Unicons CSS (íconos)
define('MONTSERRAT_FONT', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap'); // Fuente Montserrat (Google Fonts)
define('JQUERY_JS', 'https://code.jquery.com/jquery-3.6.0.min.js'); // jQuery 3.6.0 JS
define('SWEETALERT_CSS', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'); // SweetAlert2 CSS
define('SWEETALERT_JS', 'https://cdn.jsdelivr.net/npm/sweetalert2@11'); // SweetAlert2 JS

/***************************************************************************************
 * RUTAS DE ARCHIVOS ESTÁTICOS
 * 
 * Estas constantes definen las rutas para los archivos estáticos del proyecto (CSS, JS,
 * imágenes y subidas), basadas en la URL base.
 ***************************************************************************************/
define('CSS_PATH', BASE_URL . '/public/assets/css'); // Ruta a los archivos CSS del proyecto
define('JS_PATH', BASE_URL . '/public/assets/js'); // Ruta a los archivos JavaScript del proyecto
define('IMAGES_PATH', BASE_URL . '/public/assets/imgs'); // Ruta a las imágenes del proyecto
define('UPLOADS_PATH', PUBLIC_PATH . '/assets/uploads/'); // Ruta al directorio de subidas (archivos subidos por usuarios)

/***************************************************************************************
 * CONFIGURACIÓN DE LA BASE DE DATOS
 * 
 * Estas constantes definen las credenciales y el nombre de la base de datos utilizada
 * por el proyecto.
 ***************************************************************************************/
define('DB_HOST', 'localhost'); // Host del servidor de la base de datos
define('DB_USER', 'root'); // Nombre de usuario de la base de datos
define('DB_PASS', ''); // Contraseña de la base de datos (vacía por defecto en XAMPP)
define('DB_NAME', 'khloe'); // Nombre de la base de datos del proyecto

?>
