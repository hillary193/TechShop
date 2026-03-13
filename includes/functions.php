<?php
// includes/functions.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLogged()
{
    return isset($_SESSION['usuario_id']);
}

function requireLogin()
{
    if (!isLogged()) {
        header('Location: index.php');
        exit;
    }
}

function isSellerOrAdmin()
{
    return isLogged() && isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['admin', 'vendedor']);
}

function requireSellerOrAdmin()
{
    requireLogin();
    if (!isSellerOrAdmin()) {
        header('Location: productos_publicos.php');
        exit;
    }
}

//si el usuario es admin o vendedor, devuelve su info, sino false
function getLoggedUser($pdo)
{
    if (isLogged()) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $user = $stmt->fetch();
        return $user ? $user : false;
    }
    return false;
}

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>