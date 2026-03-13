// js/validaciones.js

function validarRegistro() {
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let nombreUsuario = document.getElementById('nombre_usuario').value;

    if (nombreUsuario.trim() === '') {
        alert("El nombre de usuario no puede estar vacío");
        return false;
    }
    
    if (email.trim() === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("El email no es válido");
        return false;
    }

    if (password.trim() === '') {
        alert("La contraseña no puede estar vacía");
        return false;
    }

    return true;
}

function validarProducto() {
    let nombre = document.getElementById('nombre').value.trim();
    let precio = parseFloat(document.getElementById('precio').value);
    let stock = parseInt(document.getElementById('stock').value);
    let url = document.getElementById('imagen_url').value.trim();

    // Nombre obligatorio
    if (nombre === '') {
        alert("El nombre del producto es obligatorio.");
        return false;
    }

    // Precio > 0
    if (isNaN(precio) || precio <= 0) {
        alert("El precio debe ser un número mayor a 0.");
        return false;
    }

    // Stock >= 0
    if (isNaN(stock) || stock < 0) {
        alert("El stock no puede ser negativo.");
        return false;
    }

    // Validar URL o path local
   if (url !== '') {
        // Normalize backslashes to forward slashes
        url = url.replace(/\\/g, '/');

        // Allow local paths like images/... or uploads/...
        if (!url.startsWith("images/") && !url.startsWith("uploads/")) {
            try {
                new URL(url); // check if it's a valid full URL
            } catch {
                alert("La URL de la imagen no es válida.");
                return false;
            }
        }
    }

    return true;
}

function validarCategoria() {
    let nombre = document.getElementById('nombre').value;
    if (nombre.trim() === '') {
        alert("El nombre de categoría es obligatorio.");
        return false;
    }
    return true;
}

function confirmarBorrado(elemento) {
    return confirm("¿Estás seguro de que deseas borrar este " + elemento + "? Esta acción no se puede deshacer.");
}
