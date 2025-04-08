<?php
//session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../public/config/database.php";
include_once __DIR__ . "/../../public/templates/header.php";

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ' . BASE_URL . '/?url=login');
    exit();
}

// Obtener el ID del usuario desde la URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    $_SESSION['edit-user-error'] = "Invalid user ID.";
    header('Location: ' . BASE_URL . '/?url=admin/manage-users');
    exit();
}

// Obtener los datos del usuario
try {
    global $pdo;
    $query = "SELECT users_data.*, users_login.usuario, users_login.rol 
              FROM users_data 
              JOIN users_login ON users_data.idUser = users_login.idUser 
              WHERE users_data.idUser = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['edit-user-error'] = "User not found.";
        header('Location: ' . BASE_URL . '/?url=admin/manage-users');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['edit-user-error'] = "Database error: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/?url=admin/manage-users');
    exit();
}

// Procesar el formulario de edición
if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $last_name = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $date_birth = $_POST['date_birth'];
    $address = filter_var($_POST['address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = $_POST['gender'];
    $rol = isset($_POST['category']) && $_POST['category'] == '1' ? 'admin' : 'user';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validaciones
    if (empty($name) || empty($last_name) || empty($email) || empty($phone) || empty($date_birth) || empty($address) || empty($gender)) {
        $_SESSION['edit-user-error'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['edit-user-error'] = "Invalid email format.";
    } elseif (!empty($password) && $password !== $confirmPassword) {
        $_SESSION['edit-user-error'] = "Passwords do not match.";
    } else {
        try {
            // Actualizar users_data
            $query = "UPDATE users_data 
                      SET name = :name, last_name = :last_name, email = :email, 
                          phone = :phone, date_birth = :date_birth, address = :address, gender = :gender 
                      WHERE idUser = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':date_birth', $date_birth, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Actualizar users_login
            $query = "UPDATE users_login SET rol = :rol";
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $query .= ", password = :password";
            }
            $query .= " WHERE idUser = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
            if (!empty($password)) {
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            }
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['edit-user-success'] = "User updated successfully.";
            header('Location: ' . BASE_URL . '/?url=admin/manage-users');
            exit();
        } catch (PDOException $e) {
            $_SESSION['edit-user-error'] = "Database error: " . $e->getMessage();
        }
    }

    header('Location: ' . BASE_URL . '/?url=admin/edit-user&id=' . $user_id);
    exit();
}
?>

<section class="form-signin w-100 m-auto mt-5 mb-5">
    <div class="form-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/?url=admin" class="text-center mb-4 d-block">
            <img src="<?= IMAGES_PATH ?>/logo.png" alt="Logo" class="logo-image img-fluid mx-auto d-block" style="max-width: 70px; height: auto;">
        </a>
        <h2 class="h3 mb-3 fw-normal">Editar Usuario</h2>

        <!-- Mostrar mensajes de error o éxito con SweetAlert -->
        <?php if (isset($_SESSION['edit-user-error'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: <?= json_encode($_SESSION['edit-user-error']) ?>,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    });
                });
            </script>
            <?php unset($_SESSION['edit-user-error']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/?url=admin/edit-user&id=<?= $user_id ?>" enctype="multipart/form-data">
            <div class="form-floating mb-2">
                <input name="name" type="text" class="form-control w-100" id="floatingName" placeholder="Nombre" 
                    value="<?= htmlspecialchars($user['name']) ?>" required>    
                <label for="floatingName">Nombre</label>
            </div>
            <div class="form-floating mb-2">
                <input name="lastname" type="text" class="form-control w-100" id="floatingLastname" placeholder="Apellidos" 
                    value="<?= htmlspecialchars($user['last_name']) ?>" required>
                <label for="floatingLastname">Apellidos</label>
            </div>
            <div class="form-floating mb-2">
                <input name="email" type="email" class="form-control w-100" id="floatingEmail" placeholder="Correo Electrónico" 
                    value="<?= htmlspecialchars($user['email']) ?>" required>
                <label for="floatingEmail">Email</label>
            </div>
            <div class="form-floating mb-2">
                <input name="phone" type="tel" class="form-control w-100" id="floatingPhone" placeholder="Teléfono" 
                    value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                <label for="floatingPhone">Teléfono</label>
            </div>
            <div class="form-floating mb-2">
                <input name="date_birth" type="date" class="form-control w-100" id="floatingDateBirth" 
                    value="<?= htmlspecialchars($user['date_birth'] ?? '') ?>" required>
                <label for="floatingDateBirth">Fecha de Nacimiento</label>    
            </div>
            <div class="form-floating mb-2">
                <input name="address" type="text" class="form-control w-100" id="floatingAddress" placeholder="Dirección" 
                    value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
                <label for="floatingAddress">Dirección</label>
            </div>
            <div class="form-floating mb-2">
                <select name="gender" class="form-select custom-select" id="floatingGender" required>
                    <option value="">Seleccione sexo</option>
                    <option value="male" <?= ($user['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Masculino</option>
                    <option value="female" <?= ($user['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Femenino</option>
                    <option value="other" <?= ($user['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Otro</option>
                </select>
                <label for="floatingGender">Sexo</label>
            </div>
            <div class="form-floating mb-2">
                <input name="usuario" type="text" class="form-control w-100" id="floatingUsername" placeholder="Usuario" 
                    value="<?= htmlspecialchars($user['usuario']) ?>" disabled>
                <label for="floatingUsername">Nombre de Usuario</label>
            </div>
            <div class="form-floating mb-2 position-relative">
                <input name="password" type="password" class="form-control w-100" id="floatingPassword" placeholder="Contraseña">
                <label for="floatingPassword">Nueva Contraseña (opcional)</label>
                <span class="input-icon" onclick="togglePasswordVisibility('floatingPassword', this)">
                    <i class="fas fa-eye dark-icon" id="passwordIcon"></i>
                </span>
            </div>
            <div class="form-floating mb-2 position-relative">
                <input name="confirmPassword" type="password" class="form-control w-100" id="floatingRetypePassword" placeholder="Confirmar Contraseña">
                <label for="floatingRetypePassword">Confirmar Nueva Contraseña (opcional)</label>
                <span class="input-icon" onclick="togglePasswordVisibility('floatingRetypePassword', this)">
                    <i class="fas fa-eye dark-icon" id="confirmPasswordIcon"></i>
                </span>
            </div>
            <div class="form-floating mb-2">
                <select name="category" class="form-select custom-select" id="floatingCategory" required>
                    <option value="">Seleccione rol de usuario</option>
                    <option value="0" <?= ($user['rol'] == 'user') ? 'selected' : '' ?>>Usuario</option>
                    <option value="1" <?= ($user['rol'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                </select>
                <label for="floatingCategory">Rol de usuario</label>
            </div>
            <div class="my-2">
                Volver al <a href="<?= BASE_URL ?>/?url=admin" class="text-success fw-bold">Panel de Administración</a>
            </div>
            <button class="btn btn-primary w-100 py-2" name="submit" type="submit">Actualizar Usuario</button>
            <p class="mt-3 mb-3 text-muted text-center">© <?php echo date("Y"); ?></p>
        </form>
    </div>
</section>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.js"></script>
<?php include_once __DIR__ . "/../../public/templates/footer.php"; ?>