<?php
// preparar_pedido.php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrito'])) {

    $carrito = json_decode($_POST['carrito'], true);

    if (empty($carrito)) {
        header('Location: productos_publicos.php');
        exit;
    }

    $user = getLoggedUser($pdo);
    $total = 0;
    $numItems = 0;

    foreach ($carrito as $item) {
        $total += $item['precio'] * $item['cantidad'];
        $numItems += $item['cantidad'];
    }

    $direccionEnvio = $user['direccion'] ? $user['direccion'] : 'Dirección no especificada';

    try {

        $pdo->beginTransaction();

        // 1️⃣ Crear pedido
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (id_usuario, total, direccion_envio, num_items)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $user['id'],
            $total,
            $direccionEnvio,
            $numItems
        ]);

        $pedidoId = $pdo->lastInsertId();

        // 2️⃣ Preparar inserción en pedido_detalles
        $stmtDetalle = $pdo->prepare("
            INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario)
            VALUES (?, ?, ?, ?)
        ");

        // 3️⃣ Actualizar stock
        $stmtUpdate = $pdo->prepare("
            UPDATE productos SET stock = stock - ? WHERE id = ?
        ");

        foreach ($carrito as $item) {

    // 🔎 Obtener stock actual del producto
    $stmtStock = $pdo->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
    $stmtStock->execute([$item['id']]);
    $producto = $stmtStock->fetch();

    if (!$producto) {
        throw new Exception("Producto no encontrado.");
    }

    // ❌ Validar stock disponible
    if ($item['cantidad'] > $producto['stock']) {
        throw new Exception("No hay suficiente stock para: " . $producto['nombre']);
    }

    // Guardar producto en pedido_detalles
    $stmtDetalle->execute([
        $pedidoId,
        $item['id'],
        $item['cantidad'],
        $item['precio']
    ]);

    // Descontar stock
    $stmtUpdate->execute([
        $item['cantidad'],
        $item['id']
    ]);

}

        $pdo->commit();

        $success = "Pedido #$pedidoId realizado con éxito por un total de " . number_format($total, 2) . " €.";

    } catch (Exception $e) {

        $pdo->rollBack();
        $error = "Fallo al procesar el pedido: " . $e->getMessage();

    }

} else {

    header('Location: productos_publicos.php');
    exit;

}

require_once 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 text-center">

        <?php if (isset($success)): ?>

            <div class="alert alert-success">

                <i class="bi bi-check-circle-fill fs-1 d-block mb-3"></i>

                <h4 class="alert-heading">¡Gracias por tu compra!</h4>

                <p><?= $success ?></p>

                <hr>

                <div class="d-flex justify-content-center gap-3">
                    <a href="perfil.php" class="btn btn-outline-success">Ver mis pedidos</a>
                    <a href="productos_publicos.php" class="btn btn-success">Volver a la tienda</a>
                </div>

                <script>
                    sessionStorage.removeItem('carrito');
                </script>

            </div>

        <?php else: ?>

            <div class="alert alert-danger">

                <i class="bi bi-x-circle-fill fs-1 d-block mb-3"></i>

                <h4 class="alert-heading">Error en el pedido</h4>

                <p>
                    <?= isset($error) ? htmlspecialchars($error) : 'Ha ocurrido un error inesperado al procesar el pedido.' ?>
                </p>

                <a href="productos_publicos.php" class="btn btn-primary mt-3">Volver a intentar</a>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
