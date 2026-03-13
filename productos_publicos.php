<?php
// productos_publicos.php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Filtro por categoría o búsqueda
$where = "WHERE 1=1";
$params = [];

if (isset($_GET['categoria']) && $_GET['categoria'] !== '') {
    $where .= " AND categoria = ?";
    $params[] = $_GET['categoria'];
}

if (isset($_GET['q']) && $_GET['q'] !== '') {
    $where .= " AND nombre LIKE ?";
    $params[] = '%' . $_GET['q'] . '%';
}

// Obtener productos filtrados
$stmt = $pdo->prepare("SELECT * FROM productos $where ORDER BY id DESC");
$stmt->execute($params);
$productos = $stmt->fetchAll();

// Obtener todas las categorías activas dinámicamente
$stmt_categorias = $pdo->query("SELECT nombre FROM categorias WHERE activa = 'si' ORDER BY orden ASC");
$categorias_list = $stmt_categorias->fetchAll();

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-3">Catálogo de Productos</h2>
        <form class="row g-3" method="GET" action="productos_publicos.php">
            <div class="col-md-4">
                <input type="text" class="form-control" name="q" placeholder="Buscar producto..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="categoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias_list as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['nombre']) ?>" <?= (isset($_GET['categoria']) && $_GET['categoria'] == $cat['nombre']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($productos as $p): ?>
        <div class="col">
            <div class="card h-100 card-product shadow-sm">
                <?php if ($p['destacado'] == 'si'): ?>
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Destacado</span>
                <?php endif; ?>

                <img src="<?= htmlspecialchars($p['imagen_url'] ? $p['imagen_url'] : 'https://via.placeholder.com/300x200') ?>"
                    class="card-img-top product-img" alt="<?= htmlspecialchars($p['nombre']) ?>">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-truncate" title="<?= htmlspecialchars($p['nombre']) ?>">
                        <?= htmlspecialchars($p['nombre']) ?>
                    </h5>
                    <h6 class="text-primary mb-3">
                        <?= number_format($p['precio'], 2) ?> €
                    </h6>
                    <p class="card-text text-muted small flex-grow-1">
                        <?= htmlspecialchars(substr($p['descripcion'], 0, 60)) ?>...
                    </p>

                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <?php if ($p['stock'] > 0): ?>
                            <span class="badge bg-success">En Stock: <?= $p['stock'] ?></span>
                            <button class="btn btn-outline-primary btn-sm"
                               onclick="agregarAlCarrito(
                                                <?= $p['id'] ?>,
                                                '<?= htmlspecialchars(addslashes($p['nombre'])) ?>',
                                                <?= $p['precio'] ?>,
                                                '<?= htmlspecialchars(addslashes($p['imagen_url'])) ?>',
                                                <?= $p['stock'] ?>
                                                )">
                                Añadir <i class="bi bi-cart-plus"></i>
                            </button>
                        <?php else: ?>
                            <span class="badge bg-danger">Agotado</span>
                            <button class="btn btn-outline-secondary btn-sm" disabled>Añadir <i class="bi bi-cart-plus"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (count($productos) === 0): ?>
        <div class="col-12 text-center text-muted my-5">
            <h4>No se encontraron productos</h4>
            <p>Intenta con otra búsqueda o categoría</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>