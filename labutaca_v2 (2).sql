-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 05-02-2026 a las 12:43:44
-- Versión del servidor: 8.0.43-0ubuntu0.24.04.2
-- Versión de PHP: 8.4.14

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
  `id` int NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actores`
--

INSERT INTO `actores` (`id`, `nombre`, `foto`) VALUES
(1, 'Sam Worthington', 'https://m.media-amazon.com/images/M/MV5BODAwMTQ0Y2UtYmE0ZS00Mjc4LWExZTMtNTIzMjdmYTZlMTJkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg'),
(2, 'Zoe Saldaña', 'https://m.media-amazon.com/images/M/MV5BMDFkMWQ5ZDItNGUzNS00YzI4LWIyOTctMDk0Mjc3MGQyZTYxXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg'),
(3, 'Jack Black', 'https://m.media-amazon.com/images/M/MV5BNjY3OTQwMDctY2M2Ni00OGE2LThiNjMtYjg0MDg3YjVjN2FiXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg'),
(4, 'Jason Momoa', 'https://m.media-amazon.com/images/M/MV5BZGQ0ZGRkMGQtMjhkZC00NWIzLWE3ZjctOWRlNzA3NTg4OGJiXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg'),
(5, 'Timothée Chalamet', 'https://m.media-amazon.com/images/M/MV5BYjg5ZmIwNGEtMDEzNC00MDA0LWI2NGEtNGYxZGY0MmFhNmJkXkEyXkFqcGc@._V1_.jpg'),
(6, 'Zendaya', 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Zendaya_2019_by_Glenn_Francis.jpg/250px-Zendaya_2019_by_Glenn_Francis.jpg'),
(7, 'Shameik Moore', 'https://m.media-amazon.com/images/M/MV5BMjAwNDU2OTc5M15BMl5BanBnXkFtZTgwOTk0ODMyNDE@._V1_.jpg'),
(8, 'Hailee Steinfeld', 'https://m.media-amazon.com/images/M/MV5BYjg0MjA4OGEtMTc1ZS00ZmJhLTgyYjItMTY4YjI0NjVjNjllXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg'),
(9, 'Cillian Murphy', 'https://m.media-amazon.com/images/M/MV5BMTM1MzIyMjEyMV5BMl5BanBnXkFtZTYwODUwODI1._V1_.jpg'),
(10, 'Robert Downey Jr.', 'https://m.media-amazon.com/images/M/MV5BNzg1MTUyNDYxOF5BMl5BanBnXkFtZTgwNTQ4MTE2MjE@._V1_FMjpg_UX1000_.jpg'),
(11, 'Millie Bobby Brown', 'https://image.tmdb.org/t/p/w500/kHO7hdNEVuTnQ0OjjrxP1RcAa0e.jpg'),
(12, 'Finn Wolfhard', 'https://m.media-amazon.com/images/M/MV5BNTlkZmM3YTYtMWIyYS00Y2UwLTk0YjAtZTdmNmFjNjcyMDNiXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg'),
(13, 'Winona Ryder', 'https://m.media-amazon.com/images/M/MV5BMTQ3NzM3MTc2NF5BMl5BanBnXkFtZTcwODMxNjA0NA@@._V1_FMjpg_UX1000_.jpg'),
(14, 'Leonardo DiCaprio', 'https://image.tmdb.org/t/p/w500/wo2hJpn04vbtmh0B9utCFdsQhxM.jpg'),
(15, 'Christian Bale', 'https://image.tmdb.org/t/p/w500/b7fTC9WFkgqGOv77mLQzmD2UCQD.jpg'),
(16, 'Heath Ledger', 'https://image.tmdb.org/t/p/w500/5Y9Hn3nbqjXcfhVmykR929118c7.jpg'),
(17, 'Robert De Niro', 'https://image.tmdb.org/t/p/w500/cT8htcckIuyI1Lqwt1CvDtRYlo0.jpg'),
(18, 'Bryan Cranston', 'https://image.tmdb.org/t/p/w500/7Jahy5AZpgUFxgYSvxXD8OLOROb.jpg'),
(19, 'Aaron Paul', 'https://image.tmdb.org/t/p/w500/u8UdsB9qkGlZSic9XHKhN0BhC9u.jpg'),
(20, 'Pedro Pascal', 'https://image.tmdb.org/t/p/w500/9fkN09pl7ZUuCjU5e3I3s4X40n6.jpg'),
(21, 'Keanu Reeves', 'https://image.tmdb.org/t/p/w500/4D0PpNI0km3901NZfHvRFhGRRg7.jpg'),
(22, 'Tom Hanks', 'https://image.tmdb.org/t/p/w500/xndWFsBlClOJFRdhSt4NBwiPq2o.jpg'),
(23, 'Scarlett Johansson', 'https://image.tmdb.org/t/p/w500/6NsMbJXRlDGC7yxlz4CiUh8JIe8.jpg'),
(24, 'Joaquin Phoenix', 'https://image.tmdb.org/t/p/w500/nXMzvVF6xR3tGY2r1O5AdncCWq6.jpg'),
(25, 'Mike Myers', 'https://image.tmdb.org/t/p/w500/gjfDl52Kk0A9pMCYmaBFC1TEczT.jpg'),
(26, 'Eddie Murphy', 'https://image.tmdb.org/t/p/w500/q4cE13zzb93evUObYhZ7L6Yz16U.jpg'),
(27, 'Chris Pratt', 'https://ui-avatars.com/api/?name=Chris+Pratt&background=random'),
(28, 'Anya Taylor-Joy', 'https://ui-avatars.com/api/?name=Anya+Taylor-Joy&background=random'),
(29, 'Charlie Day', 'https://ui-avatars.com/api/?name=Charlie+Day&background=random'),
(30, 'Chris Evans', 'https://ui-avatars.com/api/?name=Chris+Evans&background=random'),
(31, 'Mark Ruffalo', 'https://ui-avatars.com/api/?name=Mark+Ruffalo&background=random'),
(32, 'Tom Holland', 'https://ui-avatars.com/api/?name=Tom+Holland&background=random'),
(33, 'Samuel L. Jackson', 'https://ui-avatars.com/api/?name=Samuel+L.+Jackson&background=random'),
(34, 'Jake Gyllenhaal', 'https://ui-avatars.com/api/?name=Jake+Gyllenhaal&background=random'),
(35, 'Edward Norton', 'https://ui-avatars.com/api/?name=Edward+Norton&background=random'),
(36, 'Liv Tyler', 'https://ui-avatars.com/api/?name=Liv+Tyler&background=random'),
(37, 'Tim Roth', 'https://ui-avatars.com/api/?name=Tim+Roth&background=random'),
(38, 'Mickey Rourke', 'https://ui-avatars.com/api/?name=Mickey+Rourke&background=random'),
(39, 'Gwyneth Paltrow', 'https://ui-avatars.com/api/?name=Gwyneth+Paltrow&background=random');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `capitulos`
--

CREATE TABLE `capitulos` (
  `id` int NOT NULL,
  `temporada_id` int NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` int NOT NULL,
  `url_video` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `duracion` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `capitulos`
--

INSERT INTO `capitulos` (`id`, `temporada_id`, `titulo`, `numero`, `url_video`, `duracion`) VALUES
(1, 1, 'La desaparición de Will Byers', 1, 'https://www.youtube.com/watch?v=b9EkMc79ZSU', 48),
(2, 1, 'La loca de la calle Maple', 2, 'https://www.youtube.com/watch?v=b9EkMc79ZSU', 55),
(3, 1, 'Holly, Jolly', 3, 'https://www.youtube.com/watch?v=b9EkMc79ZSU', 52),
(4, 1, 'El cuerpo', 4, 'https://www.youtube.com/watch?v=b9EkMc79ZSU', 50),
(5, 1, 'La pulga y el acróbata', 5, 'https://www.youtube.com/watch?v=b9EkMc79ZSU', 53),
(6, 5, 'Piloto', 1, 'https://www.youtube.com/watch?v=HhesaQXLuRY', 58),
(7, 5, 'El gato está en la bolsa', 2, 'https://www.youtube.com/watch?v=HhesaQXLuRY', 48),
(8, 5, 'Y la bolsa en el río', 3, 'https://www.youtube.com/watch?v=HhesaQXLuRY', 49),
(9, 6, 'Siete treinta y siete', 1, 'https://www.youtube.com/watch?v=HhesaQXLuRY', 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenidos`
--

CREATE TABLE `contenidos` (
  `id` int NOT NULL,
  `tipo_id` int NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `anio` int DEFAULT NULL,
  `duracion` int DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen_bg` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url_video` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nivel_acceso` int DEFAULT '1',
  `vistas` int DEFAULT '0',
  `destacada` tinyint(1) DEFAULT '0',
  `fecha_agregada` datetime DEFAULT CURRENT_TIMESTAMP,
  `edad_recomendada` int NOT NULL,
  `imdb_rating` decimal(3,1) DEFAULT '0.0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenidos`
--

INSERT INTO `contenidos` (`id`, `tipo_id`, `titulo`, `descripcion`, `anio`, `duracion`, `imagen`, `imagen_bg`, `url_video`, `nivel_acceso`, `vistas`, `destacada`, `fecha_agregada`, `edad_recomendada`, `imdb_rating`) VALUES
(1, 1, 'Avatar: Fire and Ash', 'Tercera entrega...', 2025, 170, 'https://m.media-amazon.com/images/M/MV5BZDYxY2I1OGMtN2Y4MS00ZmU1LTgyNDAtODA0MzAyYjI0N2Y2XkEyXkFqcGc@._V1_.jpg', 'https://media.revistagq.com/photos/61c4ad4459ab05088d9a50e0/16:9/w_2560%2Cc_limit/avatar%25202.jpg', 'https://youtu.be/lhLsr9S3bgQ', 2, 0, 0, '2026-01-08 17:40:01', 12, 0.0),
(2, 1, 'Una película de Minecraft', 'Adaptación del juego...', 2025, 125, 'https://m.media-amazon.com/images/M/MV5BYzFjMzNjOTktNDBlNy00YWZhLWExYTctZDcxNDA4OWVhOTJjXkEyXkFqcGc@._V1_.jpg', 'https://m.media-amazon.com/images/S/pv-target-images/fdf356b8fdbdb136e5b2e2ac41aa037e908e03d2e8f057c403f1c78859df4896.jpg', 'https://youtu.be/iJQs4FPg6jY?si=ePOLmzRLeqTgkk0h', 2, 0, 0, '2026-01-08 17:40:01', 7, 0.0),
(3, 1, 'Dune: Parte Dos', 'Paul Atreides...', 2024, 155, 'https://image.tmdb.org/t/p/original/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg', 'https://wallpaperswide.com/download/dune_part_two_2_2024_movie-wallpaper-5120x2880.jpg', 'https://youtu.be/U2Qp5pL3ovA', 2, 0, 0, '2026-01-08 17:40:01', 12, 0.0),
(4, 1, 'Spider-Man: Across the Spider-Verse', 'Miles Morales...', 2023, 140, 'https://m.media-amazon.com/images/M/MV5BNThiZjA3MjItZGY5Ni00ZmJhLWEwN2EtOTBlYTA4Y2E0M2ZmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://image.tmdb.org/t/p/original/4HodYYKEIsGOdinkGi2Ucz6X9i0.jpg', 'https://www.youtube.com/watch?v=shW9i6k8cB0&pp=ygUXYWNyb3NzIHRoZSBzcGlkZXIgdmVyc2XSBwkJhwoBhyohjO8%3D', 1, 0, 0, '2026-01-08 17:40:01', 7, 0.0),
(5, 1, 'Oppenheimer', 'Historia de la bomba...', 2023, 180, 'https://m.media-amazon.com/images/M/MV5BN2JkMDc5MGQtZjg3YS00NmFiLWIyZmQtZTJmNTM5MjVmYTQ4XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://images.bauerhosting.com/empire/2022/07/oppenheimer-poster-crop.jpg?ar=16%3A9&fit=crop&crop=top&auto=format&w=undefined&q=80', 'https://youtu.be/MVvGSBKV504', 1, 0, 0, '2026-01-08 17:40:01', 16, 0.0),
(6, 2, 'Stranger Things', 'Misterios en Hawkins...', 2016, NULL, 'https://m.media-amazon.com/images/M/MV5BOWU2NjY5NWQtMjdkZi00ODJlLThkZTAtMzFlYmJmMGE2NjZkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://wallpaperswide.com/download/stranger_things_3-wallpaper-5120x2880.jpg', NULL, 2, 0, 0, '2026-01-08 17:40:01', 16, 0.0),
(7, 1, 'Batman :El Caballero Oscuro', 'Batman se enfrenta a su mayor enemigo, el Joker, quien desata el caos en Gotham.', 2008, 152, 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 'https://m.media-amazon.com/images/M/MV5BMTM1NTcwMTk4OV5BMl5BanBnXkFtZTcwOTczMTk2Mw@@._V1_.jpg', NULL, 2, 1500, 1, '2026-01-22 18:34:27', 12, 0.0),
(8, 1, 'Inception', 'Un ladrón que roba secretos corporativos a través del uso de la tecnología de compartir sueños.', 2010, 148, 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg', 'https://image.tmdb.org/t/p/original/s3TBrRGB1iav7gFOCNx3H31MoES.jpg', NULL, 2, 1200, 0, '2026-01-22 18:34:27', 12, 0.0),
(9, 1, 'Pulp Fiction', 'Las vidas de dos mafiosos, un boxeador, la esposa de un gángster y un par de bandidos se entrelazan.', 1994, 154, 'https://m.media-amazon.com/images/M/MV5BYTViYTE3ZGQtNDBlMC00ZTAyLTkyODMtZGRiZDg0MjA2YThkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://wallpaperswide.com/download/pulp_fiction-wallpaper-1280x720.jpg', NULL, 2, 900, 1, '2026-01-22 18:34:27', 18, 0.0),
(10, 2, 'Breaking Bad', 'Un profesor de química con cáncer se convierte en fabricante de metanfetamina.', 2008, NULL, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg', 'https://wallpaperswide.com/download/breaking_bad-wallpaper-2560x1440.jpg', NULL, 2, 5000, 1, '2026-01-22 18:34:27', 16, 0.0),
(11, 2, 'The Mandalorian', 'Las aventuras de un pistolero solitario en los confines de la galaxia.', 2019, NULL, 'https://image.tmdb.org/t/p/w500/sWgBv7LV2PRoQgkxwlibdGXKz1S.jpg', 'https://wallpaperswide.com/download/the_mandalorian-wallpaper-3554x1999.jpg', NULL, 2, 3000, 0, '2026-01-22 18:34:27', 12, 0.0),
(12, 1, 'Joker', 'Arthur Fleck busca su identidad mientras deambula por las calles de Gotham.', 2019, 122, 'https://m.media-amazon.com/images/M/MV5BNzY3OWQ5NDktNWQ2OC00ZjdlLThkMmItMDhhNDk3NTFiZGU4XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://media.revistagq.com/photos/5dad88a910270d00086c7e7f/16:9/w_2191,h_1232,c_limit/joker%20historia%20real%20goetz%20ny.jpeg', NULL, 2, 2200, 0, '2026-01-22 18:34:27', 18, 0.0),
(13, 1, 'Shrek', 'Un ogro hace un trato con un lord para recuperar su pantano.', 2001, 90, 'https://image.tmdb.org/t/p/w500/dyhaB19AICF7TO7CK2aD6KfymnQ.jpg', 'https://wallpaperswide.com/download/shrek_shrek_forever_after_movie-wallpaper-3554x1999.jpg', NULL, 3, 5000, 1, '2026-01-22 18:34:27', 0, 0.0),
(14, 1, 'Matrix', 'Un hacker descubre la verdad sobre su realidad y su papel en la guerra contra las máquinas.', 1999, 136, 'https://m.media-amazon.com/images/M/MV5BN2NmN2VhMTQtMDNiOS00NDlhLTliMjgtODE2ZTY0ODQyNDRhXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://as1.ftcdn.net/jpg/01/76/02/34/1000_F_176023426_fo7EV2AzzvE6FTjh983dhXUTARF4mmaH.jpg', NULL, 1, 1800, 0, '2026-01-22 18:34:27', 12, 0.0),
(15, 1, 'Toy Story', 'Juguetes que cobran vida cuando los humanos no están mirando.', 1995, 81, 'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPrlKpxab.jpg', 'https://m.media-amazon.com/images/S/aplus-media/vc/11cbfc0e-cf1c-4b72-b26e-c7b4467af2e6._CR0,0,970,300_PT0_SX970__.jpg', NULL, 3, 4500, 0, '2026-01-22 18:34:27', 0, 0.0),
(16, 1, 'Interstellar', 'Un equipo de exploradores viaja a través de un agujero de gusano en el espacio.', 2014, 169, 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 'https://image.tmdb.org/t/p/original/rAiYTfKGqDCRIIqo664sY9XZIvQ.jpg', NULL, 2, 2100, 0, '2026-01-22 18:34:27', 12, 0.0),
(17, 1, 'El Rey León', 'Un joven león huye de su reino tras la muerte de su padre.', 1994, 88, 'https://m.media-amazon.com/images/M/MV5BOTk0YjM0YmMtZTNiOC00ZjU5LWEzNmUtNTRiYzAxMTg0MzVkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://m.media-amazon.com/images/M/MV5BMTM2ODAwNTc0NV5BMl5BanBnXkFtZTcwMjQ2NTI3Ng@@._V1_.jpg', NULL, 3, 6000, 1, '2026-01-22 18:34:27', 0, 0.0),
(18, 1, 'Coco', 'Un aspirante a músico se adentra en la Tierra de los Muertos.', 2017, 105, 'https://m.media-amazon.com/images/M/MV5BOGQzNDU3MWEtZjljOS00YWNlLWI1MGUtZDY3YTEwYWMyYmE3XkEyXkFqcGc@._V1_.jpg', 'https://img2.rtve.es/i/?w=1200&i=https://img.rtve.es/imagenes/terror-familia-peliculas-halloween-infantiles-puedes-ver-este-fin-semana-rtve-play/01761906518718.jpg', NULL, 3, 3200, 0, '2026-01-22 18:34:27', 0, 0.0),
(19, 1, 'Los Vengadores', 'Los héroes más poderosos de la Tierra deben unirse y aprender a luchar como equipo.', 2012, 143, 'https://image.tmdb.org/t/p/w500/RYMX2wcKCBAr24UyPD7xwmjaTn.jpg', 'https://wallpaperswide.com/download/the_avengers_4-wallpaper-1920x1080.jpg', NULL, 2, 4100, 1, '2026-01-22 18:34:27', 12, 0.0),
(20, 1, 'Titanic', 'Una joven aristócrata se enamora de un artista pobre a bordo del R.M.S. Titanic.', 1997, 195, 'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg', 'https://cloudfront-us-east-1.images.arcpublishing.com/grupoclarin/V4RELIPUY5EWLAYVSDNN2A23CU.jpg', NULL, 1, 5500, 0, '2026-01-22 18:34:27', 12, 0.0),
(21, 1, 'Forrest Gump', 'Las presidencias de Kennedy y Johnson, Vietnam y Watergate a través de los ojos de Forrest.', 1994, 142, 'https://m.media-amazon.com/images/M/MV5BNDYwNzVjMTItZmU5YS00YjQ5LTljYjgtMjY2NDVmYWMyNWFmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://thumbnails.cbsig.net/CBS_Production_Entertainment_VMS/2024/08/30/2367992899604/PPMOV_FORRESTGUMP_MOVIE_UHD_2784533_1920x1080.jpg', NULL, 1, 3800, 0, '2026-01-22 18:34:27', 12, 0.0),
(22, 1, 'Gladiator', 'Un general romano traicionado busca venganza como gladiador.', 2000, 155, 'https://image.tmdb.org/t/p/w500/ty8TGRuvJLPUmAR1H1nRIsgwvim.jpg', 'https://images.bauerhosting.com/legacy/empire-tmdb/films/98/images/5vZw7ltCKI0JiOYTtRxaIC3DX0e.jpg?ar=16:9&fit=crop&crop=top', NULL, 2, 2900, 0, '2026-01-22 18:34:27', 16, 0.0),
(23, 1, 'Batman', 'Gotham City. Crime boss Carl Grissom (Jack Palance) effectively runs the town but there\'s a new crime fighter in town - Batman (Michael Keaton). Grissom\'s right-hand man is Jack Napier (Jack Nicholson), a brutal man who is not entirely sane... After falling out between the two Grissom has Napier set up with the Police and Napier falls to his apparent death in a vat of chemicals. However, he soon reappears as The Joker and starts a reign of terror in Gotham City. Meanwhile, reporter Vicki Vale (Kim Basinger) is in the city to do an article on Batman. She soon starts a relationship with Batman\'s everyday persona, billionaire Bruce Wayne.', 1989, 126, 'https://m.media-amazon.com/images/M/MV5BYzZmZWViM2EtNzhlMi00NzBlLWE0MWEtZDFjMjk3YjIyNTBhXkEyXkFqcGc@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BYzZmZWViM2EtNzhlMi00NzBlLWE0MWEtZDFjMjk3YjIyNTBhXkEyXkFqcGc@._V1_SX300.jpg', '', 1, 0, 0, '2026-01-26 21:25:53', 12, 0.0),
(24, 1, 'The Super Mario Bros. Movie', 'Brooklyn plumbers Mario and Luigi are warped to the magical Mushroom Kingdom, and Mario must team up with Princess Peach, Toad, and Donkey Kong to save Luigi from the evil Bowser.', 2023, 92, 'https://m.media-amazon.com/images/M/MV5BOGZlN2EzOTYtMzUzOS00NTM3LTg0MTQtZDVjZGM4YmJlNWNhXkEyXkFqcGc@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BOGZlN2EzOTYtMzUzOS00NTM3LTg0MTQtZDVjZGM4YmJlNWNhXkEyXkFqcGc@._V1_SX300.jpg', '', 1, 0, 0, '2026-01-26 22:12:29', 0, 7.1),
(25, 1, 'Avengers: Endgame', 'After the devastating events of Avengers: Infinity War (2018), the universe is in ruins due to the efforts of the Mad Titan, Thanos. With the help of remaining allies, the Avengers must assemble once more in order to undo Thanos\'s actions and undo the chaos to the universe, no matter what consequences may be in store, and no matter who they face...', 2019, 181, 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg', 'https://www.youtube.com/watch?v=UQ3bqYKnyhM', 2, 0, 1, '2026-01-26 22:15:51', 12, 8.4),
(27, 1, 'Spider-Man: Far from Home', 'Nuestro amigable superhéroe del vecindario decide unirse a sus mejores amigos Ned, MJ y el resto de la pandilla en unas vacaciones por Europa. Sin embargo, el plan de Peter de dejar atrás el mundo del superhéroe por unas semanas se ve rápidamente frustrado cuando, a regañadientes, acepta ayudar a Nick Fury a desentrañar el misterio de varios ataques de criaturas elementales que están sembrando el caos en todo el continente.', 2019, 129, 'https://m.media-amazon.com/images/M/MV5BMzNhNTE0NWQtN2E1Ny00NjcwLTg1YTctMGY1NmMwODJmY2NmXkEyXkFqcGc@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BMzNhNTE0NWQtN2E1Ny00NjcwLTg1YTctMGY1NmMwODJmY2NmXkEyXkFqcGc@._V1_SX300.jpg', 'https://www.youtube.com/watch?v=dAxa7emR1Vc', 1, 0, 0, '2026-02-03 08:24:01', 12, 7.3),
(28, 1, 'The Incredible Hulk', 'Depicting the events after the Gamma Bomb. \'The Incredible Hulk\' tells the story of Dr Bruce Banner, who seeks a cure to his unique condition, which causes him to turn into a giant green monster under emotional stress. Whilst on the run from military which seeks his capture, Banner comes close to a cure. But all is lost when a new creature emerges: The Abomination.', 2008, 112, 'https://m.media-amazon.com/images/M/MV5BMTUyNzk3MjA1OF5BMl5BanBnXkFtZTcwMTE1Njg2MQ@@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BMTUyNzk3MjA1OF5BMl5BanBnXkFtZTcwMTE1Njg2MQ@@._V1_SX300.jpg', '', 1, 0, 0, '2026-02-03 11:43:39', 12, 6.6),
(29, 1, 'Iron Man 2', 'With the world now aware of his dual life as the armored superhero Iron Man, billionaire inventor Tony Stark faces pressure from the government, the press, and the public to share his technology with the military. Unwilling to let go of his invention, Stark, along with Pepper Potts, and James \"Rhodey\" Rhodes at his side, must forge new alliances - and confront powerful enemies.', 2010, 124, 'https://m.media-amazon.com/images/M/MV5BYWYyOGQzOGYtMGQ1My00ZWYxLTgzZjktZWYzN2IwYjkxYzM0XkEyXkFqcGc@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BYWYyOGQzOGYtMGQ1My00ZWYxLTgzZjktZWYzN2IwYjkxYzM0XkEyXkFqcGc@._V1_SX300.jpg', '', 2, 0, 0, '2026-02-03 11:45:02', 12, 6.9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_actor`
--

CREATE TABLE `contenido_actor` (
  `contenido_id` int NOT NULL,
  `actor_id` int NOT NULL,
  `personaje` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido_actor`
--

INSERT INTO `contenido_actor` (`contenido_id`, `actor_id`, `personaje`) VALUES
(1, 1, 'Jake Sully'),
(1, 2, 'Neytiri'),
(2, 3, 'Steve'),
(2, 4, 'Desconocido'),
(3, 5, 'Paul Atreides'),
(3, 6, 'Chani'),
(4, 7, 'Miles Morales'),
(4, 8, 'Gwen Stacy'),
(5, 9, 'J. Robert Oppenheimer'),
(5, 10, 'Lewis Strauss'),
(6, 11, 'Eleven'),
(6, 12, 'Mike Wheeler'),
(6, 13, 'Joyce Byers'),
(7, 15, 'Bruce Wayne'),
(7, 16, 'Joker'),
(8, 14, 'Cobb'),
(9, 14, 'Rick Dalton (Cameo)'),
(10, 18, 'Walter White'),
(10, 19, 'Jesse Pinkman'),
(11, 20, 'The Mandalorian'),
(12, 24, 'Arthur Fleck'),
(13, 25, 'Shrek (Voz)'),
(13, 26, 'Burro (Voz)'),
(14, 21, 'Neo'),
(15, 22, 'Woody (Voz)'),
(20, 14, 'Jack Dawson'),
(21, 22, 'Forrest Gump'),
(22, 24, 'Comodus'),
(24, 27, ''),
(24, 28, ''),
(24, 29, ''),
(25, 10, ''),
(25, 30, ''),
(25, 31, ''),
(27, 32, ''),
(27, 33, ''),
(27, 34, ''),
(28, 35, ''),
(28, 36, ''),
(28, 37, ''),
(29, 10, ''),
(29, 38, ''),
(29, 39, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_director`
--

CREATE TABLE `contenido_director` (
  `contenido_id` int NOT NULL,
  `director_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido_director`
--

INSERT INTO `contenido_director` (`contenido_id`, `director_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 7),
(16, 7),
(9, 8),
(10, 9),
(12, 10),
(14, 11),
(13, 12),
(24, 13),
(24, 14),
(24, 15),
(25, 16),
(25, 17),
(27, 18),
(28, 19),
(29, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_genero`
--

CREATE TABLE `contenido_genero` (
  `contenido_id` int NOT NULL,
  `genero_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido_genero`
--

INSERT INTO `contenido_genero` (`contenido_id`, `genero_id`) VALUES
(1, 1),
(3, 1),
(4, 1),
(7, 1),
(8, 1),
(14, 1),
(19, 1),
(22, 1),
(23, 1),
(25, 1),
(27, 1),
(28, 1),
(29, 1),
(1, 2),
(2, 2),
(4, 2),
(11, 2),
(15, 2),
(23, 2),
(24, 2),
(25, 2),
(27, 2),
(28, 2),
(1, 3),
(3, 3),
(4, 3),
(6, 3),
(8, 3),
(11, 3),
(14, 3),
(16, 3),
(19, 3),
(25, 3),
(28, 3),
(29, 3),
(3, 4),
(5, 4),
(6, 4),
(9, 4),
(10, 4),
(12, 4),
(16, 4),
(17, 4),
(20, 4),
(21, 4),
(22, 4),
(13, 5),
(15, 5),
(17, 5),
(18, 5),
(24, 5),
(7, 6),
(9, 6),
(10, 6),
(12, 6),
(13, 7),
(24, 7),
(27, 7),
(20, 9),
(21, 9),
(18, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_idioma`
--

CREATE TABLE `contenido_idioma` (
  `contenido_id` int NOT NULL,
  `idioma_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido_idioma`
--

INSERT INTO `contenido_idioma` (`contenido_id`, `idioma_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `directores`
--

CREATE TABLE `directores` (
  `id` int NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `directores`
--

INSERT INTO `directores` (`id`, `nombre`, `foto`) VALUES
(1, 'James Cameron', 'james_cameron.jpg'),
(2, 'Jared Hess', 'jared_hess.jpg'),
(3, 'Denis Villeneuve', 'denis_villeneuve.jpg'),
(4, 'Joaquim Dos Santos', 'joaquim.jpg'),
(5, 'Christopher Nolan', 'nolan.jpg'),
(6, 'Hermanos Duffer', 'duffer.jpg'),
(7, 'Christopher Nolan', 'https://image.tmdb.org/t/p/w500/xuAIuYSmsUzKlUMBFGVZaWsY3VD.jpg'),
(8, 'Quentin Tarantino', 'https://image.tmdb.org/t/p/w500/1gjcpAa99FAjJsReICq8PPS5FA2.jpg'),
(9, 'Vince Gilligan', 'https://image.tmdb.org/t/p/w500/z3E0hjmI39yV35uY5q88jX2X1J.jpg'),
(10, 'Todd Phillips', 'https://image.tmdb.org/t/p/w500/A1c1j8F6Q9z1Z1a1f1.jpg'),
(11, 'Lana Wachowski', 'https://image.tmdb.org/t/p/w500/z3E0hjmI39yV35uY5q88jX2X1J.jpg'),
(12, 'Andrew Adamson', 'https://image.tmdb.org/t/p/w500/z3E0hjmI39yV35uY5q88jX2X1J.jpg'),
(13, 'Aaron Horvath', 'https://ui-avatars.com/api/?name=Aaron+Horvath&background=random'),
(14, 'Michael Jelenic', 'https://ui-avatars.com/api/?name=Michael+Jelenic&background=random'),
(15, 'Pierre Leduc', 'https://ui-avatars.com/api/?name=Pierre+Leduc&background=random'),
(16, 'Anthony Russo', 'https://ui-avatars.com/api/?name=Anthony+Russo&background=random'),
(17, 'Joe Russo', 'https://ui-avatars.com/api/?name=Joe+Russo&background=random'),
(18, 'Jon Watts', 'https://ui-avatars.com/api/?name=Jon+Watts&background=random'),
(19, 'Louis Leterrier', 'https://ui-avatars.com/api/?name=Louis+Leterrier&background=random'),
(20, 'Jon Favreau', 'https://ui-avatars.com/api/?name=Jon+Favreau&background=random');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `generos`
--

CREATE TABLE `generos` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `generos`
--

INSERT INTO `generos` (`id`, `nombre`) VALUES
(1, 'Acción'),
(2, 'Aventura'),
(3, 'Ciencia Ficción'),
(4, 'Drama'),
(5, 'Animación'),
(6, 'Crimen'),
(7, 'Comedia'),
(8, 'Thriller'),
(9, 'Romance'),
(10, 'Fantasía'),
(11, 'Comedia Española');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `idiomas`
--

CREATE TABLE `idiomas` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `codigo` varchar(5) COLLATE utf8mb4_general_ci NOT NULL
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
  `usuario_id` int NOT NULL,
  `contenido_id` int NOT NULL,
  `fecha_agregado` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mi_lista`
--

INSERT INTO `mi_lista` (`usuario_id`, `contenido_id`, `fecha_agregado`) VALUES
(2, 14, '2026-02-03 07:43:58'),
(2, 23, '2026-02-03 07:44:09'),
(3, 2, '2026-01-22 16:09:36'),
(3, 3, '2026-01-21 17:46:21'),
(3, 4, '2026-01-22 16:02:41'),
(3, 10, '2026-02-03 08:02:15'),
(3, 13, '2026-02-05 11:35:20'),
(3, 14, '2026-02-03 09:30:46'),
(3, 20, '2026-02-03 07:51:13'),
(3, 24, '2026-02-03 07:37:35'),
(4, 13, '2026-01-22 23:09:23'),
(4, 15, '2026-01-22 23:09:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `calidad` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'HD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id`, `nombre`, `precio`, `calidad`) VALUES
(1, 'Free', 0.00, '720p'),
(2, 'Premium', 9.99, '4K Ultra HD'),
(3, 'Kids', 4.99, 'HD');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `contenido_id` int NOT NULL,
  `puntuacion` int NOT NULL,
  `comentario` text COLLATE utf8mb4_general_ci,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporadas`
--

CREATE TABLE `temporadas` (
  `id` int NOT NULL,
  `contenido_id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temporadas`
--

INSERT INTO `temporadas` (`id`, `contenido_id`, `nombre`, `numero`) VALUES
(1, 6, 'Temporada 1', 1),
(2, 6, 'Temporada 2', 2),
(3, 6, 'Temporada 3', 3),
(4, 6, 'Temporada 4', 4),
(5, 10, 'Temporada 1', 1),
(6, 10, 'Temporada 2', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_contenido`
--

CREATE TABLE `tipos_contenido` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_contenido`
--

INSERT INTO `tipos_contenido` (`id`, `nombre`) VALUES
(1, 'Película'),
(2, 'Serie');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('admin','usuario') COLLATE utf8mb4_general_ci DEFAULT 'usuario',
  `plan_id` int DEFAULT '1',
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'default.png',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `password`, `rol`, `plan_id`, `avatar`, `fecha_registro`) VALUES
(1, 'Admin', 'admin@labutaca.com', '$2y$10$NWRxl6ZoScpFC8lckYI7O.WyAdnbAFUox/xSwYa64T5jLoslIDlCq', 'admin', 2, 'avatar_admin.jpg', '2026-01-21 14:29:51'),
(2, 'FreeUser', 'invitado@labutaca.com', '$2y$10$NWRxl6ZoScpFC8lckYI7O.WyAdnbAFUox/xSwYa64T5jLoslIDlCq', 'usuario', 1, 'https://images.ecestaticos.com/an3NIKmUWjvoxIwgQ4K3J0pqAqo=/0x0:828x470/1200x1200/filters:fill(white):format(jpg)/f.elconfidencial.com%2Foriginal%2F280%2F050%2F590%2F2800505903fc39bbeaf82f750b823ec3.jpg', '2026-01-21 14:29:51'),
(3, 'PremiumUser', 'prueba@labutaca.com', '$2y$10$NWRxl6ZoScpFC8lckYI7O.WyAdnbAFUox/xSwYa64T5jLoslIDlCq', 'usuario', 2, 'https://upload.wikimedia.org/wikipedia/en/9/90/HeathJoker.png', '2026-01-21 15:53:11'),
(4, 'KidsUser', 'peque@labutaca.com', '$2y$10$NWRxl6ZoScpFC8lckYI7O.WyAdnbAFUox/xSwYa64T5jLoslIDlCq', 'usuario', 3, 'https://media.revistagq.com/photos/62a8546d6b74c0e2031238a6/16:9/w_1280,c_limit/buzz.jpg', '2026-01-22 11:35:40'),
(6, 'pruebanueva', 'pruebanueva', '1234', 'admin', 2, 'default.png', '2026-01-26 21:25:17');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `capitulos`
--
ALTER TABLE `capitulos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `contenidos`
--
ALTER TABLE `contenidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `directores`
--
ALTER TABLE `directores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `generos`
--
ALTER TABLE `generos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `idiomas`
--
ALTER TABLE `idiomas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipos_contenido`
--
ALTER TABLE `tipos_contenido`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
