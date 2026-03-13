# TechShop - Tienda de diversos productos SGE

## Tecnologías Utilizadas
- **Backend:** PHP 8.x (PDO)
- **Base de Datos:** MySQL
- **Frontend:** HTML5, CSS3, JS Vanilla, Bootstrap 5
- **API Externa:** The fakestore API (https://fakestoreapi.com)

## Puesta en Marcha (Instrucciones)

1. **Requisitos:**
   - Servidor web local (XAMPP, WAMP, o similar).
   - PHP 8 o superior.
   - MySQL 5.7 o superior (o MariaDB equivalente).

2. **Instalación:**
   - Copia la carpeta `TechShop` en el directorio raíz de tu servidor web (por ejemplo, `C:\xampp\htdocs\TechShop`).
   
3. **Base de Datos:**
   - Abre PHPMyAdmin o tu cliente MySQL.
   - Crea una base de datos de nombre `TechShop` con comandos o simplemente importa el archivo.
   - Importa el archivo `dump.sql` incluido en la raíz de este proyecto. Esto creará las 4 tablas principales (`usuarios`, `productos`, `pedidos`, `categorias`) y poblará datos iniciales de prueba enfocados en mascotas (incluyendo 20 productos iniciales).
   - Asegúrate de que las credenciales en `config/db.php` coinciden con las de tu servidor local (por defecto usa `root` sin contraseña).

4. **Permisos (Linux/Mac) y Subida de Archivos:**
   - La aplicación permite la subida *local* de imágenes para productos del catálogo. Asegúrate de dar permisos de escritura (777) a la carpeta `uploads/`. Si no existe, al subir la primera imagen se intentará crear automáticamente.

5. **Acceso:**
   - Accede mediante el navegador: `http://localhost/TechShop`
   - **Usuario Demo:**
     - **Email:** admin@techshop.com
     - **Contraseña:** admin123 (Cuenta con auto-reparación de hash al primer login)

## Estructura
- `/config/` - Archivos de conexión a BD.
- `/includes/` - Elementos comunes (Header, Footer, Funciones).
- `/js/` y `/css/` - Assets Frontend.
- Los archivos `.php` en la raíz son las vistas de usuario y paneles administrativos de CRUD.
