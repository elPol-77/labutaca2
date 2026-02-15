-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-02-2026 a las 14:11:34
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
(39, 'Gwyneth Paltrow', 'https://ui-avatars.com/api/?name=Gwyneth+Paltrow&background=random'),
(40, 'Robert Pattinson', 'https://image.tmdb.org/t/p/original/8A4PS5iG7GWEAVFftyqMZKl3qcr.jpg'),
(41, 'Zoë Kravitz', 'https://image.tmdb.org/t/p/original/iRsOS82q5WRAZBquTgtgzDQf01S.jpg'),
(42, 'Jeffrey Wright', 'https://image.tmdb.org/t/p/original/yGcuHGW4glqRpOPxgiCvjcren7F.jpg'),
(43, 'Colin Farrell', 'https://image.tmdb.org/t/p/original/oyQVfpowL9eSudYhwoPGkd9WxS7.jpg'),
(44, 'Paul Dano', 'https://image.tmdb.org/t/p/original/zEJJsm0z07EPNl2Pi1h67xuCmcA.jpg'),
(45, 'John Turturro', 'https://image.tmdb.org/t/p/original/6O9W9cJW0kCqMzYeLupV9oH0ftn.jpg'),
(46, 'Andy Serkis', 'https://image.tmdb.org/t/p/original/eNGqhebQ4cDssjVeNFrKtUvweV5.jpg'),
(47, 'Peter Sarsgaard', 'https://image.tmdb.org/t/p/original/5UANyM4co2nwYPgSEmGeNlZRm7V.jpg'),
(48, 'Barry Keoghan', 'https://image.tmdb.org/t/p/original/ngoitknM6hw8fffLywyvjzy6Iti.jpg'),
(49, 'Jayme Lawson', 'https://image.tmdb.org/t/p/original/ntwbhdUeW8K5KYJENqyiSZcsIj9.jpg'),
(50, 'Gil Perez-Abraham', 'https://image.tmdb.org/t/p/original/obJ2pGNaRJ8mtIEPshw3ocIfcIj.jpg'),
(51, 'Peter McDonald', 'https://image.tmdb.org/t/p/original/ahacAWWP4zzuVumOUB8hZoJcHTA.jpg'),
(52, 'Con O\'Neill', 'https://image.tmdb.org/t/p/original/7Fnj1dB9kStTuy29eEVK4IuOxWO.jpg'),
(53, 'Alex Ferns', 'https://image.tmdb.org/t/p/original/3V3L7MJGURXU6lVaqai80zFT4Wa.jpg'),
(54, 'Rupert Penry-Jones', 'https://image.tmdb.org/t/p/original/50mfkdU5TY8nHuz7vXCz60ywg2c.jpg'),
(55, 'Sigourney Weaver', 'https://image.tmdb.org/t/p/original/wTSnfktNBLd6kwQxgvkqYw6vEon.jpg'),
(56, 'Stephen Lang', 'https://image.tmdb.org/t/p/original/gnO5VfkDgA2fsHweD0622LUY3Hu.jpg'),
(57, 'Michelle Rodríguez', 'https://image.tmdb.org/t/p/original/wVcbrae4eRqGMFZz8Eh52Dl1biP.jpg'),
(58, 'Giovanni Ribisi', 'https://image.tmdb.org/t/p/original/8EAiS9D3YtGOrwNM0OrwmDpWK7s.jpg'),
(59, 'Joel David Moore', 'https://image.tmdb.org/t/p/original/mMVhVglj6BZFuvqAXnEibce08k7.jpg'),
(60, 'CCH Pounder', 'https://image.tmdb.org/t/p/original/mr6BLDN75T8DJl9dNK3hN4YwqJa.jpg'),
(61, 'Wes Studi', 'https://image.tmdb.org/t/p/original/dAO2bY2NCceYLZiJaBjBdY19Hox.jpg'),
(62, 'Laz Alonso', 'https://image.tmdb.org/t/p/original/nmgOd3X2Xn3jIp9OLCRJzLExRWN.jpg'),
(63, 'Dileep Rao', 'https://image.tmdb.org/t/p/original/jRNn8SZqFXuI5wOOlHwYsWh0hXs.jpg'),
(64, 'Matt Gerald', 'https://image.tmdb.org/t/p/original/six7c3aZpiEomHjj4ZYokq8Vgtx.jpg'),
(65, 'Sean Anthony Moran', 'https://image.tmdb.org/t/p/original/9bsNN91o9iNVLK3W7js99kvg26L.jpg'),
(66, 'Jason Whyte', 'https://image.tmdb.org/t/p/original/lCZ5Z3RJG6BxP05FPf7kCoQGZdI.jpg'),
(67, 'Scott Lawrence', 'https://image.tmdb.org/t/p/original/8lwnmlHN6QPK75NwfufsIFZZhmB.jpg'),
(68, 'David Harbour', 'https://image.tmdb.org/t/p/original/qMFtMWlYVtFVyBoBhX5IoA5sN5a.jpg'),
(69, 'Gaten Matarazzo', 'https://image.tmdb.org/t/p/original/alVT7oDp8N5G9WLIApI9jqeuqHq.jpg'),
(70, 'Caleb McLaughlin', 'https://image.tmdb.org/t/p/original/4jVS3EziBn7bf97ErxkW7jsdiLM.jpg'),
(71, 'Noah Schnapp', 'https://image.tmdb.org/t/p/original/6NIffxe2yZm6OmOwulY19govKRs.jpg'),
(72, 'Sadie Sink', 'https://image.tmdb.org/t/p/original/o6DZF6dgQjLC96qEqCw23AoY2HN.jpg'),
(73, 'Natalia Dyer', 'https://image.tmdb.org/t/p/original/bOe1IMrtMYjXhtsBW19M4vypLiT.jpg'),
(74, 'Charlie Heaton', 'https://image.tmdb.org/t/p/original/8Se6WZuvRmoB990bT29OPgVAyBo.jpg'),
(75, 'Joe Keery', 'https://image.tmdb.org/t/p/original/ayIAVLMfZGEGIFwAo3pPnY7p59.jpg'),
(76, 'Maya Hawke', 'https://image.tmdb.org/t/p/original/9jX7HdSHocuZxOrzDCEC49qy9po.jpg'),
(77, 'Brett Gelman', 'https://image.tmdb.org/t/p/original/ub2IuMWFNQGYghHTPq0lpmn2Ue0.jpg'),
(78, 'Priah Ferguson', 'https://image.tmdb.org/t/p/original/cDk4YhC46OFdNz0sDhUjllKer7d.jpg'),
(79, 'Cara Buono', 'https://image.tmdb.org/t/p/original/QQv5pitEXvrGR4sgECvNDLwhTG.jpg'),
(80, 'Tom Hiddleston', 'https://image.tmdb.org/t/p/original/mclHxMm8aPlCPKptP67257F5GPo.jpg'),
(81, 'Sophia Di Martino', 'https://image.tmdb.org/t/p/original/qZdFp18btpQJfDoknxr7DgfRpcB.jpg'),
(82, 'Wunmi Mosaku', 'https://image.tmdb.org/t/p/original/mWDsVCo9sBcekrsjUTsoCFLhtYt.jpg'),
(83, 'Eugene Cordero', 'https://image.tmdb.org/t/p/original/waruLSR8lXBjhAFL0J6ihuVY62d.jpg'),
(84, 'Ke Huy Quan', 'https://image.tmdb.org/t/p/original/iestHyn7PLuVowj5Jaa1SGPboQ4.jpg'),
(85, 'Owen Wilson', 'https://image.tmdb.org/t/p/original/lFxHIlcywMlYpe6wkW39sfBrqag.jpg'),
(86, 'William Hurt', 'https://image.tmdb.org/t/p/original/j3mjmuHLBW4XQSw53C8Sh0Lh3ZQ.jpg'),
(87, 'Tim Blake Nelson', 'https://image.tmdb.org/t/p/original/rWuTGiAMaaHIJ30eRkQS23LbRSW.jpg'),
(88, 'Ty Burrell', 'https://image.tmdb.org/t/p/original/zXrrbvW2ZKHYHbhujDj8aBlO4yx.jpg'),
(89, 'Christina Cabot', 'https://image.tmdb.org/t/p/original/h1vwbOfITSvDvDq8E9MVvWqMYSr.jpg'),
(90, 'Peter Mensah', 'https://image.tmdb.org/t/p/original/t94TFc6f71AUmZFqdaQfjr7LTRp.jpg'),
(91, 'Lou Ferrigno', 'https://image.tmdb.org/t/p/original/obTtRrm8EbDzuiKpLjAd9s1i9v5.jpg'),
(92, 'Paul Soles', 'https://image.tmdb.org/t/p/original/96fdxpuz3gUUMWhvpPQXrsiUGmh.jpg'),
(93, 'Débora Nascimento', 'https://image.tmdb.org/t/p/original/W0chjQKQbMRBe3WQTfPWUS6D4g.jpg'),
(94, 'Greg Bryk', 'https://image.tmdb.org/t/p/original/1I3SxKFvQSam6KOMT4j5f0nFxRg.jpg'),
(95, 'Chris Owens', 'https://image.tmdb.org/t/p/original/hT55s8UvUyzqzaf4Y7bXQgbjiY7.jpg'),
(96, 'Al Vrkljan', 'https://image.tmdb.org/t/p/original/xDIZmQ1FfHQKhXbXkV87coOOfy6.jpg'),
(97, 'Adrian Hein', 'https://ui-avatars.com/api/?name=Adrian+Hein&background=random'),
(98, 'Don Cheadle', 'https://image.tmdb.org/t/p/original/b1EVJWdFn7a75qVYJgwO87W2TJU.jpg'),
(99, 'Sam Rockwell', 'https://image.tmdb.org/t/p/original/afYhNpLwpa65Yy0Q0g00FNFhzx5.jpg'),
(100, 'Clark Gregg', 'https://image.tmdb.org/t/p/original/mq686D91XoZpqkzELn0888NOiZW.jpg'),
(101, 'John Slattery', 'https://image.tmdb.org/t/p/original/tm7g84OWD5gQRoyfG4hduBphP1j.jpg'),
(102, 'Garry Shandling', 'https://image.tmdb.org/t/p/original/zGjPMqSqtZtP3npd5fhm7MYqxIU.jpg'),
(103, 'Paul Bettany', 'https://image.tmdb.org/t/p/original/vcAVrAOZrpqmi37qjFdztRAv1u9.jpg'),
(104, 'Kate Mara', 'https://image.tmdb.org/t/p/original/xZYD8wYHMmN9dMMPiohnKEFoTGx.jpg'),
(105, 'Leslie Bibb', 'https://image.tmdb.org/t/p/original/g3a1O9lOTZvrwQupUtg4Fc3CdTd.jpg'),
(106, 'Jon Favreau', 'https://image.tmdb.org/t/p/original/tnx7iMVydPQXGOoLsxXl84PXtbA.jpg'),
(107, 'Christiane Amanpour', 'https://image.tmdb.org/t/p/original/48JaEwyIxaGEi39JrEq6gORfXsX.jpg'),
(108, 'Keegan-Michael Key', 'https://image.tmdb.org/t/p/original/vAR5gVXRG2Cl6WskXT99wgkAoH8.jpg'),
(109, 'Seth Rogen', 'https://image.tmdb.org/t/p/original/nYl9bvQzaPQLzlf0wf75clLN6Hi.jpg'),
(110, 'Fred Armisen', 'https://image.tmdb.org/t/p/original/lQ9AEIjbVcSeJ6THEJk72CMU5Qs.jpg'),
(111, 'Sebastian Maniscalco', 'https://image.tmdb.org/t/p/original/8TvA9HEwURJmY9MkkUruB4Sl0lR.jpg'),
(112, 'Charles Martinet', 'https://image.tmdb.org/t/p/original/tNlPyVRMTM7PICLJsLY8TfOClzE.jpg'),
(113, 'Kevin Michael Richardson', 'https://image.tmdb.org/t/p/original/xXt9Nh7RAT5bOen66TaXreNYmCl.jpg'),
(114, 'Khary Payton', 'https://image.tmdb.org/t/p/original/4PgEGuAb2KkaRb7P9PdK40pPeVH.jpg'),
(115, 'Rino Romano', 'https://image.tmdb.org/t/p/original/rpyq7xdQ5zSCHYiwnlPvkx7gLrl.jpg'),
(116, 'John DiMaggio', 'https://image.tmdb.org/t/p/original/qcbQe71nSlULDsP1OxTqltEKFbl.jpg'),
(117, 'Jessica DiCicco', 'https://image.tmdb.org/t/p/original/1Cp0PFtup0mlsecGyfImzd4LDiL.jpg'),
(118, 'Eric Bauza', 'https://image.tmdb.org/t/p/original/afOlsVPQxbtkom604MeCemjlwEV.jpg'),
(119, 'Junko Takeuchi', 'https://image.tmdb.org/t/p/original/zNjrblq3xS1idpCsmSl5P5eTon7.jpg'),
(120, 'Mamoru Miyano', 'https://image.tmdb.org/t/p/original/nuok8ueG7k9hPZ09Tpr8e7Qn0ah.jpg'),
(121, 'Hiroyuki Yoshino', 'https://image.tmdb.org/t/p/original/nZQbjjvAjPpjGYenY7kLDtW9e7N.jpg'),
(122, 'Hiroshi Kamiya', 'https://image.tmdb.org/t/p/original/5aAAOlQ2hW5EqEQOzk4UeroxcTN.jpg'),
(123, 'Hirofumi Nojima', 'https://image.tmdb.org/t/p/original/sKj8nGVxv4hci3xuZgiFYPZvaX5.jpg');

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
  `id` int(11) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `imagen_bg` varchar(500) DEFAULT NULL,
  `url_video` varchar(500) DEFAULT NULL,
  `nivel_acceso` int(11) DEFAULT 1,
  `vistas` int(11) DEFAULT 0,
  `destacada` tinyint(1) DEFAULT 0,
  `fecha_agregada` datetime DEFAULT current_timestamp(),
  `edad_recomendada` int(11) NOT NULL,
  `imdb_rating` decimal(3,1) DEFAULT 0.0,
  `imdb_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenidos`
--

INSERT INTO `contenidos` (`id`, `tipo_id`, `titulo`, `descripcion`, `anio`, `duracion`, `imagen`, `imagen_bg`, `url_video`, `nivel_acceso`, `vistas`, `destacada`, `fecha_agregada`, `edad_recomendada`, `imdb_rating`, `imdb_id`) VALUES
(1, 1, 'Avatar: Fuego y ceniza', 'Tercera entrega...', 2025, 170, 'https://m.media-amazon.com/images/M/MV5BZDYxY2I1OGMtN2Y4MS00ZmU1LTgyNDAtODA0MzAyYjI0N2Y2XkEyXkFqcGc@._V1_.jpg', 'https://media.revistagq.com/photos/61c4ad4459ab05088d9a50e0/16:9/w_2560%2Cc_limit/avatar%25202.jpg', 'https://youtu.be/lhLsr9S3bgQ', 2, 0, 0, '2026-01-08 17:40:01', 12, 0.0, NULL),
(2, 1, 'Una película de Minecraft', 'Adaptación del juego...', 2025, 125, 'https://m.media-amazon.com/images/M/MV5BYzFjMzNjOTktNDBlNy00YWZhLWExYTctZDcxNDA4OWVhOTJjXkEyXkFqcGc@._V1_.jpg', 'https://m.media-amazon.com/images/S/pv-target-images/fdf356b8fdbdb136e5b2e2ac41aa037e908e03d2e8f057c403f1c78859df4896.jpg', 'https://youtu.be/iJQs4FPg6jY?si=ePOLmzRLeqTgkk0h', 2, 0, 0, '2026-01-08 17:40:01', 7, 0.0, NULL),
(3, 1, 'Dune: Parte Dos', 'Paul Atreides...', 2024, 155, 'https://image.tmdb.org/t/p/original/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg', 'https://wallpaperswide.com/download/dune_part_two_2_2024_movie-wallpaper-5120x2880.jpg', 'https://youtu.be/U2Qp5pL3ovA', 2, 0, 0, '2026-01-08 17:40:01', 12, 0.0, NULL),
(4, 1, 'Spider-Man: Across the Spider-Verse', 'Miles Morales...', 2023, 140, 'https://m.media-amazon.com/images/M/MV5BNThiZjA3MjItZGY5Ni00ZmJhLWEwN2EtOTBlYTA4Y2E0M2ZmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://image.tmdb.org/t/p/original/4HodYYKEIsGOdinkGi2Ucz6X9i0.jpg', 'https://www.youtube.com/watch?v=shW9i6k8cB0&pp=ygUXYWNyb3NzIHRoZSBzcGlkZXIgdmVyc2XSBwkJhwoBhyohjO8%3D', 1, 0, 0, '2026-01-08 17:40:01', 7, 0.0, NULL),
(5, 1, 'Oppenheimer', 'Historia de la bomba...', 2023, 180, 'https://m.media-amazon.com/images/M/MV5BN2JkMDc5MGQtZjg3YS00NmFiLWIyZmQtZTJmNTM5MjVmYTQ4XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://images.bauerhosting.com/empire/2022/07/oppenheimer-poster-crop.jpg?ar=16%3A9&fit=crop&crop=top&auto=format&w=undefined&q=80', 'https://youtu.be/MVvGSBKV504', 1, 0, 0, '2026-01-08 17:40:01', 16, 0.0, NULL),
(6, 2, 'Stranger Things', 'Misterios en Hawkins...', 2016, NULL, 'https://m.media-amazon.com/images/M/MV5BOWU2NjY5NWQtMjdkZi00ODJlLThkZTAtMzFlYmJmMGE2NjZkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://wallpaperswide.com/download/stranger_things_3-wallpaper-5120x2880.jpg', NULL, 2, 0, 0, '2026-01-08 17:40:01', 16, 0.0, NULL),
(7, 1, 'Batman :El Caballero Oscuro', 'Batman se enfrenta a su mayor enemigo, el Joker, quien desata el caos en Gotham.', 2008, 152, 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 'https://m.media-amazon.com/images/M/MV5BMTM1NTcwMTk4OV5BMl5BanBnXkFtZTcwOTczMTk2Mw@@._V1_.jpg', NULL, 2, 1500, 1, '2026-01-22 18:34:27', 12, 0.0, NULL),
(8, 1, 'Inception', 'Un ladrón que roba secretos corporativos a través del uso de la tecnología de compartir sueños.', 2010, 148, 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg', 'https://image.tmdb.org/t/p/original/s3TBrRGB1iav7gFOCNx3H31MoES.jpg', NULL, 2, 1200, 0, '2026-01-22 18:34:27', 12, 0.0, NULL),
(9, 1, 'Pulp Fiction', 'Las vidas de dos mafiosos, un boxeador, la esposa de un gángster y un par de bandidos se entrelazan.', 1994, 154, 'https://m.media-amazon.com/images/M/MV5BYTViYTE3ZGQtNDBlMC00ZTAyLTkyODMtZGRiZDg0MjA2YThkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://wallpaperswide.com/download/pulp_fiction-wallpaper-1280x720.jpg', NULL, 2, 900, 1, '2026-01-22 18:34:27', 18, 0.0, NULL),
(10, 2, 'Breaking Bad', 'Un profesor de química con cáncer se convierte en fabricante de metanfetamina.', 2008, NULL, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg', 'https://wallpaperswide.com/download/breaking_bad-wallpaper-2560x1440.jpg', NULL, 2, 5000, 1, '2026-01-22 18:34:27', 16, 0.0, NULL),
(11, 2, 'The Mandalorian', 'Las aventuras de un pistolero solitario en los confines de la galaxia.', 2019, NULL, 'https://image.tmdb.org/t/p/w500/sWgBv7LV2PRoQgkxwlibdGXKz1S.jpg', 'https://wallpaperswide.com/download/the_mandalorian-wallpaper-3554x1999.jpg', NULL, 1, 3000, 0, '2026-01-22 18:34:27', 12, 0.0, NULL),
(12, 1, 'Joker', 'Arthur Fleck busca su identidad mientras deambula por las calles de Gotham.', 2019, 122, 'https://m.media-amazon.com/images/M/MV5BNzY3OWQ5NDktNWQ2OC00ZjdlLThkMmItMDhhNDk3NTFiZGU4XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://media.revistagq.com/photos/5dad88a910270d00086c7e7f/16:9/w_2191,h_1232,c_limit/joker%20historia%20real%20goetz%20ny.jpeg', NULL, 2, 2200, 0, '2026-01-22 18:34:27', 18, 0.0, NULL),
(13, 1, 'Shrek', 'Un ogro hace un trato con un lord para recuperar su pantano.', 2001, 90, 'https://image.tmdb.org/t/p/w500/dyhaB19AICF7TO7CK2aD6KfymnQ.jpg', 'https://wallpaperswide.com/download/shrek_shrek_forever_after_movie-wallpaper-3554x1999.jpg', NULL, 3, 5000, 1, '2026-01-22 18:34:27', 0, 0.0, NULL),
(14, 1, 'Matrix', 'Un hacker descubre la verdad sobre su realidad y su papel en la guerra contra las máquinas.', 1999, 136, 'https://m.media-amazon.com/images/M/MV5BN2NmN2VhMTQtMDNiOS00NDlhLTliMjgtODE2ZTY0ODQyNDRhXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://as1.ftcdn.net/jpg/01/76/02/34/1000_F_176023426_fo7EV2AzzvE6FTjh983dhXUTARF4mmaH.jpg', NULL, 1, 1800, 0, '2026-01-22 18:34:27', 12, 0.0, NULL),
(15, 1, 'Toy Story', 'Juguetes que cobran vida cuando los humanos no están mirando.', 1995, 81, 'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPrlKpxab.jpg', 'https://m.media-amazon.com/images/S/aplus-media/vc/11cbfc0e-cf1c-4b72-b26e-c7b4467af2e6._CR0,0,970,300_PT0_SX970__.jpg', NULL, 3, 4500, 0, '2026-01-22 18:34:27', 0, 0.0, NULL),
(16, 1, 'Interstellar', 'Un equipo de exploradores viaja a través de un agujero de gusano en el espacio.', 2014, 169, 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 'https://image.tmdb.org/t/p/original/rAiYTfKGqDCRIIqo664sY9XZIvQ.jpg', NULL, 2, 2100, 0, '2026-01-22 18:34:27', 12, 0.0, NULL),
(17, 1, 'El Rey León', 'Un joven león huye de su reino tras la muerte de su padre.', 1994, 88, 'https://m.media-amazon.com/images/M/MV5BOTk0YjM0YmMtZTNiOC00ZjU5LWEzNmUtNTRiYzAxMTg0MzVkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://m.media-amazon.com/images/M/MV5BMTM2ODAwNTc0NV5BMl5BanBnXkFtZTcwMjQ2NTI3Ng@@._V1_.jpg', NULL, 3, 6000, 1, '2026-01-22 18:34:27', 0, 0.0, NULL),
(18, 1, 'Coco', 'Un aspirante a músico se adentra en la Tierra de los Muertos.', 2017, 105, 'https://m.media-amazon.com/images/M/MV5BOGQzNDU3MWEtZjljOS00YWNlLWI1MGUtZDY3YTEwYWMyYmE3XkEyXkFqcGc@._V1_.jpg', 'https://img2.rtve.es/i/?w=1200&i=https://img.rtve.es/imagenes/terror-familia-peliculas-halloween-infantiles-puedes-ver-este-fin-semana-rtve-play/01761906518718.jpg', NULL, 3, 3200, 0, '2026-01-22 18:34:27', 0, 0.0, NULL),
(19, 1, 'Los Vengadores', 'Los héroes más poderosos de la Tierra deben unirse y aprender a luchar como equipo.', 2012, 143, 'https://image.tmdb.org/t/p/w500/RYMX2wcKCBAr24UyPD7xwmjaTn.jpg', 'https://wallpaperswide.com/download/the_avengers_4-wallpaper-1920x1080.jpg', NULL, 2, 4100, 1, '2026-01-22 18:34:27', 12, 0.0, NULL),
(20, 1, 'Titanic', 'Una joven aristócrata se enamora de un artista pobre a bordo del R.M.S. Titanic.', 1997, 195, 'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg', 'https://cloudfront-us-east-1.images.arcpublishing.com/grupoclarin/V4RELIPUY5EWLAYVSDNN2A23CU.jpg', NULL, 1, 5500, 0, '2026-01-22 18:34:27', 12, 0.0, NULL),
(21, 1, 'Forrest Gump', 'Las presidencias de Kennedy y Johnson, Vietnam y Watergate a través de los ojos de Forrest.', 1994, 142, 'https://m.media-amazon.com/images/M/MV5BNDYwNzVjMTItZmU5YS00YjQ5LTljYjgtMjY2NDVmYWMyNWFmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'https://thumbnails.cbsig.net/CBS_Production_Entertainment_VMS/2024/08/30/2367992899604/PPMOV_FORRESTGUMP_MOVIE_UHD_2784533_1920x1080.jpg', NULL, 1, 3800, 0, '2026-01-22 18:34:27', 12, 0.0, NULL),
(22, 1, 'Gladiator', 'Un general romano traicionado busca venganza como gladiador.', 2000, 155, 'https://image.tmdb.org/t/p/w500/ty8TGRuvJLPUmAR1H1nRIsgwvim.jpg', 'https://images.bauerhosting.com/legacy/empire-tmdb/films/98/images/5vZw7ltCKI0JiOYTtRxaIC3DX0e.jpg?ar=16:9&fit=crop&crop=top', NULL, 2, 2900, 0, '2026-01-22 18:34:27', 16, 0.0, NULL),
(23, 1, 'Batman', 'Gotham City. Crime boss Carl Grissom (Jack Palance) effectively runs the town but there\'s a new crime fighter in town - Batman (Michael Keaton). Grissom\'s right-hand man is Jack Napier (Jack Nicholson), a brutal man who is not entirely sane... After falling out between the two Grissom has Napier set up with the Police and Napier falls to his apparent death in a vat of chemicals. However, he soon reappears as The Joker and starts a reign of terror in Gotham City. Meanwhile, reporter Vicki Vale (Kim Basinger) is in the city to do an article on Batman. She soon starts a relationship with Batman\'s everyday persona, billionaire Bruce Wayne.', 1989, 126, 'https://m.media-amazon.com/images/M/MV5BYzZmZWViM2EtNzhlMi00NzBlLWE0MWEtZDFjMjk3YjIyNTBhXkEyXkFqcGc@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BYzZmZWViM2EtNzhlMi00NzBlLWE0MWEtZDFjMjk3YjIyNTBhXkEyXkFqcGc@._V1_SX300.jpg', '', 1, 0, 0, '2026-01-26 21:25:53', 12, 0.0, NULL),
(24, 1, 'Super Mario Bros: La película', 'Mientras trabajan en una avería subterránea, los fontaneros de Brooklyn, Mario y su hermano Luigi, viajan por una misteriosa tubería hasta un nuevo mundo mágico. Pero, cuando los hermanos se separan, Mario deberá emprender una épica misión para encontrar a Luigi. Con la ayuda del champiñón local Toad y unas cuantas nociones de combate de la guerrera líder del Reino Champiñón, la princesa Peach, Mario descubre todo el poder que alberga en su interior.', 2023, 92, 'https://image.tmdb.org/t/p/original/k36QyeVsy851npTUQL08jO8hqip.jpg', 'https://image.tmdb.org/t/p/original/9n2tJBplPbgR2ca05hS5CKXwP2c.jpg', 'https://www.youtube.com/watch?v=8OtTfN6k7_Y', 1, 0, 0, '2026-01-26 22:12:29', 12, 7.6, '502356'),
(25, 1, 'Avengers: Endgame', 'After the devastating events of Avengers: Infinity War (2018), the universe is in ruins due to the efforts of the Mad Titan, Thanos. With the help of remaining allies, the Avengers must assemble once more in order to undo Thanos\'s actions and undo the chaos to the universe, no matter what consequences may be in store, and no matter who they face...', 2019, 181, 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg', 'https://www.youtube.com/watch?v=UQ3bqYKnyhM', 2, 0, 1, '2026-01-26 22:15:51', 12, 8.4, NULL),
(27, 1, 'Spider-Man: Far from Home', 'Nuestro amigable superhéroe del vecindario decide unirse a sus mejores amigos Ned, MJ y el resto de la pandilla en unas vacaciones por Europa. Sin embargo, el plan de Peter de dejar atrás el mundo del superhéroe por unas semanas se ve rápidamente frustrado cuando, a regañadientes, acepta ayudar a Nick Fury a desentrañar el misterio de varios ataques de criaturas elementales que están sembrando el caos en todo el continente.', 2019, 129, 'https://m.media-amazon.com/images/M/MV5BMzNhNTE0NWQtN2E1Ny00NjcwLTg1YTctMGY1NmMwODJmY2NmXkEyXkFqcGc@._V1_SX300.jpg', 'https://m.media-amazon.com/images/M/MV5BMzNhNTE0NWQtN2E1Ny00NjcwLTg1YTctMGY1NmMwODJmY2NmXkEyXkFqcGc@._V1_SX300.jpg', 'https://www.youtube.com/watch?v=dAxa7emR1Vc', 1, 0, 0, '2026-02-03 08:24:01', 12, 7.3, NULL),
(28, 1, 'El increíble Hulk', 'El científico Bruce Banner recorre el mundo tratando de encontrar una cura a su problema, en busca de un antídoto que le permita librarse de su Alter Ego. Perseguido por el ejército y por su propia rabia interna, es incapaz de sacar de su cabeza a Betty Ross. Así que se decide a volver a la civilización, donde debe enfrentarse a una criatura creada cuando el agente de la KGB, Emil Blonsky, se expone a una dosis superior de la radiación que convirtió a Bruce en Hulk. Incapaz de volver a su estado humano, Emil hace responsable a Hulk de su aterradora condición, mientras que la ciudad de Nueva York se convierte en el escenario de la última batalla entre las dos criaturas más poderosas que jamás han pisado la Tierra.', 2008, 114, 'https://image.tmdb.org/t/p/original/rCTSJzsU2GkH5uLwP1RFb3OjtLs.jpg', 'https://image.tmdb.org/t/p/original/jPu8yiadqgzwFPGKJmGo637ASVP.jpg', 'https://www.youtube.com/watch?v=hLPmTK8SSq8', 1, 0, 0, '2026-02-03 11:43:39', 12, 6.2, '1724'),
(29, 1, 'Iron Man 2', 'El mundo sabe que el multimillonario Tony Stark es Iron Man, el superhéroe enmascarado. Sometido a presiones por parte del gobierno, la prensa y la opinión pública para que comparta su tecnología con el ejército, Tony es reacio a desvelar los secretos de la armadura de Iron Man porque teme que esa información pueda caer en manos indeseables.', 2010, 125, 'https://image.tmdb.org/t/p/original/ayyJVOV5I4MGjti7nIHC3mVCagR.jpg', 'https://image.tmdb.org/t/p/original/7lmBufEG7P7Y1HClYK3gCxYrkgS.jpg', 'https://www.youtube.com/watch?v=HlOQqg8qy70', 1, 0, 0, '2026-02-03 11:45:02', 12, 6.8, '10138'),
(30, 2, 'Ley y orden: Unidad de Víctimas Especiales', '‘Ley y Orden: Unidad de Víctimas Especiales’ es una serie de televisión estadounidense grabada en Nueva York donde es también principalmente producida. Con el estilo de la original ‘Ley y Orden’ los episodios son usualmente \"sacados de los titulares\" o basados libremente en verdaderos asesinatos que han recibido la atención de los medios.', 1999, 0, 'https://image.tmdb.org/t/p/w500/kvo558UKEhp8v3JoRGCSIx3Xxab.jpg', 'https://image.tmdb.org/t/p/original/obtdxPgmfykYwVnvuYXC5f2xKlQ.jpg', NULL, 2, 0, 0, '2026-02-09 08:01:56', 12, 7.9, '2734'),
(33, 1, 'Avatar', 'Año 2154. Jake Sully, un exmarine en silla de ruedas, es enviado al planeta Pandora, donde se ha creado el programa Avatar, gracias al cual los seres humanos pueden controlar de forma remota un cuerpo biológico con apariencia y genética de la especie nativa. Pronto se encontrará con la encrucijada entre seguir las órdenes de sus superiores o defender al mundo que le ha acogido y siente como suyo.', 2009, 161, 'https://image.tmdb.org/t/p/original/t5T3LPbLLgP2OP6kloM9p2PXpJL.jpg', 'https://image.tmdb.org/t/p/original/vL5LR6WdxWPjLPFRLe133jXWsh5.jpg', 'https://www.youtube.com/watch?v=AZS_d_hS2dM', 1, 0, 0, '2026-02-09 09:09:04', 7, 7.6, '19995'),
(37, 2, 'Loki', 'Loki es llevado ante la misteriosa organización llamada AVT (Autoridad de Variación Temporal) después de los acontecimientos  ocurridos en  \"Avengers: Endgame (2019)\" y  se le da a elegir  enfrentarse a ser borrado de la existencia debido a que es una \"variante de tiempo\" o ayudar a arreglar la línea de tiempo y detener una amenaza mayor.', 2021, 0, 'https://image.tmdb.org/t/p/original/53aonG0QS3ynbYuuwhPtyoOwTDD.jpg', 'https://image.tmdb.org/t/p/original/N1hWzVPpZ8lIQvQskgdQogxdsc.jpg', 'https://www.youtube.com/watch?v=uz1CREWyISo', 1, 0, 0, '2026-02-09 12:24:04', 12, 8.2, '84958'),
(38, 2, '¡Fenomenoide!', 'Cuenta la historia de Dexter Douglas, un joven apasionado por la computación y que desea encontrar novia. Un día, mientras estaba en su computador, luego de instalar un chip \"extraño\" (Chip Pinnacle en inglés que en español significa Chip Cumbre), su gato, que paseaba por sobre su teclado ingreso accidentalmente un código secreto a este chip, la \"secuencia de clave secreta\" que debe ser escrito para que el error informático se active (una referencia al fallo Pentium FDIV) con: \"@[=g3,8d]\\&fbb=-q]/hk%fg\"', 1995, 22, 'https://image.tmdb.org/t/p/w500/mBfybbiJmimZl0AVs2amZbxorwg.jpg', 'https://image.tmdb.org/t/p/original/t9owI1MGsFCIamNQmPkjUFh8b5T.jpg', NULL, 2, 0, 0, '2026-02-09 18:44:44', 12, 7.8, '4334'),
(39, 2, 'Prodigiosa: Las aventuras de Ladybug', 'Cuando París corre peligro, Marinette se convierte en Ladybug. Lo que no sabe, es que su apuesto amigo Adrien es Cat Noir, otro superhéroe al servicio de la ciudad.', 2015, 0, 'https://image.tmdb.org/t/p/w500/z9n5ZOECtbug5h07kq7RxoLRBOZ.jpg', 'https://image.tmdb.org/t/p/original/lH22EnM0ofC7GsVGU8dAA2RXV1c.jpg', NULL, 2, 0, 0, '2026-02-09 18:45:11', 12, 8.0, '65334'),
(40, 1, 'Inazuma Eleven: La película', 'Una organización del futuro envía al Equipo Ogro especialmente entrenado para derrotar a Endou Mamoru y a su equipo, para evitar que influya en el mundo con su fútbol.', 2010, 90, 'https://image.tmdb.org/t/p/original/mxv6vMB8qSeD8yHXmnwikYQR3Ot.jpg', 'https://image.tmdb.org/t/p/original/g08t3r6oesgg2Js0bQgWY4ZnHY1.jpg', '', 1, 0, 0, '2026-02-13 16:11:37', 12, 7.6, '118301'),
(41, 1, 'La asistenta', 'Una joven (Sydney Sweeney), con un pasado complicado comienza a trabajar como asistenta en la lujosa casa de los Winchester. A medida que se adentra en la vida de la familia, descubrirá secretos oscuros que pondrán en peligro su seguridad, pero quizá ya sea demasiado tarde... Adaptación de la novela de Freida McFadden.', 2025, 131, 'https://image.tmdb.org/t/p/w500/A6S15iqfHpoit02leDfDVnpklys.jpg', 'https://image.tmdb.org/t/p/original/tNONILTe9OJz574KZWaLze4v6RC.jpg', NULL, 2, 0, 0, '2026-02-14 14:10:32', 12, 7.2, '1368166'),
(42, 1, 'Los hermanos demolición', 'Un improbable dúo de hermanastros, uno un impulsivo detective y el otro un disciplinado, se ven atraídos por el asesinato de su padre en Hawai, lo que les lleva a un peligroso viaje para desenmascarar una conspiración de largo alcance.', 2026, 124, 'https://image.tmdb.org/t/p/w500/ttEESBvVrO8ngZr19qp6eBMVS9F.jpg', 'https://image.tmdb.org/t/p/original/cz4vLJrmaV1zJlRYbxqtvLzeLWB.jpg', NULL, 2, 0, 0, '2026-02-14 14:11:04', 12, 6.8, '1168190'),
(43, 2, 'The Rookie', 'Comenzar de nuevo no es fácil, especialmente para el chico de una ciudad pequeña John Nolan que, después de un incidente que cambia su vida, está persiguiendo su sueño de ser un oficial de policía de Los Ángeles. Como el novato más viejo de la fuerza, se ha encontrado con el escepticismo de algunos de los superiores que lo ven como una crisis ambulante.', 2018, 0, 'https://image.tmdb.org/t/p/w500/g39Rn6PcZjK2yLzz5Z5oBKSpsZR.jpg', 'https://image.tmdb.org/t/p/original/6iNWfGVCEfASDdlNb05TP5nG0ll.jpg', NULL, 2, 0, 0, '2026-02-14 14:11:30', 12, 8.5, '79744'),
(44, 1, 'Los Cuatro Fantásticos: Primeros pasos', 'Con un mundo retrofuturista inspirado en los años 60 como telón de fondo, la Primera Familia de Marvel deberá hacer frente a su mayor desafío hasta la fecha. Obligados a buscar el equilibrio entre su papel de héroes y sus fuertes vínculos familiares, tendrán que defender la Tierra de un hambriento dios espacial llamado Galactus y su intrigante heraldo, Estela Plateada.', 2025, 115, 'https://image.tmdb.org/t/p/w500/ckfiXWGEMWrUP53cc6QyHijLlhl.jpg', 'https://image.tmdb.org/t/p/original/s94NjfKkcSczZ1FembwmQZwsuwY.jpg', NULL, 2, 0, 0, '2026-02-14 15:35:58', 12, 7.0, '617126'),
(45, 2, 'Arrow', 'Después de un violento naufragio y tras haber desaparecido y creído muerto durante cinco años, el multimillonario playboy Oliver Queen es rescatado con vida en una isla del Pacífico. De vuelta en casa en Starling City, es recibido por su madre, su hermana y su mejor amigo, quienes rápidamente notan que la terrible experiencia sufrida lo ha cambiado. Por otra parte, trata de ocultar la verdad acerca de en quién se ha convertido mientras trata de enmendar los errores que cometió en el pasado y de reconciliarse con su ex novia, Laurel Lance. Mientras trata de volver a contactar a las personas de su pasado jugando el papel del mujeriego adinerado, despreocupado y descuidado que solía ser, ayudado por su fiel chofer y guardaespaldas John Diggle, crea en secreto el personaje de un justiciero encapuchado, un vigilante que lucha contra los males de la sociedad tratando de darle a su ciudad la gloria que antes tenía; complicando esta misión.', 2012, 0, 'https://image.tmdb.org/t/p/w500/u8ZHFj1jC384JEkTt3vNg1DfWEb.jpg', 'https://image.tmdb.org/t/p/original/wAFuDJfZJrnh6a1wf1Vt7PqYBvR.jpg', 'https://www.youtube.com/embed/nPL9wWWuFqA', 2, 0, 0, '2026-02-14 15:48:55', 12, 6.8, '1412'),
(46, 1, 'Zootrópolis 2', 'Después de resolver el caso más importante en la historia de Zootrópolis, los policías novatos Judy Hopps y Nick Wilde descubren que su asociación no es tan sólida como pensaban cuando el Jefe Bogo les ordena unirse al programa de consejería \"Compañeros en Crisis\". Pero no pasa mucho tiempo para que su asociación se ponga a prueba al máximo, cuando la llegada de Gary, la serpiente, pone la ciudad patas arriba.', 2025, 107, 'https://image.tmdb.org/t/p/w500/4fRdfQPjHiuDSkzaxFAV44k7Iem.jpg', 'https://image.tmdb.org/t/p/original/5h2EsPKNDdB3MAtOk9MB9Ycg9Rz.jpg', 'https://www.youtube.com/embed/VZU-glDcBAw', 2, 0, 0, '2026-02-14 15:50:00', 12, 7.6, '1084242'),
(47, 1, 'Primate', 'Lucy, una estudiante universitaria, pasa las vacaciones con sus amigos en la casa de su familia en Hawái, donde vive con su mascota, un chimpancé llamado Ben. Sin embargo, cuando Ben contrae rabia tras ser mordido por un animal rabioso, el grupo debe luchar por su vida para evitar al ahora violento chimpancé.', 2026, 89, 'https://image.tmdb.org/t/p/w500/cyqEghDphEUunLfongtpmROjpID.jpg', 'https://image.tmdb.org/t/p/original/9uakM2woks0JV8HKIc4oatIVS88.jpg', 'https://www.youtube.com/embed/F0fn4knTR2s', 2, 0, 0, '2026-02-14 15:50:10', 12, 6.5, '1315303'),
(48, 1, 'Hermandad: Estado de terror', 'En medio de una ola de violencia inédita que sacude São Paulo, una abogada ligada al mundo criminal debe pactar con la policía para salvar a su sobrina secuestrada.', 2026, 103, 'https://image.tmdb.org/t/p/w500/xdyCFS4jUzp7kghoptaiYqir48X.jpg', 'https://image.tmdb.org/t/p/original/1Xdsq7U7bvJJd2X7Jg4qNaKVkYO.jpg', 'https://www.youtube.com/embed/syg6TKsx8aU', 2, 0, 0, '2026-02-14 16:11:15', 12, 3.8, '1426964'),
(49, 2, 'Anatomía de Grey', 'La vida de Meredith Grey no es nada fácil. Intenta tomar las riendas de su vida, aunque su trabajo sea de esos que te hacen la vida imposible. Meredith es una cirujana interna de primer año en el Hospital Grace de Seattle, el programa de prácticas más duro de la Facultad de Medicina de Harvard. Y ella lo va a comprobar. Pero no estará sola. Un elenco de compañeros de promoción tendrán que superar la misma prueba. Ahora están en el mundo real, son doctores del hospital. Y en un mundo donde la experiencia en el trabajo puede ser un factor de vida o muerte, todos ellos tendrán que lidiar con los altibajos de sus vidas personales.', 2005, 0, 'https://image.tmdb.org/t/p/w500/kpwjer9hgwFFc3wCVZFnwnL1dS2.jpg', 'https://image.tmdb.org/t/p/original/jP0Rhj9OTPDAwQlHQwOLFDdeE8t.jpg', NULL, 2, 0, 0, '2026-02-14 16:26:10', 12, 8.2, '1416'),
(50, 2, 'Smallville', 'Serie que narra los inicios de Superman -Clark Kent- en su pueblo natal, Smallville. Allí vivía con sus padres, estudiaba en el instituto local y conoció a su primera novia, Lana Lang, y a su futuro rival, Lex Luthor.', 2001, 43, 'https://image.tmdb.org/t/p/w500/mHZSq8LA5Dt48JjaOZ5tcPXQRVN.jpg', 'https://image.tmdb.org/t/p/original/hBSSm06lrxjhbCKhCUiRN8AGBQG.jpg', NULL, 2, 0, 0, '2026-02-14 16:28:07', 12, 8.2, '4604'),
(51, 1, 'Anaconda', 'Doug y Griff son muy buenos amigos desde que eran niños y siempre han soñado con hacer un remake de su película favorita de todos los tiempos: el «clásico cinematográfico» Anaconda. Cuando una crisis de mediana edad les anima a lanzarse por fin a la aventura, se adentran en la selva amazónica para empezar a rodar. Pero las cosas se ponen serias cuando aparece una anaconda gigante de verdad, convirtiendo su caótico y cómico plató de rodaje en una situación mortal.', 2025, 100, 'https://image.tmdb.org/t/p/w500/zX1KJPiGTrzpFcjfOWlmqdRgpQd.jpg', 'https://image.tmdb.org/t/p/original/swxhEJsAWms6X1fDZ4HdbvYBSf9.jpg', 'https://www.youtube.com/embed/FVPjkbmgX74', 2, 0, 0, '2026-02-14 16:32:20', 12, 5.8, '1234731'),
(52, 1, 'Capitán América: Civil War', 'Continúa la historia de “Avengers: Age of Ultron”, con Steve Rogers liderando un nuevo equipo de Vengadores en su esfuerzo por proteger a la humanidad. Tras otro incidente internacional relacionado con los Vengadores que ocasiona daños colaterales, la presión política fuerza a crear un sistema de registro y un cuerpo gubernamental para determinar cuándo se requiere los servicios del equipo. El nuevo status quo divide a los Vengadores mientras intentan salvar al mundo de un nuevo y perverso villano.', 2016, 147, 'https://image.tmdb.org/t/p/w500/jPPy7tCfglppQo6J9nGwU6UmJ8X.jpg', 'https://image.tmdb.org/t/p/original/wdwcOBMkt3zmPQuEMxB3FUtMio2.jpg', 'https://www.youtube.com/embed/6aWYmFCRsEI', 2, 0, 0, '2026-02-14 16:33:32', 12, 7.4, '271110');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_actor`
--

CREATE TABLE `contenido_actor` (
  `contenido_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `personaje` varchar(100) DEFAULT NULL
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
(24, 3, 'Bowser (voice)'),
(24, 27, 'Mario (voice)'),
(24, 28, 'Princess Peach (voice)'),
(24, 29, 'Luigi (voice)'),
(24, 108, 'Toad (voice)'),
(24, 109, 'Donkey Kong (voice)'),
(24, 110, 'Cranky Kong (voice)'),
(24, 111, 'Spike (voice)'),
(24, 112, 'Mario\'s Dad / Giuseppe (voice)'),
(24, 113, 'Kamek (voice)'),
(24, 114, 'Penguin King (voice)'),
(24, 115, 'Uncle Tony (voice)'),
(24, 116, 'Uncle Arthur (voice)'),
(24, 117, 'Mario\'s Mom (voice)'),
(24, 118, 'Toad General (voice)'),
(25, 10, ''),
(25, 30, ''),
(25, 31, ''),
(27, 32, ''),
(27, 33, ''),
(27, 34, ''),
(28, 35, 'Bruce Banner'),
(28, 36, 'Betty Ross'),
(28, 37, 'Emil Blonsky'),
(28, 86, 'General \'Thunderbolt\' Ross'),
(28, 87, 'Samuel Sterns'),
(28, 88, 'Leonard'),
(28, 89, 'Major Kathleen Sparr'),
(28, 90, 'General Joe Greller'),
(28, 91, 'Voice of The Incredible Hulk / Security Guard'),
(28, 92, 'Stanley'),
(28, 93, 'Martina'),
(28, 94, 'Commando'),
(28, 95, 'Commando'),
(28, 96, 'Commando'),
(28, 97, 'Commando'),
(33, 1, 'Jake Sully'),
(33, 2, 'Neytiri'),
(33, 55, 'Dr. Grace Augustine'),
(33, 56, 'Colonel Miles Quaritch'),
(33, 57, 'Trudy Chacon'),
(33, 58, 'Parker Selfridge'),
(33, 59, 'Norm Spellman'),
(33, 60, 'Mo\'at'),
(33, 61, 'Eytukan'),
(33, 62, 'Tsu\'Tey'),
(33, 63, 'Dr. Max Patel'),
(33, 64, 'Corporal Lyle Wainfleet'),
(33, 65, 'Private Fike'),
(33, 66, 'Cryo Vault Med Tech'),
(33, 67, 'Venture Star Crew Chief'),
(37, 80, 'Loki Laufeyson'),
(37, 81, 'Sylvie / The Variant'),
(37, 82, 'Hunter B-15'),
(37, 83, 'Casey / Hunter K-5E'),
(37, 84, 'Ouroboros \'OB\''),
(37, 85, 'Mobius M. Mobius'),
(40, 119, 'Mamoru Endou / Kanon Endou (voice)'),
(40, 120, 'Shirou Fubuki (voice)'),
(40, 121, 'Yuuto Kidou (voice)'),
(40, 122, 'Baddap Sleed (voice)'),
(40, 123, 'Shuuya Gouenji (voice)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_director`
--

CREATE TABLE `contenido_director` (
  `contenido_id` int(11) NOT NULL,
  `director_id` int(11) NOT NULL
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
(9, 8),
(10, 9),
(12, 10),
(13, 12),
(14, 11),
(16, 7),
(24, 13),
(24, 14),
(25, 16),
(25, 17),
(27, 18),
(28, 19),
(33, 1),
(37, 24),
(40, 25);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_genero`
--

CREATE TABLE `contenido_genero` (
  `contenido_id` int(11) NOT NULL,
  `genero_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido_genero`
--

INSERT INTO `contenido_genero` (`contenido_id`, `genero_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(3, 1),
(3, 3),
(3, 4),
(4, 1),
(4, 2),
(4, 3),
(5, 4),
(6, 3),
(6, 4),
(7, 1),
(7, 6),
(8, 1),
(8, 3),
(9, 4),
(9, 6),
(10, 4),
(10, 6),
(11, 2),
(11, 3),
(12, 4),
(12, 6),
(13, 5),
(13, 7),
(14, 1),
(14, 3),
(15, 2),
(15, 5),
(16, 3),
(16, 4),
(17, 4),
(17, 5),
(18, 5),
(18, 10),
(19, 1),
(19, 3),
(20, 4),
(20, 9),
(21, 4),
(21, 9),
(22, 1),
(22, 4),
(23, 1),
(23, 2),
(24, 2),
(24, 5),
(24, 7),
(24, 10),
(24, 16),
(25, 1),
(25, 2),
(25, 3),
(27, 1),
(27, 2),
(27, 7),
(28, 1),
(28, 2),
(28, 3),
(33, 1),
(33, 2),
(33, 3),
(33, 10),
(37, 4),
(37, 14),
(40, 5),
(40, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_idioma`
--

CREATE TABLE `contenido_idioma` (
  `contenido_id` int(11) NOT NULL,
  `idioma_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido_idioma`
--

INSERT INTO `contenido_idioma` (`contenido_id`, `idioma_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
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
(22, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `directores`
--

CREATE TABLE `directores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
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
(20, 'Jon Favreau', 'https://ui-avatars.com/api/?name=Jon+Favreau&background=random'),
(21, 'Matt Reeves', 'https://image.tmdb.org/t/p/original/5rA459xpMt6IeJG7ZqvhLbSozEH.jpg'),
(22, 'Ross Duffer', 'https://image.tmdb.org/t/p/original/7wdhSHgMLry5jBKJT1mdLT3BYaZ.jpg'),
(23, 'Matt Duffer', 'https://image.tmdb.org/t/p/original/kXO5CnSxC0znMAICGxnPeuGP73U.jpg'),
(24, 'Michael Waldron', 'https://image.tmdb.org/t/p/original/5hf8B7h92GhSSch0FVNSfWMyEG2.jpg'),
(25, '宮尾佳和', '');

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
(4, 'Drama'),
(5, 'Animación'),
(6, 'Crimen'),
(7, 'Comedia'),
(8, 'Thriller'),
(9, 'Romance'),
(10, 'Fantasía'),
(11, 'Comedia Española'),
(12, 'Misterio'),
(13, 'Suspense'),
(14, 'Sci-Fi & Fantasy'),
(15, 'Action & Adventure'),
(16, 'Familia');

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

--
-- Volcado de datos para la tabla `mi_lista`
--

INSERT INTO `mi_lista` (`usuario_id`, `contenido_id`, `fecha_agregado`) VALUES
(2, 14, '2026-02-03 07:43:58'),
(2, 23, '2026-02-03 07:44:09'),
(3, 2, '2026-01-22 16:09:36'),
(3, 3, '2026-02-14 15:11:05'),
(3, 4, '2026-01-22 16:02:41'),
(3, 6, '2026-02-08 15:56:17'),
(3, 10, '2026-02-03 08:02:15'),
(3, 11, '2026-02-08 15:38:19'),
(3, 13, '2026-02-05 11:35:20'),
(3, 14, '2026-02-03 09:30:46'),
(3, 20, '2026-02-03 07:51:13'),
(3, 24, '2026-02-03 07:37:35'),
(3, 27, '2026-02-05 16:21:40'),
(3, 30, '2026-02-14 15:16:12'),
(3, 42, '2026-02-14 14:49:23'),
(3, 44, '2026-02-14 14:35:58'),
(3, 45, '2026-02-14 15:10:02'),
(3, 46, '2026-02-14 14:50:07'),
(3, 47, '2026-02-14 14:50:11'),
(3, 48, '2026-02-14 15:11:45'),
(3, 49, '2026-02-14 15:26:32'),
(3, 50, '2026-02-14 15:28:11'),
(4, 13, '2026-01-22 23:09:23'),
(4, 15, '2026-01-22 23:09:19'),
(4, 39, '2026-02-09 17:45:11');

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
(2, 'Premium', 9.99, '4K Ultra HD'),
(3, 'Kids', 4.99, 'HD');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `contenido_id` int(11) NOT NULL,
  `puntuacion` int(11) NOT NULL,
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
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
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
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `plan_id` int(11) DEFAULT 1,
  `avatar` varchar(255) DEFAULT 'default.png',
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `fecha_fin_suscripcion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `password`, `rol`, `plan_id`, `avatar`, `fecha_registro`, `fecha_fin_suscripcion`) VALUES
(1, 'Admin', 'admin@labutaca.com', '$2y$10$NWRxl6ZoScpFC8lckYI7O.WyAdnbAFUox/xSwYa64T5jLoslIDlCq', 'admin', 2, 'avatar_admin.jpg', '2026-01-21 14:29:51', NULL),
(2, 'FreeUser', 'invitado@labutaca.com', '$2y$10$UkzhDFgsfsPVCedzSLHlN.8.o0WeraE00a.didn3B0bpBtosdHOTK', 'usuario', 1, 'https://images.ecestaticos.com/an3NIKmUWjvoxIwgQ4K3J0pqAqo=/0x0:828x470/1200x1200/filters:fill(white):format(jpg)/f.elconfidencial.com%2Foriginal%2F280%2F050%2F590%2F2800505903fc39bbeaf82f750b823ec3.jpg', '2026-01-21 14:29:51', NULL),
(3, 'PremiumUser', 'prueba@labutaca.com', '$2y$10$lWAYHhAbYb0lEo.To7hZPOcXzR3sB9tAjmgmJTc1LmYKaIlYQJFt2', 'usuario', 2, 'https://upload.wikimedia.org/wikipedia/en/9/90/HeathJoker.png', '2026-01-21 15:53:11', NULL),
(4, 'KidsUser', 'peque@labutaca.com', '$2y$10$NWRxl6ZoScpFC8lckYI7O.WyAdnbAFUox/xSwYa64T5jLoslIDlCq', 'usuario', 3, 'https://media.revistagq.com/photos/62a8546d6b74c0e2031238a6/16:9/w_1280,c_limit/buzz.jpg', '2026-01-22 11:35:40', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT de la tabla `capitulos`
--
ALTER TABLE `capitulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `contenidos`
--
ALTER TABLE `contenidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `directores`
--
ALTER TABLE `directores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `generos`
--
ALTER TABLE `generos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `idiomas`
--
ALTER TABLE `idiomas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipos_contenido`
--
ALTER TABLE `tipos_contenido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
