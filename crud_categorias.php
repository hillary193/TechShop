<?php
// crud_categorias.php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireSellerOrAdmin();

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type'])) {
        $nombre = sanitizeInput($_POST['nombre']);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $icono = sanitizeInput($_POST['icono']);
        $orden = intval($_POST['orden']);
        $activa = $_POST['activa'] ?? 'si';

        if ($_POST['action_type'] === 'create') {
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion, icono, orden, activa) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $descripcion, $icono, $orden, $activa])) {
                $success = "Categoría creada con éxito.";
                $action = 'list';
            } else {
                $error = "Error al crear categoría.";
            }
        } elseif ($_POST['action_type'] === 'edit') {
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE categorias SET nombre=?, descripcion=?, icono=?, orden=?, activa=? WHERE id=?");
            if ($stmt->execute([$nombre, $descripcion, $icono, $orden, $activa, $id])) {
                $success = "Categoría actualizada con éxito.";
                $action = 'list';
            } else {
                $error = "Error al actualizar categoría.";
            }
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success = "Categoría borrada con éxito.";
        } else {
            $error = "Error al borrar categoría.";
        }
    } catch (PDOException $e) {
        $error = "No puedes borrar esta categoría porque puede tener elementos asociados u otras restricciones.";
    }
    $action = 'list';
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Categorías</h2>
    <?php if ($action === 'list'): ?>
        <a href="?action=create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nueva Categoría</a>
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
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY orden ASC, id DESC");
    $categorias = $stmt->fetchAll();
    ?>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $c): ?>
                            <tr>
                                <td>
                                    <?= $c['id'] ?>
                                </td>
                                <td><i class="bi <?= htmlspecialchars($c['icono'] ?? 'bi-tag') ?>"></i></td>
                                <td><strong>
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </strong></td>
                                <td class="text-truncate" style="max-width: 200px;">
                                    <?= htmlspecialchars($c['descripcion']) ?>
                                </td>
                                <td><span class="badge bg-secondary">
                                        <?= $c['orden'] ?>
                                    </span></td>
                                <td>
                                    <?php if ($c['activa'] == 'si'): ?>
                                        <span class="badge bg-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?action=edit&id=<?= $c['id'] ?>" class="btn btn-outline-primary"
                                            title="Editar"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $c['id'] ?>" class="btn btn-outline-danger" title="Borrar"
                                            onclick="return confirmarBorrado('categoría');"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($categorias) === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No hay categorías registradas</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <?php
    $c = [
        'id' => '',
        'nombre' => '',
        'descripcion' => '',
        'icono' => 'bi-tag',
        'orden' => '0',
        'activa' => 'si'
    ];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch();
        if ($data)
            $c = $data;
    }
    ?>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <?= $action === 'create' ? 'Crear Categoría' : 'Editar Categoría' ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="crud_categorias.php" onsubmit="return validarCategoria()">
                <input type="hidden" name="action_type" value="<?= $action ?>">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?= htmlspecialchars($c['nombre']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="icono" class="form-label">Icono (Clase Bootstrap)</label>
                        <input type="text" class="form-control" id="icono" name="icono"
                            value="<?= htmlspecialchars($c['icono']) ?>" placeholder="bi-laptop">
                    </div>
                    <div class="col-md-3">
                        <label for="orden" class="form-label">Orden</label>
                        <input type="number" class="form-control" id="orden" name="orden" value="<?= $c['orden'] ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion"
                        rows="3"><?= htmlspecialchars($c['descripcion']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label d-block">Estado</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="activa" id="activasi" value="si"
                            <?= $c['activa'] == 'si' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activasi">Activa</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="activa" id="activano" value="no"
                            <?= $c['activa'] == 'no' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activano">Inactiva</label>
                    </div>
                </div>

                <hr>
                <div class="text-end">
                    <a href="?action=list" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>