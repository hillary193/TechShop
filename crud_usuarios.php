<?php
// crud_usuarios.php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireSellerOrAdmin();

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type'])) {
        $nombre_usuario = sanitizeInput($_POST['nombre_usuario']);
        $email = sanitizeInput($_POST['email']);
        $nombre = sanitizeInput($_POST['nombre']);
        $apellidos = sanitizeInput($_POST['apellidos']);
        $telefono = sanitizeInput($_POST['telefono']);
        $direccion = sanitizeInput($_POST['direccion']);
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($_POST['action_type'] === 'create') {
            $password = $_POST['password'];
            if (empty($password)) {
                $error = "La contraseña es obligatoria para nuevos usuarios.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, password, nombre, apellidos, telefono, direccion, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$nombre_usuario, $email, $hashed, $nombre, $apellidos, $telefono, $direccion, $activo])) {
                        $success = "Usuario creado con éxito.";
                        $action = 'list';
                    }
                } catch (PDOException $e) {
                    $error = "Error: El email o nombre de usuario ya existe.";
                }
            }
        } elseif ($_POST['action_type'] === 'edit') {
            $id = intval($_POST['id']);
            $query = "UPDATE usuarios SET nombre_usuario=?, email=?, nombre=?, apellidos=?, telefono=?, direccion=?, activo=? WHERE id=?";
            $params = [$nombre_usuario, $email, $nombre, $apellidos, $telefono, $direccion, $activo, $id];

            if (!empty($_POST['password'])) {
                $query = "UPDATE usuarios SET nombre_usuario=?, email=?, password=?, nombre=?, apellidos=?, telefono=?, direccion=?, activo=? WHERE id=?";
                $params = [$nombre_usuario, $email, password_hash($_POST['password'], PASSWORD_DEFAULT), $nombre, $apellidos, $telefono, $direccion, $activo, $id];
            }

            try {
                $stmt = $pdo->prepare($query);
                if ($stmt->execute($params)) {
                    $success = "Usuario actualizado con éxito.";
                    $action = 'list';
                }
            } catch (PDOException $e) {
                $error = "Error: El email o nombre de usuario ya existe.";
            }
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Prevenir auto-borrado
    if ($id == $_SESSION['usuario_id']) {
        $error = "No puedes borrar tu propio usuario.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success = "Usuario borrado con éxito.";
        } else {
            $error = "Error al borrar usuario.";
        }
    }
    $action = 'list';
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Usuarios</h2>
    <?php if ($action === 'list'): ?>
        <a href="?action=create" class="btn btn-primary"><i class="bi bi-person-plus"></i> Nuevo Usuario</a>
    <?php else: ?>
        <a href="?action=list" class="btn btn-secondary">Volver al listado</a>
    <?php endif; ?>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <?php
    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
    $usuarios = $stmt->fetchAll();
    ?>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Nombre Completo</th>
                            <th>Registro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td>
                                    <?= $u['id'] ?>
                                </td>
                                <td><strong>
                                        <?= htmlspecialchars($u['nombre_usuario']) ?>
                                    </strong></td>
                                <td>
                                    <?= htmlspecialchars($u['email']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($u['fecha_registro'])) ?>
                                </td>
                                <td>
                                    <?php if ($u['activo'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?action=edit&id=<?= $u['id'] ?>" class="btn btn-outline-primary"
                                            title="Editar"><i class="bi bi-pencil"></i></a>
                                        <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                                            <a href="?delete=<?= $u['id'] ?>" class="btn btn-outline-danger" title="Borrar"
                                                onclick="return confirmarBorrado('usuario');"><i class="bi bi-trash"></i></a>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary" disabled
                                                title="No puedes borrarte a ti mismo"><i class="bi bi-trash"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($usuarios) === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No hay usuarios registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <?php
    $u = [
        'id' => '',
        'nombre_usuario' => '',
        'email' => '',
        'nombre' => '',
        'apellidos' => '',
        'telefono' => '',
        'direccion' => '',
        'activo' => 1
    ];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch();
        if ($data)
            $u = $data;
    }
    ?>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <?= $action === 'create' ? 'Crear Usuario' : 'Editar Usuario' ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="crud_usuarios.php" onsubmit="return validarRegistro()">
                <input type="hidden" name="action_type" value="<?= $action ?>">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">

                <h6 class="border-bottom pb-2 mb-3">Datos de Acceso</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="nombre_usuario" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario"
                            value="<?= htmlspecialchars($u['nombre_usuario']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($u['email']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="password" class="form-label">Contraseña
                            <?= $action === 'create' ? '*' : '' ?>
                        </label>
                        <input type="password" class="form-control" id="password" name="password" <?= $action === 'create' ? 'required' : '' ?>>
                        <?php if ($action === 'edit'): ?>
                            <div class="form-text">Dejar en blanco para mantener la actual.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3 mt-4">Datos Personales</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?= htmlspecialchars($u['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos"
                            value="<?= htmlspecialchars($u['apellidos']) ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono"
                            value="<?= htmlspecialchars($u['telefono']) ?>">
                    </div>
                    <div class="col-md-8">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion"
                            value="<?= htmlspecialchars($u['direccion']) ?>">
                    </div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo"
                        <?= $u['activo'] == 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activo">Usuario Activo</label>
                </div>

                <hr>
                <div class="text-end">
                    <a href="?action=list" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>