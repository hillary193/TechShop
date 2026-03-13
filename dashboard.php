<?php
// dashboard.php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireSellerOrAdmin();

$user = getLoggedUser($pdo);

// Estadisticas rápidas
$stats = [];
$stats['usuarios'] = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$stats['productos'] = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$stats['pedidos'] = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$stats['categorias'] = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();

// Ultimos pedidos
$ultimosPedidos = $pdo->query("
    SELECT p.*, u.nombre_usuario 
    FROM pedidos p 
    JOIN usuarios u ON p.id_usuario = u.id 
    ORDER BY p.fecha_pedido DESC LIMIT 5
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-3">Dashboard</h2>
        <p class="lead">Bienvenido/a, <?= htmlspecialchars($user['nombre']) ?> <?= htmlspecialchars($user['apellidos']) ?></p>
        <hr>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-5">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase text-white-50">Usuarios</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['usuarios'] ?></h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                <a href="crud_usuarios.php" class="text-white text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase text-white-50">Productos</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['productos'] ?></h2>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                <a href="crud_productos.php" class="text-white text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase text-white-50">Pedidos</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['pedidos'] ?></h2>
                    </div>
                    <i class="bi bi-cart-check fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                <a href="crud_pedidos.php" class="text-white text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase text-white-50">Categorías</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['categorias'] ?></h2>
                    </div>
                    <i class="bi bi-tags fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                <a href="crud_categorias.php" class="text-white text-decoration-none">Ver todas <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimos pedidos listado -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Últimos Pedidos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($ultimosPedidos) > 0): ?>
                                <?php foreach($ultimosPedidos as $p): ?>
                                <tr>
                                    <td>#<?= $p['id'] ?></td>
                                    <td><?= htmlspecialchars($p['nombre_usuario']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_pedido'])) ?></td>
                                    <td><?= number_format($p['total'], 2) ?> €</td>
                                    <td>
                                        <span class="badge bg-<?= $p['estado'] == 'Entregado' ? 'success' : ($p['estado'] == 'Enviado' ? 'info' : 'warning') ?>">
                                            <?= $p['estado'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-3">No hay pedidos registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="crud_pedidos.php" class="text-decoration-none">Gestionar Pedidos</a>
            </div>
        </div>
    </div>

    <!-- Novedades Externas (The Fake Store API) -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-globe"></i> Novedades Externas (Fake Store API)</h5>
            </div>
            <div class="card-body">
                <div id="externalApiWidget" class="row gx-2">
                    <!-- Aquí se cargan 3 productos de The Fake Store API vía JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Cargar 3 productos para el widget
    document.addEventListener('DOMContentLoaded', () => {
        fetch('https://fakestoreapi.com/products?limit=3')
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(p => {
                    html += `
                    <div class="col-4 text-center">
                        <img src="${p.image}" class="img-fluid mb-2" style="height:70px; width:100%; object-fit:contain;" alt="${p.title}">
                        <div class="small fw-bold text-truncate" title="${p.title}">${p.title}</div>
                        <div class="small text-primary">${p.price} $</div>
                    </div>
                    `;
                });
                document.getElementById('externalApiWidget').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('externalApiWidget').innerHTML = `<p class="text-danger small">Error cargando API: ${err.message}</p>`;
            });
    });
</script>

<?php require_once 'includes/footer.php'; ?>
