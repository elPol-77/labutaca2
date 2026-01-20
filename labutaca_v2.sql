-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-01-2026 a las 17:34:22
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
-- Base de datos: `labutaca_v2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actores`
--

CREATE TABLE `actores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `capitulos`
--

CREATE TABLE `capitulos` (
  `id` int(11) NOT NULL,
  `temporada_id` int(11) NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `numero` int(11) NOT NULL,
  `url_video` varchar(500) NOT NULL,
  `duracion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenidos`
--

CREATE TABLE `contenidos` (
  `id` int(11) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `anio` int(4) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `imagen_bg` varchar(500) DEFAULT NULL,
  `url_video` varchar(500) DEFAULT NULL,
  `nivel_acceso` int(11) DEFAULT 1,
  `vistas` int(11) DEFAULT 0,
  `destacada` tinyint(1) DEFAULT 0,
  `fecha_agregada` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenidos`
--

INSERT INTO `contenidos` (`id`, `tipo_id`, `titulo`, `descripcion`, `anio`, `duracion`, `imagen`, `imagen_bg`, `url_video`, `nivel_acceso`, `vistas`, `destacada`, `fecha_agregada`) VALUES
(1, 1, 'Avatar: Fire and Ash', 'Tercera entrega...', 2025, 170, 'avatar3.jpg', 'https://image.tmdb.org/t/p/original/8rpDcsfLJypbO6vREc0547TJqqQ.jpg', 'https://youtu.be/lhLsr9S3bgQ', 2, 0, 0, '2026-01-08 17:40:01'),
(2, 1, 'Una película de Minecraft', 'Adaptación del juego...', 2025, 125, 'minecraft.jpg', 'https://image.tmdb.org/t/p/original/9n2tJBplPbgR2ca05hS5CKXwP2c.jpg', 'https://youtu.be/peQj4Yq7cI8', 2, 0, 0, '2026-01-08 17:40:01'),
(3, 1, 'Dune: Parte Dos', 'Paul Atreides...', 2024, 155, 'dune2.jpg', 'https://image.tmdb.org/t/p/original/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg', 'https://youtu.be/U2Qp5pL3ovA', 2, 0, 0, '2026-01-08 17:40:01'),
(4, 1, 'Spiderman: Cruzando el Multiverso', 'Miles Morales...', 2023, 140, 'spiderverse2.jpg', 'https://image.tmdb.org/t/p/original/4HodYYKEIsGOdinkGi2Ucz6X9i0.jpg', 'https://youtu.be/cqGjhVJWtEg', 1, 0, 0, '2026-01-08 17:40:01'),
(5, 1, 'Oppenheimer', 'Historia de la bomba...', 2023, 180, 'oppenheimer.jpg', 'https://image.tmdb.org/t/p/original/rLb2cs785pePbIKYQz1CADtovh7.jpg', 'https://youtu.be/MVvGSBKV504', 1, 0, 0, '2026-01-08 17:40:01'),
(6, 2, 'Stranger Things', 'Misterios en Hawkins...', 2016, NULL, 'stranger.jpg', 'https://image.tmdb.org/t/p/original/56v2KjBlU4XaOv9rVYkJu64COcfe.jpg', NULL, 2, 0, 0, '2026-01-08 17:40:01'),
(7, 4, 'Pac-Man Online', 'Juega al clásico arcade.', 1980, NULL, 'pacman.jpg', 'https://image.tmdb.org/t/p/original/pacman_bg.jpg', 'https://freepacman.org/', 1, 0, 0, '2026-01-08 17:40:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_actor`
--

CREATE TABLE `contenido_actor` (
  `contenido_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `personaje` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_director`
--

CREATE TABLE `contenido_director` (
  `contenido_id` int(11) NOT NULL,
  `director_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_genero`
--

CREATE TABLE `contenido_genero` (
  `contenido_id` int(11) NOT NULL,
  `genero_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_idioma`
--

CREATE TABLE `contenido_idioma` (
  `contenido_id` int(11) NOT NULL,
  `idioma_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `directores`
--

CREATE TABLE `directores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `generos`
--

CREATE TABLE `generos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `generos`
--

INSERT INTO `generos` (`id`, `nombre`) VALUES
(1, 'Acción'),
(2, 'Aventura'),
(3, 'Ciencia Ficción'),
(4, 'Drama');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `idiomas`
--

CREATE TABLE `idiomas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `codigo` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `idiomas`
--

INSERT INTO `idiomas` (`id`, `nombre`, `codigo`) VALUES
(1, 'Español', 'es'),
(2, 'Inglés', 'en');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mi_lista`
--

CREATE TABLE `mi_lista` (
  `usuario_id` int(11) NOT NULL,
  `contenido_id` int(11) NOT NULL,
  `fecha_agregado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `calidad` varchar(50) DEFAULT 'HD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id`, `nombre`, `precio`, `calidad`) VALUES
(1, 'Free', 0.00, '720p'),
(2, 'Premium', 9.99, '4K Ultra HD');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `contenido_id` int(11) NOT NULL,
  `puntuacion` int(1) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporadas`
--

CREATE TABLE `temporadas` (
  `id` int(11) NOT NULL,
  `contenido_id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `numero` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_contenido`
--

CREATE TABLE `tipos_contenido` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_contenido`
--

INSERT INTO `tipos_contenido` (`id`, `nombre`) VALUES
(1, 'Película'),
(2, 'Serie'),
(3, 'Documental'),
(4, 'Juego');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `plan_id` int(11) DEFAULT 1,
  `avatar` varchar(255) DEFAULT 'default.png',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actores`
--
ALTER TABLE `actores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `capitulos`
--
ALTER TABLE `capitulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temporada_id` (`temporada_id`);

--
-- Indices de la tabla `contenidos`
--
ALTER TABLE `contenidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_id` (`tipo_id`),
  ADD KEY `nivel_acceso` (`nivel_acceso`);

--
-- Indices de la tabla `contenido_actor`
--
ALTER TABLE `contenido_actor`
  ADD PRIMARY KEY (`contenido_id`,`actor_id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indices de la tabla `contenido_director`
--
ALTER TABLE `contenido_director`
  ADD PRIMARY KEY (`contenido_id`,`director_id`),
  ADD KEY `director_id` (`director_id`);

--
-- Indices de la tabla `contenido_genero`
--
ALTER TABLE `contenido_genero`
  ADD PRIMARY KEY (`contenido_id`,`genero_id`),
  ADD KEY `genero_id` (`genero_id`);

--
-- Indices de la tabla `contenido_idioma`
--
ALTER TABLE `contenido_idioma`
  ADD PRIMARY KEY (`contenido_id`,`idioma_id`),
  ADD KEY `idioma_id` (`idioma_id`);

--
-- Indices de la tabla `directores`
--
ALTER TABLE `directores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `generos`
--
ALTER TABLE `generos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `idiomas`
--
ALTER TABLE `idiomas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mi_lista`
--
ALTER TABLE `mi_lista`
  ADD PRIMARY KEY (`usuario_id`,`contenido_id`),
  ADD KEY `contenido_id` (`contenido_id`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `contenido_id` (`contenido_id`);

--
-- Indices de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contenido_id` (`contenido_id`);

--
-- Indices de la tabla `tipos_contenido`
--
ALTER TABLE `tipos_contenido`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `plan_id` (`plan_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actores`
--
ALTER TABLE `actores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `capitulos`
--
ALTER TABLE `capitulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contenidos`
--
ALTER TABLE `contenidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `directores`
--
ALTER TABLE `directores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `generos`
--
ALTER TABLE `generos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `idiomas`
--
ALTER TABLE `idiomas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos_contenido`
--
ALTER TABLE `tipos_contenido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `capitulos`
--
ALTER TABLE `capitulos`
  ADD CONSTRAINT `capitulos_ibfk_1` FOREIGN KEY (`temporada_id`) REFERENCES `temporadas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contenidos`
--
ALTER TABLE `contenidos`
  ADD CONSTRAINT `contenidos_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `tipos_contenido` (`id`),
  ADD CONSTRAINT `contenidos_ibfk_2` FOREIGN KEY (`nivel_acceso`) REFERENCES `planes` (`id`);

--
-- Filtros para la tabla `contenido_actor`
--
ALTER TABLE `contenido_actor`
  ADD CONSTRAINT `contenido_actor_ibfk_1` FOREIGN KEY (`contenido_id`) REFERENCES `contenidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contenido_actor_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `actores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contenido_director`
--
ALTER TABLE `contenido_director`
  ADD CONSTRAINT `contenido_director_ibfk_1` FOREIGN KEY (`contenido_id`) REFERENCES `contenidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contenido_director_ibfk_2` FOREIGN KEY (`director_id`) REFERENCES `directores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contenido_genero`
--
ALTER TABLE `contenido_genero`
  ADD CONSTRAINT `contenido_genero_ibfk_1` FOREIGN KEY (`contenido_id`) REFERENCES `contenidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contenido_genero_ibfk_2` FOREIGN KEY (`genero_id`) REFERENCES `generos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contenido_idioma`
--
ALTER TABLE `contenido_idioma`
  ADD CONSTRAINT `contenido_idioma_ibfk_1` FOREIGN KEY (`contenido_id`) REFERENCES `contenidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contenido_idioma_ibfk_2` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mi_lista`
--
ALTER TABLE `mi_lista`
  ADD CONSTRAINT `mi_lista_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mi_lista_ibfk_2` FOREIGN KEY (`contenido_id`) REFERENCES `contenidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resenas_ibfk_2` FOREIGN KEY (`contenido_id`) REFERENCES `contenidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `temporadas`
--
ALTER TABLE `temporadas`
  ADD CONSTRAINT `temporadas_ibfk_1` FOREIGN KEY (`contenido_id`) REFERENCES `contenidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
