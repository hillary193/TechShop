<?php
// includes/header.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
$loggedUser = getLoggedUser($pdo);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <!-- JS validations -->
    <script src="js/validaciones.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark nav-petshop shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-heart-fill text-danger me-1"></i> TechShop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isLogged()): ?>
                        <?php if (isSellerOrAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="productos_publicos.php">Tienda</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="api_externa.php">Novedades (API)</a>
                            </li>
                            <!-- CRUDs -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    Gestión
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="crud_productos.php">Productos</a></li>
                                    <li><a class="dropdown-item" href="crud_categorias.php">Categorías</a></li>
                                    <li><a class="dropdown-item" href="crud_usuarios.php">Usuarios</a></li>
                                    <li><a class="dropdown-item" href="crud_pedidos.php">Pedidos</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <!-- Para Clientes -->
                            <li class="nav-item">
                                <a class="nav-link" href="productos_publicos.php">Tienda</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="api_externa.php">Sorpresas</a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="productos_publicos.php">Tienda Publica</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLogged()): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative me-3" href="#" data-bs-toggle="modal"
                                data-bs-target="#cartModal">
                                <i class="bi bi-cart3 fs-5"></i>
                                <span
                                    class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger"
                                    id="cartCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?= htmlspecialchars($loggedUser['nombre_usuario']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registro.php">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal Carrito -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mi Carrito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="cartItemsContainer"></div>
                    <div class="text-end mt-3 fw-bold fs-5">
                        Total: <span id="cartTotal">0.00</span> €
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir Comprando</button>
                    <button type="button" class="btn btn-success" id="btnCheckout" onclick="realizarPedido()">Realizar
                        Pedido</button>
                </div>
            </div>
        </div>
    </div>

    <main class="container my-4">