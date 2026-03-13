<?php
/**
 * crud_productos.php
 * 
 * Gestión completa de productos: crear, leer, actualizar y eliminar (CRUD)
 * Incluye manejo de subida de imágenes locales
 * Acceso restringido a vendedores y administradores
 */
require_once 'config/db.php';
require_once 'includes/functions.php';
requireSellerOrAdmin(); // Verifica que el usuario sea vendedor o admin

$action = $_GET['action'] ?? 'list'; // Acción actual: 'list', 'create', 'edit'
$error = ''; // Almacena mensajes de error
$success = ''; // Almacena mensajes de éxito

// Procesar solicitudes POST (crear o actualizar productos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type'])) {
        // Sanitizar y validar todos los datos del formulario
        $nombre = sanitizeInput($_POST['nombre']);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $precio = floatval($_POST['precio']); // Convertir a número decimal
        $stock = intval($_POST['stock']); // Convertir a entero
        $categoria = sanitizeInput($_POST['categoria']);
        $imagen_url = sanitizeInput($_POST['imagen_url']);

        // Procesar subida de imagen local si el usuario proporcionó un archivo
        if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
            // Crear directorio de uploads si no existe
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Generar nombre único para el archivo con timestamp
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['imagen_archivo']['name']));
            $targetPath = $uploadDir . $fileName;
            // Mover archivo subido a la carpeta de destino
            if (move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], $targetPath)) {
                $imagen_url = 'uploads/' . $fileName; // Usar ruta relativa en BD
            }
        }

        $destacado = $_POST['destacado'] ?? 'no'; // Producto destacado por defecto es 'no'
        $proveedor = sanitizeInput($_POST['proveedor']);

        // ===== CREAR nuevo producto =====
        if ($_POST['action_type'] === 'create') {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen_url, destacado, proveedor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $imagen_url, $destacado, $proveedor])) {
                $success = "Producto creado con éxito.";
                $action = 'list'; // Volver a la lista después de crear
            } else {
                $error = "Error al crear producto.";
            }
        } 
        // ===== EDITAR producto existente =====
       
elseif ($_POST['action_type'] === 'edit') {
    $id = intval($_POST['id']);

    // Obtener imagen actual del producto
    $stmt = $pdo->prepare("SELECT imagen_url FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto_actual = $stmt->fetch();
    $imagen_final = $producto_actual['imagen_url']; // mantener la imagen actual por defecto

    // Si el usuario escribió una nueva URL, reemplazar la imagen
    if (!empty($_POST['imagen_url'])) {
        $imagen_final = sanitizeInput($_POST['imagen_url']);
    }

    // Si el usuario subió un archivo nuevo, reemplazar la imagen
    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['imagen_archivo']['name']));
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], $targetPath)) {
            $imagen_final = 'uploads/' . $fileName;
        }
    }

    // Actualizar el producto manteniendo la imagen si no cambió
    $stmt = $pdo->prepare("UPDATE productos 
        SET nombre=?, descripcion=?, precio=?, stock=?, categoria=?, imagen_url=?, destacado=?, proveedor=? 
        WHERE id=?");

    if ($stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $imagen_final, $destacado, $proveedor, $id])) {
        $success = "Producto actualizado con éxito.";
        $action = 'list';
    } else {
        $error = "Error al actualizar producto.";
    }
}
    }
} 
// ===== ELIMINAR producto =====
elseif (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // ID del producto a eliminar
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "Producto borrado con éxito.";
    } else {
        $error = "Error al borrar producto.";
    }
    $action = 'list'; // Volver a la lista después de eliminar
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Productos</h2>
    <?php if ($action === 'list'): ?>
        <a href="?action=create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Producto</a>
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
    // Obtener todos los productos ordenados por más recientes primero
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll();
    ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Info</th>
                            <th>Categoria</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Destacado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td>
                                    <?= $p['id'] ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($p['imagen_url'] ? $p['imagen_url'] : 'https://via.placeholder.com/40') ?>"
                                            style="width: 40px; height: 40px; object-fit: contain" class="me-2 border bg-white">
                                        <div>
                                            <strong>
                                                <?= htmlspecialchars($p['nombre']) ?>
                                            </strong>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($p['proveedor']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary">
                                        <?= htmlspecialchars($p['categoria']) ?>
                                    </span></td>
                                <td>
                                    <?= number_format($p['precio'], 2) ?> €
                                </td>
                                <td>
                                    <span
                                        class="badge <?= $p['stock'] > 10 ? 'bg-success' : ($p['stock'] > 0 ? 'bg-warning' : 'bg-danger') ?>">
                                        <?= $p['stock'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $p['destacado'] == 'si' ? '<i class="bi bi-star-fill text-warning"></i>' : '-' ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline-primary"
                                            title="Editar"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $p['id'] ?>" class="btn btn-outline-danger" title="Borrar"
                                            onclick="return confirmarBorrado('producto');"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($productos) === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No hay productos registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <?php
    // Obtener todas las categorías activas ordenadas por orden de visualización
    $stmt_categorias = $pdo->query("SELECT id, nombre FROM categorias WHERE activa = 'si' ORDER BY orden ASC");
    $categorias_list = $stmt_categorias->fetchAll();
    
    // Valores por defecto para un nuevo producto
    $p = [
        'id' => '',
        'nombre' => '',
        'descripcion' => '',
        'precio' => '',
        'stock' => '',
        'categoria' => 'Electronica',
        'imagen_url' => '',
        'destacado' => 'no',
        'proveedor' => ''
    ];
    
    // Si es EDICIÓN, cargar datos del producto de la BD
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch();
        if ($data)
            $p = $data; // Sobrescribir valores por defecto con datos del producto
    }
    ?>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <?= $action === 'create' ? 'Crear Producto' : 'Editar Producto' ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="crud_productos.php" enctype="multipart/form-data"
                onsubmit="return validarProducto()">
                <input type="hidden" name="action_type" value="<?= $action ?>">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre del Producto *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?= htmlspecialchars($p['nombre']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="proveedor" class="form-label">Proveedor</label>
                        <input type="text" class="form-control" id="proveedor" name="proveedor"
                            value="<?= htmlspecialchars($p['proveedor']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion"
                        rows="3"><?= htmlspecialchars($p['descripcion']) ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="precio" class="form-label">Precio (€) *</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio"
                            value="<?= $p['precio'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stock *</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="<?= $p['stock'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="categoria" class="form-label">Categoría *</label>
                        <select class="form-select" id="categoria" name="categoria">
                            <option value="">-- Selecciona una categoría --</option>
                            <?php foreach ($categorias_list as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['nombre']) ?>" <?= $p['categoria'] == $cat['nombre'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label class="form-label">Imagen del Producto (URL o Archivo Local)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text">URL</span>
                            <input type="text" class="form-control" id="imagen_url" name="imagen_url"
    value="<?= htmlspecialchars($p['imagen_url']) ?>"
    placeholder="images/producto.jpg o https://ejemplo.com/imagen.jpg">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">Subir</span>
                            <input type="file" class="form-control" id="imagen_archivo" name="imagen_archivo"
                                accept="image/*">
                        </div>
                        <?php if ($p['imagen_url']): ?>
                            <small class="text-muted d-block mt-1">
                                Actual: <a href="<?= htmlspecialchars($p['imagen_url']) ?>" target="_blank"
                                    class="text-break"><?= htmlspecialchars($p['imagen_url']) ?></a>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input" type="radio" name="destacado" id="destacadono" value="no"
                                <?= $p['destacado'] == 'no' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="destacadono">Normal</label>
                        </div>
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input" type="radio" name="destacado" id="destacadosi" value="si"
                                <?= $p['destacado'] == 'si' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="destacadosi">Destacado</label>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="text-end">
                    <a href="?action=list" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>