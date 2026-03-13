-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2026 at 12:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `activa` enum('si','no') DEFAULT 'si',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `padre_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`, `orden`, `activa`, `fecha_creacion`, `padre_id`) VALUES
(17, 'Electronica', 'Todo tipo de dispositivos electrónicos', NULL, 0, 'si', '2026-03-11 17:23:26', NULL),
(18, 'fotografia', 'Equipos de camara profesional ideal para fotografía y video', NULL, 0, 'si', '2026-03-11 17:23:26', NULL),
(19, 'Accesorios', 'Accesorios para dispositivos y uso diario', NULL, 0, 'si', '2026-03-11 17:23:26', NULL),
(20, 'Gaming', 'Productos para gamers', NULL, 0, 'si', '2026-03-11 17:23:26', NULL),
(21, 'Audio', 'Auriculares, altavoces y sistemas de sonido', NULL, 0, 'si', '2026-03-11 17:23:26', NULL),
(22, 'Wearables', 'Relojes inteligentes, pulseras y gadgets wearables', NULL, 0, 'si', '2026-03-11 17:23:26', NULL),
(23, 'Oficina', 'Equipos y accesorios para oficina', NULL, 0, 'si', '2026-03-11 17:23:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Enviado','Entregado') DEFAULT 'Pendiente',
  `direccion_envio` text NOT NULL,
  `notas` text DEFAULT NULL,
  `num_items` int(11) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT 0.00,
  `descuento` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_usuario`, `fecha_pedido`, `total`, `estado`, `direccion_envio`, `notas`, `num_items`, `impuesto`, `descuento`) VALUES
(11, 3, '2026-03-12 23:56:47', 330.00, 'Pendiente', 'Calle Principal 1', NULL, 3, 0.00, 0.00),
(12, 3, '2026-03-13 00:01:40', 255.00, 'Pendiente', 'Calle Principal 1', NULL, 4, 0.00, 0.00),
(13, 3, '2026-03-13 00:25:46', 75.00, 'Pendiente', 'Calle Principal 1', NULL, 2, 0.00, 0.00),
(14, 3, '2026-03-13 00:27:00', 225.00, 'Pendiente', 'Calle Principal 1', NULL, 3, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`) VALUES
(14, 11, 106, 1, 220.00),
(15, 11, 109, 1, 30.00),
(16, 11, 103, 1, 80.00),
(17, 12, 110, 3, 75.00),
(18, 12, 109, 1, 30.00),
(19, 13, 74, 1, 45.00),
(20, 13, 109, 1, 30.00),
(21, 14, 110, 3, 75.00);

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `categoria` enum('Electronica','fotografia','Accesorios','Gaming','Audio','Wearables','Oficina') NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `fecha_alta` datetime DEFAULT current_timestamp(),
  `destacado` enum('si','no') DEFAULT 'no',
  `proveedor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `categoria`, `imagen_url`, `fecha_alta`, `destacado`, `proveedor`) VALUES
(61, 'Smartphone Z5', 'Teléfono inteligente', 599.99, 50, 'Electronica', 'images/smartphone.jpg', '2026-03-11 17:24:51', 'si', 'Samsung'),
(62, 'Laptop Pro', 'Portátil de alto rendimiento', 1200.00, 30, 'Electronica', 'images/laptop.jpg', '2026-03-11 17:24:51', 'no', 'Dell'),
(63, 'Auriculares NoiseCancel', 'Auriculares Bluetooth con cancelación de ruido', 299.99, 60, 'Electronica', 'images/headphones.jpg', '2026-03-11 17:24:51', 'si', 'Sony'),
(64, 'Monitor edge', 'Monitor amplio', 350.00, 20, 'Electronica', 'images/monitor.jpg', '2026-03-11 17:24:51', 'no', 'LG'),
(65, 'Teclado Mecánico', 'Teclado inalambrico moderno', 75.00, 40, 'Electronica', 'images/keyboard.jpg', '2026-03-11 17:24:51', 'no', 'Logitech'),
(71, 'Reloj Smart', 'Smartwatch', 325.00, 40, 'Accesorios', 'images/smart_watch.webp', '2026-03-11 17:24:51', 'si', 'Apple'),
(72, 'Gafas AR', 'Gafas de realidad aumentada', 250.00, 30, 'Accesorios', 'images/ar_glasses.jpg', '2026-03-11 17:24:51', 'no', 'ARTech'),
(73, 'Cargador Inalámbrico', 'Cargador rápido portátil', 30.00, 50, 'Accesorios', 'images/cargador_portatil.jpg', '2026-03-11 17:24:51', 'no', 'Gucci'),
(74, 'Funda Laptop', 'Funda acolchada para guardar portatiles', 45.00, 28, 'Accesorios', 'images/laptop_cover.jpg', '2026-03-11 17:24:51', 'si', 'TechBag'),
(75, 'Auriculares Inalámbricos', 'Auriculares bluetooth', 75.00, 45, 'Accesorios', 'images/wireless_earbuds.jpg', '2026-03-11 17:24:51', 'no', 'Sony'),
(76, 'Consola PS5', 'Última generación de consola', 499.00, 40, 'Gaming', 'images/ps5.jpg', '2026-03-11 17:24:51', 'si', 'Sony'),
(77, 'Consola Xbox', 'Xbox Series X', 399.00, 30, 'Gaming', 'images/xbox.jpg', '2026-03-11 17:24:51', 'no', 'Microsoft'),
(78, 'Control Inalámbrico', 'Control para Xbox', 65.00, 49, 'Gaming', 'images/control_inalambrico.jpg', '2026-03-11 17:24:51', 'si', 'Sony'),
(79, 'Silla Gamer', 'Silla ergonómica para gaming', 200.00, 25, 'Gaming', 'images/gaming_chair.webp', '2026-03-11 17:24:51', 'no', 'DXRacer'),
(80, 'Auriculares Gamer', 'Auriculares con micrófono para gaming', 80.00, 40, 'Gaming', 'images/gaming_headphones.jpg', '2026-03-11 17:24:51', 'si', 'Logitech'),
(81, 'Mezclador de Sonido', 'Mezclador de sonido compacto para control de múltiples entradas', 180.00, 15, 'Audio', 'images/Mezclador_sonido.webp', '2026-03-11 17:24:51', 'no', 'Bose'),
(82, 'Amplificador de Audio', 'Amplificador de audio profesional para estudios y eventos', 200.00, 20, 'Audio', 'images/Amplificador_audio.jpg', '2026-03-11 17:24:51', 'no', 'Sony'),
(84, 'Barra Sonido', 'Barra de sonido compacta y potente para oficina y hogar', 150.00, 25, 'Audio', 'images/soundbar.jpg', '2026-03-11 17:24:51', 'no', 'Samsung'),
(85, 'Micrófono Condensador', 'Micrófono de condensador para streaming y grabación profesional', 90.00, 38, 'Audio', 'images/microfono_condensador.jpg', '2026-03-11 17:24:51', 'si', 'Blue'),
(86, 'Reloj Fitness', 'Reloj inteligente con monitor de actividad física y sueño', 110.00, 59, 'Wearables', 'images/reloj_fitness.jpg', '2026-03-11 17:24:51', 'si', 'Fitbit'),
(89, 'Sensor de Ritmo Cardíaco', 'Sensor wearable para control de ritmo cardiaco y salud', 60.00, 50, 'Wearables', 'images/Sensor_cardiaco.webp', '2026-03-11 17:24:51', 'no', 'Sony'),
(90, 'Anillo Inteligente', 'Anillo para monitoreo de actividad y notificaciones', 165.00, 28, 'Wearables', 'images/Anillo_inteligente.webp', '2026-03-11 17:24:51', 'si', 'SmartRing'),
(91, 'Escáner de Documentos', 'Escáner de documentos compacto con conexión USB', 150.00, 10, 'Oficina', 'images/escaner.jpg', '2026-03-11 17:24:51', 'no', 'Dell'),
(92, 'Calculadora Profesional', 'Calculadora científica y profesional ', 30.00, 50, 'Oficina', 'images/Calculadora_profesional.jpg', '2026-03-11 17:24:51', 'no', 'Logitech'),
(93, 'Lámpara LED', 'Lámpara de escritorio LED con control de brillo y color', 45.00, 20, 'Oficina', 'images/lampara.jpg', '2026-03-11 17:24:51', 'no', 'Ikea'),
(94, 'Imp. Laser', 'Impresora multifunción láser para oficina', 250.00, 24, 'Oficina', 'images/impresora.webp', '2026-03-11 17:24:51', 'no', 'HP'),
(95, 'Teclado Inalámbrico', 'Teclado inalámbrico ergonómico', 75.00, 29, 'Oficina', 'images/teclado_inalambrico.webp', '2026-03-11 17:24:51', 'no', 'Ikea'),
(96, 'Smartphone X', 'Teléfono inteligente con cámara 108MP', 699.00, 40, 'Electronica', 'images/smartphoneZ.jpg', '2026-03-11 17:34:35', 'si', 'Samsung'),
(98, 'Soporte Tablet', 'Soporte ajustable para diferentes tipos de tablets', 25.00, 49, 'Accesorios', 'images/soporte_tablet.jpg', '2026-03-11 17:34:35', 'no', 'Accesorize Inc'),
(99, 'Mouse Gaming Pro', 'Mouse con sensor de alta precisión y retroiluminación RGB', 60.00, 34, 'Gaming', 'images/gaming_mouse.webp', '2026-03-11 17:34:35', 'si', 'Logitech'),
(101, 'VR Set', 'Kit de realidad virtual para juegos y simulaciones', 350.00, 10, 'Wearables', 'images/VR_set.jpg', '2026-03-11 17:34:35', 'no', 'FitTech'),
(102, 'Proyector Portátil', 'Proyector compacto para presentaciones y reuniones', 250.00, 14, 'Oficina', 'images/proyector_portatil.webp', '2026-03-11 17:34:35', 'no', 'OfficePro'),
(103, 'Alfombrilla Gaming ', 'Alfombrilla superficie de alta precisión', 80.00, 26, 'Gaming', 'images/gaming_mat.jpg', '2026-03-11 17:34:35', 'no', 'HyperX'),
(104, 'Cámara DSLR Pro', 'Cámara DSLR profesional para fotografía avanzada', 1200.00, 10, 'fotografia', 'images/camara_dslr.jpg', '2026-03-11 19:38:38', 'no', NULL),
(105, 'Trípode Profesional', 'Trípode ajustable de alta estabilidad para todas tus cámaras', 150.00, 15, 'fotografia', 'images/tripod.jpg', '2026-03-11 19:38:38', 'no', NULL),
(106, 'Lente 50mm f/1.8', 'Lente prime de 50mm para retratos y fotografía con gran apertura', 220.00, 18, 'fotografia', 'images/lens.jpg', '2026-03-11 19:38:38', 'no', ''),
(109, 'Mochila para Cámara', '', 30.00, 15, 'fotografia', 'uploads/1773278038_bolso_camara.jpg', '2026-03-12 02:13:58', 'no', ''),
(110, 'Flash Externo', 'Equipo flash externo compatible con diferentes modelos', 75.00, -1, 'fotografia', 'uploads/1773278248_external_flash.jpg', '2026-03-12 02:17:28', 'no', '');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  `rol` enum('admin','vendedor','cliente') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `email`, `password`, `nombre`, `apellidos`, `telefono`, `direccion`, `fecha_registro`, `activo`, `rol`) VALUES
(3, 'admin', 'admin@techshop.com', '$2y$10$3CP9odsCBv86xT4p4AhQneiUWByZNRNREaMgiJaZPApVtKVcXCNp2', 'Administrador', 'Sistema', '123456789', 'Calle Principal 1', '2026-03-11 15:16:31', 1, 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `padre_id` (`padre_id`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`padre_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
