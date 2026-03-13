<?php
// registro.php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (isLogged()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = sanitizeInput($_POST['nombre_usuario']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $nombre = sanitizeInput($_POST['nombre']);
    $apellidos = sanitizeInput($_POST['apellidos']);

    // Validacion servidor
    if (empty($nombreUsuario) || empty($email) || empty($password)) {
        $error = "Nombre de usuario, email y constraseña son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido.";
    } else {
        // Comprobar existencia
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR nombre_usuario = ?");
        $stmt->execute([$email, $nombreUsuario]);
        if ($stmt->fetch()) {
            $error = "El email o nombre de usuario ya está registrado.";
        } else {
            // El usuario elige el rol ('cliente' o 'vendedor')
            $rolSeleccionado = isset($_POST['rol']) ? sanitizeInput($_POST['rol']) : 'cliente';
            if (!in_array($rolSeleccionado, ['cliente', 'vendedor'])) {
                $rolSeleccionado = 'cliente'; // Por seguridad
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, password, nombre, apellidos, rol) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$nombreUsuario, $email, $hashed_password, $nombre, $apellidos, $rolSeleccionado])) {
                $success = "Registro exitoso. Ya puedes <a href='index.php'>iniciar sesión</a>.";
            } else {
                $error = "Error al registrar el usuario.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Registro de Usuario</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="registro.php" onsubmit="return validarRegistro()">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_usuario" class="form-label">Usuario *</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Tipo de Cuenta</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="cliente">Cliente (Comprar productos)</option>
                            <option value="vendedor">Vendedor (Gestionar tienda)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                    <p class="text-center mt-3 text-muted"><small>* Campos obligatorios</small></p>
                </form>

                <div class="text-center mt-3">
                    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>