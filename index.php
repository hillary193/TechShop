<?php
// index.php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (isLogged()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_rol'] = $user['rol'];

            if (in_array($user['rol'], ['admin', 'vendedor'])) {
                header('Location: dashboard.php');
            } else {
                header('Location: productos_publicos.php');
            }
            exit;
        } elseif ($email === 'admin@techshop.com' && $password === 'admin123') {
            // Auto-reparar el hash del administrador
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmtUpdate->execute([$newHash, $user['id']]);

            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_rol'] = 'admin';
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Credenciales incorrectas o la cuenta no existe.";
        }
    } else {
        $error = "Credenciales incorrectas o la cuenta no existe.";
    }
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>

                <div class="text-center mt-3">
                    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                </div>
            </div>
        </div>

        <div class="mt-4 alert alert-info text-center">
            <strong>Demo:</strong><br>
            Email: admin@techshop.com<br>
            Password: admin123
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>