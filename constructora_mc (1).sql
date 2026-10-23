-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 23-10-2026 a las 16:45:01
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `constructora_mc`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `nombre`, `correo`, `contrasena`, `creado_en`) VALUES
(1, 'Administrador', 'admin@constructoramc.com', '$2y$10$piE3zF4ywPFP550WuYvjX.XBPEpWg5Kh2LyDXYDY8Q/HuW/Qmegm6', '2026-10-18 06:38:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_empresa`
--

CREATE TABLE `configuracion_empresa` (
  `id` int(11) NOT NULL,
  `nombre_empresa` varchar(150) NOT NULL,
  `descripcion_corta` text DEFAULT NULL,
  `texto_nosotros` text DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `correo_contacto` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `hero_titulo` varchar(150) DEFAULT NULL,
  `hero_subtitulo` text DEFAULT NULL,
  `hero_imagen` varchar(255) DEFAULT NULL,
  `stat_1_numero` varchar(20) DEFAULT NULL,
  `stat_1_texto` varchar(100) DEFAULT NULL,
  `stat_2_numero` varchar(20) DEFAULT NULL,
  `stat_2_texto` varchar(100) DEFAULT NULL,
  `stat_3_numero` varchar(20) DEFAULT NULL,
  `stat_3_texto` varchar(100) DEFAULT NULL,
  `stat_4_numero` varchar(20) DEFAULT NULL,
  `stat_4_texto` varchar(100) DEFAULT NULL,
  `nosotros_imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_empresa`
--

INSERT INTO `configuracion_empresa` (`id`, `nombre_empresa`, `descripcion_corta`, `texto_nosotros`, `telefono`, `whatsapp`, `correo_contacto`, `direccion`, `facebook`, `instagram`, `hero_titulo`, `hero_subtitulo`, `hero_imagen`, `stat_1_numero`, `stat_1_texto`, `stat_2_numero`, `stat_2_texto`, `stat_3_numero`, `stat_3_texto`, `stat_4_numero`, `stat_4_texto`, `nosotros_imagen`) VALUES
(1, 'Constructora MC', 'Construimos el futuro con bases sólidas y visión de vanguardia para mejorar tu futura casa.', 'En Constructora MC nos dedicamos a la creación de espacios arquitectónicos de alta calidad. Combinamos experiencia, innovación y compromiso para entregar proyectos que superan las expectativas de nuestros clientes.', '+506 6333 8585', '+506 6333 8585', 'marcoquesadaruiz@gmail.com', 'Alajuela, San Carlos, Forencia', '', '', 'CONSTRUIMOS EL', 'Futuro de la arquitectura con precisión, calidad y pasión en cada proyecto.', 'uploads/329cdc1a8988578ec19eb051c5a08061.jpg', '+26', 'Años de experiencia', '+250', 'Proyectos completados', '+500', 'Clientes', '98%', 'Satisfacción', 'uploads/33d62cf7f75edd0f3a5bc6438e5ec89a.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_proyectos`
--

CREATE TABLE `imagenes_proyectos` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes_proyectos`
--

INSERT INTO `imagenes_proyectos` (`id`, `proyecto_id`, `ruta_imagen`, `orden`) VALUES
(2, 2, 'uploads/4d81f893f91d492f2e6ebaea23457a89.png', 0),
(3, 1, 'uploads/900b41d339159c156f0996725b355fd4.png', 0),
(4, 1, 'uploads/57e355004c5798a9c7e7b259654855dc.png', 0),
(5, 1, 'uploads/295e34a98fd4695f0c32c047e591c3c2.png', 0),
(6, 2, 'uploads/f2bba62053795a3ced387d14ea3cda1e.png', 0),
(7, 2, 'uploads/0a89f22ef0a9e4904c535f7744961472.jpg', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto`
--

CREATE TABLE `mensajes_contacto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes_contacto`
--

INSERT INTO `mensajes_contacto` (`id`, `nombre`, `correo`, `telefono`, `mensaje`, `leido`, `fecha_envio`) VALUES
(2, 'Patrick quesada', 'quesadamatthew28@gmail.com', '63979785', 'prueba', 1, '2026-10-18 08:19:46'),
(3, 'Camila murilo', 'carnala48@gmail.com', '84049786', 'prueba 2', 1, '2026-10-18 08:37:53'),
(4, 'Camila murilo', 'carnala48@gmail.com', '84049786', 'prueba 2', 1, '2026-10-18 08:38:47'),
(5, 'Camila murilo', 'carnala48@gmail.com', '84049786', 'prueba 2', 1, '2026-10-18 08:39:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `ubicacion` varchar(150) DEFAULT NULL,
  `estado` enum('borrador','publicado') DEFAULT 'borrador',
  `imagen_portada` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `titulo`, `descripcion`, `categoria`, `ubicacion`, `estado`, `imagen_portada`, `fecha_creacion`) VALUES
(1, 'Torre Empresarial Nova', 'Edificio corporativo de 15 pisos con diseño sustentable.', 'Comercial', 'Ciudad de México', 'publicado', 'uploads/57e355004c5798a9c7e7b259654855dc.png', '2026-10-18 06:38:18'),
(2, 'Ampliación de casa: cuarto y baño nuevos', 'Este fue una remodelacion hecha para nose', 'Remodelacion', 'Alajuelo, San Carlos, Ciudad Quesada', 'publicado', 'uploads/4d81f893f91d492f2e6ebaea23457a89.png', '2026-10-18 19:50:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `titulo`, `descripcion`, `icono`, `orden`) VALUES
(1, 'Construcción Residencial', 'Diseño y construcción de viviendas de alta gama, adaptadas a tus necesidades.', 'fa-home', 1),
(2, 'Edificación Comercial', 'Espacios funcionales y modernos para impulsar tu negocio.', 'fa-store', 2),
(3, 'Remodelaciones', 'Transformamos espacios existentes en entornos renovados y vanguardistas.', 'fa-hammer', 3),
(4, 'Supervisión de Obra', 'Garantizamos la calidad y seguridad en cada etapa de tu proyecto.', 'fa-hard-hat', 4),
(6, 'Los mejores equipamientos', 'Garantia de manejar los proyectos con lo mejor del mercado', 'fa-truck-pickup', 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `configuracion_empresa`
--
ALTER TABLE `configuracion_empresa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `imagenes_proyectos`
--
ALTER TABLE `imagenes_proyectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `configuracion_empresa`
--
ALTER TABLE `configuracion_empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `imagenes_proyectos`
--
ALTER TABLE `imagenes_proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `imagenes_proyectos`
--
ALTER TABLE `imagenes_proyectos`
  ADD CONSTRAINT `imagenes_proyectos_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
