# Documentación Techshop (Simulación PDF 10 Páginas)

---
*Página 1: Portada*
# PROYECTO FINAL DAM2V
### Desarrollo de una Tienda Online Completa (SGE)
**Alumno:** [Tu Nombre]
**Aplicación:** Techshop
**Curso:** 2025/2026

---
*Página 2: Índice*
# Índice
1. Introducción
2. Requisitos y Objetivos
3. Entorno de Desarrollo
4. Diseño de Base de Datos
5. Estructura de la Aplicación
6. Desarrollo del Backend
7. Desarrollo del Frontend y Validaciones
8. Integración API Externa
9. Pruebas y Manual de Usuario
10. Conclusión y Posibles Mejoras

---
*Página 3: 1. Introducción*
## 1. Introducción
El proyecto **Techshop** surge de la necesidad de aplicar los conocimientos adquiridos durante el módulo de Sistemas de Gestión Empresarial en DAM. Se trata de un e-commerce integral desarrollado con tecnologías web tradicionales (PHP, MySQL, HTML, CSS, JavaScript) orientado a la venta de artículos.

La web permite a usuarios realizar procesos de compra, registrarse, administrar su perfil, mientras el administrador dispone de un Dashboard y operaciones CRUD para cada entidad del sistema, garantizando la escalabilidad.

---
*Página 4: 2. Requisitos y Objetivos*
## 2. Requisitos Cumplidos
1. **Gestión de Usuarios (20%):** Registro, Login, Perfil seguro utilizando `password_hash()` de PHP, y sesiones en el servidor.
2. **Base de Datos (25%):** 4 tablas conectadas por FKs con más de 8 campos cada una.
3. **CRUD Completo:** Listar, Crear, Editar, y Borrar con confirmaciones JS en cada entidad (Usuarios, Productos, Categorías, Pedidos). Incluye sistema de subida de imágenes para productos locales.
4. **Funcionalidad Tienda:** Dashboard métricas, catálogo público, carrito mediante `sessionStorage`.
5. **API Externa (10%):** Conexión con *The fake store API* utilizando Fetch API en JS Vanilla (actuando como catálogo anexo de venta cruzada).
6. **UI/UX (10%):** Bootstrap 5 implementado consistentemente, temática visual adaptada a tono cálido.
7. **Código Limpio:** Nomenclatura camelCase, buenas prácticas de PDO.

---
*Página 5: 3. Entorno y 4. Diseño de BD*
## 3. Entorno de Desarrollo
- Editor: VS Code
- Lenguaje Server: PHP 8.2
- SGDB: MySQL (vía XAMPP)

## 4. Diseño de la Base de Datos (Modelo Lógico)
- **usuarios:** id, nombre_usuario, email, password, nombre, apellidos, telefono, direccion, fecha_registro, activo.
- **productos:** id, nombre, descripcion, precio, stock, categoria, imagen_url, fecha_alta, destacado, proveedor.
- **categorias:** id, nombre, descripcion, icono, orden, activa, fecha_creacion, padre_id.
- **pedidos:** id, id_usuario, fecha_pedido, total, estado, direccion_envio, notas, num_items, impuesto, descuento.

---
*Página 6: 5. Estructura y 6. Backend*
## 5. Estructura de la Aplicación y Roles
La aplicación divide a sus usuarios mediante un campo `rol` en la base de datos (`cliente`, `vendedor`, `admin`).
- **Clientes**: Tienen acceso exclusivo a la tienda y su carrito. No ven opciones de administración.
- **Vendedores / Administradores**: Poseen una cabecera extendida con enlaces al **Dashboard**, métricas, y al área de **Gestión** (CRUDs). Si un cliente intenta acceder a estas urls, el núcleo (*requireSellerOrAdmin()*) lo expulsará.
El núcleo utiliza `includes/` para reutilizar elementos como cabeceras y funciones de verificación.

## 6. Desarrollo del Backend
El backend basa su seguridad en Prepared Statements a través de la interfaz **PDO**. 
Se protege contra XSS usando `htmlspecialchars()` y contra inyecciones SQL en los CRUDs. Las operaciones de checkout utilizan Transacciones SQL simples para asegurar consistencia (p.e., descontar stock en paralelo). Se soporta sistema `move_uploaded_file()` de PHP directo al sistema de archivos local para imágenes personalizadas.

---
*Página 7: 7. Frontend y Validaciones*
## 7. Frontend y Validaciones
Se ha utilizado el framework de CSS **Bootstrap 5** desde el CDN para ofrecer de inmediato una Interfaz Gráfica (UI) Responsive que se adapta a dispositivos móviles. Se han modificado las variables globales CSS para proveer un tema de color cálido apropiado para mascotas.

**Validaciones en lado cliente:**
- No se usa `required` de HTML5 de forma exclusiva. 
- Scripts de validación en `js/validaciones.js` comprueban envíos de formulario con Vanilla JS (comprobar strings vacíos, Regex de email, números en stock > 0, etc.) emitiendo `alert()` en caso de fallar como indica el requerimiento.
- Las operaciones destructivas en los listados usan `confirm()`.

---
*Página 8: 8. Integración API Externa*
## 8. Integración de API Externa
Como requisito de datos abiertos/externos, se ha conectado con la API de `The fake store API`. 
En el **Dashboard**, se utiliza AJAX/Fetch asíncrono en JavaScript para pintar un resumen de 3 productos aleatorios.
En la vista **`api_externa.php`**, los usuarios pueden listar visualmente estos artículos para ver fotos de perros y su temperamento. 

---
*Página 9: 9. Manual de Usuario / Capturas Simuladas*
## 9. Manual de Usuario (Simulado)

1. **Instalación:** Lanzar XAMPP, importar `dump.sql`.
2. **Acceso:** Abrir `localhost/Techshop`. Ingresar como `admin@techshop.com` / `admin123`.
3. **Flujo Cesta:** 
   - Dirígete a "Tienda". 
   - Presiona "Añadir" en productos variados para tus mascotas.
   - Presiona el Carrito en la esquina superior derecha (Modal de Bootstrap).
   - "Realizar Pedido" inyectará estos datos hacia el perfil.
4. **CRUDs:** Ve a "Gestión > Productos". Podrás modificar todos los datos o subir imágenes (`.jpg`, `.png`). Las interfaces son auto-explicativas.

---
*Página 10: 10. Conclusiones*
## 10. Conclusiones y Mejoras
**Conclusión:**
"Techshop" ha cumplido su objetivo de demostrar la competencia en desarrollo de aplicaciones del lado del servidor interconectadas con bases de datos relacionales, manejando sesiones, APIs frontend y aplicando bases sólidas de diseño responsivo.

**Mejoras a futuro:**
- Tablas intermedias de "Líneas de pedido" para reflejar con detalle qué productos y qué cantidad exactamente se ha comprado, antes que solo reflejarlos en una sola tabla principal por simplicidad en este MVP.
- Pasarela de pago real (Stripe / PayPal API).
- Hashear tokens para recuperar contraseñas vía email (PHPMailer).
