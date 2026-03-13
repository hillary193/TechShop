<?php
// perfil.php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

$user = getLoggedUser($pdo);

// Verificar que el usuario existe en la BD si no redirigir al index (caso raro pero por seguridad)
if (!$user) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizeInput($_POST['nombre']);
    $apellidos = sanitizeInput($_POST['apellidos']);
    $telefono = sanitizeInput($_POST['telefono']);
    $direccion = sanitizeInput($_POST['direccion']);

    // Actualizar datos básicos en la base de datos
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, telefono = ?, direccion = ? WHERE id = ?");
    if ($stmt->execute([$nombre, $apellidos, $telefono, $direccion, $user['id']])) {
        $success = "Perfil actualizado correctamente.";
        // Refrescar usuario
        $user = getLoggedUser($pdo);
    } else {
        $error = "Error al actualizar perfil.";
    }

    // Cambio de contraseña
    if (!empty($_POST['new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        if (password_verify($current_password, $user['password'])) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user['id']]);
            $success = "Perfil y contraseña actualizados correctamente.";
        } else {
            $error = "La contraseña actual es incorrecta.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Mi Perfil</h4>
            </div>
            <div class="card-body">
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

                <form method="POST" action="perfil.php">
                    <h5 class="border-bottom pb-2">Datos Personales</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre de Usuario (no editable)</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($user['nombre_usuario']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email (no editable)</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"
                                disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?= htmlspecialchars($user['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos"
                                value="<?= htmlspecialchars($user['apellidos']) ?>" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono"
                                value="<?= htmlspecialchars($user['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion"
                                value="<?= htmlspecialchars($user['direccion'] ?? '') ?>">
                        </div>
                    </div>

                    <h5 class="border-bottom pb-2">Cambiar Contraseña</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                            <div class="form-text">Solo necesario si deseas cambiarla.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

 <!-- Historial de Pedidos -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <h4 class="mb-0">Mis Pedidos</h4>
    </div>
    <div class="card-body">
        <?php
        // Obtener todos los pedidos del usuario
        $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha_pedido DESC");
        $stmt->execute([$user['id']]);
        $pedidos = $stmt->fetchAll();

        if (count($pedidos) > 0):
        ?>
        <div class="accordion" id="pedidosAccordion">
            <?php foreach ($pedidos as $pedido): ?>
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading<?= $pedido['id'] ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $pedido['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $pedido['id'] ?>">
                            Pedido #<?= $pedido['id'] ?> - <?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?> - <?= number_format($pedido['total'], 2) ?> €
                            <span class="badge ms-3 <?= $pedido['estado']=='Enviado' ? 'bg-info' : ($pedido['estado']=='Entregado' ? 'bg-success' : 'bg-warning') ?>">
                                <?= $pedido['estado'] ?>
                            </span>
                        </button>
                    </h2>
                    <div id="collapse<?= $pedido['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $pedido['id'] ?>" data-bs-parent="#pedidosAccordion">
                        <div class="accordion-body">
                            <?php
                            // Obtener los detalles del pedido
                            $stmt_det = $pdo->prepare("
                                SELECT pd.*, p.nombre, p.imagen_url 
                                FROM pedido_detalles pd 
                                JOIN productos p ON pd.producto_id = p.id 
                                WHERE pd.pedido_id = ?
                            ");
                            $stmt_det->execute([$pedido['id']]);
                            $detalles = $stmt_det->fetchAll();
                            ?>

                            <?php if(count($detalles) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Imagen</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($detalles as $d): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($d['nombre']) ?></td>
                                                    <td>
                                                        <img src="<?= htmlspecialchars($d['imagen_url'] ? $d['imagen_url'] : 'https://via.placeholder.com/50') ?>" style="width:50px;height:50px;object-fit:contain;">
                                                    </td>
                                                    <td><?= $d['cantidad'] ?></td>
                                                    <td><?= number_format($d['precio_unitario'], 2) ?> €</td>
                                                    <td><?= number_format($d['cantidad'] * $d['precio_unitario'], 2) ?> €</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <p><strong>Dirección de envío:</strong> <?= htmlspecialchars($pedido['direccion_envio']) ?></p>
                                <?php if(!empty($pedido['notas'])): ?>
                                    <p><strong>Notas:</strong> <?= htmlspecialchars($pedido['notas']) ?></p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-muted">No hay productos en este pedido.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p class="text-muted mb-0">No has realizado ningún pedido todavía.</p>
        <?php endif; ?>
    </div>
</div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>