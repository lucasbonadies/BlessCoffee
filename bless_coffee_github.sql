-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-05-2025 a las 18:47:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bless_coffee_github`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id_articulo` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `imagen` varchar(20) DEFAULT NULL,
  `precio_unitario` decimal(10,0) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`id_articulo`, `id_tipo`, `nombre`, `imagen`, `precio_unitario`, `descripcion`, `id_estado`) VALUES
(1, 1, 'Café', 'sin_imagen.jpg', 2500, NULL, 1),
(2, 1, 'Leche', 'sin_imagen.jpg', 1300, NULL, 1),
(3, 4, 'Tostado', 'sin_imagen.jpg', 10000, NULL, 1),
(4, 6, 'Ensala de Frutas', 'sin_imagen.jpg', 1000, 'Las Frutas mas Frescas', 1),
(8, 3, 'Medialunas', 'sin_imagen.jpg', 1000, '', 1),
(9, 4, 'Sanwich de miga', 'sin_imagen.jpg', 5200, 'Jamon y queso', 1),
(10, 1, 'Jugo exprimido de naranja', 'sin_imagen.jpg', 5000, 'fresco', 1),
(11, 2, 'Mocachino', 'sin_imagen.jpg', 4800, '', 1),
(12, 3, 'Brownie', 'sin_imagen.jpg', 2000, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id_detalle` int(11) NOT NULL,
  `id_articulo` int(11) DEFAULT NULL,
  `id_promocion` int(11) DEFAULT NULL,
  `id_pedido` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `notas` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`id_detalle`, `id_articulo`, `id_promocion`, `id_pedido`, `cantidad`, `notas`) VALUES
(1, NULL, 1, 1, 1, ''),
(2, NULL, 3, 1, 1, ''),
(3, NULL, 4, 1, 1, ''),
(4, NULL, 1, 3, 3, NULL),
(5, NULL, 2, 3, 3, NULL),
(6, NULL, 1, 3, 3, NULL),
(7, NULL, 2, 3, 3, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `entidad` varchar(20) NOT NULL,
  `descripcion` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `nombre`, `entidad`, `descripcion`) VALUES
(1, 'activo', 'usuario', 'permitido ingresar al sistema'),
(2, 'inactivo', 'usuario', 'no puede ingresar al sistema'),
(3, 'pendiente', 'pedido', NULL),
(4, 'preparado', 'pedido', NULL),
(5, 'entregado', 'pedido', NULL),
(6, 'cancelado', 'pedido', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id_nivel` int(11) NOT NULL,
  `nombre_nivel` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`id_nivel`, `nombre_nivel`) VALUES
(1, 'administrador'),
(5, 'desarrollador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `metodo_pago` varchar(20) NOT NULL,
  `monto_total` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `notas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_estado` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `fecha_pedido`, `id_estado`, `id_persona`) VALUES
(1, '2025-05-01 00:32:09', 3, 1),
(2, '2025-05-01 01:08:25', 3, 1),
(3, '2025-05-01 01:10:11', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `dni` int(30) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `imagen` varchar(30) DEFAULT NULL,
  `nro_telefono` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id_persona`, `nombre`, `apellido`, `dni`, `fecha_nacimiento`, `imagen`, `nro_telefono`) VALUES
(1, 'example', 'example', 999999, '2001-01-10', 'Perfil_1.jpg', '9999999');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id_promocion` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `imagen` varchar(20) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` varchar(30) DEFAULT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`id_promocion`, `id_tipo`, `nombre`, `imagen`, `precio`, `descripcion`, `id_estado`) VALUES
(1, 8, 'Café + 5 Medialunas', 'sin_imagen.jpg', 100.99, '', 1),
(2, 8, '1/2 Tostado + Café con Leche', 'Papas-Rusticas.webp', 1000.99, '', 1),
(3, 8, 'Sanwich de miga + 2 medialunas', 'sin_imagen.jpg', 7500.00, 'Las medialunas tienen jamon y ', 1),
(4, 8, 'Cafe + brownie', 'sin_imagen.jpg', 8050.00, 'rico', 1),
(8, 8, 'mocachino + sanwich de miga', 'coca-cola.jpg', 8900.00, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocion_detalle`
--

CREATE TABLE `promocion_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_promocion` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `promocion_detalle`
--

INSERT INTO `promocion_detalle` (`id_detalle`, `id_promocion`, `id_articulo`, `cantidad`) VALUES
(1, 1, 1, 1),
(2, 1, 8, 5),
(3, 2, 1, 1),
(4, 2, 3, 1),
(5, 3, 9, 1),
(6, 3, 8, 2),
(7, 4, 1, 1),
(8, 4, 12, 1),
(15, 8, 11, 1),
(16, 8, 9, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_articulos`
--

CREATE TABLE `tipo_articulos` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tipo_articulos`
--

INSERT INTO `tipo_articulos` (`id_tipo`, `nombre`, `descripcion`) VALUES
(1, 'Bebidas', ''),
(2, 'Cafés Especiales', ''),
(3, 'Pastelería y Panadería', ''),
(4, 'Sandwiches y Snacks', ''),
(5, 'Postres y Dulces', ''),
(6, 'Opciones Saludables', ''),
(7, 'Productos Adicionales', ''),
(8, 'Promociones', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens`
--

CREATE TABLE `tokens` (
  `id_token` int(11) NOT NULL,
  `token_generado` varchar(80) NOT NULL,
  `expiracion` datetime NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `intento_inicio_sesion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_estado`, `id_persona`, `id_nivel`, `clave`, `email`, `intento_inicio_sesion`) VALUES
(3, 1, 1, 1, '12345678', 'example@example.com', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id_articulo`),
  ADD KEY `fk_articulos_tipo` (`id_tipo`),
  ADD KEY `fk_articulos_estado` (`id_estado`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `fk_detalle_pedidos_articulo` (`id_articulo`),
  ADD KEY `fk_detalle_pedidos_pedido` (`id_pedido`),
  ADD KEY `FK_detalle_pedidos_promocion` (`id_promocion`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id_nivel`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `fk_pagos_id_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `fk_pedidos_estado` (`id_estado`),
  ADD KEY `fk_pedidos_persona` (`id_persona`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id_promocion`),
  ADD KEY `fk_promociones_tipo` (`id_tipo`),
  ADD KEY `fk_promociones_estado` (`id_estado`);

--
-- Indices de la tabla `promocion_detalle`
--
ALTER TABLE `promocion_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_promocion` (`id_promocion`),
  ADD KEY `id_articulo` (`id_articulo`);

--
-- Indices de la tabla `tipo_articulos`
--
ALTER TABLE `tipo_articulos`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id_token`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_usuarios_estado` (`id_estado`),
  ADD KEY `fk_usuarios_persona` (`id_persona`),
  ADD KEY `fk_usuarios_nivel` (`id_nivel`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id_articulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `promocion_detalle`
--
ALTER TABLE `promocion_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `tipo_articulos`
--
ALTER TABLE `tipo_articulos`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD CONSTRAINT `fk_articulos_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articulos_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_articulos` (`id_tipo`);

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `FK_detalle_pedidos_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`),
  ADD CONSTRAINT `fk_detalle_pedidos_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  ADD CONSTRAINT `fk_detalle_pedidos_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pagos_id_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`),
  ADD CONSTRAINT `fk_pedidos_persona` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`);

--
-- Filtros para la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD CONSTRAINT `fk_promociones_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_promociones_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_articulos` (`id_tipo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `promocion_detalle`
--
ALTER TABLE `promocion_detalle`
  ADD CONSTRAINT `promocion_detalle_ibfk_1` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`) ON DELETE CASCADE,
  ADD CONSTRAINT `promocion_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`),
  ADD CONSTRAINT `fk_usuarios_nivel` FOREIGN KEY (`id_nivel`) REFERENCES `niveles` (`id_nivel`),
  ADD CONSTRAINT `fk_usuarios_persona` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
