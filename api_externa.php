<?php
// api_externa.php
require_once 'config/db.php';
require_once 'includes/functions.php';

require_once 'includes/header.php';
?>

<div class="container mt-4">
    <div class="py-3 mb-4 border-bottom">
        <h1><i class="bi bi-cloud-arrow-down-fill text-primary me-2"></i> Catálogo Internacional (API)</h1>
        <p class="text-muted">Productos obtenidos en tiempo real desde <strong>Fake Store API</strong> mediante Fetch
            Asíncrono en JavaScript.</p>
    </div>
</div>

<!-- Loader -->
<div id="api-loader" class="text-center py-5">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    <p class="mt-3 text-muted">Obteniendo productos remotos...</p>
</div>

<!-- Contenedor -->
<div class="row" id="productos-api">
    <!-- llenado con JS -->
</div>

<script>
    async function cargarProductos() {
        const contenedor = document.getElementById('productos-api');
        const loader = document.getElementById('api-loader');

        try {
            const res = await fetch('https://fakestoreapi.com/products');

            if (!res.ok) throw new Error('Error al conectar con la API');

            const data = await res.json();

            loader.style.display = 'none';

            if (data.length === 0) {
                contenedor.innerHTML = '<div class="alert alert-warning w-100">No se encontraron resultados de la API.</div>';
                return;
            }

            let html = '';
            data.forEach(product => {
                html += `
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 card-product shadow-sm">
                        <img src="${product.image}" class="card-img-top product-img" alt="${product.title}" style="object-fit:contain; height:200px; padding:1rem;">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-secondary mb-2 align-self-start">${product.category}</span>
                            <h5 class="card-title small fw-bold" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="${product.title}">${product.title}</h5>
                            <p class="card-text flex-grow-1 small text-muted" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                                ${product.description}
                            </p>
                            <h5 class="text-primary mt-2">${product.price} $</h5>
                            <button class="btn btn-outline-primary btn-sm mt-2 w-100 disabled">Consumido via Fake Store API</button>
                        </div>
                    </div>
                </div>
                `;
            });

            contenedor.innerHTML = html;

        } catch (error) {
            loader.style.display = 'none';
            contenedor.innerHTML = `<div class="alert alert-danger w-100">Error al cargar la API: ${error.message}</div>`;
        }
    }

    cargarProductos();
</script>

<?php require_once 'includes/footer.php'; ?>