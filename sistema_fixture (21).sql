-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-12-2024 a las 15:44:20
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
-- Base de datos: `sistema_fixture`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fixtures`
--

CREATE TABLE `fixtures` (
  `id` int(11) NOT NULL,
  `tournament_version_id` int(11) DEFAULT NULL,
  `round` varchar(20) DEFAULT 'Regular',
  `home_team_id` int(11) DEFAULT NULL,
  `away_team_id` int(11) DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `match_time` time DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `status` enum('Programado','En curso','Completado','Aplazado','Cancelado') DEFAULT 'Programado',
  `home_team_score` int(11) DEFAULT NULL,
  `away_team_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fixtures`
--

INSERT INTO `fixtures` (`id`, `tournament_version_id`, `round`, `home_team_id`, `away_team_id`, `match_date`, `match_time`, `venue`, `status`, `home_team_score`, `away_team_score`) VALUES
(1, 1, 'Regular', 7, 8, '2024-12-05', '13:00:00', NULL, 'Programado', NULL, NULL),
(2, 2, 'Regular', 9, 10, '2024-12-05', '13:00:00', NULL, 'Completado', 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `match_events`
--

CREATE TABLE `match_events` (
  `id` int(11) NOT NULL,
  `fixture_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `event_type` enum('goal','card') NOT NULL,
  `card_type` enum('yellow','red') DEFAULT NULL,
  `minute` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `social_profile` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `document_type` enum('DNI','Pasaporte','Cedula Extranjera','Otro') NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('Varon','Femenino') NOT NULL,
  `position` enum('Portero','Defensa Central','Defensa Izquierdo','Defensa Derecho','Medio Campo','Medio Campo Izquierdo','Medio Campo Derecho','Medio Ofensivo','Delantero Derecho','Delantero Izquierdo','Delantero Central','Cierre - Futsal','Alas - Futsal','Pivot - Futsal') NOT NULL,
  `shirt_number` int(11) NOT NULL,
  `status` enum('Habilitado','Suspendido') NOT NULL,
  `photo` varchar(255) NOT NULL,
  `identity_document` varchar(255) NOT NULL,
  `team_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `players`
--

INSERT INTO `players` (`id`, `document_number`, `document_type`, `full_name`, `address`, `contact_number`, `birth_date`, `gender`, `position`, `shirt_number`, `status`, `photo`, `identity_document`, `team_id`, `created_at`) VALUES
(1, '12345678', 'DNI', 'CARLOS ALBERTO GOMEZ PEREZ', 'AV. SIEMPRE VIVA 123', '987654321', '1995-01-15', 'Varon', 'Portero', 1, 'Habilitado', 'photo1.jpg', 'doc1.jpg', 7, '2024-12-05 15:34:21'),
(2, '12345679', 'DNI', 'JUAN RAMIREZ MARTINEZ', 'CALLE FICTICIA 456', '987654322', '1996-02-20', 'Varon', 'Defensa Central', 2, 'Habilitado', 'photo2.jpg', 'doc2.jpg', 7, '2024-12-05 15:34:21'),
(3, '12345680', 'DNI', 'MIGUEL ANGEL RUIZ GARCIA', 'CALLE PRINCIPAL 789', '987654323', '1994-03-25', 'Varon', 'Defensa Izquierdo', 3, 'Habilitado', 'photo3.jpg', 'doc3.jpg', 7, '2024-12-05 15:34:21'),
(4, '12345681', 'DNI', 'JOSE MANUEL SANCHEZ LOPEZ', 'AV. CENTRAL 101', '987654324', '1993-04-30', 'Varon', 'Defensa Derecho', 4, 'Habilitado', 'photo4.jpg', 'doc4.jpg', 7, '2024-12-05 15:34:21'),
(5, '12345682', 'DNI', 'LUIS FERNANDO DIAZ TORRES', 'CALLE 5 SIN NOMBRE', '987654325', '1995-05-15', 'Varon', 'Medio Campo', 5, 'Habilitado', 'photo5.jpg', 'doc5.jpg', 7, '2024-12-05 15:34:21'),
(6, '12345683', 'DNI', 'JUAN CARLOS CASTRO PENA', 'CALLE LAS FLORES 22', '987654326', '1997-06-10', 'Varon', 'Medio Campo Izquierdo', 6, 'Habilitado', 'photo6.jpg', 'doc6.jpg', 7, '2024-12-05 15:34:21'),
(7, '12345684', 'DNI', 'PEDRO ALEJANDRO MORALES GOMEZ', 'AV. SIEMPRE VIVA 333', '987654327', '1994-07-05', 'Varon', 'Medio Campo Derecho', 7, 'Habilitado', 'photo7.jpg', 'doc7.jpg', 7, '2024-12-05 15:34:21'),
(8, '12345685', 'DNI', 'RICARDO PEREZ CRUZ', 'CALLE SOLITARIA 19', '987654328', '1995-08-20', 'Varon', 'Medio Ofensivo', 8, 'Habilitado', 'photo8.jpg', 'doc8.jpg', 7, '2024-12-05 15:34:21'),
(9, '12345686', 'DNI', 'OSCAR ANDRES MUNOZ RIVERA', 'CALLE CIELO AZUL', '987654329', '1996-09-15', 'Varon', 'Delantero Derecho', 9, 'Habilitado', 'photo9.jpg', 'doc9.jpg', 7, '2024-12-05 15:34:21'),
(10, '12345687', 'DNI', 'JAVIER RAMOS RODRIGUEZ', 'CALLE SAN MARTIN 10', '987654330', '1997-10-30', 'Varon', 'Delantero Izquierdo', 10, 'Habilitado', 'photo10.jpg', 'doc10.jpg', 7, '2024-12-05 15:34:21'),
(11, '12345688', 'DNI', 'ENRIQUE GONZALEZ LOPEZ', 'AV. ANDES 55', '987654331', '1994-11-20', 'Varon', 'Delantero Central', 11, 'Habilitado', 'photo11.jpg', 'doc11.jpg', 7, '2024-12-05 15:34:21'),
(12, '12345689', 'DNI', 'DIEGO FERNANDO MARTINEZ MORA', 'AV. PERU 44', '987654332', '1995-12-25', 'Varon', 'Portero', 12, 'Habilitado', 'photo12.jpg', 'doc12.jpg', 7, '2024-12-05 15:34:21'),
(13, '12345690', 'DNI', 'DANIEL SERRANO CASTILLO', 'CALLE NORTE 11', '987654333', '1998-01-10', 'Varon', 'Defensa Central', 13, 'Habilitado', 'photo13.jpg', 'doc13.jpg', 7, '2024-12-05 15:34:21'),
(14, '12345691', 'DNI', 'FERNANDO PENA PEREZ', 'CALLE ESTE 90', '987654334', '1997-02-14', 'Varon', 'Defensa Derecho', 14, 'Habilitado', 'photo14.jpg', 'doc14.jpg', 7, '2024-12-05 15:34:21'),
(15, '12345692', 'DNI', 'CARLOS VARGAS RUIZ', 'AV. GRANDE 77', '987654335', '1996-03-18', 'Varon', 'Defensa Izquierdo', 15, 'Habilitado', 'photo15.jpg', 'doc15.jpg', 7, '2024-12-05 15:34:21'),
(16, '22345678', 'DNI', 'MARIO ALONSO RIOS CRUZ', 'CALLE LUNA 3', '987654336', '1995-04-15', 'Varon', 'Portero', 1, 'Habilitado', 'photo16.jpg', 'doc16.jpg', 8, '2024-12-05 15:34:21'),
(17, '22345679', 'DNI', 'VICTOR HUGO PEREZ SOTO', 'AV. CERRO 5', '987654337', '1994-05-12', 'Varon', 'Defensa Central', 2, 'Habilitado', 'photo17.jpg', 'doc17.jpg', 8, '2024-12-05 15:34:21'),
(18, '22345680', 'DNI', 'RAUL GARCIA FLORES', 'CALLE PRINCIPAL 15', '987654338', '1993-06-18', 'Varon', 'Defensa Izquierdo', 3, 'Habilitado', 'photo18.jpg', 'doc18.jpg', 8, '2024-12-05 15:34:21'),
(19, '22345681', 'DNI', 'ANDRES RAMIREZ MORALES', 'CALLE MAYOR 4', '987654339', '1992-07-21', 'Varon', 'Defensa Derecho', 4, 'Habilitado', 'photo19.jpg', 'doc19.jpg', 8, '2024-12-05 15:34:21'),
(20, '22345682', 'DNI', 'FELIPE SUAREZ LOPEZ', 'AV. ESPERANZA 2', '987654340', '1991-08-10', 'Varon', 'Medio Campo', 5, 'Habilitado', 'photo20.jpg', 'doc20.jpg', 8, '2024-12-05 15:34:21'),
(21, '22345683', 'DNI', 'ALEJANDRO MENDOZA GOMEZ', 'CALLE SOL 7', '987654341', '1993-09-12', 'Varon', 'Medio Campo Izquierdo', 6, 'Habilitado', 'photo21.jpg', 'doc21.jpg', 8, '2024-12-05 15:34:21'),
(22, '22345684', 'DNI', 'JORGE MARTIN LOPEZ CRUZ', 'CALLE LAGO 9', '987654342', '1994-10-05', 'Varon', 'Medio Campo Derecho', 7, 'Habilitado', 'photo22.jpg', 'doc22.jpg', 8, '2024-12-05 15:34:21'),
(23, '22345685', 'DNI', 'PABLO CESAR PEREZ MORA', 'CALLE RIO 11', '987654343', '1995-11-18', 'Varon', 'Medio Ofensivo', 8, 'Habilitado', 'photo23.jpg', 'doc23.jpg', 8, '2024-12-05 15:34:21'),
(24, '22345686', 'DNI', 'LUIS ANGEL FERNANDEZ RUIZ', 'AV. BOSQUE 23', '987654344', '1996-12-02', 'Varon', 'Delantero Derecho', 9, 'Habilitado', 'photo24.jpg', 'doc24.jpg', 8, '2024-12-05 15:34:21'),
(25, '22345687', 'DNI', 'DIEGO RODRIGO MARTINEZ PEREZ', 'CALLE ROBLE 12', '987654345', '1997-01-20', 'Varon', 'Delantero Izquierdo', 10, 'Habilitado', 'photo25.jpg', 'doc25.jpg', 8, '2024-12-05 15:34:21'),
(26, '22345688', 'DNI', 'HUGO EMILIANO REYES CASTRO', 'CALLE PINO 14', '987654346', '1998-02-14', 'Varon', 'Delantero Central', 11, 'Habilitado', 'photo26.jpg', 'doc26.jpg', 8, '2024-12-05 15:34:21'),
(27, '22345689', 'DNI', 'MARTIN ALEJANDRO LOZANO GOMEZ', 'AV. OLIVO 18', '987654347', '1999-03-03', 'Varon', 'Portero', 12, 'Habilitado', 'photo27.jpg', 'doc27.jpg', 8, '2024-12-05 15:34:21'),
(28, '22345690', 'DNI', 'FRANCISCO JAVIER PEREZ LOPEZ', 'CALLE JARDIN 15', '987654348', '2000-04-22', 'Varon', 'Defensa Central', 13, 'Habilitado', 'photo28.jpg', 'doc28.jpg', 8, '2024-12-05 15:34:21'),
(29, '22345691', 'DNI', 'DAVID EDUARDO SOTO RAMIREZ', 'AV. MONTANA 20', '987654349', '1991-05-06', 'Varon', 'Defensa Derecho', 14, 'Habilitado', 'photo29.jpg', 'doc29.jpg', 8, '2024-12-05 15:34:21'),
(30, '22345692', 'DNI', 'ADRIAN ENRIQUE RUIZ CRUZ', 'CALLE VISTA 21', '987654350', '1992-06-10', 'Varon', 'Defensa Izquierdo', 15, 'Habilitado', 'photo30.jpg', 'doc30.jpg', 8, '2024-12-05 15:34:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `standings`
--

CREATE TABLE `standings` (
  `id` int(11) NOT NULL,
  `tournament_version_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `played` int(11) DEFAULT 0,
  `won` int(11) DEFAULT 0,
  `drawn` int(11) DEFAULT 0,
  `lost` int(11) DEFAULT 0,
  `goals_for` int(11) DEFAULT 0,
  `goals_against` int(11) DEFAULT 0,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `standings`
--

INSERT INTO `standings` (`id`, `tournament_version_id`, `team_id`, `played`, `won`, `drawn`, `lost`, `goals_for`, `goals_against`, `points`) VALUES
(1, 1, 7, 0, 0, 0, 0, 0, 0, 0),
(2, 1, 8, 0, 0, 0, 0, 0, 0, 0),
(3, 2, 9, 0, 0, 0, 0, 0, 0, 0),
(4, 2, 10, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `coach` varchar(255) DEFAULT NULL,
  `social_media` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `logo` varchar(255) NOT NULL,
  `tournament_version_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teams`
--

INSERT INTO `teams` (`id`, `name`, `country`, `color`, `user_id`, `tournament_id`, `nickname`, `city`, `origin`, `coach`, `social_media`, `image`, `logo`, `tournament_version_id`, `category_id`, `points`) VALUES
(7, 'Real Madrid', 'España', '#ffffff', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '../public/uploads2/logos/6751bf0fe1c62_Real-Madrid-logo (1).jpg', 1, NULL, 0),
(8, 'FC Barcelona', 'España', '#091677', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '../public/uploads2/logos/6743b1c55433d_fc-barcelona-logo-on-transparent-background-free-vector.jpg', 1, NULL, 0),
(9, 'FC Barcelona', 'España', '#002aff', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '../public/uploads2/logos/6752993c71a4a_6743b1c55433d_fc-barcelona-logo-on-transparent-background-free-vector.jpg', 2, NULL, 0),
(10, 'Real Madrid', 'España', '#002aff', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '../public/uploads2/logos/6752994ee40e8_Real-Madrid-logo (1).jpg', 2, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tournaments`
--

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `competition_type` enum('Aficionado','Profesional') NOT NULL,
  `sport_type` enum('Futbol','Futbol 7','Futbol 8','Fulbito','Futsal') NOT NULL,
  `gender` enum('General','Varones','Mujeres','Menores') NOT NULL,
  `url_slug` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_image` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tournaments`
--

INSERT INTO `tournaments` (`id`, `name`, `description`, `competition_type`, `sport_type`, `gender`, `url_slug`, `created_by`, `created_at`, `cover_image`, `user_id`, `logo_image`) VALUES
(1, 'Copa Paita 2024', 'Competicion en La ciudad de Paita', 'Profesional', 'Futbol', 'Varones', 'copa-paita-2024', 2, '2024-12-05 14:24:00', NULL, 2, NULL),
(2, 'Copa del rey', 'Liga de España', 'Profesional', 'Futbol', 'Varones', 'copa-del-rey', 2, '2024-12-06 06:24:35', NULL, 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tournament_categories`
--

CREATE TABLE `tournament_categories` (
  `id` int(11) NOT NULL,
  `tournament_version_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tournament_categories`
--

INSERT INTO `tournament_categories` (`id`, `tournament_version_id`, `name`, `description`) VALUES
(18, NULL, 'Grupo A', 'Sub12'),
(21, 2, 'Grupo C', 'fgfg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tournament_versions`
--

CREATE TABLE `tournament_versions` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `format_type` enum('Liga','Eliminatoria','Play Off','Relámpago') NOT NULL,
  `name` varchar(255) NOT NULL,
  `points_winner` int(11) DEFAULT 3,
  `points_draw` int(11) DEFAULT 1,
  `points_loss` int(11) DEFAULT 0,
  `points_walkover` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tournament_versions`
--

INSERT INTO `tournament_versions` (`id`, `tournament_id`, `format_type`, `name`, `points_winner`, `points_draw`, `points_loss`, `points_walkover`, `created_at`) VALUES
(2, 1, 'Liga', 'Copa Paita 2024 - Liga', 3, 1, 0, 0, '2024-12-06 03:20:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tournament_version_details`
--

CREATE TABLE `tournament_version_details` (
  `id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `tournament_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `country` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `registration_fee` decimal(10,2) DEFAULT 0.00,
  `google_maps_url` text DEFAULT NULL,
  `playing_days` varchar(100) NOT NULL,
  `match_time_range` varchar(50) NOT NULL,
  `status` enum('Pendiente','En Progreso','Finalizado') DEFAULT 'Pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_image` varchar(255) DEFAULT NULL,
  `prizes` text DEFAULT NULL,
  `tournament_bases` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tournament_version_details`
--

INSERT INTO `tournament_version_details` (`id`, `version_id`, `tournament_name`, `start_date`, `end_date`, `country`, `city`, `address`, `registration_fee`, `google_maps_url`, `playing_days`, `match_time_range`, `status`, `created_at`, `cover_image`, `prizes`, `tournament_bases`) VALUES
(0, 2, 'Copa Paita 2024 - Liga', '2024-12-05', '2025-01-05', 'Peru', 'Piura - Piura', 'Estadio Hermanos Carcamo', 0.00, 'https://maps.app.goo.gl/WooVks3XJTMaejCEA', 'Martes,Jueves,Sábado', '1:00 PM - 7:00 PM', 'Pendiente', '2024-12-06 03:20:39', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usertable`
--

CREATE TABLE `usertable` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` mediumint(50) NOT NULL,
  `status` text NOT NULL,
  `role` enum('Administrador','Usuario') NOT NULL DEFAULT 'Usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usertable`
--

INSERT INTO `usertable` (`id`, `name`, `email`, `password`, `code`, `status`, `role`) VALUES
(1, 'Jefferson Calderon', 'jeffersoncalderonburgos53@gmail.com', '$2y$10$RB1c6NPBpkPSy4.6hlJSpuFQYBR40UKjjOStvXzmzxpTodVkLHBqO', 0, 'verified', 'Administrador'),
(2, 'Jefferson', 'Jeffersoncalderonburgos@gmail.com', '$2y$10$4fpIEcOp5KY8MFiT4.LrvO2IkYf7/cqh.90OmpW4q5EJ.WozKSBbm', 0, 'verified', 'Usuario'),
(3, 'Juan', 'dragonballzuper1989@gmail.com', '$2y$10$Qt3mYdXvotZZY/okO2qXNurfoLZbZic7S2CHeTrUr.zrcp59eixui', 0, 'verified', 'Usuario'),
(4, 'Bryan Gefferson', 'bryanibarburu@gmail.com', '$2y$10$BBhOnjYLXelKpI9QsYtHj.FWF4r9937V.RrxmtH./Ka2X4oR7jKTi', 0, 'verified', 'Usuario'),
(5, 'manuel', 'umbrellasrl@gmail.com', '$2y$10$BzK1IaLRrSFx9OlI1P94V.7uiQh6PhAevCg4QWHBhcGIzqrRgRQD.', 0, 'verified', 'Usuario'),
(6, 'bryan', 'bryanibarburu@gmail.com', '$2y$10$D.gNkNh.o7ffK9..rZBDBuxT3botdojLOJM5Yb6EJWmPgDyC9lJWm', 0, 'verified', 'Usuario'),
(7, 'manu', 'manuel.aguado@magustechnologies.com', '$2y$10$NzW/f3YEm0F5jIo82V3xhef0xOBCzXB7jhiUu1LBNKkzDiNWJJUCO', 0, 'verified', 'Usuario'),
(8, 'copias', 'copiassurpm@gmail.com', '$2y$10$FxJBqa/7t.tADQYQqd9GVu4AAeri3YqZPWpu0nSV/ajgPpiM2KXyy', 0, 'verified', 'Usuario'),
(9, 'Manuel', 'systemcraft.pe@gmail.com', '$2y$10$.0tpzDIlHKZPQhoTBR6KuONVy.9/FgsPIctNj1RlIhhTWykF82W0m', 0, 'verified', 'Usuario'),
(10, 'Alex Ventura ', 'alexh-85@hotmail.com', '$2y$10$fe9t1AQ7XkyIMr5aOzFJoOFlYgNm044FNUxzcqdjP8NHGZQgnDWzm', 0, 'verified', 'Usuario'),
(11, 'Alex Ventura Maquera ', 'alexmix2390@gmail.com', '$2y$10$xJ.RisHSjwVDbzbnYtIUXujsCHpE4E10Mq0ab2.6h.Xpo8CUt3/cS', 0, 'verified', 'Usuario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `fixtures`
--
ALTER TABLE `fixtures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_version_id` (`tournament_version_id`),
  ADD KEY `home_team_id` (`home_team_id`),
  ADD KEY `away_team_id` (`away_team_id`);

--
-- Indices de la tabla `match_events`
--
ALTER TABLE `match_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fixture_id` (`fixture_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indices de la tabla `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indices de la tabla `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indices de la tabla `standings`
--
ALTER TABLE `standings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_version_id` (`tournament_version_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indices de la tabla `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `teams_ibfk_2` (`tournament_id`),
  ADD KEY `tournament_version_id` (`tournament_version_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `tournament_categories`
--
ALTER TABLE `tournament_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_version_id` (`tournament_version_id`);

--
-- Indices de la tabla `tournament_versions`
--
ALTER TABLE `tournament_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Indices de la tabla `usertable`
--
ALTER TABLE `usertable`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `fixtures`
--
ALTER TABLE `fixtures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `match_events`
--
ALTER TABLE `match_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `standings`
--
ALTER TABLE `standings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tournament_categories`
--
ALTER TABLE `tournament_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tournament_versions`
--
ALTER TABLE `tournament_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usertable`
--
ALTER TABLE `usertable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `match_events`
--
ALTER TABLE `match_events`
  ADD CONSTRAINT `match_events_ibfk_1` FOREIGN KEY (`fixture_id`) REFERENCES `fixtures` (`id`),
  ADD CONSTRAINT `match_events_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`);

--
-- Filtros para la tabla `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tournament_categories`
--
ALTER TABLE `tournament_categories`
  ADD CONSTRAINT `tournament_categories_ibfk_1` FOREIGN KEY (`tournament_version_id`) REFERENCES `tournament_versions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
