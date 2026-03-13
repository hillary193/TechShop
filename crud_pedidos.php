<?php
// crud_pedidos.php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireSellerOrAdmin();

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type']) && $_POST['action_type'] === 'edit') {
        $id = intval($_POST['id']);
        $estado = $_POST['estado'];
        $notas = sanitizeInput($_POST['notas']);

        $stmt = $pdo->prepare("UPDATE pedidos SET estado=?, notas=? WHERE id=?");
        if ($stmt->execute([$estado, $notas, $id])) {
            $success = "Pedido actualizado con éxito.";
            $action = 'list';
        } else {
            $error = "Error al actualizar pedido.";
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "Pedido borrado con éxito.";
    } else {
        $error = "Error al borrar pedido.";
    }
    $action = 'list';
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Pedidos</h2>
    <?php if ($action !== 'list'): ?>
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
    $stmt = $pdo->query("
        SELECT p.*, u.nombre_usuario, u.email 
        FROM pedidos p 
        JOIN usuarios u ON p.id_usuario = u.id 
        ORDER BY p.id DESC
    ");
    $pedidos = $stmt->fetchAll();
    ?>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $p): ?>
                            <tr>
                                <td><strong>#
                                        <?= $p['id'] ?>
                                    </strong></td>
                                <td>
                                    <?= htmlspecialchars($p['nombre_usuario']) ?>
                                    <div class="small text-muted">
                                        <?= htmlspecialchars($p['email']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?>
                                </td>
                                <td><span class="badge bg-secondary">
                                        <?= $p['num_items'] ?>
                                    </span></td>
                                <td><strong>
                                        <?= number_format($p['total'], 2) ?> €
                                    </strong></td>
                                <td>
                                    <?php
                                    $bg = 'bg-warning';
                                    if ($p['estado'] == 'Enviado')
                                        $bg = 'bg-info';
                                    if ($p['estado'] == 'Entregado')
                                        $bg = 'bg-success';
                                    ?>
                                    <span class="badge <?= $bg ?>">
                                        <?= $p['estado'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline-primary"
                                            title="Editar/Ver Detalles"><i class="bi bi-eye"></i></a>
                                        <a href="?delete=<?= $p['id'] ?>" class="btn btn-outline-danger" title="Borrar"
                                            onclick="return confirmarBorrado('pedido');"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($pedidos) === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No hay pedidos registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'edit' && isset($_GET['id'])): ?>
    <?php
    $stmt = $pdo->prepare("
        SELECT p.*, u.nombre, u.apellidos, u.email, u.telefono 
        FROM pedidos p 
        JOIN usuarios u ON p.id_usuario = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $p = $stmt->fetch();

    if (!$p) {
        echo "<div class='alert alert-danger'>Pedido no encontrado.</div>";
        require_once 'includes/footer.php';
        exit;
    }
    ?>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pedido #
                        <?= $p['id'] ?>
                    </h5>
                    <span class="text-muted">
                        <?= date('d/m/Y H:i:s', strtotime($p['fecha_pedido'])) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-2">Datos del Cliente</h6>
                            <p class="mb-1"><strong>
                                    <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?>
                                </strong></p>
                            <p class="mb-1"><i class="bi bi-envelope"></i>
                                <?= htmlspecialchars($p['email']) ?>
                            </p>
                            <p class="mb-0"><i class="bi bi-telephone"></i>
                                <?= htmlspecialchars($p['telefono'] ?? 'No especificado') ?>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-2">Información de Envío</h6>
                            <p class="mb-1 border p-2 bg-light rounded info-box">
                                <?= nl2br(htmlspecialchars($p['direccion_envio'])) ?>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-4 text-center border-end">
                            <h6 class="text-muted">Artículos</h6>
                            <h3>
                                <?= $p['num_items'] ?>
                            </h3>
                        </div>
                        <div class="col-sm-8 text-center text-primary">
                            <h6 class="text-muted">Total del Pedido</h6>
                            <h2 class="mb-0">
                                <?= number_format($p['total'], 2) ?> €
                            </h2>
                        </div>
                    </div>

                    <form method="POST" action="crud_pedidos.php" class="bg-light p-3 border mt-4 rounded">
                        <h6 class="border-bottom pb-2 mb-3">Actualizar Estado y Notas</h6>
                        <input type="hidden" name="action_type" value="edit">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">

                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="estado" class="form-label mb-0 fw-bold">Estado del Pedido:</label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-select" id="estado" name="estado">
                                    <option value="Pendiente" <?= $p['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente de
                                        Envío</option>
                                    <option value="Enviado" <?= $p['estado'] == 'Enviado' ? 'selected' : '' ?>>Enviado</option>
                                    <option value="Entregado" <?= $p['estado'] == 'Entregado' ? 'selected' : '' ?>>Entregado al
                                        Cliente</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notas" class="form-label fw-bold">Notas de Administración (No visibles para el
                                cliente)</label>
                            <textarea class="form-control" id="notas" name="notas" rows="3"
                                placeholder="Añade notas internas aquí..."><?= htmlspecialchars($p['notas'] ?? '') ?></textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>