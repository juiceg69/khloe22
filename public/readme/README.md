# Proyecto Khloe - Sistema de Gestión de Noticias, Usuarios y Citas

Este proyecto es un sistema web desarrollado en PHP que permite la gestión de noticias, usuarios y citas. Incluye un panel de administración para usuarios con rol de administrador y vistas públicas para usuarios regulares y visitantes. El sistema utiliza una base de datos MySQL/MariaDB y sigue un patrón de enrutamiento básico para manejar las solicitudes.

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- **Servidor web**: Apache (por ejemplo, XAMPP, WAMP, o MAMP).
- **PHP**: Versión 7.4 o superior (recomendado 8.0+).
- **MySQL/MariaDB**: Versión 5.7 o superior.
- **Navegador web**: Cualquier navegador moderno (Chrome, Firefox, Edge, etc.).
- **Acceso a phpMyAdmin** (opcional, para importar la base de datos fácilmente).
- **Composer** (opcional, si deseas instalar dependencias adicionales en el futuro).
- **Conexión a internet** (necesaria para cargar recursos externos como SweetAlert2, Bootstrap y jQuery).

## Estructura del Proyecto

El proyecto está organizado de la siguiente manera:

- **`index.php`**: Punto de entrada principal del sitio, delega el enrutamiento a `init.php`.
- **`init.php`**: Enrutador principal que carga las vistas desde `public/views/` y conecta con la configuración y la base de datos.
- **`public/config/`**:
  - `constants.php`: Define constantes globales como `BASE_URL`, `DB_HOST`, `DB_USER`, `DB_PASS`, y `DB_NAME`.
  - `database.php`: Establece la conexión a la base de datos usando PDO.
- **`public/views/`**: Contiene las vistas públicas (como `blog.php`, `post.php`) y las vistas de administración (en `admin/pages/`).
- **`admin/`**:
  - `pages/`: Vistas del panel de administración (por ejemplo, `add-user.php`, `manage-users.php`).
  - `admin_controllers/`: Controladores para manejar la lógica del panel de administración (por ejemplo, `add-user-logic.php`).
  - `delete/`: Controladores para manejar eliminaciones (por ejemplo, `delete-user.php`).
- **`public/templates/`**: Plantillas reutilizables como `header.php` y `footer.php`.
- **`public/assets/`**: Archivos estáticos como CSS, JavaScript e imágenes.
- **`sql/`**:
  - `create_db.sql`: Script SQL para crear la base de datos `khloe` y las tablas necesarias (`users_data`, `users_login`, `noticias`, `citas`).

## Instrucciones para Configurar y Probar el Proyecto

### 1. Descomprimir el Proyecto
1. Descomprime el archivo `khloe.zip` en la carpeta `htdocs` de tu servidor local:
   - **Windows (XAMPP)**: `C:\xampp\htdocs\`.
   - **macOS (XAMPP)**: `/Applications/XAMPP/htdocs/`.
   - **Linux (XAMPP)**: `/opt/lampp/htdocs/`.
   Esto debería crear una carpeta llamada `khloe`.

### 2. Configurar la Base de Datos
1. Crea la base de datos e importa las tablas:
   - **Usando phpMyAdmin**:
     1. Accede a phpMyAdmin (por ejemplo, en `http://localhost/phpmyadmin/`).
     2. Crea una base de datos llamada `khloe`.
     3. Selecciona la base de datos `khloe`, ve a la pestaña "Importar", y sube el archivo `sql/create_db.sql`.
   - **Usando la terminal**:
     ```bash
     mysql -u root -p < sql/create_db.sql