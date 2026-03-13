// js/carrito.js

document.addEventListener('DOMContentLoaded', () => {
    actualizarCarritoUI();
});
// Funciones para gestionar el carrito de compras usando sessionStorage
function agregarAlCarrito(id, nombre, precio, imagenUrl, stock) {
    let carrito = JSON.parse(sessionStorage.getItem('carrito')) || [];
    let index = carrito.findIndex(item => item.id == id);

    if (index !== -1) {
        if (carrito[index].cantidad + 1 > stock) {
            alert("No hay más unidades disponibles en stock");
            return;
        }
        carrito[index].cantidad++;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: parseFloat(precio),
            cantidad: 1,
            imagen: imagenUrl,
            stock: stock
        });
    }

    sessionStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarCarritoUI();

    alert(nombre + ' añadido al carrito.');
}
// Función para quitar un producto del carrito
function quitarDelCarrito(id) {
    let carrito = JSON.parse(sessionStorage.getItem('carrito')) || [];
    carrito = carrito.filter(item => item.id != id);
    sessionStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarCarritoUI();
}
// Función para cambiar la cantidad de un producto en el carrito
function cambiarCantidad(id, mod) {
    let carrito = JSON.parse(sessionStorage.getItem('carrito')) || [];
    let index = carrito.findIndex(item => item.id == id);

    if (index !== -1) {
        let nuevaCantidad = carrito[index].cantidad + mod;

        // Prevent going below 1
        if (nuevaCantidad <= 0) {
            carrito.splice(index, 1);
        } 
        // Prevent exceeding stock
        else if (nuevaCantidad > carrito[index].stock) {
            alert("No hay más unidades disponibles en stock");
        } 
        else {
            carrito[index].cantidad = nuevaCantidad;
        }
    }

    sessionStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarCarritoUI();
}
// Función para actualizar la interfaz del carrito
function actualizarCarritoUI() {
    let carrito = JSON.parse(sessionStorage.getItem('carrito')) || [];
    let count = 0;
    let total = 0;
    let html = '';

    carrito.forEach(item => {
        count += item.cantidad;
        let subtotal = item.precio * item.cantidad;
        total += subtotal;

        html += `
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                <div class="d-flex align-items-center">
                    <img src="${item.imagen || 'https://via.placeholder.com/50'}" alt="${item.nombre}" style="width: 50px; height: 50px; object-fit: contain;" class="me-2">
                    <div>
                        <h6 class="mb-0">${item.nombre}</h6>
                        <small class="text-muted">${item.precio.toFixed(2)} € x ${item.cantidad}</small>
                    </div>
                </div>
                <div>
                    <strong>${subtotal.toFixed(2)} €</strong>
                    <div class="btn-group btn-group-sm ms-2">
                        <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${item.id}, -1)">-</button>
                        <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${item.id}, 1)">+</button>
                        <button class="btn btn-outline-danger" onclick="quitarDelCarrito(${item.id})"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
    });

    if (carrito.length === 0) {
        html = '<p class="text-center text-muted mt-3">El carrito está vacío</p>';
        document.getElementById('btnCheckout').disabled = true;
    } else {
        document.getElementById('btnCheckout').disabled = false;
    }

    let cartCountElem = document.getElementById('cartCount');
    let cartContainerElem = document.getElementById('cartItemsContainer');
    let cartTotalElem = document.getElementById('cartTotal');

    if (cartCountElem) cartCountElem.innerText = count;
    if (cartContainerElem) cartContainerElem.innerHTML = html;
    if (cartTotalElem) cartTotalElem.innerText = total.toFixed(2);
}
// Función para realizar el pedido (enviar los datos al servidor)
function realizarPedido() {
    let carrito = JSON.parse(sessionStorage.getItem('carrito')) || [];
    if (carrito.length === 0) {
        alert("El carrito está vacío");
        return;
    }

    // Crear un form invisible para enviar los datos con POST
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = 'preparar_pedido.php';

    let inputDatos = document.createElement('input');
    inputDatos.type = 'hidden';
    inputDatos.name = 'carrito';
    inputDatos.value = JSON.stringify(carrito);

    form.appendChild(inputDatos);
    document.body.appendChild(form);
    form.submit();
}
