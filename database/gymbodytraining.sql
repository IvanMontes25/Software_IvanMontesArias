-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-03-2026 a las 19:17:09
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
-- Base de datos: `gymbodytraining`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`user_id`, `username`, `password`, `name`) VALUES
(1, 'admin', '$2y$10$FKzLzlebrE2JLrMT86BhU.ytGu2enA6UI9/97jBziZ6oyg6Wxfqzy', 'Administracion'),
(2, 'igmontes', 'igmontes', ''),
(4, 'maria', '12345', ''),
(5, 'ivanmontes', '12345', ''),
(13, 'entrenador', '$2y$10$oybXJlJlrm6xztSkzLk7x.E1jt7t43xIZQWnsKtl.uvnFtfQsKTfS', 'entrenador');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `administradores`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `administradores` (
`id_usuario` int(11)
,`usuario` varchar(50)
,`clave` varchar(255)
,`nombre` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_inbox`
--

CREATE TABLE `admin_inbox` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `origen` varchar(50) DEFAULT 'sistema',
  `prioridad` enum('baja','media','alta') DEFAULT 'media',
  `is_read` tinyint(1) DEFAULT 0,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin_inbox`
--

INSERT INTO `admin_inbox` (`id`, `tipo`, `titulo`, `mensaje`, `origen`, `prioridad`, `is_read`, `payload`, `created_at`) VALUES
(1, 'vencimiento', 'Reporte de vencimientos - 19/02/2026 - Por vencer: 0 / Vencidas: 1', '[VENCIDA] Betty Calle - Aeróbicos - Vencio hace 750 dias - 31/01/2024', 'n8n', 'alta', 1, '{\"tipo\":\"vencimiento\",\"titulo\":\"Reporte de vencimientos - 19\\/02\\/2026 - Por vencer: 0 \\/ Vencidas: 1\",\"mensaje\":\"[VENCIDA] Betty Calle - Aer\\u00f3bicos - Vencio hace 750 dias - 31\\/01\\/2024\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-19 23:41:19'),
(2, 'general', 'Notificacion', '', 'n8n', 'media', 1, '{\"origen\":\"n8n\"}', '2026-02-19 23:54:12'),
(3, 'pago', 'Pago confirmado - Carlos Paredes Candia - Bs. 100', '`Pago confirmado', 'n8n', 'alta', 1, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Carlos Paredes Candia - Bs. 100\",\"mensaje\":\"`Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 00:04:28'),
(4, 'pago', 'Pago confirmado - Hilarion Mamani - Bs. 98', 'Pago confirmado', 'n8n', 'alta', 0, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Hilarion Mamani - Bs. 98\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 18:41:40'),
(5, 'pago', 'Pago confirmado - Tatiana Morales - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 0, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Tatiana Morales - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:20:42'),
(6, 'pago', 'Pago confirmado - Tatiana Morales - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 0, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Tatiana Morales - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:20:46'),
(7, 'pago', 'Pago confirmado - Tatiana Morales - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 0, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Tatiana Morales - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:20:50'),
(8, 'pago', 'Pago confirmado - Tatiana Morales - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 0, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Tatiana Morales - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:20:53'),
(9, 'pago', 'Pago confirmado - Tatiana Morales - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 0, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Tatiana Morales - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:20:57'),
(10, 'pago', 'Pago confirmado - Tatiana Morales - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 1, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Tatiana Morales - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:21:01'),
(11, 'pago', 'Pago confirmado - Nacho Montes Arias - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 1, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Nacho Montes Arias - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:54:02'),
(12, 'pago', 'Pago confirmado - Gonzalo Montes Arias - Bs. 100', 'Pago confirmado', 'n8n', 'alta', 1, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Gonzalo Montes Arias - Bs. 100\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 22:58:52'),
(13, 'pago', 'Pago confirmado - Ivan Montes - Bs. 98', 'Pago confirmado', 'n8n', 'alta', 1, '{\"tipo\":\"pago\",\"titulo\":\"Pago confirmado - Ivan Montes - Bs. 98\",\"mensaje\":\"Pago confirmado\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-20 23:13:30'),
(14, 'abandono', 'Alerta abandono — 16 críticos, 0 medios', 'Clientes en riesgo: 16 total. Críticos: 16. Medios: 0.', 'n8n', 'alta', 0, '{\"tipo\":\"abandono\",\"titulo\":\"Alerta abandono \\u2014 16 cr\\u00edticos, 0 medios\",\"mensaje\":\"Clientes en riesgo: 16 total. Cr\\u00edticos: 16. Medios: 0.\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-22 22:11:37'),
(15, 'reporte', 'Reporte semanal — 23/02/2026 al 22/02/2026', 'Ingresos: Bs.7300.00 (+217.9% vs semana anterior) | Nuevos: 69 | Activas: 112 | Por vencer: 0', 'n8n', 'media', 0, '{\"tipo\":\"reporte\",\"titulo\":\"Reporte semanal \\u2014 23\\/02\\/2026 al 22\\/02\\/2026\",\"mensaje\":\"Ingresos: Bs.7300.00 (+217.9% vs semana anterior) | Nuevos: 69 | Activas: 112 | Por vencer: 0\",\"origen\":\"n8n\",\"prioridad\":\"media\"}', '2026-02-22 22:12:50'),
(16, 'segmentacion', 'Segmentación semanal — 22/02/2026', 'VIP: 5 | Regular: 17 | Nuevo: 116 | Dormido: 3 | Perdido: 2 | Sin_pagos: 24', 'n8n', 'media', 1, '{\"tipo\":\"segmentacion\",\"titulo\":\"Segmentaci\\u00f3n semanal \\u2014 22\\/02\\/2026\",\"mensaje\":\"VIP: 5 | Regular: 17 | Nuevo: 116 | Dormido: 3 | Perdido: 2 | Sin_pagos: 24\",\"origen\":\"n8n\",\"prioridad\":\"media\"}', '2026-02-22 22:14:39'),
(17, 'reporte', 'Reporte semanal — 23/02/2026 al 22/02/2026', 'Ingresos: Bs.7300.00 (+217.9% vs semana anterior) | Nuevos: 69 | Activas: 112 | Por vencer: 0', 'n8n', 'media', 0, '{\"tipo\":\"reporte\",\"titulo\":\"Reporte semanal \\u2014 23\\/02\\/2026 al 22\\/02\\/2026\",\"mensaje\":\"Ingresos: Bs.7300.00 (+217.9% vs semana anterior) | Nuevos: 69 | Activas: 112 | Por vencer: 0\",\"origen\":\"n8n\",\"prioridad\":\"media\"}', '2026-02-22 23:06:11'),
(18, 'vencimiento', 'Reporte de vencimientos - 22/02/2026 - Por vencer: 0 / Vencidas: 1', '[VENCIDA] Betty Calle - Aeróbicos - Vencio hace 753 dias - 31/01/2024', 'n8n', 'alta', 0, '{\"tipo\":\"vencimiento\",\"titulo\":\"Reporte de vencimientos - 22\\/02\\/2026 - Por vencer: 0 \\/ Vencidas: 1\",\"mensaje\":\"[VENCIDA] Betty Calle - Aer\\u00f3bicos - Vencio hace 753 dias - 31\\/01\\/2024\",\"origen\":\"n8n\",\"prioridad\":\"alta\"}', '2026-02-22 23:06:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `date` date NOT NULL,
  `images_json` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `announcements`
--

INSERT INTO `announcements` (`id`, `message`, `date`, `images_json`) VALUES
(1, 'esta es una publicacion de prueba!', '2025-09-16', NULL),
(2, 'es la segunda prueba!', '2025-09-16', NULL),
(3, 'ewqewq', '2025-09-25', NULL),
(5, 'hOLAA 28 DE ENERO', '2026-01-28', NULL),
(6, 'HELOU 28 DE ENERO', '2026-01-28', '[\"uploads/publicaciones/pub_20260128_195417_be2bef7eb6fc.png\"]'),
(7, 'pueba de imagen', '2026-01-28', '[\"uploads/publicaciones/pub_20260128_195534_619ef3af44cf.png\"]'),
(8, 'Hola gente!', '2026-01-28', NULL),
(9, 'Hola gente!', '2026-01-28', NULL),
(10, 'Publicacion del 13 de febrero!', '2026-02-13', '[\"uploads/publicaciones/pub_20260213_171940_ea02d852038a.jpg\"]'),
(11, 'Se comunica a todos los clientes que el dia sábado 20 de febrero no se atendera el gimnasio por moti', '2026-02-20', NULL),
(12, 'ewqewqewqrrqw', '2026-02-20', NULL),
(13, 'Se comunica a todoasdnqwjrqwjrbjwqfwqnbfjkwqbfjkbqw jkfb wqjdbjqwnedkjwqebwqjlrfbwqjlrjhnwqoñhrjqwñhrwqr', '2026-02-20', NULL),
(14, 'eqw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrfw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrfw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrfw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrfw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrfw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrf', '2026-02-02', NULL),
(15, 'w eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrfw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrfw eiqweiuwqewqfrjopskgfpodsjhpefjhprwejhirewjfieqjhfuqerh9q2u8ry9hrf', '2026-02-20', NULL),
(16, 'rqrwqrwqffdsf', '2026-02-20', NULL),
(17, 'ewwqqqqqqq', '2026-02-20', NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `anuncios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `anuncios` (
`id` int(11)
,`mensaje` longtext
,`fecha` date
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `asistencias`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `asistencias` (
`id` int(11)
,`id_usuario` int(100)
,`fecha` text
,`hora` text
,`presente` tinyint(4)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(100) NOT NULL,
  `curr_date` text NOT NULL,
  `curr_time` text NOT NULL,
  `present` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `curr_date`, `curr_time`, `present`) VALUES
(1, 24, '2025-09-07', '08:03 PM', 1),
(2, 24, '2025-09-10', '09:48 PM', 1),
(3, 24, '2025-09-16', '06:22 PM', 1),
(4, 24, '2025-09-18', '11:02 PM', 1),
(5, 1, '2025-09-22', '10:14 PM', 1),
(6, 27, '2025-09-22', '10:23 PM', 1),
(8, 24, '2025-09-25', '10:10 PM', 1),
(9, 24, '2025-09-29', '09:44 PM', 1),
(13, 24, '2025-11-29', '09:43 PM', 1),
(14, 61, '2025-11-29', '09:48 PM', 1),
(15, 24, '2025-12-10', '09:35 PM', 1),
(19, 33, '2026-01-26', '12:06 PM', 1),
(21, 24, '2026-01-26', '12:21 PM', 1),
(22, 83, '2026-01-26', '02:02 PM', 1),
(23, 68, '2026-01-26', '23:19:59', 1),
(24, 28, '2026-01-26', '23:31:54', 1),
(25, 62, '2026-01-26', '23:34:43', 1),
(26, 61, '2026-01-26', '23:35:09', 1),
(29, 74, '2026-01-26', '23:41:13', 1),
(30, 78, '2026-01-26', '23:41:19', 1),
(32, 70, '2026-01-26', '23:47:42', 1),
(33, 84, '2026-01-27', '12:02 AM', 1),
(36, 24, '2026-01-27', '04:01 PM', 1),
(37, 24, '2026-01-28', '12:15 PM', 1),
(38, 24, '2026-01-29', '08:40 PM', 1),
(39, 24, '2026-02-04', '12:16 PM', 1),
(42, 0, '2026-02-04', '12:45 PM', 1),
(43, 60, '2026-02-04', '12:50 PM', 1),
(44, 8, '2026-02-04', '02:14 PM', 1),
(57, 24, '2026-02-06', '12:26 AM', 1),
(64, 24, '2026-02-09', '10:10 PM', 1),
(68, 140, '2026-02-10', '09:35:47', 1),
(71, 108, '2026-02-11', '13:37:17', 1),
(73, 24, '2026-02-11', '11:21 PM', 1),
(75, 148, '2026-02-12', '08:10 PM', 1),
(77, 24, '2026-02-13', '05:09 PM', 1),
(79, 24, '2026-02-18', '11:44 PM', 1),
(80, 24, '2026-02-20', '02:16 PM', 1),
(81, 114, '2026-02-20', '03:23 PM', 1),
(82, 228, '2026-02-20', '03:44 PM', 1),
(83, 218, '2026-02-20', '03:52 PM', 1),
(84, 24, '2026-02-23', '04:17 PM', 1),
(85, 199, '2026-02-23', '10:55 PM', 1),
(86, 24, '2026-02-27', '05:59 PM', 1),
(87, 24, '2026-03-03', '10:51 AM', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ID del staff/admin que hizo la acción',
  `username` varchar(100) NOT NULL COMMENT 'Nombre de usuario del staff',
  `fullname` varchar(200) NOT NULL COMMENT 'Nombre completo del staff',
  `rol` varchar(50) NOT NULL COMMENT 'Rol: admin, recepcionista, cajero, etc.',
  `accion` varchar(50) NOT NULL COMMENT 'Tipo: crear_cliente, registrar_pago, etc.',
  `descripcion` text NOT NULL COMMENT 'Detalle legible de lo que se hizo',
  `modulo` varchar(50) NOT NULL COMMENT 'Módulo: clientes, pagos, asistencias, etc.',
  `ip` varchar(45) DEFAULT NULL COMMENT 'IP del usuario',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `username`, `fullname`, `rol`, `accion`, `descripcion`, `modulo`, `ip`, `created_at`) VALUES
(1, 12, 'squispe', 'Solange Quispe', 'recepcionista', 'crear_cliente', 'Inscribió al cliente Ricardo Mendoza (ID 247)', 'clientes', '192.168.1.210', '2026-02-23 20:34:24'),
(2, 12, 'squispe', 'Solange Quispe', 'recepcionista', 'crear_cliente', 'Inscribió al cliente desdestaff (ID 248)', 'clientes', '192.168.1.210', '2026-02-23 20:39:42'),
(3, 12, 'squispe', 'Solange Quispe', 'recepcionista', 'crear_cliente', 'Inscribió al cliente desdeadmin (ID 249)', 'clientes', '192.168.1.210', '2026-02-23 20:40:51'),
(4, 12, 'squispe', 'Solange Quispe', 'recepcionista', 'crear_cliente', 'Inscribió al cliente desdstaf (ID 250)', 'clientes', '192.168.1.210', '2026-02-23 20:43:24'),
(5, 12, 'squispe', 'Solange Quispe', 'recepcionista', 'registrar_pago', 'Pago de Bs 100 para desdstaf (ID 250) - Método: Transferencia', 'pagos', '192.168.1.210', '2026-02-23 20:43:29'),
(6, 1, 'admin', 'Administrador', 'admin', 'registrar_pago', 'Pago de Bs 15 para desdstaf (ID 250) - Método: Efectivo', 'pagos', '192.168.1.210', '2026-02-23 20:47:02'),
(7, 12, 'squispe', 'Solange Quispe', 'recepcionista', 'registrar_pago', 'Pago de Bs 15 para Carla Jimenez (ID 147) - Método: Efectivo', 'pagos', '192.168.1.210', '2026-02-23 20:48:11'),
(8, 1, 'admin', 'Administrador', 'admin', 'crear_plan', 'Creó plan: Plan Carnavalero', 'administracion', '192.168.1.210', '2026-02-23 22:10:42'),
(9, 1, 'admin', 'Administrador', 'admin', 'crear_cliente', 'Inscribió al cliente Marcelo Jimenez (ID 251)', 'clientes', '192.168.1.210', '2026-02-23 22:11:16'),
(10, 1, 'admin', 'Administrador', 'admin', 'registrar_pago', 'Pago de Bs 99 para Marcelo Jimenez (ID 251) - Método: Efectivo', 'pagos', '192.168.1.210', '2026-02-23 22:11:27'),
(11, 1, 'admin', 'Administrador', 'admin', 'eliminar_plan', 'Desactivó el plan ID 9', 'administracion', '192.168.1.210', '2026-02-23 22:11:54'),
(12, 1, 'admin', 'Administrador', 'admin', 'crear_cliente', 'Inscribió al cliente Marcelo Quenallata (ID 252)', 'clientes', '127.0.0.1', '2026-02-24 09:53:01'),
(13, 1, 'admin', 'Administrador', 'admin', 'registrar_pago', 'Pago de Bs 100 para Marcelo Quenallata (ID 252) - Método: QR', 'pagos', '127.0.0.1', '2026-02-24 09:53:19'),
(14, 1, 'admin', 'Administrador', 'admin', 'registrar_pago', 'Pago de Bs 12 para Anthony Apaza (ID 108) - Método: QR', 'pagos', '127.0.0.1', '2026-02-24 10:06:09'),
(15, 1, 'admin', 'Administrador', 'admin', 'crear_staff', 'Registró al personal entrenador (Entrenador) - usuario: entrenador', 'administracion', '192.168.1.210', '2026-02-27 20:52:33'),
(16, 1, 'admin', 'Administrador', 'admin', 'agendar_clase', 'Agendó clase (sesión #1) para 2026-02-28 09:00-10:00, cupo 15', 'clases', '127.0.0.1', '2026-02-28 10:41:26'),
(17, 24, 'admin', 'Administrador', 'admin', 'agendar_clase', 'Agendó clase (sesión #2) para 2026-02-28 11:30-12:30, cupo 15', 'clases', '127.0.0.1', '2026-02-28 11:12:55'),
(18, 24, 'admin', 'Administrador', 'admin', 'reservar_clase', 'Cliente #24 reservó sesión #2', 'clases', '127.0.0.1', '2026-02-28 11:13:13'),
(19, 13, 'entrenador', 'entrenador', 'entrenador', 'agendar_clase', 'Agendó clase (sesión #8) para 2026-02-28 13:00-14:00, cupo 1', 'clases', '127.0.0.1', '2026-02-28 11:55:21'),
(20, 24, 'entrenador', 'entrenador', 'entrenador', 'reservar_clase', 'Cliente #24 reservó sesión #8', 'clases', '127.0.0.1', '2026-02-28 11:55:53'),
(21, 13, 'entrenador', 'entrenador', 'entrenador', 'agendar_clase', 'Agendó clase (sesión #9) para 2026-02-28 14:00-15:00, cupo 2', 'clases', '192.168.1.210', '2026-02-28 13:33:54'),
(22, 13, 'entrenador', 'entrenador', 'entrenador', 'agendar_clase', 'Agendó clase (sesión #10) para 2026-02-28 19:00-20:00, cupo 7', 'clases', '192.168.1.210', '2026-02-28 16:24:25'),
(23, 24, 'desconocido', 'desconocido', 'desconocido', 'reservar_clase', 'Cliente #24 reservó sesión #10', 'clases', '127.0.0.1', '2026-02-28 16:29:59'),
(24, 1, 'admin', 'Administrador', 'admin', 'crear_staff', 'Registró al personal entrenadordos (Entrenador) - usuario: entrenadordos', 'administracion', '192.168.1.210', '2026-02-28 19:02:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `automation_logs`
--

CREATE TABLE `automation_logs` (
  `id` int(11) NOT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `automation_logs`
--

INSERT INTO `automation_logs` (`id`, `event_type`, `user_id`, `payment_id`, `message`, `created_at`) VALUES
(1, 'pago_confirmado', 194, 223, 'Aquí tienes algunas opciones, elige la que mejor se adapte al tono de tu comunicación:\n\n**Opción 1 (Concisa y directa):**\n¡Pago confirmado! Estimado Usuario 194, hemos recibido tu pago de 100 Bs con fecha 19/02/2026. ¡Gracias!\n\n**Opción 2 (Un poco más formal):**\nEstimado Usuario 194, le confirmamos la recepción y procesamiento de su pago de 100 Bs, realizado el 19 de febrero de 2026. Gracias.\n\n**Opción 3 (Muy breve):**\nPago de 100 Bs del Usuario 194 confirmado el 19/02/2026. ¡Gracias!', '2026-02-19 11:13:22'),
(2, 'pago_confirmado', 195, 224, 'Aquí tienes algunas opciones, dependiendo del nivel de formalidad y brevedad que necesites:\n\n**Opción 1 (Concisa y Directa):**\n> ¡Pago confirmado! Hemos recibido 100 Bs del usuario 195. Fecha: 2026-02-19.\n\n**Opción 2 (Un poco más formal):**\n> Confirmación de Pago: Su transacción de 100 Bs del usuario 195 ha sido procesada exitosamente el 2026-02-19.\n\n**Opción 3 (Tipo SMS/Notificación):**\n> Pago CONFIRMADO: 100 Bs de U-195. 2026-02-19.', '2026-02-19 11:26:45'),
(3, 'pago_confirmado', 196, 225, 'Aquí tienes algunas opciones, elige la que mejor se adapte al tono de tu comunicación:\n\n**Opción 1 (Concisa y directa):**\n\n> ¡Pago confirmado!\n> Hemos recibido exitosamente el pago de **100 Bs** del **Usuario 196**, realizado el **2026-02-19**. ¡Gracias!\n\n**Opción 2 (Un poco más formal):**\n\n> Estimado(a) Usuario 196,\n>\n> Le informamos que su pago de **100 Bs** con fecha **2026-02-19** ha sido confirmado exitosamente.\n>\n> Agradecemos su confianza.\n\n**Opción 3 (Muy breve):**\n\n> Confirmamos la recepción del pago de 100 Bs del Usuario 196, el 2026-02-19. ¡Gracias!', '2026-02-19 11:29:40'),
(4, 'pago_confirmado', 197, 226, 'Aquí tienes algunas opciones, dependiendo del tono y la formalidad:\n\n**Opción 1 (Concisa y directa):**\n\n> ¡Tu pago ha sido confirmado! Hemos recibido tus 100 Bs el 2026-02-19. Gracias.\n\n**Opción 2 (Con un poco más de detalle/referencia):**\n\n> Estimado/a Usuario/a 197,\n> Confirmamos la recepción de tu pago por 100 Bs, realizado el 2026-02-19.\n> ¡Gracias por tu transacción!\n\n**Opción 3 (Muy corta):**\n\n> Pago confirmado: 100 Bs del Usuario 197 el 2026-02-19. Gracias.', '2026-02-19 11:31:54'),
(5, 'pago_confirmado', 198, 227, 'Aquí tienes algunas opciones, dependiendo del tono y la brevedad que necesites:\n\n**Opción 1 (Muy corta y directa):**\nPago confirmado: Usuario 198. Monto: 100 Bs. Fecha: 2026-02-19.\n\n**Opción 2 (Un poco más formal):**\nSe confirma el pago de 100 Bs del usuario 198, registrado el 2026-02-19.\n\n**Opción 3 (Con un saludo si es para el usuario):**\n¡Pago confirmado! Hemos recibido tu pago de 100 Bs (usuario 198) el 2026-02-19.', '2026-02-19 11:34:58'),
(6, 'pago_confirmado', 199, 228, 'Se confirma el pago de 100 Bs realizado por el Usuario 199 el 2026-02-19 en Efectivo.', '2026-02-19 11:38:34'),
(7, 'pago_confirmado', 202, 231, 'Pago confirmado de 100 Bs de usuario 202 el 2026-02-19 vía Efectivo.', '2026-02-19 11:48:21'),
(8, 'pago_confirmado', 203, 232, 'Pago confirmado de 100 Bs (Efectivo) el 2026-02-19.', '2026-02-19 11:55:13'),
(9, 'pago_confirmado', 204, 233, '¡Hola! Su pago de 98 Bs, realizado en Efectivo el 2026-02-19, ha sido confirmado.', '2026-02-19 11:57:38'),
(10, 'pago_confirmado', 205, 234, '¡Pago confirmado! Se ha registrado el pago de 100 Bs. de Marcelo Gutierrez, realizado el 2026-02-19 mediante Efectivo. Gracias.', '2026-02-19 12:08:24'),
(11, 'pago_confirmado', 205, 234, '¡Pago confirmado! Hemos recibido los 100 Bs. de Marcelo Gutierrez el 2026-02-19 en efectivo. ¡Gracias!', '2026-02-19 12:08:27'),
(12, 'pago_confirmado', 206, 235, 'Se confirma el pago de 100 Bs. de Karla Sirpa, realizado el 2026-02-19 en Efectivo. ¡Gracias!', '2026-02-19 12:11:31'),
(13, 'pago_confirmado', 209, 238, '¡Pago confirmado! Hemos recibido el pago de Ximena Apaza por 98 Bs. el 2026-02-19 mediante Efectivo. Notificacion generada apartir de n8n.', '2026-02-19 12:17:28'),
(14, 'pago_confirmado', 210, 239, 'Confirmamos que Karen Mendoza realizó un pago de 100 Bs. el 19/02/2026 mediante Efectivo. Notificacion generada apartir de n8n.', '2026-02-19 12:32:30'),
(15, 'pago_confirmado', 211, 240, '¡Hola! Confirmamos la recepción del pago de 100 Bs. realizado por Ignacio Montes Arias el 19/02/2026 a través de Efectivo. Notificación generada a partir de n8n.', '2026-02-19 14:01:46'),
(16, 'pago_confirmado', 212, 241, 'Se ha registrado exitosamente el pago de Ignaccio Montess por 100 Bs. el 2026-02-19, realizado mediante Efectivo. Notificacion generada apartir de n8n.', '2026-02-19 14:33:47'),
(17, 'pago_confirmado', 213, 242, '¡Pago confirmado! imontesss realizó un pago de 100 Bs. el 2026-02-19 mediante Efectivo. Notificacion generada apartir de n8n.', '2026-02-19 14:34:35'),
(18, 'pago_confirmado', 216, 245, NULL, '2026-02-19 15:07:42'),
(19, 'pago_confirmado', 217, 246, NULL, '2026-02-19 15:13:42'),
(20, 'pago_confirmado', 218, 247, NULL, '2026-02-19 15:23:22'),
(21, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola !  Tu pago de Bs.  fue registrado correctamente el  mediante .  ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #)', '2026-02-19 15:24:16'),
(22, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola !  Tu pago de Bs.  fue registrado correctamente el  mediante .  ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #)', '2026-02-19 15:29:17'),
(23, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola !  Tu pago de Bs.  fue registrado correctamente el  mediante .  ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #)', '2026-02-19 15:35:52'),
(24, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola ! Tu pago de Bs.  fue registrado correctamente el  mediante . ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #)\n', '2026-02-19 15:54:17'),
(25, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola ! Tu pago de Bs.  fue registrado correctamente el  mediante . ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #)\n', '2026-02-19 16:02:13'),
(26, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Dayana Velasquez! Tu pago de Bs. 98 fue registrado correctamente el 2026-02-19 mediante Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #253)\n\n', '2026-02-19 16:07:14'),
(27, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Kevin Apaza! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-19 mediante Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #254)\n\n', '2026-02-19 17:07:43'),
(28, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Juancito Pinto! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-19 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #255)\n\n', '2026-02-19 23:54:08'),
(29, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Carlos Paredes Candia! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #256)\n\n', '2026-02-20 00:04:23'),
(30, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Hilarion Mamani! Tu pago de Bs. 98 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #258)\n\n', '2026-02-20 18:41:37'),
(31, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Ivancho Montes! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #259)\n\n', '2026-02-20 19:35:11'),
(32, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #264)\n\n', '2026-02-20 22:20:38'),
(33, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #265)\n\n', '2026-02-20 22:20:42'),
(34, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #266)\n\n', '2026-02-20 22:20:46'),
(35, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #267)\n\n', '2026-02-20 22:20:50'),
(36, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #268)\n\n', '2026-02-20 22:20:54'),
(37, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #269)\n\n', '2026-02-20 22:20:57'),
(38, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Nacho Montes Arias! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #270)\n\n', '2026-02-20 22:53:54'),
(39, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Gonzalo Montes Arias! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #271)\n\n', '2026-02-20 22:58:45'),
(40, 'pago_confirmado', NULL, NULL, '🎉 ¡Hola Ivan Montes! Tu pago de Bs. 98 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #272)\n\n', '2026-02-20 23:13:23'),
(41, 'alerta_abandono', 0, NULL, '', '2026-02-22 22:11:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases_reservas`
--

CREATE TABLE `clases_reservas` (
  `id` int(11) NOT NULL,
  `sesion_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `estado` enum('confirmada','cancelada','asistio','no_asistio','en_espera') DEFAULT 'confirmada',
  `posicion_espera` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cancelled_at` datetime DEFAULT NULL,
  `attended_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clases_reservas`
--

INSERT INTO `clases_reservas` (`id`, `sesion_id`, `cliente_id`, `estado`, `posicion_espera`, `created_at`, `cancelled_at`, `attended_at`) VALUES
(1, 2, 24, 'confirmada', NULL, '2026-02-28 15:13:13', NULL, NULL),
(2, 8, 24, 'confirmada', NULL, '2026-02-28 15:55:53', NULL, NULL),
(3, 10, 24, 'confirmada', NULL, '2026-02-28 20:29:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases_sesiones`
--

CREATE TABLE `clases_sesiones` (
  `id` int(11) NOT NULL,
  `tipo_clase_id` int(11) NOT NULL,
  `entrenador_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `cupo_maximo` int(11) NOT NULL DEFAULT 15,
  `cupo_disponible` int(11) NOT NULL DEFAULT 15,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activa','cancelada','finalizada') DEFAULT 'activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clases_sesiones`
--

INSERT INTO `clases_sesiones` (`id`, `tipo_clase_id`, `entrenador_id`, `fecha`, `hora_inicio`, `hora_fin`, `cupo_maximo`, `cupo_disponible`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 4, 13, '2026-02-28', '09:00:00', '10:00:00', 15, 15, '', 'activa', '2026-02-28 14:41:26', '2026-02-28 14:41:26'),
(2, 11, 13, '2026-02-28', '11:30:00', '12:30:00', 15, 14, '', 'activa', '2026-02-28 15:12:55', '2026-02-28 15:13:13'),
(8, 1, 13, '2026-02-28', '13:00:00', '14:00:00', 1, 0, '', 'activa', '2026-02-28 15:55:21', '2026-02-28 15:55:53'),
(9, 1, 13, '2026-02-28', '14:00:00', '15:00:00', 2, 2, '', 'activa', '2026-02-28 17:33:54', '2026-02-28 17:33:54'),
(10, 1, 13, '2026-02-28', '19:00:00', '20:00:00', 7, 6, '', 'activa', '2026-02-28 20:24:25', '2026-02-28 20:29:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase_tipos`
--

CREATE TABLE `clase_tipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3b82f6',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clase_tipos`
--

INSERT INTO `clase_tipos` (`id`, `nombre`, `descripcion`, `color`, `activo`, `created_at`) VALUES
(1, 'Karate Niños', '', '#6366f1', 1, '2026-02-28 02:24:33'),
(2, 'Yoga', NULL, '#10b981', 1, '2026-02-28 02:24:33'),
(3, 'Funcional', NULL, '#8b5cf6', 1, '2026-02-28 02:24:33'),
(4, 'Spinning', NULL, '#f59e0b', 1, '2026-02-28 02:24:33'),
(5, 'Boxeoewqeqw', 'eqwewq', '#ec4899', 1, '2026-02-28 02:24:33'),
(6, 'Crossfit', 'Entrenamiento funcional de alta intensidad', '#ef4444', 1, '2026-02-28 02:46:31'),
(7, 'Yoga', 'Flexibilidad, equilibrio y relajación', '#10b981', 1, '2026-02-28 02:46:31'),
(8, 'Funcional', 'Ejercicios con movimientos naturales del cuerpo', '#8b5cf6', 1, '2026-02-28 02:46:31'),
(9, 'Spinning', 'Ciclismo indoor de alta intensidad', '#f59e0b', 1, '2026-02-28 02:46:31'),
(10, 'Boxeo', 'Técnica de boxeo y cardio', '#10b981', 1, '2026-02-28 02:46:31'),
(11, 'Zumba', 'Baile fitness con música latina', '#ec4899', 1, '2026-02-28 02:46:31'),
(12, 'Pilates', 'Fortalecimiento del core y postura', '#6366f1', 1, '2026-02-28 02:46:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `amount` int(100) NOT NULL,
  `quantity` int(100) NOT NULL,
  `vendor` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `address` varchar(20) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `amount`, `quantity`, `vendor`, `description`, `address`, `contact`, `date`) VALUES
(24, 'Caminadora plomo', 1500, 2, 'Caminadora', 'Caminadora', 'Caminadora', '78794120', '2026-02-17'),
(25, 'Corredora e', 4800, 3, 'Corredora', 'Corredora', 'Corredora', '72048741', '2026-02-17'),
(27, 'Prensad', 1500, 2, 'Prensa', 'Prensa', '', '77961411', '2026-02-18'),
(28, 'Banca Plana', 1000, 3, 'Banca Plana', 'Banca Plana', '', '71542110', '2026-02-18');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `equipos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `equipos` (
`id` int(11)
,`nombre` varchar(30)
,`monto` int(100)
,`cantidad` int(100)
,`proveedor` varchar(50)
,`descripcion` varchar(50)
,`direccion` varchar(20)
,`contacto` varchar(10)
,`fecha` date
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logros`
--

CREATE TABLE `logros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `meta_asistencias` int(11) NOT NULL,
  `descuento_porcentaje` decimal(5,2) NOT NULL DEFAULT 0.00,
  `icono_fa` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logros`
--

INSERT INTO `logros` (`id`, `nombre`, `meta_asistencias`, `descuento_porcentaje`, `icono_fa`, `activo`, `creado_en`) VALUES
(1, 'Primera visita', 1, 0.00, 'fa-person-walking', 1, '2025-11-27 01:42:14'),
(2, 'Constancia semanal', 8, 10.00, 'fa-calendar-week', 1, '2025-11-27 01:42:14'),
(3, 'Mes completo', 12, 15.00, 'fa-calendar-check', 1, '2025-11-27 01:42:14'),
(4, 'Bronce', 50, 25.00, 'fa-medal', 1, '2025-11-27 01:42:14'),
(5, 'Plata', 100, 35.00, 'fa-award', 1, '2025-11-27 01:42:14'),
(6, 'Oro', 200, 50.00, 'fa-trophy', 1, '2025-11-27 01:42:14'),
(7, 'Diamante', 600, 80.00, '', 1, '2025-11-27 01:49:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members`
--

CREATE TABLE `members` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(120) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` varchar(20) NOT NULL DEFAULT 'Otro',
  `dor` date NOT NULL DEFAULT curdate(),
  `ci` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `attendance_count` int(11) NOT NULL DEFAULT 0,
  `reminder` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = recibe recordatorios, 0 = no recibe',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `password_reset_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `members`
--

INSERT INTO `members` (`user_id`, `fullname`, `username`, `password`, `gender`, `dor`, `ci`, `correo`, `contact`, `attendance_count`, `reminder`, `must_change_password`, `password_reset_at`) VALUES
(1, 'nacho', 'nacho', 'b623e24add2f342de2acdf8b4edad496', 'Masculino', '2025-09-05', '333333333', 'ivang23montes@gmail.com', '8521479633', 1, 1, 0, NULL),
(3, 'Valeria Gutierrez Ar', 'valeria', '7902b7c0be5cedb6fbada8d4c7fc42a0', 'Femenino', '2025-09-05', '222222222', 'valery@gmail.com', '69709696', 0, 1, 0, NULL),
(4, 'Carlos Nina', 'carlos', 'dc599a9972fde3045dab59dbd1ae170b', 'Masculino', '2025-08-06', '666666666', 'carlos25@gmail.com', '2454578', 0, 0, 0, NULL),
(10, 'juan', 'juan', 'a94652aa97c7211ba8954dd15a3cf838', 'Masculino', '2025-09-10', '986432451', 'iv412423ontes@gmail.com', '7777722', 0, 0, 0, NULL),
(14, 'ver', 'ver', '0812f14f43315611dd0ef462515c9d00', 'Masculino', '2025-09-10', '123848123', 'erewqete@gmail.com', '7773213777', 0, 1, 0, NULL),
(24, 'Ivan Gonzalo Montes Arias', 'ivanmontes', '$2y$10$capbUDebvxcv./jrUptcGO2uY/LhELdIhHjAFHCSal40tzeEhrMFS', 'Masculino', '2025-09-07', '9865351', 'ivang25montes@gmail.com', '69709696', 22, 1, 0, '2026-02-12 23:26:53'),
(25, 'nuevito', 'nuevito', '6db3f9d570aee33277c1ab48260c8689', 'Masculino', '2025-09-09', '67655543', 'iv43243253montes@gmail.com', '1112233112', 0, 1, 0, NULL),
(27, 'prueba1', 'prueba1', '3f1b7ccad63d40a7b4c27dda225bf941', 'Masculino', '2025-09-22', '653454323', 'prueba1@gmail.com', '7732132177', 1, 0, 0, NULL),
(29, 'guti', 'gutierr', 'e3e8f8dc9020c7d857ef6199c32a1b1a', 'Masculino', '2025-09-29', '64352535', 'guti24@gmail.com', '6654545696', 0, 0, 0, NULL),
(31, 'chelinn', 'chelin', 'b304ee01ef3bd9972c058244c42b54aa', 'Masculino', '2025-10-06', '89853241', 'chelin@gmail.com', '697054254', 3, 0, 0, NULL),
(32, 'pruebita', 'pruebita', 'c753088bca71224884eda3f942d73f60', 'Masculino', '2025-10-06', '43225634', 'pruebita@gmail.com', '69704326', 0, 1, 0, NULL),
(33, 'ter', 'ter', '7defa8ded3cb08c6aba1a571dd0035fd', 'Masculino', '2025-10-06', '7657345352', 'ter@gmail.com', '45436346', 1, 1, 0, NULL),
(34, 'Pruebastaff', 'Pruebastaff', '5290d4a2ba338714b54c595ebe01f2e5', 'Masculino', '2025-10-06', '68653563', 'Pruebastaff@gmail.com', '543223532', 0, 1, 0, NULL),
(35, 'vvv', 'vvv', '4fa7d43935db9be0d70478518dc4c0c7', 'Masculino', '2025-10-02', '423994544', 'vvv@gmail.com', '697876796', 0, 1, 0, NULL),
(60, 'gonzi', 'gonzi', 'd9669f18b1c7f45efb8d1ec4404f4d05', 'Male', '2025-11-28', '31231265351', 'ivang24montes@gmail.com', '32129696', 0, 0, 0, NULL),
(61, 'pruebita7', 'pruebita7', 'caaf2a6d3c86655c6c5eab5ef0f31736', 'Male', '2025-11-29', '453124545', 'ivang27montes@gmail.com', '6123309696', 1, 0, 0, NULL),
(64, 'veza', 'veza', 'a8fb3e519b1eff8ebc5cd4cba1272d79', 'Female', '2025-11-29', '98432415', 'ivang451montes@gmail.com', '6974144444', 0, 1, 0, NULL),
(65, 'titoy', 'titoy', '28490b0cd00785d553234b21975a891d', 'Male', '2025-11-29', '922165351', 'ivang77887montes@gmail.com', '69555240', 0, 1, 0, NULL),
(67, 'verta', 'verta', '4a29cc3200c104cf1af49aa81c019e09', 'Male', '2025-11-29', '12321248', 'iv3143montes@gmail.com', '4128677', 0, 0, 0, NULL),
(73, 'Jhovana', 'Jhovana', 'c4e909d0e13e564af4971d2d43ba636d', 'Female', '2025-11-30', '43124545', 'jhova25@gmail.com', '77541125', 0, 0, 0, NULL),
(75, 'julio', 'julio', 'c027636003b468821081e281758e35ff', 'Male', '2025-11-30', '45774544', 'julio25@gmail.com', '75256548', 0, 0, 0, NULL),
(76, 'romeo', 'romeo', '5d907853a9617cfd55fb62eae803595b', 'Male', '2026-12-01', '54253412', 'rome412@gmail.com', '60129697', 0, 1, 0, NULL),
(77, 'santos', 'santos', '114fdfefd3d69799f0b6f73ef764d405', 'Male', '2025-12-01', '54234545', 'santos12@gmail.com', '695432690', 0, 1, 0, NULL),
(80, 'Nancy', 'anancy', '7b0f81bdd2b24ba32cb27f6c16e6b900', 'Femenino', '2026-01-25', '3333416', 'anancy20@gmail.com', '69709696', 0, 0, 0, NULL),
(81, 'pruebavigencia', 'pruebavigencia', 'd897fd5379ebaee64477458edf77c83f', 'Masculino', '2025-12-25', '78889652', 'ivang24montes@hotmail.com', '67485212', 0, 1, 0, NULL),
(82, 'nuevocliente', 'nuevocliente', '5e88a757d1c0a26ef39da077d0cf3e17', 'Masculino', '2026-01-26', '55536621', 'nuevocliente@gmail.com', '78451200', 0, 1, 0, NULL),
(83, 'hilarion', 'hilarion', '74c15b637e6e89742c13ba1a68e6d395', 'Masculino', '2026-01-26', '3371266', 'hilarion@gmail.com', '72047055', 1, 1, 0, NULL),
(84, 'pruebadia', 'pruebadia', '1c0d42885cfc62c4d4245b3a01df4bf6', 'Masculino', '2026-01-26', '124214331', 'pruebadia@gmail.com', '598781232', 1, 1, 0, NULL),
(85, 'nuevoclienteg', 'nuevoclienteg', 'd09e86451f8c31ebd029c63758ca1532', 'Masculino', '2026-01-27', '778526325', 'nuevoclienteg@gmail.com', '45454220', 0, 1, 0, NULL),
(86, 'gonzalogonze', 'gonzalogonzi', 'ee2e9230bb0b675c1c93606e12b3a00b', 'Masculino', '2026-01-28', '12568995', 'gonzalogonzi@gmail.com', '745545520', 0, 0, 0, NULL),
(87, 'gonzalogonza', 'gonzalogonza', '7defb47c0244698f12a68d352052824c', 'Masculino', '2026-01-28', '789789585', 'gonzalogonza@gmail.com', '7599523', 0, 0, 0, NULL),
(88, 'Pamela Quiroga', 'pquiroga', 'ebad7034d2dbf318adfe9b0cfc967311', 'Femenino', '2026-01-28', '78989887', 'pquiroga@hotmail.com', '69700005', 0, 0, 0, NULL),
(90, 'teststaff', 'teststaff', 'c1beb55b00bd8305341530ef21d34842', 'Masculino', '2026-01-29', '11220003', 'teststaff@gmail.com', '7440110032', 0, 0, 0, NULL),
(91, 'testeo', 'testeo', 'a494bfd29b0333678e84861e0bd71c23', 'Masculino', '2026-01-29', '789877733', 'testeo@gmail.com', '96962355', 0, 0, 0, NULL),
(92, 'testeos', 'testeos', '03cf791e6c55514dd6ac12be15248745', 'Masculino', '2025-01-29', '56563722', 'testeos@gmail.com', '79898636', 0, 0, 0, NULL),
(93, 'teto', 'teto', '587cd982e580a3ae2ddba160031e0fb4', 'Masculino', '2026-01-30', '55569901', 'teto@gmail.com', '78798820', 0, 0, 0, NULL),
(94, 'pruebacontrasena', 'pruebacontrasena', '$2y$10$CHRCyaO0a4E.u3GDeCPiMOSVfNdkXdLpKudJCynQ8Cc07yjvHaWqC', 'Masculino', '2026-01-30', '79898770', 'pruebacontrasena@gmail.com', '77889205', 0, 0, 0, NULL),
(95, 'Guillermo Mamani', 'gmamani', '$2y$10$H8CC45CaL.llfclnqfC76.elLwm0dMFlrnmdXgASFGSXBZtPk2fma', 'Masculino', '2026-02-04', '789533301', 'gmamani@gmail.com', '65825319', 0, 0, 0, NULL),
(96, 'nuevotesteo', 'nuevotesteo', '$2y$10$Pcorb7wv2drSYd07xEh5cONK3uI4HIlSWH7gLtihuiF2gR48xNn96', 'Masculino', '2026-02-04', '7989532', 'nuevotesteo@gmail.com', '44466339', 0, 0, 0, NULL),
(97, 'Ignacio Montes', 'igmontes', '$2y$10$SrqIHxJP/Mu4epZozr.3iOvgxJxJCyYD/5dk0F0eoNnC2ZhKINJmm', 'Masculino', '2026-02-04', '78852207', 'igmontes@gmail.com', '99683203', 0, 0, 0, NULL),
(98, 'Marcelo Arias', 'marias', '$2y$10$qItt8yfQYu08oV9Ubq/OReeA5PwOY0g0WDzahK0ORA2HL36hEFOHe', 'Masculino', '2026-02-04', '79879993', 'marias@gmail.com', '62156489', 0, 0, 0, NULL),
(99, 'testeor', 'testeor', '$2y$10$5hE6WGk/qTbPIAAPBtlgb.b7UPiuxDxDPezrG0bvRDUS69OylzIV.', 'Masculino', '2026-02-04', '23214388', 'testeor@gmail.com', '558702354', 0, 0, 0, NULL),
(101, 'beladdq', 'bela', '$2y$10$IGEOgTkWzM4kz128QQWgRu6pDWz1xRqtmeUsV5eXXb6GQHEfRn3Ry', 'Masculino', '2026-02-04', '88655265', 'bela@gmail.com', '79884521', 0, 0, 0, NULL),
(102, 'nuevoingreso', 'nuevoingreso', '$2y$10$9lkijxvqyvoktEm57wfTcePOEKtuihKTNBzhJcjubZqQ2rSa1lJ52', 'Masculino', '2026-02-04', '47898253', 'nuevoingreso@gmail.com', '78954520', 0, 0, 0, NULL),
(103, 'Juan Flores', 'jflores', '$2y$10$py6AK14FYFeRsRYERljhDuQ894eG7kpE0ZypDGxnb/NWTRPrlyecq', 'Masculino', '2026-02-04', '78953215', 'jflores@gmail.com', '72058589', 0, 0, 0, NULL),
(104, 'Omar Espejo', 'oespejo', '$2y$10$JIhOIJJ1Ds38NoW3fs3YFuF7NVJznuo2hCSYJn6KQz./xlwiSrFbe', 'Masculino', '2026-02-04', '78985742', 'oespejo@gmail.com', '74512578', 0, 0, 0, NULL),
(105, 'Justin Vargas', 'jvargas', '$2y$10$5/UmxJQfQ9sKszfRs7W5f.DW1nBGbzvXgANXU.42eUMvbuzAGi9/m', 'Masculino', '2026-02-04', '78552589', 'jvargas@gmail.com', '78984112', 0, 0, 0, NULL),
(106, 'jeje', 'jeje', '$2y$10$nKw2C/hPMZJTiXSvHXx58OyLQiQFREGvsCU/D4WieBEt/4pSa5AAO', 'Masculino', '2026-02-04', '3124521513', 'jeje@gmail.com', '54216524', 0, 0, 0, NULL),
(107, 'Vilma Cepeda', 'vcepeda', '$2y$10$HGA1nVX7Uy1zwcm5qahXVOn0jWBY3z0NFB99XiNidRMyOIFDa6psy', 'Masculino', '2026-02-04', '78978788', 'vcepeda@gmail.com', '78549260', 0, 0, 0, NULL),
(108, 'Anthony Apaza', 'aapazaa', '$2y$10$WQ14pnYKULoh17y/4GjbIOVfl5JWzX/k0FgZwogOWy8hPLhxoF.by', 'Masculino', '2026-02-04', '74989494', 'aapaza@gmail.com', '79887944', 1, 0, 0, NULL),
(109, 'Fermín Davalos', 'fdavalos', '$2y$10$bc6JKKSnFL3zJULhHHqZuOFv3676d5paswKfy3a1vPxQp8TGu5jIG', 'Masculino', '2025-02-04', '79888492', 'fdavaloss@gmail.com', '79887984', 0, 0, 0, NULL),
(110, 'verolandia', 'verolandia', '$2y$10$g3v3Mvivy2IkdXfQXO2RFO5a.h5CmzjGjUSSybdevUAp0MmoDrFq2', 'Masculino', '2026-02-04', '79879520', 'verolandia@gmail.com', '89792311', 0, 0, 0, NULL),
(111, 'fjimenezz', 'fjimenez', '$2y$10$0nRRZdPdPvGSW7cVb2F13OgYAvOxTqQukOIGH7qGrJDBrRY.4WTg6', 'Masculino', '2026-02-04', '79513208', 'fjimenez@gmail.com', '77892512', 0, 0, 0, NULL),
(112, 'Fredy Calle', 'fcalle', '$2y$10$IAE.SC0tyObqjnWt3HdykupBAyQshKgiY1ddXJ/3yHYapjCClq.rW', 'Masculino', '2026-02-04', '79875231', 'fcalle@gmail.om', '777944110', 0, 1, 0, NULL),
(113, 'Tatiana Huanca', 'thuanca', '$2y$10$m/oWBtkJKl6/9P7M1EZWDOZARJzECkqXWxJnzHFMgm//CGsRv6Kki', 'Femenino', '2026-02-04', '53258793', 'thuanca@gmail.om', '36569823', 0, 0, 0, NULL),
(114, 'Betty Calle', 'bcalle', '$2y$10$z7wrEQ28B8Z0aG9tSE0t4ef4DKdJfX9AfaTYb4oSlZSqgrb3mWzE.', 'Femenino', '2026-02-04', '123452147', 'bcalle@gmail.com', '74441578', 1, 0, 0, NULL),
(115, 'Reynaldo Bautista', 'rbautista', '$2y$10$MN1vncyCsV76mf451BaOWObJnNsCeJttdjAaplSuev8VfXJopTHGa', 'Masculino', '2026-02-04', '88579245', 'rbautista@gmail.com', '77484121', 0, 0, 0, NULL),
(116, 'Damian Apaza', 'dapaza', '$2y$10$Esn9OiIdfZDuJu79whrVouBZMYoU/o2ljMQI8WxMGgWXB3uynD.fW', 'Masculino', '2026-02-04', '77898500', 'dapaza@gmail.com', '00871245', 0, 0, 0, NULL),
(117, 'Fabiana Vila', 'fvila', '$2y$10$Mh7fZK52YFZTYknAP.sAruqZ5iprNsRuoi9xp535b9giLyfNy.i9u', 'Femenino', '2026-02-04', '0585446', 'fvila@gmail.com', '0487862', 0, 0, 0, NULL),
(119, 'rwqrwqr', 'rwqrwqr', '$2y$10$hpinBma2pzNHG1CqWjuv/OwZuWcAtRse.VaAtwdzWq1.2n5MrCsqi', 'Masculino', '2026-02-04', '8707079', 'erqwrwq@gmail.com', '98797000', 0, 0, 0, NULL),
(120, 'ultimap', 'ultimap', '$2y$10$H0DmJf.G0rMX5Szy6L5iZ.5aQw8/V4i.Sb.yIs8gCIJkHxj2WUgZq', 'Masculino', '2026-02-04', '00379990', 'ultimap@gmail.com', '00798790', 0, 1, 0, NULL),
(121, 'ultime', 'ultime', '$2y$10$/wGwBolZiMa7H3KDkwd8leny52mYv5UojECN2aLoPc64h6wuytBT.', 'Masculino', '2026-02-04', '0408086', 'ultime@gmail.com', '03311548', 0, 1, 0, NULL),
(122, 'sara ralde', 'sralde', '$2y$10$sN25uoU0SqeybwgF7/usIus6PlbHerIqZn3FaUfJ6sbZyS8nLhK82', 'Femenino', '2026-02-04', '4879804', 'sralde@gmail.com', '798879044', 0, 0, 0, NULL),
(123, 'Karla Tintaya', 'ktintaya', '$2y$10$b6NMPhtlVLNJX3wwM5Lxw.rxQZE7tHp.xJHagKJz0kcSZ05qWMNye', 'Femenino', '2026-02-04', '007979044', 'ktintaya@gmail.com', '798794000', 0, 0, 0, NULL),
(124, 'rqtqwtwq', 'tqwtwqt', '$2y$10$EN77nYHhWaCUT4s1U6J1JuEBo/FgUhyFSprb5lDDHw.ZNUei2DJ1W', 'Masculino', '2026-02-05', '80098951', 'rqtqw@gmail.com', '1005894', 0, 0, 0, NULL),
(125, 'ivanchom', 'ivanchom', '$2y$10$MOOeeWvo2ddUR.3Bm3meXOY7NrER.m.sdwfF4NveGbDwoiqxTUBpS', 'Masculino', '2026-02-05', '100874954', 'ivanchom@gmail.com', '87700993', 0, 0, 0, NULL),
(128, 'vevee', 'vevee', '$2y$10$Gl6crA2ezjTKTPAj95/axO6F7VPuX0iC57M9bHOLvy6XWZ1TpYmIC', 'Femenino', '2026-02-05', '321321321321', 'vevee@gmail.com', '01127741', 0, 0, 0, NULL),
(129, 'nuevoingresot', 'nuevoingresot', '$2y$10$QALkqpfvVoqKTOwLPX6qZeYY.01.gs7lY904VUvXD5eUk8zcTQ81a', 'Masculino', '2026-02-05', '44898412', 'nuevoingresot@gmail.com', '688449696', 0, 0, 0, NULL),
(130, 'Yuri Huanca', 'yhuanca', '$2y$10$KFY70ksW99B51PKHNfaS7edV/0FRxOTFl2KaKYFp42zGKwFesegje', 'Masculino', '2026-02-05', '77899522', 'yhuanca@gmail.com', '001002140', 0, 0, 0, NULL),
(131, 'Ignacio Arias', 'iarias', '$2y$10$aUBDPg7m2hl/MOeWPDL6PepS2U2IRp/UdYW8VtRH1LDzBDfne92HK', 'Masculino', '2026-02-05', '77799521', 'iarias@gmail.com', '777899236', 0, 0, 0, NULL),
(132, 'pruebapago', 'pruebapago', '$2y$10$plZpyGKl60jR97s50H2YWO8b9gt6xQgb6ufzI9/Db13xDEJ0NK1I.', 'Masculino', '2026-02-05', '77988741', 'pruebapago@gmail.com', '77895100', 0, 0, 0, NULL),
(133, 'Hylda Mamani', 'hmamani', '$2y$10$BbDTrVucSELHNlWIID9cWurBnTRnbYaIPV2H4bX726vC.PyZpxcmm', 'Femenino', '2026-02-05', '0012477789', 'hmamani@gmail.com', '788977700', 0, 0, 0, NULL),
(134, 'pruebault', 'pruebault', '$2y$10$aw/49dBRBs6MAYCDpLJNMunYfml1j7Bua.Qiy4CD0.IIw7xNfbj76', 'Masculino', '2026-02-06', '777844242', 'pruebault@gmail.com', '99985021', 0, 0, 0, NULL),
(135, 'Roberto Cornejo', 'rcornejo', '$2y$10$3W5HvStBU5V7Tce6qv.yief4N7AKv8fA8zt564Seyckl7kSXrdVQi', 'Masculino', '2026-02-06', '77746444', 'rcornejo@gmail.com', '77741001', 0, 0, 0, NULL),
(137, 'prueagg', 'prueagg', '$2y$10$NdRe7kr8NWbwb2HA1fBi.ecXB9w/jQri/KOIXWCFspov5j2StyRdq', 'Masculino', '2026-02-06', '41241233', 'prueagg@gmail.com', '779998985', 0, 1, 0, NULL),
(138, 'tetet', 'tetet', '$2y$10$4Cg8KMmjcvQ2qvunPloWU.Fow0/Pg0Qw4lyhh0qnpmuEV0Tr02UeG', 'Masculino', '2026-02-06', '498798844', 'tetet@gmail.com', '797981200', 0, 0, 0, NULL),
(139, 'Daniel Ramos', 'dramos', '$2y$10$CKMTDOfO1bFFgfYoc0.fSumLh4qAOLl/sqlesMpzyUc2Qu28lKW8y', 'Masculino', '2026-02-06', '77798874', 'dramos@gmail.com', '77798421', 0, 0, 0, NULL),
(140, 'Berta balboa', 'bbbb', '$2y$10$trpt8XvU8wq/ZO6.Jy.0JuaoRyYymSUrqqKeTHXcYGjWIjRD5blJ2', 'Masculino', '2025-02-06', '13213441', 'bbbb@gmail.com', '777951412', 1, 0, 0, NULL),
(141, 'Ulises Gutierrez', 'ugutierrez', '$2y$10$W3D.A4qyaRPtG0YlLMOMpedWWcdTM.ezrJVZokCiDfAF3uWLnGn4q', 'Masculino', '2026-02-06', '88852237', 'ugutierrez@gmail.com', '54797121', 0, 0, 0, NULL),
(142, 'Jimena Vera', 'jvera', '$2y$10$GpdBzmO4Y41vpIpXdgx.ZuzbxdQVd0fxp68L.1oyuRTVMjnhR8lD6', 'Femenino', '2026-02-06', '78978125', 'jvera@gmail.com', '77898522', 0, 0, 0, NULL),
(143, 'Hilmer Balboa Jr.', 'hbalboa', '$2y$10$9J4D2SNpnHW.vaoC.4fF3ellSTcC9oHHuxCG5jfF07sZhakG3QrQO', 'Masculino', '2026-02-07', '77892335', 'hbalboa@gmail.com', '88995621', 0, 0, 0, NULL),
(144, 'Donovan Hernandez', 'dhernandez', '$2y$10$ioqpmBtbh6iPUufSL/amT.KCZCbgx0X2lxG0VX2QJFlB6nfEp3gea', 'Masculino', '2026-02-07', '77798220', 'dhernandez@gmail.com', '78894502', 0, 0, 0, NULL),
(145, 'Percy Mamani Huanca', 'pmamani', '$2y$10$3kAFTES8LBWFML0sbh2p9uRaAtUlcIGqwcP7fbJgIOvgVgKS0IgJy', 'Masculino', '2026-02-09', '789955584', 'pmamani@gmail.com', '6744511200', 0, 0, 0, NULL),
(146, 'nuevo ingreso', 'ningreso', '$2y$10$9UqZa9gu5RqZIUFLVhNAr.J728SKnHgqIvI8aIR/15YIgvevOXYiS', 'Masculino', '2026-02-11', '779874252', 'ningreso@gmail.com', '78980041', 0, 0, 0, NULL),
(147, 'Carla Jimenez', 'cjimenez', '$2y$10$/gjbSWG.YQIfxHCn6BUBqebzFeBrRFkBh2ysF/LDF0ru1AwGTP7Ba', 'Femenino', '2026-02-11', '00325498', 'cjimenez@gmil.acom', '007874111', 0, 0, 0, NULL),
(148, 'Vladimir Montes Arias', 'vmontes', '$2y$10$.w7RxXtjDdJdUzDaCGBNKuivvdMqdV6TPPqNTYnW8gyUiEg5X2uvy', 'Masculino', '2026-02-12', '7778899536', 'vmontes@gmail.com', '98895462', 1, 0, 0, NULL),
(149, 'pruebaproducto', 'pruebaproducto', '$2y$10$dlzJ.Qm8hbJ.sLpxZDxi3OGerdIaaZ6h3YJMzS/0khtQlRyvHmK3W', 'Masculino', '2026-02-12', '008879841', 'pruebaproducto@gmail.com', '007745411', 0, 0, 0, NULL),
(150, 'pepito flores', 'pflores', '$2y$10$xveyJlf0Tlkw488Kn2.lV.OQMFxb9zXCxnRDR69bzt1XK/MJ4VOJm', 'Masculino', '2026-02-12', '790907421', 'pflores@gmail.com', '879789412', 0, 0, 0, NULL),
(152, 'pruebamvc', 'pruebamvc', '$2y$10$Wamx6ZYWEv.Oz5QxKrzB3OwkH4s/PpUIfG.GCu3IKb/k./SSI5LIu', 'Masculino', '2026-02-13', '79788777', 'pruebamvc@gmail.com', '7797874110', 0, 1, 0, NULL),
(153, 'pruebitaar', 'pruebitaar', '$2y$10$uUT8UDsiYmwnQecNDKurnu0a3K4jnCa3.8Zfbh8MixOFsFbZk8CGe', 'Masculino', '2026-02-13', '044981454', 'pruebitaar@gmail.com', '779841200', 0, 0, 0, NULL),
(154, 'pruebahh', 'pruebahh', '$2y$10$BaJGEDydYwhE1g9miwA/L.lnqxe8qw8FhRhIzdSn9YwvZxJdfAUCK', 'Masculino', '2026-02-13', '797970410', 'pruebahh@gmail.com', '7987984311', 0, 0, 0, NULL),
(155, 'probaag', 'probaag', '$2y$10$n8fQgk4pxVv2Wx6QFmchSuBd8sqn8nWQQcKM/lv9UObgw.xOLKlNm', 'Masculino', '2026-02-13', '7978794161', 'probaag@gmail.com', '008841124', 0, 0, 0, NULL),
(156, 'nachitox', 'nachitox', '$2y$10$7gb2dIThL4D4p1QMUnZ4sOTLtdqPm5DQ2InFihW1kriXORa4DdeYC', 'Masculino', '2026-02-13', '7988798400', 'nachitox@gmail.com', '79879800', 0, 0, 0, NULL),
(157, 'probaj', 'probaj', '$2y$10$cr.9fFY2G1kfOyvNAsnaK.anUpiFl9bNzg7QsuPFTyAioFYSlJOyq', 'Masculino', '2026-02-13', '749878909', 'probaj@gmail.com', '7879878011', 0, 1, 0, NULL),
(160, 'probak', 'probak', '$2y$10$txVBbpyQF167AgvKfm774OGx/Lm7R/IauNheQ4Z.iemoqlGnp18rq', 'Masculino', '2026-02-13', '49889461', 'probak@gmail.com', '77987984', 0, 1, 0, NULL),
(161, 'pruebilla', 'pruebilla', '$2y$10$Sux2gI55lMJJ978cHLQbHuTDl2Mz1Rexl0QybxZy/yQCWanRCG/WO', 'Masculino', '2026-02-13', '798789741', 'pruebilla@gmail.com', '777780010', 0, 1, 0, NULL),
(164, 'pruebillau', 'pruebillau', '$2y$10$6SIaG2HrlZtZBRDmH/hyKOjT1uYRPV74Kw6uXuG8Q./KtMO3glvVe', 'Masculino', '2026-02-13', '79878636', 'pruebillau@gmail.com', '7775343450', 0, 1, 0, NULL),
(166, 'Gerardo Quispe', 'gquispe', '$2y$10$aV0lRkrZfXIYs6BfHf7OgOehFFs1j7nlegy2mZrdHLkO0CcXaebdq', 'Masculino', '2028-02-17', '889789140', 'gquispe@gmail.com', '778978011', 0, 1, 0, NULL),
(167, 'Tatiana Apaza', 'tapaza', '$2y$10$1pqv4sXWQjsmyZVBZxnrhevD7Ek/Oio7IeSqKo8wsYG10kwE1QNBG', 'Masculino', '2026-02-17', '798979078', 'tapaza@gmai.com', '799880002', 0, 1, 0, NULL),
(168, 'Nancy Huanca', 'nhuanca', '$2y$10$3utctnPgE63y6gt0yV7skOlZCfKL9NmYCSNR3cyIT0Y7UhJ2aV.ta', 'Masculino', '2026-02-17', '7477988880', 'nhuanca@gmail.com', '7789784110', 0, 1, 0, NULL),
(169, 'Nancy Uriona', 'nuriona', '$2y$10$bErxsaehrWezcNOmWdwvUetrSiEN2O5hDz6ul9loTF0W6xcb0cPP.', 'Femenino', '2026-02-17', '123214542532', 'nuriona@gmail.com', '999856354', 0, 1, 0, NULL),
(170, 'Emanuel Paredes', 'eparedes', '$2y$10$6zW1QuZBSs.xzqcH3Ac4lutR5wYKHFIaUwHkSJUJIRYFFlb2Yt.mC', 'Masculino', '2026-02-17', '7799801255', 'eparedes@gmail.com', '778410551', 0, 1, 0, NULL),
(171, 'Jose Taborga', 'jtaborga', '$2y$10$ZZPFfegz6IMhCOeBFKAMbOiF5fXO1vhcpE0hyYtDdLHhTEZEqUMba', 'Masculino', '2026-02-17', '787944211', 'jtaborga@gmail.com', '474770100', 0, 0, 0, NULL),
(172, 'Nachito Arias', 'narias', '$2y$10$xYLD9T7p3vrHRe4Li6J3cOn5xn8IB0.6nT90vm7we7fDJsURlLpUm', 'Masculino', '2026-02-18', '77784041', 'narias@gmail.com', '77520440', 0, 0, 0, NULL),
(173, 'Guillermo Fernandez', 'gfernandez', '$2y$10$chvo.4ZuGj.G3/qvqDmfKeSFiMNaikePnGgfOBv3xdFy5UCtPnkHq', 'Masculino', '2026-02-18', '79879841', 'gfernandez@gmail.com', '75441120', 0, 0, 0, NULL),
(174, 'gggr', 'ggr', '', 'Masculino', '2026-02-18', '7897741', 'grgr@gmail.com', '778797', 0, 1, 0, NULL),
(175, 'pruebann', 'pruebann', '$2y$10$VJHRPSBhoAmUy1/vTHbPsOC9sy/TNbEcAGjYzxSoelbXftD3uYI7u', 'Masculino', '2026-02-19', '77841155', 'pruebann@gmail.com', '77874111', 0, 0, 0, NULL),
(176, 'pruebaaaak', 'pruebaaaak', '$2y$10$rR.pWjlJvoS8zMStZvWYEu5LYguzhYPJi0REbbvjKDBrPOKUiCPJS', 'Masculino', '2026-02-19', '7898941', 'pruebaaaak@gmail.com', '77798440', 0, 0, 0, NULL),
(177, 'pruebaaaap', 'pruebaaaap', '$2y$10$X5ouF.W2Mio6eQnFyNUvUep5qQ1sQcyS5UFOCKGH3KM5WYp/l3weu', 'Masculino', '2026-02-19', '7798840', 'pruebaaaap@gmail.com', '7778410', 0, 0, 0, NULL),
(178, 'Humberto Jimenez', 'hjimenez', '$2y$10$gKd/T.xQq8zk3KAIJkrq9OdwJ1Z0jc.c./uS91IA6hFf11BHkCEbK', 'Masculino', '2026-02-19', '7784401', 'hjimenez@gmail.com', '77894101', 0, 0, 0, NULL),
(179, 'Aurelio Vera', 'averaa', '$2y$10$IjOoTo8CQU9c.yXd.p1TO.Ywa3yOG1qq9Pt0n07YQTp5qd.UuupYy', 'Masculino', '2026-02-19', '78984550', 'averaa@gmail.com', '77894122', 0, 0, 0, NULL),
(180, 'Carlos Mamani', 'cmamani', '$2y$10$K/4TwY2ISBDQ.DXZI89lteLHHoHaW9J2em2EFNQyvIX945vSIaSK6', 'Masculino', '2026-02-19', '78844122', 'cmamani@gmail.com', '77998410', 0, 0, 0, NULL),
(181, 'Ximena Enriquez', 'xenriquez', '$2y$10$dhqDItv8RIPq9nuWLHtJxeTkQgmkvlDfx1BvHUGBAIrlBxqT16fie', 'Femenino', '2026-02-19', '99885101', 'xenriquez@gmail.com', '779842111', 0, 0, 0, NULL),
(182, 'juan carlos', 'jcarlos', '$2y$10$QbQFAGBqOdMYAdMr6YW3Yucg6616HPZNv2NV8gxfHdOp/OPdsx8Cy', 'Masculino', '2026-02-19', '7987411', 'jcarlos@gmail.com', '547988711', 0, 0, 0, NULL),
(183, 'fasfsa', 'fsafsaf', '$2y$10$G0kmopsYSGjsVDgI0twg/OCTUFvJnT/dk6hHxxCnfOQoGqGTEIfdi', 'Masculino', '2026-02-19', '798798441', 'fsafsaf@gmail.com', '79887911', 0, 0, 0, NULL),
(184, 'Testeoh', 'Testeoh', '$2y$10$fSQjMZfltQNg2WyXDPE8seNOknDP.zJnUSt3GZAmkyCqNHHYqjDVa', 'Masculino', '2026-02-19', '87987984', 'Testeoh@gmail.com', '98798710', 0, 0, 0, NULL),
(185, 'pruebaset', 'pruebaset', '$2y$10$t.jmuox0Icle/tXNMEAyEOWisryPR7uhF24LuSovfgbt2nju9O1Ty', 'Masculino', '2026-02-19', '987987441', 'pruebaset@gmail.com', '7987891', 0, 0, 0, NULL),
(186, 'pruebasetf', 'pruebasetf', '$2y$10$TTaZFkBmkJiQFxk8YjCG/.1y6fkj5zNYpBgK/L.irKXCvIeN3zENS', 'Masculino', '2026-02-19', '749879411', 'pruebasetf@gmail.com', '77700111', 0, 0, 0, NULL),
(187, 'Zulma Mamani', 'zmamani', '$2y$10$UdEliEVW8hBPlfwUSO2l3upwx4Fi30kASDcTiXHafj3Q8Zqp84tZi', 'Femenino', '2026-02-19', '79884421', 'zmamani@gmail.com', '79879811', 0, 0, 0, NULL),
(188, 'Beto Gutierrez', 'bgutierrez', '$2y$10$vnO9EsfFyXr9glVar9LfiuvasL2KgkiPbjfidsdZNMlvSFCaZiV1a', 'Femenino', '2026-02-19', '79879811', 'bgutierrez@gmail.com', '78971100', 0, 0, 0, NULL),
(189, 'fwqeqw', 'fwqeqw', '$2y$10$Xy4Kjmuq0IUmOJHMl4V5nuU2evKHmKBdRDSGX0F45zT795KE806Uu', 'Masculino', '2026-02-19', '87987164', 'fwqeqw@gmail.com', '789887411', 0, 0, 0, NULL),
(190, 'Benito Perez', 'bperez', '$2y$10$vnHWD1piMuLxe3Rwnw12HOkkd6knvuqxy.URK7eK9BZVtqiFxiMiy', 'Masculino', '2026-02-19', '797984169', 'bperez@gmail.com', '879874111', 0, 0, 0, NULL),
(191, 'pruebawebhook', 'pruebawebhook', '$2y$10$pXjCHgSJSCNBvW.D2wmvwelRXdUAcefM/4kyifsvOQPOOAIyuBu.6', 'Masculino', '2026-02-19', '12321451', 'pruebawebhook@gmail.com', '7998851', 0, 0, 0, NULL),
(192, 'pruenbaauto', 'pruenbaauto', '$2y$10$eNiSnXrysxGRM57T7N.AGu3b22Cl9gVnNLt.Uc4GwMmvJ.rUOwbGu', 'Masculino', '2026-02-19', '897987411', 'pruenbaauto@gmail.com', '897987121', 0, 0, 0, NULL),
(193, 'hrthtr', 'hrthtr', '$2y$10$CXQPTo1i08wZr4XCSTZu.ObqX0HV0xytneC53Ieg2nUIHbB9MO0f2', 'Masculino', '2026-02-19', '987987890', 'hrthtr@gmail.com', '77987410', 0, 0, 0, NULL),
(194, 'mensaje', 'mensaje', '$2y$10$0hkY6ihEGNg/OFr3zXONr.wKpSR8LcX9BGnmSIOpQAUPCxWGXI7i.', 'Masculino', '2026-02-19', '8779871', 'mensaje@gmail.com', '7798771', 0, 0, 0, NULL),
(195, 'Jorge Arias', 'jarias', '$2y$10$tLrfgSUbeCdm3L0laGnSaOApWzn/dASrtmhJdv5jIXTG6a8jpjAqS', 'Masculino', '2026-02-19', '8978941', 'jarias@gmail.com', '47878911', 0, 0, 0, NULL),
(196, 'Emanuel Vera', 'everaa', '$2y$10$yMw/ZwHRb32ZVBHm1TMMWude5E8IrUIvBhhKajOcpZIEZp6h.AwOS', 'Masculino', '2026-02-19', '12412435', 'everaa@gmail.com', '897981121', 0, 0, 0, NULL),
(197, 'Emanuel Ramirez', 'eramirez', '$2y$10$SpvNysZsU2oN5iEblL78hOeLp7EBBEoX6hS3eNMs/7i1uQfuM5j96', 'Masculino', '2026-02-19', '749784001', 'eramirez@gamil.com', '5487891101', 0, 0, 0, NULL),
(198, 'Leonardo Ticona', 'lticona', '$2y$10$34KbSyF6WBKDQBA3vrGzhuLDBWzAwkyT5/AmO7EXT6COLJCXkSNQ2', 'Masculino', '2026-02-19', '798474110', 'lticona@gmail.com', '77841015', 0, 0, 0, NULL),
(199, 'Carlitos Jurado', 'cjurado', '$2y$10$hNWVUPSggcED10ewlVb8S.oVS0yiS4gxjaj0HgLOL1paE.1wR/HBq', 'Masculino', '2026-02-19', '89789711', 'cjurado@gamil.com', '77496140', 1, 0, 0, NULL),
(200, 'Juan Carlos Verda', 'jcvera', '$2y$10$3S/YpxdYnoWW6eXK..vPmuBPQTFFedlMwTlT2Sa5ArOT/KzA3BtMm', 'Masculino', '2026-02-19', '89798110', 'jcvera@gmail.com', '77987100', 0, 0, 0, NULL),
(201, 'Juana Balboa', 'jbalboaa', '$2y$10$qekqB1f77HuBVk/fGj0oG.348T1a3BeKMfGfn3FOeXJRMBjDU8WHW', 'Masculino', '2026-02-19', '79841315', 'jbalboaa@gmail.com', '89789101', 0, 0, 0, NULL),
(202, 'Vilma Pacasi', 'vpacasi', '$2y$10$oKke/GBKfjXgJ.sVWB3EX.HM0TOrniBFXoXH3QGn/RyEZe7MSSa9e', 'Masculino', '2026-02-19', '98798411', 'vpacasi@gmail.com', '2132134211', 0, 0, 0, NULL),
(203, 'Fredy Nina', 'fninana', '$2y$10$HvjTfYho6Tt/wtrKwGp9p.B84X4CbMKlw4lqa583Z0tw.RUvhEe0C', 'Masculino', '2026-02-19', '874891164', 'fninana@gmail.com', '744211044', 0, 0, 0, NULL),
(204, 'Veronica Juarez', 'vjuarez', '$2y$10$tPwQACxjvBy7JWjoWqeQueRGc7/6q1H16A5ayBJrwuHevTc1/gCte', 'Femenino', '2026-02-19', '94247782', 'vjuarez@gmail.com', '75441023', 0, 0, 0, NULL),
(205, 'Marcelo Gutierrez', 'mgutierrez', '$2y$10$vI9nCoCGROxVkIirjqJlauMnNLJnSjGkVeYsPaVxOTIIl0AtJ0yxm', 'Masculino', '2026-02-19', '798791011', 'mgutierrez@gmail.com', '77410143', 0, 0, 0, NULL),
(206, 'Karla Sirpa', 'ksirpa', '$2y$10$hdcsSPfrtNw.P5PKi59vxumPxOCtbKDnMEdeiC5kI6Zm8GqeKT2o2', 'Femenino', '2026-02-19', '74771100', 'ksirpa@gmail.com', '77401035', 0, 0, 0, NULL),
(207, 'Felipe Yupanqui', 'fyupanqui', '$2y$10$SbECNNxw93ssSNohQqc/4uXCOPo2lrMUtnLI39uZUTVe68S/FEDK2', 'Masculino', '2026-02-19', '778401011', 'fyupanqui@gmail.com', '771101541', 0, 0, 0, NULL),
(208, 'Silvia Enriquez', 'senriquez', '$2y$10$nHDgtnMd/XvTgNQ.KuW5xu1NGkCB55eHLwre7XSRWAJ.xqr4X3PDu', 'Femenino', '2026-02-19', '798044001', 'senriquez@gmail.com', '77798424', 0, 0, 0, NULL),
(209, 'Ximena Apaza', 'xapaza', '$2y$10$RYdz3.I5LasSTpRLBAGqueNzedLEJfaWOR5S7BlWBoJqj12Gmchc2', 'Femenino', '2026-02-19', '79887900', 'xapaza@gmail.com', '741558921', 0, 0, 0, NULL),
(210, 'Karen Mendoza', 'kmendoza', '$2y$10$HZv9WaItgMoPnWAMjY7Y6elsFsAosf63XbTPR5ziHdR1kLZiKCEse', 'Femenino', '2026-02-19', '999844611', 'kmendoza@gmail.com', '77984012', 0, 0, 0, NULL),
(212, 'Ignaccio Montess', 'imontess', '$2y$10$PMddiSPnnuRMFaMfW6L15.TAZULJUjcFvSAxUBHyRjro/a3jUVvc.', 'Masculino', '2026-02-19', '7897098704', 'imontess@gmail.com', '7987980401', 0, 0, 0, NULL),
(213, 'imontesss', 'imontesss', '$2y$10$P8kE9yE9NBj8hYymkqeJHuBJMCIQxe4fQzzuVJGAvVYfy2FmWe0aG', 'Masculino', '2026-02-19', '7987980700', 'imontesss@gmail.com', '87080906', 0, 0, 0, NULL),
(214, 'pepito prueba', 'pprueba', '$2y$10$xdHk3ZeRwyGzAO0oz0Mixe9oqpug4xPEsjlMmXoRcQb3zn9d1SmEG', 'Masculino', '2026-02-19', '19448400', 'pprueba@gmail.com', '777894010', 0, 0, 0, NULL),
(215, 'carlota mamani', 'cmamanii', '$2y$10$LX3vgKfVwIWQsKgbTSUOm.xxGz.njQOgVDqlxyTtgzg4uYA/8/tlm', 'Femenino', '2026-02-19', '8498440044', 'cmamanii@gmail.com', '77704014', 0, 0, 0, NULL),
(216, 'Fernando Tito', 'fertito', '$2y$10$GyEb4QwNSFo0Z1eIopz8GOZJpEYsdYp6hRntmMLBzFGcm.c7hhKTS', 'Masculino', '2026-02-19', '789078094', 'fertito@gmail.com', '771414050', 0, 0, 0, NULL),
(217, 'Daniel Ramos Arispe', 'dramosarisp', '$2y$10$hOA0fMKeRdTi7xKK7cStlu9L6gPGFQxJHM/mV9QSzGHXFlf/GVDmS', 'Masculino', '2026-02-19', '99851012', 'dramosarisp@gmail.com', '77841031', 0, 0, 0, NULL),
(218, 'Beronica Arias', 'berarias', '$2y$10$4Mh4LNGmn.6gPuF5Dg5UyO6yHq0KR6aS8W9Tcx1b1tnSmouOolpt.', 'Femenino', '2026-02-19', '3338416', 'berarias@gmail.com', '72501074', 1, 0, 0, NULL),
(219, 'Nancy Beronica Arias', 'nanbarias', '$2y$10$FDdMYHmM9DJzrV6QGwp.wuGhTV0/emG08Mf02UxUW/3CHxI0TC/3y', 'Femenino', '2026-02-19', '3338417', 'nanbarias@gmail.com', '72047019', 0, 0, 0, NULL),
(220, 'Carlos Jimenez', 'cjimenezz', '$2y$10$VqJMSztWnouSZL17lkf33OdkZekZp8TsGOXlRRLFUGSQvzg0PJ.0a', 'Masculino', '2026-02-19', '78970980', 'cjimenezz@gmail.com', '79800440', 0, 0, 0, NULL),
(221, 'Pepo Bautista', 'pbautista', '$2y$10$U1qN5EGQMD5gwpLrHfpRXOA9MI9aeYvjyfyrJEWFEQqnjKw6UeRAO', 'Masculino', '2026-02-19', '4600707800', 'pbautista@gmail.com', '7980781', 0, 0, 0, NULL),
(222, 'Dayana Vasquez', 'dvasquez', '$2y$10$PwXzmcseWb3uerhXzfhAvOndXOpkU.64M6hP6Ny5KuDR22fp43QcC', 'Femenino', '2026-02-19', '494900458410', 'dvasquez@gmail.com', '774401152', 0, 0, 0, NULL),
(223, 'Betty Montes Calle', 'bmontesc', '$2y$10$nH99LxeTIXE6pS71Wv1Bxe8Y9hdCPhtJlJG0rKZCyRFOQSOyZsjei', 'Femenino', '2026-02-19', '49880808', 'bmontesc@gmail.com', '48404012', 0, 0, 0, NULL),
(224, 'Dayana Velasquez', 'dvelasquez', '$2y$10$1Cgyo8RhNRLDDNnU2KBY2uDm5l47fn2XofU2ZNyUXWkoZ6id.T6la', 'Femenino', '2026-02-19', '904500550', 'dvelasquez@gmail.com', '778900410', 0, 0, 0, NULL),
(225, 'Kevin Apaza', 'kapaza', '$2y$10$ZelmIkkKC8Xf.f4MT9bJw./GD0lc8UL3X3Ew6D0jKwyHTMs9X3L6C', 'Masculino', '2026-02-19', '79808009', 'kapaza@gmail.com', '879841641', 0, 0, 0, NULL),
(226, 'Juancito Pinto', 'jpintoo', '$2y$10$J53E/Aap3po8OBxQ.INMse/6pzPaZSoZRe4dQTZLJgRsnwd8v2oC.', 'Masculino', '2026-02-19', '797986667', 'jpinto@gmail.com', '78979412', 0, 0, 0, NULL),
(227, 'Carlos Paredes Candia', 'cpcandia', '$2y$10$6OFpP2og/1EH/HfpSL/cN.FTCRjhfZ6EO71Wgo1kSaCgIEjYb5key', 'Masculino', '2026-02-20', '9849840904', 'cpcandia@gmail.com', '790870904', 0, 0, 0, NULL),
(228, 'Reynaldo Arias', 'rariass', '$2y$10$t070Hwbzdh2Cmly5zJkWEuISyqKxTDnf4aQTyLFXIExIu2OoDoSCO', 'Masculino', '2026-02-20', '09480980', 'rariass@gmail.com', '0480411010', 1, 0, 0, NULL),
(229, 'Hilarion Mamani', 'hmamanii', '$2y$10$rLipKarrfz4rLi5OXhFes.Odv2flOvUpJYHLRX.zjI2cYJDj8kHP2', 'Masculino', '2026-02-20', '9807498480', 'hmamanii@gmail.com', '5584004', 0, 0, 0, NULL),
(232, 'carlote', 'charlote', '$2y$10$ItkrGn7.kTrVHr/kpe5X1.2tFQowPBPhEU65ypf./.ZcWRxUcButC', 'Masculino', '0000-00-00', '89409840', 'charlote@gmail.com', '85089004', 0, 0, 0, NULL),
(234, 'Tatiana Morales', 'tmorales', '$2y$10$TuVYyt2TKxGHrPCcrf2CKurEqwA0pZtEu524iDJMplPn6sRdwcn9K', 'Femenino', '2026-02-20', 'ewewqe', 'tmorales@gmail.com', '4521542151', 0, 0, 0, NULL),
(235, 'ivan juarez', 'ijuarez', '$2y$10$YQlQZhQPfkEdqSbJ4uVO7.5QQCAc9IzvsgLi/NHGOM2ui6zDteBp.', 'Masculino', '2026-02-20', 'eweqw', 'ijuarez@gmail.com', '321321321', 0, 1, 0, NULL),
(236, 'fasfsafas', 'fasfsaf', '$2y$10$2JACPl/nHPs.7o7SeZjU6ucwmJjKFyPVA2/LCxyxetHRGWX78M3Dy', 'Masculino', '2026-02-20', '789708708', 'gariasss@gmail.com', '970907980', 0, 1, 0, NULL),
(237, 'Nacho Montes Arias', 'nachoarias', '$2y$10$JG0EdJyS/6EHLGwWvpxQuubg8e.LLXq5Lyy.X.XTz6e7w9YQgJchm', 'Masculino', '2023-02-20', '7090780154', 'vladimirmontesarias@gmail.com', '74890074', 0, 0, 0, NULL),
(238, 'Gonzalo Montes Arias', 'gonzmontes', '$2y$10$3dThRZ948e1IzfSywvwLmuo7VQJ8hYN/KAh4Ppet2SueothfhR9Pu', 'Masculino', '2026-02-20', '409849040', 'ivang21montes@gmail.com', '697096701', 0, 0, 0, NULL),
(239, 'Ivan Montes', 'imontes', '$2y$10$METJYskpiM/0l.Dm6yKln.nl5wAYR5A/3bLbuE.GGPpO0YO7SEQs.', 'Masculino', '2026-02-20', '49400489', 'i5044ewqwqe@gmail.com', '504015212', 0, 0, 0, NULL),
(240, 'ewqewqrwq', 'rwqrwqrqwr', '$2y$10$ke1Tn6.X3je2hVWBLtfFTOepibzWqys45lSBbofnmDbUILv20w8p2', 'Masculino', '2026-02-22', 'ewqewqfd', 'ewqewq@gmail.com', '400844040', 0, 0, 0, NULL),
(241, 'Felix Paredes', 'feparedes', '$2y$10$uzKbd82E53nX/z4yVR01/uc.SCLTF1H82pN72N00TrzsNog1tV.Iu', 'Masculino', '2026-02-23', '4045411444', 'feparedes@gmail.com', '121204041', 0, 0, 0, NULL),
(242, 'Hernan Vera', 'hervera', '$2y$10$yIwpxNHOfbhZlstB8N0khOw3rMRwFZdEH24uUNNgnevS2Sp7x6WC2', 'Masculino', '2026-02-23', '409640400', 'hervera@gmail.com', '5798701', 0, 0, 0, NULL),
(243, 'Saul Hinojosa', 'shinojosa', '$2y$10$HY9QzPi1AgjlRyuVBW39OOBuSeD033FbH0QteomWMur4GA6NF7i92', 'Masculino', '2026-02-23', '50940840804', 'shinojosa@gmail.com', '79874841', 0, 0, 0, NULL),
(244, 'Sara Ramirez', 'sramirez', '$2y$10$Tagi3rRRe3n8GpHxW1F51OsoZwciBO8WZEPDCT0hAZEOfDyuzBUDm', 'Femenino', '2026-02-23', '9878461212', 'sramirez@gmail.com', '72454451', 0, 0, 0, NULL),
(245, 'Nancy Claure', 'nclaure', '$2y$10$ENLn62.Vbck5kiiU56yOTOnU5FsRg4SC64QERaJUTOR1EwUeJxFp2', 'Femenino', '2026-02-23', '79808044', 'nclaure@gmail.com', '78721220', 0, 0, 0, NULL),
(246, 'Gabriel Ferrufino', 'gferrufino', '$2y$10$zDeC4OWWbidz7WuP0tH.OeAceM9D77H23n2jN1..WvveIl0ED3itm', 'Femenino', '2026-02-23', '8940840400', 'gferrufino@gmail.com', '79883234', 0, 0, 0, NULL),
(247, 'Ricardo Mendoza', 'rmendoza', '$2y$10$7ehOHW0f0yzMHwEAFYhQCeVK1VGAKxgWgPoqMB4c37AGzMDu2W7XS', 'Masculino', '2026-02-23', '78984449', 'rmendoza@gmail.com', '77897841', 0, 0, 0, NULL),
(248, 'desdestaff', 'desdestaff', '$2y$10$VYYP8kKwLpwL89dtK.Pb/.aULaa77UinjuZn6j1Ns.zJhM6fMM7oK', 'Masculino', '2026-02-23', '79874015', 'desdestaff@gmail.com', '75451021', 0, 0, 0, NULL),
(249, 'desdeadmin', 'desdeadmin', '$2y$10$y7ZBVHmXx0yn9NdQRSJJYO9tNz7KhCzL9FncL61Isu9q95kJbd02O', 'Masculino', '2026-02-23', '89789090', 'desdeadmin@gmail.com', '790808041', 0, 0, 0, NULL),
(250, 'desdstaf', 'desdstaf', '$2y$10$H3SWQSD4za.geS6wjCSMSeCqrLLO2CWItsF3mU4zqDzeOTgEkoixy', 'Masculino', '2026-02-23', '8978041', 'desdstaf@gmail.com', '725521150', 0, 0, 0, NULL),
(251, 'Marcelo Jimenez', 'mjimenez', '$2y$10$K22hDIvqF8w8vXULxZvbjuB6kWu2Fq1U2XzKJEv8ylW1cPxrHWpjm', 'Masculino', '2026-02-23', '97878941', 'mjimenez@gmail.com', '74652501', 0, 0, 0, NULL),
(252, 'Marcelo Quenallata', 'mquenallata', '$2y$10$6fiEnm2bILRSo5UpYHXJAOgfHvIrIHUcj75xyCJyeZRWQKkNg/yJq', 'Masculino', '2026-02-24', '897894411', 'mquenallata@gmail.com', '79781121', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `miembros`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `miembros` (
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `paid_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `productos` text DEFAULT NULL,
  `status` enum('pagado','anulado') NOT NULL,
  `method` enum('Efectivo','Tarjeta','QR','Transferencia','Otro') DEFAULT 'Efectivo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `paid_date`, `start_date`, `amount`, `plan_id`, `productos`, `status`, `method`, `created_at`) VALUES
(1, 4, '2025-09-05', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-05 20:12:24'),
(2, 4, '2025-09-05', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-05 20:12:28'),
(4, 24, '2025-09-07', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-08 00:03:19'),
(6, 11, '2025-09-10', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-11 01:55:26'),
(8, 4, '2025-09-18', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-19 02:07:31'),
(9, 4, '2025-09-18', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-19 02:13:13'),
(10, 4, '2025-09-18', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-19 02:19:30'),
(13, 10, '2025-09-18', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-19 02:21:47'),
(14, 1, '2025-09-18', NULL, 150.00, NULL, NULL, 'pagado', 'QR', '2025-09-19 02:32:21'),
(15, 27, '2025-09-22', NULL, 250.00, NULL, NULL, 'pagado', 'Efectivo', '2025-09-23 02:22:15'),
(18, 61, '2025-11-29', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-11-30 01:47:35'),
(20, 63, '2025-11-29', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-11-30 02:13:48'),
(21, 64, '2025-11-29', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-11-30 02:24:49'),
(23, 67, '2025-11-29', NULL, 1215.00, NULL, NULL, 'pagado', 'Efectivo', '2025-11-30 02:37:54'),
(24, 73, '2025-11-30', NULL, 100.00, NULL, NULL, 'pagado', 'QR', '2025-11-30 21:06:01'),
(26, 75, '2025-11-30', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-11-30 23:19:15'),
(28, 76, '2025-12-01', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-12-02 00:05:07'),
(29, 77, '2025-12-01', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-12-02 00:06:30'),
(32, 24, '2025-12-10', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2025-12-11 00:36:45'),
(33, 31, '2025-12-11', NULL, 99.00, NULL, NULL, 'pagado', 'Efectivo', '2025-12-12 01:47:27'),
(34, 80, '2026-01-25', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 01:46:29'),
(35, 81, '2026-01-26', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 14:30:13'),
(37, 24, '2026-01-26', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 15:57:49'),
(38, 82, '2026-01-26', NULL, 99.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 16:27:59'),
(39, 83, '2026-01-26', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 18:01:42'),
(40, 4, '2026-01-26', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 19:23:41'),
(41, 4, '2026-01-26', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 19:27:14'),
(42, 24, '2026-01-26', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-26 20:34:03'),
(43, 84, '2026-01-27', NULL, 190.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-27 04:01:33'),
(44, 85, '2026-01-27', NULL, 190.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-27 12:12:38'),
(47, 63, '2026-01-27', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-27 21:15:25'),
(48, 10, '2026-01-27', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-27 21:17:33'),
(49, 86, '2026-01-28', NULL, 99.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-28 14:25:01'),
(50, 87, '2026-01-28', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-28 14:27:54'),
(51, 88, '2026-01-28', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-28 15:52:09'),
(52, 90, '2026-01-29', NULL, 190.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-29 18:39:08'),
(53, 90, '2026-01-29', NULL, 190.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-29 18:48:20'),
(54, 4, '2026-01-29', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-29 19:15:25'),
(55, 70, '2026-01-29', NULL, 168.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-29 20:00:32'),
(61, 31, '2026-01-29', NULL, 115.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-29 20:46:50'),
(64, 31, '2026-01-29', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-29 22:55:10'),
(65, 31, '2026-01-29', NULL, 99.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-29 22:59:36'),
(70, 11, '2026-01-29', NULL, 35.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 00:21:00'),
(72, 11, '2026-01-29', NULL, 25.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 00:38:27'),
(73, 91, '2026-01-29', NULL, 150.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 01:37:16'),
(74, 92, '2026-01-29', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 01:39:52'),
(77, 70, '2026-01-29', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 02:21:05'),
(80, 70, '2026-01-30', NULL, 99.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 04:11:24'),
(92, 61, '2026-01-30', NULL, 99.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 05:02:58'),
(93, 93, '2026-01-30', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 15:05:07'),
(94, 10, '2026-01-30', NULL, 9.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 15:47:25'),
(95, 86, '2026-01-30', NULL, 115.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 15:53:59'),
(97, 94, '2026-01-30', NULL, 80.00, NULL, NULL, 'pagado', 'Efectivo', '2026-01-30 20:44:40'),
(98, 95, '2026-02-04', NULL, 85.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 13:52:17'),
(99, 11, '2026-02-04', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:19:27'),
(100, 11, '2026-02-04', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:23:36'),
(101, 11, '2026-02-04', NULL, 85.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:34:37'),
(102, 11, '2026-02-04', NULL, 85.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:41:22'),
(103, 11, '2026-02-04', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:48:23'),
(104, 11, '2026-02-04', NULL, 85.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:51:12'),
(105, 11, '2026-02-04', NULL, 180.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:55:21'),
(106, 11, '2026-02-04', NULL, 80.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:55:31'),
(107, 11, '2026-02-04', NULL, 80.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 14:58:18'),
(108, 11, '2026-02-04', NULL, 80.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 15:36:03'),
(110, 95, '2026-02-04', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 15:41:06'),
(111, 67, '2026-02-04', NULL, 180.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 15:41:52'),
(112, 96, '2026-02-04', NULL, 186.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 16:33:03'),
(113, 60, '2026-02-04', NULL, 100.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 16:37:09'),
(114, 60, '2024-01-01', NULL, 180.00, NULL, NULL, 'pagado', 'Efectivo', '2024-01-01 16:37:35'),
(115, 75, '2026-02-04', NULL, 80.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 19:14:51'),
(116, 73, '2026-02-04', NULL, 180.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 19:15:06'),
(117, 29, '2026-02-04', NULL, 80.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 19:23:00'),
(118, 27, '2026-02-04', NULL, 180.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 19:31:53'),
(119, 97, '2026-02-04', NULL, 187.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 20:05:50'),
(120, 98, '2026-02-04', NULL, 80.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 20:11:16'),
(121, 99, '2026-02-04', NULL, 85.00, NULL, NULL, 'pagado', 'Efectivo', '2026-02-04 20:25:48'),
(123, 101, '2026-02-04', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-04 20:49:03'),
(124, 102, '2026-02-04', NULL, 85.00, 5, '[]', 'pagado', 'Efectivo', '2026-02-04 21:03:59'),
(125, 103, '2026-02-04', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-04 21:13:51'),
(126, 104, '2026-02-04', NULL, 80.00, 4, '[]', 'pagado', 'Efectivo', '2026-02-04 21:19:52'),
(127, 105, '2026-02-04', NULL, 260.00, 4, '[{\"nombre\":\"Proteina\",\"precio\":180}]', 'pagado', 'Efectivo', '2026-02-04 22:09:06'),
(128, 106, '2026-02-04', NULL, 105.00, 4, '[{\"nombre\":\"Tomatodo\",\"precio\":25}]', 'pagado', 'Efectivo', '2026-02-04 23:33:44'),
(129, 107, '2026-02-04', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-05 00:55:14'),
(130, 108, '2026-02-04', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-05 01:03:09'),
(131, 109, '2026-02-04', NULL, 86.00, 4, '[{\"nombre\":\"Toalla pequeña\",\"precio\":6}]', 'pagado', 'Efectivo', '2026-02-05 01:26:34'),
(132, 110, '2026-02-04', NULL, 107.00, 6, '[{\"nombre\":\"Toalla Color Verde\",\"precio\":7}]', 'pagado', 'Efectivo', '2026-02-05 01:35:33'),
(134, 80, '2026-02-04', NULL, 85.00, 5, '[]', 'pagado', 'Efectivo', '2026-02-05 02:09:58'),
(135, 111, '2026-02-04', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-05 02:40:49'),
(136, 113, '2026-02-04', NULL, 85.00, 5, '[]', 'pagado', 'Efectivo', '2026-02-05 02:47:03'),
(137, 114, '2024-01-01', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-05 02:48:54'),
(138, 115, '2026-02-04', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-05 02:57:40'),
(139, 116, '2026-02-04', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-05 03:04:12'),
(140, 117, '2026-02-04', NULL, 85.00, 5, '[]', 'pagado', 'Efectivo', '2026-02-05 03:07:23'),
(142, 119, '2026-02-04', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-05 03:24:58'),
(147, 122, '2026-02-04', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-05 03:42:51'),
(148, 123, '2026-02-04', NULL, 80.00, 4, '[]', 'pagado', 'Efectivo', '2026-02-05 03:43:39'),
(149, 124, '2026-02-05', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-05 15:03:28'),
(150, 125, '2026-02-05', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-05 15:48:36'),
(151, 126, '2026-02-05', NULL, 187.00, 3, '[{\"nombre\":\"Toalla Griss\",\"precio\":7}]', 'pagado', 'Efectivo', '2026-02-05 15:51:54'),
(152, 128, '2026-02-05', NULL, 110.00, 6, '[{\"nombre\":\"Botella\",\"precio\":10}]', 'pagado', 'Efectivo', '2026-02-05 16:25:23'),
(153, 128, '2026-02-05', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-05 16:44:32'),
(154, 129, '2026-02-05', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-05 16:50:08'),
(155, 130, '2026-02-05', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-05 17:14:13'),
(156, 131, '2026-02-05', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-05 19:01:10'),
(157, 132, '2026-02-05', NULL, 111.00, 2, '[{\"nombre\":\"Agua embotellada\",\"precio\":11}]', 'pagado', 'Efectivo', '2026-02-05 19:29:30'),
(158, 133, '2026-02-05', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-06 02:45:22'),
(159, 24, '2026-02-05', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-06 02:48:14'),
(160, 4, '2026-02-06', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-06 04:41:10'),
(161, 31, '2026-02-06', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-06 04:41:38'),
(163, 134, '2026-02-06', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-06 13:35:14'),
(164, 135, '2026-02-06', NULL, 118.00, 2, '[{\"nombre\":\"Botella Energizante\",\"precio\":18}]', 'pagado', 'Efectivo', '2026-02-06 15:13:28'),
(165, 136, '2026-02-06', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-06 15:24:01'),
(166, 138, '2026-02-06', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-06 15:41:53'),
(167, 139, '2026-02-06', NULL, 530.00, 3, '[{\"nombre\":\"Ganador de masa Muscletech\",\"precio\":350}]', 'pagado', 'Efectivo', '2026-02-06 18:15:42'),
(168, 140, '2026-02-06', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-06 18:27:50'),
(169, 141, '2026-02-06', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-06 18:34:06'),
(170, 142, '2026-02-06', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-07 03:13:46'),
(171, 143, '2026-02-07', NULL, 180.00, 3, '[]', 'pagado', 'Efectivo', '2026-02-07 18:39:34'),
(172, 144, '2026-02-07', NULL, 85.00, 5, '[]', 'pagado', 'Efectivo', '2026-02-07 19:28:04'),
(173, 101, '2026-02-07', NULL, 25.00, NULL, '[{\"nombre\":\"Agua embotellada\",\"precio\":25}]', 'pagado', 'Efectivo', '2026-02-07 19:37:57'),
(174, 145, '2026-02-09', NULL, 189.00, 3, '[{\"nombre\":\"Agua\",\"precio\":9}]', 'pagado', 'Efectivo', '2026-02-10 02:58:11'),
(175, 70, '2026-02-09', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-10 03:40:03'),
(176, 108, '2026-02-10', NULL, 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-10 14:23:10'),
(177, 101, '2026-02-10', NULL, 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-10 14:23:52'),
(178, 146, '2026-02-11', '2026-02-11', 107.00, 6, '[{\"nombre\":\"Agua embotellada\",\"precio\":7}]', 'pagado', 'Efectivo', '2026-02-11 16:11:45'),
(179, 147, '2026-02-11', '2026-02-11', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-11 21:20:03'),
(180, 108, '2026-02-11', '2026-02-11', 10.00, NULL, '[{\"nombre\":\"TOALLA\",\"precio\":10}]', 'pagado', 'Efectivo', '2026-02-11 21:27:08'),
(181, 147, '2026-02-11', '2026-03-13', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-12 03:11:51'),
(182, 109, '2026-02-12', '2026-03-06', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-12 13:39:48'),
(183, 148, '2026-02-12', '2026-02-12', 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-12 13:46:36'),
(184, 148, '2026-02-12', '2026-03-14', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-12 15:16:42'),
(185, 24, '2026-02-12', '2026-02-12', 15.00, NULL, '[{\"nombre\":\"Agua embotellada\",\"precio\":15}]', 'pagado', 'Efectivo', '2026-02-12 16:18:52'),
(186, 149, '2026-02-12', '2026-02-12', 115.00, 6, '[{\"nombre\":\"Toalla Café\",\"precio\":15}]', 'pagado', 'Efectivo', '2026-02-12 16:37:39'),
(187, 150, '2026-02-12', '2026-02-12', 110.00, 8, '[{\"nombre\":\"Agua embotallada\",\"precio\":10}]', 'pagado', 'Efectivo', '2026-02-12 17:59:14'),
(188, 151, '2026-02-13', '2026-02-13', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-13 13:36:09'),
(189, 151, '2026-02-13', '2026-03-15', 100.00, 8, '[]', 'pagado', 'Efectivo', '2026-02-13 13:36:40'),
(192, 101, '2026-02-13', '2026-02-13', 50.00, NULL, '[{\"nombre\":\"Toalla Verde\",\"precio\":50}]', 'pagado', 'Efectivo', '2026-02-13 13:47:23'),
(193, 101, '2026-02-13', '2026-03-12', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-13 13:47:45'),
(194, 153, '2026-02-13', '2026-02-13', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-13 16:34:53'),
(195, 154, '2026-02-13', '2026-02-13', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-13 16:40:55'),
(196, 155, '2026-02-13', '2026-02-13', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-13 16:43:30'),
(197, 156, '2026-02-13', '2026-02-13', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-13 16:46:32'),
(198, 164, '2026-02-13', '2026-02-13', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-13 17:48:43'),
(199, 165, '2026-02-14', '2026-02-14', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-14 04:32:20'),
(200, 171, '2026-02-17', '2026-02-17', 100.00, 8, '[]', 'pagado', 'Efectivo', '2026-02-18 02:29:42'),
(201, 172, '2026-02-18', '2026-02-18', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-18 13:50:57'),
(202, 173, '2026-02-18', '2026-02-18', 112.00, 6, '[{\"nombre\":\"Agua embotellada Villa Santa\",\"precio\":12}]', 'pagado', 'Efectivo', '2026-02-18 14:41:01'),
(203, 175, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 13:54:44'),
(204, 176, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 13:57:39'),
(205, 177, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 13:58:18'),
(206, 178, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:03:27'),
(207, 179, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:07:30'),
(208, 180, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:13:25'),
(209, 181, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:15:51'),
(210, 182, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:19:00'),
(211, 183, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:20:32'),
(212, 184, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:21:51'),
(213, 185, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:24:22'),
(214, 186, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:25:14'),
(215, 187, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:27:57'),
(216, 188, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:29:38'),
(217, 189, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:34:06'),
(218, 190, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:37:06'),
(219, 191, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:49:04'),
(220, 192, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:54:26'),
(221, 192, '2026-02-19', '2026-03-21', 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-19 14:56:26'),
(222, 193, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 14:58:43'),
(223, 194, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:13:15'),
(224, 195, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:26:38'),
(225, 196, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:29:32'),
(226, 197, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:31:41'),
(227, 198, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:34:50'),
(228, 199, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:38:30'),
(229, 200, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:42:54'),
(230, 201, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:46:47'),
(231, 202, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:48:19'),
(232, 203, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 15:55:10'),
(233, 204, '2026-02-19', '2026-02-19', 98.00, 7, '[]', 'pagado', 'Efectivo', '2026-02-19 15:57:32'),
(234, 205, '2026-02-19', '2026-02-19', 100.00, 8, '[]', 'pagado', 'Efectivo', '2026-02-19 16:08:19'),
(235, 206, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 16:11:27'),
(236, 207, '2026-02-19', '2026-02-19', 98.00, 7, '[]', 'pagado', 'Efectivo', '2026-02-19 16:14:00'),
(237, 208, '2026-02-19', '2026-02-19', 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-19 16:15:38'),
(238, 209, '2026-02-19', '2026-02-19', 98.00, 7, '[]', 'pagado', 'Efectivo', '2026-02-19 16:17:23'),
(239, 210, '2026-02-19', '2026-02-19', 100.00, 8, '[]', 'pagado', 'Efectivo', '2026-02-19 16:32:26'),
(240, 211, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 18:01:41'),
(241, 212, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 18:33:42'),
(242, 213, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 18:34:32'),
(243, 214, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 18:38:18'),
(244, 215, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 18:51:20'),
(245, 216, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 19:07:42'),
(246, 217, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 19:13:41'),
(247, 218, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 19:23:22'),
(248, 219, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 19:24:16'),
(249, 220, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 19:29:17'),
(250, 221, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 19:35:52'),
(251, 222, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 19:54:17'),
(252, 223, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-19 20:02:13'),
(253, 224, '2026-02-19', '2026-02-19', 98.00, 7, '[]', 'pagado', 'Efectivo', '2026-02-19 20:07:14'),
(254, 225, '2026-02-19', '2026-02-19', 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-19 21:07:43'),
(255, 226, '2026-02-19', '2026-02-19', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-20 03:54:08'),
(256, 227, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-20 04:04:23'),
(257, 228, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-20 19:43:50'),
(258, 229, '2026-02-20', '2026-02-20', 98.00, 7, '[]', 'pagado', 'Efectivo', '2026-02-20 22:41:36'),
(259, 230, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-20 23:35:11'),
(260, 231, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 01:24:19'),
(261, 232, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 01:53:34'),
(262, 233, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:18:42'),
(263, 233, '2026-02-20', '2026-03-22', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:18:47'),
(264, 234, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:20:38'),
(265, 234, '2026-02-20', '2026-03-22', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:20:42'),
(266, 234, '2026-02-20', '2026-04-21', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:20:46'),
(267, 234, '2026-02-20', '2026-05-21', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:20:50'),
(268, 234, '2026-02-20', '2026-06-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:20:53'),
(269, 234, '2026-02-20', '2026-07-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:20:57'),
(270, 237, '2026-02-20', '2026-02-20', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-21 02:53:54'),
(271, 238, '2026-02-20', '2026-02-20', 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-21 02:58:45'),
(272, 239, '2026-02-20', '2026-02-20', 98.00, 7, '[]', 'pagado', 'Efectivo', '2026-02-21 03:13:23'),
(273, 240, '2026-02-22', '2026-02-22', 98.00, 7, '[]', 'pagado', 'Efectivo', '2026-02-23 03:17:56'),
(274, 241, '2026-02-23', '2026-02-23', 100.00, 2, '[]', 'pagado', 'Efectivo', '2026-02-23 15:53:37'),
(275, 242, '2026-02-23', '2026-02-23', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-23 15:55:59'),
(276, 243, '2026-02-23', '2026-02-23', 100.00, 8, '[]', 'pagado', 'Efectivo', '2026-02-23 16:08:26'),
(277, 244, '2026-02-23', '2026-02-23', 100.00, 6, '[]', 'pagado', 'Efectivo', '2026-02-23 16:17:26'),
(278, 245, '2026-02-23', '2026-02-23', 100.00, 8, '[]', 'pagado', 'QR', '2026-02-23 16:21:42'),
(279, 246, '2026-02-23', '2026-02-23', 115.00, 6, '[{\"nombre\":\"Agua embotellada\",\"precio\":15}]', 'pagado', 'QR', '2026-02-23 16:23:04'),
(280, 140, '2026-02-23', '2026-02-23', 240.00, NULL, '[{\"nombre\":\"Creatina\",\"precio\":240}]', 'pagado', 'QR', '2026-02-23 19:58:46'),
(281, 140, '2026-02-23', '2026-02-23', 15.00, NULL, '[{\"nombre\":\"Prueba\",\"precio\":15}]', 'pagado', 'QR', '2026-02-23 20:08:39'),
(282, 247, '2026-02-23', '2026-02-23', 98.00, 7, '[]', 'pagado', 'Transferencia', '2026-02-24 00:34:33'),
(283, 248, '2026-02-23', '2026-02-23', 100.00, 6, '[]', 'pagado', 'Transferencia', '2026-02-24 00:39:51'),
(284, 249, '2026-02-23', '2026-02-23', 100.00, 6, '[]', 'pagado', 'Transferencia', '2026-02-24 00:40:56'),
(285, 250, '2026-02-23', '2026-02-23', 100.00, 6, '[]', 'pagado', 'Transferencia', '2026-02-24 00:43:29'),
(286, 250, '2026-02-23', '2026-02-23', 15.00, NULL, '[{\"nombre\":\"Toalla Color Negra\",\"precio\":15}]', 'pagado', 'Efectivo', '2026-02-24 00:47:02'),
(287, 147, '2026-02-23', '2026-02-23', 15.00, NULL, '[{\"nombre\":\"Toalla color beishe\",\"precio\":15}]', 'pagado', 'Efectivo', '2026-02-24 00:48:11'),
(288, 251, '2026-02-23', '2026-02-23', 99.00, 9, '[]', 'pagado', 'Efectivo', '2026-02-24 02:11:27'),
(289, 252, '2026-02-24', '2026-02-24', 100.00, 8, '[]', 'pagado', 'QR', '2026-02-24 13:53:19'),
(290, 108, '2026-02-24', '2026-02-24', 12.00, NULL, '[{\"nombre\":\"Agua embotellada\",\"precio\":12}]', 'pagado', 'QR', '2026-02-24 14:06:09');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `personal`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `personal` (
`id_usuario` int(11)
,`usuario` varchar(50)
,`clave` varchar(255)
,`correo` varchar(50)
,`nombre_completo` varchar(50)
,`direccion` varchar(20)
,`cargo` varchar(20)
,`genero` varchar(10)
,`contacto` int(10)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion_dias` int(11) NOT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `tipo_acceso` varchar(50) DEFAULT 'General',
  `visitas_max` int(11) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id`, `nombre`, `descripcion`, `duracion_dias`, `precio_base`, `tipo_acceso`, `visitas_max`, `estado`, `created_at`) VALUES
(2, 'Aeróbicos', 'Mensual', 30, 100.00, 'General', NULL, 'activo', '2025-11-27 02:55:01'),
(3, 'Fisiculturismo', 'Levantamiento de pesas', 30, 180.00, 'General', 30, '', '2025-11-30 20:52:49'),
(4, 'Aerobicos y Bailes', 'Promocion navideña del 1 al 20 de diciembre', 30, 80.00, 'General', 30, '', '2025-12-12 01:47:01'),
(5, 'Karate', 'Karate para niños 8 - 15 años', 30, 85.00, 'Karate Niños', 30, '', '2026-02-04 13:51:04'),
(6, 'Promo Live', 'Promoción live solo para conectados en TikTok', 30, 100.00, 'General', 30, 'activo', '2026-02-05 00:54:10'),
(7, 'NUEVOPLAN', 'de prueba', 30, 98.00, 'General', 30, 'activo', '2026-02-11 21:52:16'),
(8, 'TAE KON DO', 'Taekondo para niñode 10 a 15 años', 30, 100.00, 'General', 30, 'activo', '2026-02-12 17:57:06'),
(9, 'Plan Carnavalero', 'Plan carnavlero hasta el ultimo dia de febrero', 30, 99.00, 'General', 30, '', '2026-02-24 02:10:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_config`
--

CREATE TABLE `portal_config` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `grupo` varchar(50) NOT NULL DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_config`
--

INSERT INTO `portal_config` (`id`, `clave`, `valor`, `grupo`, `updated_at`) VALUES
(1, 'hero_badge', 'La Paz, Bolivia — Gym N°1 en transformación', 'hero', '2026-02-24 16:18:38'),
(2, 'hero_titulo_1', 'TRANSFORMA', 'hero', '2026-02-24 16:18:38'),
(3, 'hero_titulo_2', 'TU CUERPO', 'hero', '2026-02-24 16:18:38'),
(4, 'hero_titulo_3', 'TU VIDA', 'hero', '2026-02-24 16:18:38'),
(5, 'hero_descripcion', 'Entrenamiento profesional, planes personalizados y la mejor comunidad fitness de La Paz. Tu mejor versión comienza hoy.', 'hero', '2026-02-24 16:18:38'),
(6, 'hero_stat_1_num', '500', 'hero', '2026-02-24 16:18:38'),
(7, 'hero_stat_1_label', 'Miembros', 'hero', '2026-02-24 16:18:38'),
(8, 'hero_stat_2_num', '15', 'hero', '2026-02-24 16:18:38'),
(9, 'hero_stat_2_label', 'Disciplinas', 'hero', '2026-02-24 16:18:38'),
(10, 'hero_stat_3_num', '8', 'hero', '2026-02-24 16:18:38'),
(11, 'hero_stat_3_label', 'Entrenadores', 'hero', '2026-02-24 16:18:38'),
(12, 'hero_stat_4_num', '5', 'hero', '2026-02-24 16:18:38'),
(13, 'hero_stat_4_label', 'Años', 'hero', '2026-02-24 16:18:38'),
(14, 'direccion', 'Av. Buenos Aires, Puente Vita, (Centro Comercial Génesis)\nLa Paz, Bolivia', 'contacto', '2026-02-24 19:14:18'),
(15, 'horario_ls', 'Lunes a Sábado: 5:30 AM – 10:00 PM', 'contacto', '2026-02-24 16:18:38'),
(16, 'horario_dom', 'Domingos: 8:00 AM – 12:00 PM', 'contacto', '2026-02-24 16:18:38'),
(17, 'telefono_1', '+591 2 244-5678', 'contacto', '2026-02-24 16:18:38'),
(18, 'telefono_2', '+591 71254370', 'contacto', '2026-02-24 19:14:18'),
(19, 'email_1', 'info@gymbodytraining.com', 'contacto', '2026-02-24 16:18:38'),
(20, 'email_2', 'inscripciones@gymbodytraining.com', 'contacto', '2026-02-24 16:18:38'),
(21, 'whatsapp', '+591 7xx xx xxx', 'contacto', '2026-02-24 16:18:38'),
(22, 'mapa_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d478.2057209286428!2d-68.1448603200356!3d-16.493464898895578!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915edfc73f5e70b7%3A0x82e45179a706fff3!2sGALERIA%20GENESIS!5e0!3m2!1ses-419!2sbo!4v1771963574266!5m2!1ses-419!2sbo', 'contacto', '2026-02-24 22:33:51'),
(23, 'social_facebook', 'https://www.facebook.com/GYMbodytraining?locale=es_LA', 'redes', '2026-02-24 19:14:18'),
(24, 'social_instagram', '#', 'redes', '2026-02-24 16:18:38'),
(25, 'social_tiktok', 'https://www.tiktok.com/@bodytraining_gym', 'redes', '2026-02-24 19:14:18'),
(26, 'social_whatsapp', '#', 'redes', '2026-02-24 16:18:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_features`
--

CREATE TABLE `portal_features` (
  `id` int(11) NOT NULL,
  `icono` varchar(60) NOT NULL DEFAULT 'fas fa-star',
  `color` varchar(100) NOT NULL DEFAULT 'linear-gradient(135deg,#4e73df,#224abe)',
  `titulo` varchar(120) NOT NULL,
  `descripcion` text NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_features`
--

INSERT INTO `portal_features` (`id`, `icono`, `color`, `titulo`, `descripcion`, `orden`, `activo`, `updated_at`) VALUES
(1, 'fas fa-dumbbell', 'linear-gradient(135deg,#4e73df,#224abe)', 'Equipamiento Premium', 'Máquinas de última generación, peso libre completo y área funcional para entrenar sin límites.', 1, 1, '2026-02-24 16:18:38'),
(2, 'fas fa-users', 'linear-gradient(135deg,#ff6b35,#f6c23e)', 'Entrenadores Certificados', 'Staff profesional con años de experiencia y certificaciones internacionales en fitness y nutrición.', 2, 1, '2026-02-24 16:18:38'),
(3, 'fas fa-heart-pulse', 'linear-gradient(135deg,#1cc88a,#17a673)', 'Seguimiento Digital', 'Sistema web con control de asistencias QR, seguimiento de progreso y logros personalizados.', 3, 1, '2026-02-24 16:18:38'),
(4, 'fas fa-fire-flame-curved', 'linear-gradient(135deg,#e74a3b,#c0392b)', 'Clases Grupales', 'CrossFit, funcional, cardio, boxeo, yoga y más. Cada semana nuevas clases para mantenerte motivado.', 4, 1, '2026-02-24 16:18:38'),
(5, 'fas fa-trophy', 'linear-gradient(135deg,#f6c23e,#e0a800)', 'Sistema de Logros', 'Gamificación real: desbloquea medallas por asistencia, constancia y superación personal.', 5, 1, '2026-02-24 16:18:38'),
(6, 'fas fa-mobile-screen-button', 'linear-gradient(135deg,#6f42c1,#5a32a3)', 'Acceso Móvil', 'Consulta tus pagos, membresía y progreso desde tu celular. Tu gym en tu bolsillo, siempre.', 6, 1, '2026-02-24 16:18:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_horarios`
--

CREATE TABLE `portal_horarios` (
  `id` int(11) NOT NULL,
  `dia` varchar(20) NOT NULL,
  `dia_orden` tinyint(4) NOT NULL DEFAULT 0,
  `hora_inicio` varchar(10) NOT NULL,
  `hora_fin` varchar(10) NOT NULL,
  `clase` varchar(80) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_horarios`
--

INSERT INTO `portal_horarios` (`id`, `dia`, `dia_orden`, `hora_inicio`, `hora_fin`, `clase`, `activo`, `updated_at`) VALUES
(1, 'LUN', 1, '06:00', '07:00', 'Baile', 1, '2026-02-24 19:05:08'),
(2, 'LUN', 1, '09:00', '10:00', 'Funcional', 1, '2026-02-24 16:18:38'),
(3, 'LUN', 1, '18:00', '19:00', 'Karate Niños', 1, '2026-02-24 19:05:39'),
(4, 'LUN', 1, '19:30', '20:30', 'Spinning', 1, '2026-02-24 16:18:38'),
(5, 'MAR', 2, '06:00', '07:00', 'HIIT', 1, '2026-02-24 16:18:38'),
(6, 'MAR', 2, '10:00', '11:00', 'Yoga', 1, '2026-02-24 16:18:38'),
(7, 'MAR', 2, '18:00', '19:00', 'Funcional', 1, '2026-02-24 16:18:38'),
(8, 'MAR', 2, '19:30', '20:30', 'Cardio Mix', 1, '2026-02-24 16:18:38'),
(9, 'MIÉ', 3, '06:00', '07:00', 'CrossFit', 1, '2026-02-24 16:18:38'),
(10, 'MIÉ', 3, '09:00', '10:00', 'Pilates', 1, '2026-02-24 16:18:38'),
(11, 'MIÉ', 3, '18:00', '19:00', 'Boxeo', 1, '2026-02-24 16:18:38'),
(12, 'MIÉ', 3, '19:30', '20:30', 'Body Pump', 1, '2026-02-24 16:18:38'),
(13, 'JUE', 4, '06:00', '07:00', 'HIIT', 1, '2026-02-24 16:18:38'),
(14, 'JUE', 4, '10:00', '11:00', 'Yoga', 1, '2026-02-24 16:18:38'),
(15, 'JUE', 4, '18:00', '19:00', 'Funcional', 1, '2026-02-24 16:18:38'),
(16, 'JUE', 4, '19:30', '20:30', 'Spinning', 1, '2026-02-24 16:18:38'),
(17, 'VIE', 5, '06:00', '07:00', 'CrossFit', 1, '2026-02-24 16:18:38'),
(18, 'VIE', 5, '09:00', '10:00', 'Cardio Mix', 1, '2026-02-24 16:18:38'),
(19, 'VIE', 5, '18:00', '19:00', 'Boxeo', 1, '2026-02-24 16:18:38'),
(20, 'VIE', 5, '19:30', '20:30', 'Body Pump', 1, '2026-02-24 16:18:38'),
(21, 'SÁB', 6, '08:00', '09:30', 'CrossFit Open', 1, '2026-02-24 16:18:38'),
(22, 'SÁB', 6, '10:00', '11:00', 'Funcional', 1, '2026-02-24 16:18:38'),
(23, 'SÁB', 6, '11:30', '12:30', 'Yoga', 1, '2026-02-24 16:18:38'),
(24, 'DOM', 7, '09:00', '11:00', 'Sala Libre', 1, '2026-02-24 16:18:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_instructores`
--

CREATE TABLE `portal_instructores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `icono` varchar(60) NOT NULL DEFAULT 'fas fa-user',
  `color` varchar(100) NOT NULL DEFAULT 'linear-gradient(135deg,#4e73df33,#1cc88a33)',
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_instructores`
--

INSERT INTO `portal_instructores` (`id`, `nombre`, `cargo`, `descripcion`, `foto`, `icono`, `color`, `orden`, `activo`, `updated_at`) VALUES
(1, 'Carlos Mamani', 'Head Coach · CrossFit L2', 'Especialista en entrenamiento funcional y CrossFit con 8 años de experiencia.', NULL, 'fas fa-user-tie', 'linear-gradient(135deg,#4e73df33,#1cc88a33)', 1, 1, '2026-02-24 16:18:38'),
(2, 'Nelida Guzmán', 'Entrenadora · Yoga & Pilates', 'Certificada en Hatha Yoga y Pilates reformer. Bienestar integral y flexibilidad.', NULL, 'fas fa-user', 'linear-gradient(135deg,#4e73df33,#1cc88a33)', 2, 1, '2026-02-24 23:30:01'),
(3, 'Diego Flores Quispe', 'Coach · Boxeo & HIIT', 'Ex-competidor nacional de boxeo. Clases intensas para quemar calorías y ganar resistencia.', NULL, 'fas fa-user', 'linear-gradient(135deg,#4e73df33,#1cc88a33)', 3, 1, '2026-02-25 00:31:11'),
(4, 'Andrea Soliz', 'Nutricionista · Planes VIP', 'Licenciada en nutrición deportiva. Diseña planes alimenticios personalizados para cada meta.', NULL, 'fas fa-user', 'linear-gradient(135deg,#1cc88a33,#17a67333)', 4, 1, '2026-02-24 16:18:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_mensajes`
--

CREATE TABLE `portal_mensajes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_mensajes`
--

INSERT INTO `portal_mensajes` (`id`, `nombre`, `email`, `telefono`, `mensaje`, `leido`, `created_at`) VALUES
(1, 'Nely Valenzuela', 'vaing@gmail.com', '7894611', 'Cuando sacaran otra promo?', 0, '2026-02-24 23:02:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_planes`
--

CREATE TABLE `portal_planes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `icono` varchar(60) NOT NULL DEFAULT 'fas fa-bolt',
  `color` varchar(100) NOT NULL DEFAULT 'linear-gradient(135deg,#1cc88a,#17a673)',
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `moneda` varchar(10) NOT NULL DEFAULT 'Bs',
  `duracion` varchar(50) NOT NULL DEFAULT '30 días',
  `tipo_acceso` varchar(80) NOT NULL DEFAULT 'Acceso limitado',
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_planes`
--

INSERT INTO `portal_planes` (`id`, `nombre`, `icono`, `color`, `precio`, `moneda`, `duracion`, `tipo_acceso`, `destacado`, `orden`, `activo`, `updated_at`) VALUES
(1, 'General', 'fas fa-bolt', 'linear-gradient(135deg,#1cc88a,#17a673)', 149.00, 'Bs', '30 días', 'Acceso limitado', 0, 1, 1, '2026-02-24 19:04:45'),
(2, 'PREMIUM', 'fas fa-crown', 'linear-gradient(135deg,#4e73df,#224abe)', 250.00, 'Bs', '30 días', 'Acceso completo', 1, 2, 1, '2026-02-24 16:18:38'),
(3, 'VIP', 'fas fa-gem', 'linear-gradient(135deg,#ff6b35,#f6c23e)', 400.00, 'Bs', '30 días', 'Acceso total + personal', 0, 3, 1, '2026-02-24 16:18:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_plan_beneficios`
--

CREATE TABLE `portal_plan_beneficios` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `texto` varchar(200) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_plan_beneficios`
--

INSERT INTO `portal_plan_beneficios` (`id`, `plan_id`, `texto`, `orden`) VALUES
(6, 2, 'Acceso ilimitado 24/7', 1),
(7, 2, 'Todas las clases grupales', 2),
(8, 2, 'Casillero personal', 3),
(9, 2, 'Plan de entrenamiento', 4),
(10, 2, 'Seguimiento de progreso', 5),
(11, 2, '1 sesión de evaluación/mes', 6),
(12, 3, 'Todo lo del plan Premium', 1),
(13, 3, 'Entrenador personal dedicado', 2),
(14, 3, 'Plan nutricional', 3),
(15, 3, 'Acceso zona VIP', 4),
(16, 3, 'Toalla y amenities', 5),
(17, 3, 'Evaluación corporal semanal', 6),
(18, 1, 'Acceso a sala de musculación', 1),
(19, 1, 'Horario diurno (6am–2pm)', 2),
(20, 1, 'Casillero compartido', 3),
(21, 1, 'App de seguimiento', 4),
(22, 1, 'Control QR de asistencia', 5);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `recordatorios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `recordatorios` (
`id` int(50)
,`nombre` varchar(50)
,`mensaje` text
,`estado` text
,`fecha_hora` datetime
,`id_usuario` int(11)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_pago`
--

CREATE TABLE `recordatorios_pago` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `payment_id` int(11) NOT NULL,
  `tipo_alerta` varchar(20) NOT NULL,
  `sent_channel` varchar(20) DEFAULT NULL,
  `sent_ok` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `error_msg` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recordatorios_pago`
--

INSERT INTO `recordatorios_pago` (`id`, `user_id`, `message`, `created_at`, `is_read`, `payment_id`, `tipo_alerta`, `sent_channel`, `sent_ok`, `sent_at`, `error_msg`) VALUES
(1, 24, 'Tienes un pago pendiente en Gym Body Training. Por favor regulariza tu membresía.', '2025-12-01 21:52:26', 0, 0, '', NULL, 0, '0000-00-00 00:00:00', NULL),
(2, 68, 'Tienes un pago pendiente en Gym Body Training. Por favor regulariza tu membresía.', '2025-12-01 21:58:08', 0, 0, '', NULL, 0, '0000-00-00 00:00:00', NULL),
(20, 114, '🔔 Membresía vencida dentro/hace 749 día(s).\n', '2026-02-18 23:38:17', 0, 137, 'vencida', 'db', 1, '2026-02-18 23:38:17', NULL),
(21, 223, '🎉 ¡Hola ! Tu pago de Bs.  fue registrado correctamente el  mediante . ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #)\n', '2026-02-19 16:02:17', 0, 252, 'pago', 'db', 1, '2026-02-19 15:02:17', ''),
(22, 224, '🎉 ¡Hola Dayana Velasquez! Tu pago de Bs. 98 fue registrado correctamente el 2026-02-19 mediante Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #253)\n\n', '2026-02-19 16:07:17', 0, 253, 'pago', 'db', 1, '2026-02-19 15:07:17', ''),
(23, 225, '🎉 ¡Hola Kevin Apaza! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-19 mediante Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #254)\n\n', '2026-02-19 17:07:52', 0, 254, 'pago', 'db', 1, '2026-02-19 16:07:52', ''),
(24, 226, '🎉 ¡Hola Juancito Pinto! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-19 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #255)\n\n', '2026-02-19 23:54:12', 0, 255, 'pago', 'db', 1, '2026-02-19 22:54:12', ''),
(25, 227, '🎉 ¡Hola Carlos Paredes Candia! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #256)\n\n', '2026-02-20 00:04:28', 0, 256, 'pago', 'db', 1, '2026-02-19 23:04:28', ''),
(26, 229, '🎉 ¡Hola Hilarion Mamani! Tu pago de Bs. 98 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #258)\n\n', '2026-02-20 18:41:40', 0, 258, 'pago', 'db', 1, '2026-02-20 17:41:40', ''),
(27, 234, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #264)\n\n', '2026-02-20 22:20:42', 0, 264, 'pago', 'db', 1, '2026-02-20 21:20:42', ''),
(28, 234, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #265)\n\n', '2026-02-20 22:20:46', 0, 265, 'pago', 'db', 1, '2026-02-20 21:20:46', ''),
(29, 234, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #266)\n\n', '2026-02-20 22:20:50', 0, 266, 'pago', 'db', 1, '2026-02-20 21:20:50', ''),
(30, 234, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #267)\n\n', '2026-02-20 22:20:53', 0, 267, 'pago', 'db', 1, '2026-02-20 21:20:53', ''),
(31, 234, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #268)\n\n', '2026-02-20 22:20:57', 0, 268, 'pago', 'db', 1, '2026-02-20 21:20:57', ''),
(32, 234, '🎉 ¡Hola Tatiana Morales! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #269)\n\n', '2026-02-20 22:21:01', 0, 269, 'pago', 'db', 1, '2026-02-20 21:21:01', ''),
(33, 237, '🎉 ¡Hola Nacho Montes Arias! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #270)\n\n', '2026-02-20 22:54:02', 0, 270, 'pago', 'db', 1, '2026-02-20 21:54:02', ''),
(34, 238, '🎉 ¡Hola Gonzalo Montes Arias! Tu pago de Bs. 100 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #271)\n\n', '2026-02-20 22:58:52', 0, 271, 'pago', 'db', 1, '2026-02-20 21:58:52', ''),
(35, 239, '🎉 ¡Hola Ivan Montes! Tu pago de Bs. 98 fue registrado correctamente el 2026-02-20 mediante método Efectivo. ¡Gracias por confiar en Gym Body Training! 💪 (Ref: Pago #272)\n\n', '2026-02-20 23:13:30', 0, 272, 'pago', 'db', 1, '2026-02-20 22:13:30', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reminder`
--

CREATE TABLE `reminder` (
  `id` int(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `status` text NOT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `staffs`
--

CREATE TABLE `staffs` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `address` varchar(20) NOT NULL,
  `designation` varchar(20) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `contact` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `staffs`
--

INSERT INTO `staffs` (`user_id`, `username`, `password`, `email`, `fullname`, `address`, `designation`, `gender`, `contact`) VALUES
(10, 'imontes', '$2y$10$JvKBKCEyYWuHmpkhLyd4oOujQIuycL7JqN38dMORDnF', 'imontes@gmail.com', 'Ivan Montes Arias ', 'Chorolque 1090', 'Recepcionista', 'Masculino', 69709696),
(11, 'bcastañeta', '$2y$10$XZ46co6XUNCpNj2WobHhcOX9j7J74LfWs3IXo/U6wf3', 'bcastañeta@gmail.com', 'Belen Castañeta', 'Vino tinto N° 1057', 'Recepcionista', 'Femenino', 76455012),
(12, 'squispe', '$2y$10$MP1KofEYVhU2xVoV2INK9O8kMSkQPcaxV4MrbifJN3E5IdInBhjWG', 'squispe@gmail.com', 'Solange Quispe', 'Buenos Aires, esquin', 'Recepcionista', 'Femenino', 78971011),
(13, 'entrenador', '$2y$10$oybXJlJlrm6xztSkzLk7x.E1jt7t43xIZQWnsKtl.uvnFtfQsKTfS', 'entrenador@gmail.com', 'entrenador', 'Vino Tinto', 'Entrenador', 'Masculino', 77894101),
(14, 'entrenadordos', '$2y$10$1pTFIcvGMlipr/o62sLh6OftmBUJTrMOQg7zfh9Rt3N99xtAy1/qC', 'entrenadordos@gmail.com', 'entrenadordos', 'Av. Calatayud N° 545', 'Entrenador', 'Masculino', 79887101);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `tarifas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `tarifas` (
);

-- --------------------------------------------------------

--
-- Estructura para la vista `administradores`
--
DROP TABLE IF EXISTS `administradores`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `administradores`  AS SELECT `admin`.`user_id` AS `id_usuario`, `admin`.`username` AS `usuario`, `admin`.`password` AS `clave`, `admin`.`name` AS `nombre` FROM `admin` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `anuncios`
--
DROP TABLE IF EXISTS `anuncios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `anuncios`  AS SELECT `announcements`.`id` AS `id`, `announcements`.`message` AS `mensaje`, `announcements`.`date` AS `fecha` FROM `announcements` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `asistencias`
--
DROP TABLE IF EXISTS `asistencias`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `asistencias`  AS SELECT `attendance`.`id` AS `id`, `attendance`.`user_id` AS `id_usuario`, `attendance`.`curr_date` AS `fecha`, `attendance`.`curr_time` AS `hora`, `attendance`.`present` AS `presente` FROM `attendance` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `equipos`
--
DROP TABLE IF EXISTS `equipos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `equipos`  AS SELECT `equipment`.`id` AS `id`, `equipment`.`name` AS `nombre`, `equipment`.`amount` AS `monto`, `equipment`.`quantity` AS `cantidad`, `equipment`.`vendor` AS `proveedor`, `equipment`.`description` AS `descripcion`, `equipment`.`address` AS `direccion`, `equipment`.`contact` AS `contacto`, `equipment`.`date` AS `fecha` FROM `equipment` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `miembros`
--
DROP TABLE IF EXISTS `miembros`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `miembros`  AS SELECT `members`.`user_id` AS `id_usuario`, `members`.`fullname` AS `nombre_completo`, `members`.`username` AS `usuario`, `members`.`password` AS `clave`, `members`.`gender` AS `genero`, `members`.`dor` AS `fecha_registro`, `members`.`services` AS `servicios`, `members`.`amount` AS `monto`, `members`.`paid_date` AS `fecha_pago`, `members`.`p_year` AS `anio_pago`, `members`.`plan` AS `plan`, `members`.`ci` AS `direccion`, `members`.`contact` AS `contacto`, CASE WHEN `members`.`status` = 'Active' THEN 'Activo' WHEN `members`.`status` = 'Pending' THEN 'Pendiente' ELSE `members`.`status` END AS `estado`, `members`.`attendance_count` AS `conteo_asistencias`, `members`.`ini_weight` AS `peso_inicial`, `members`.`curr_weight` AS `peso_actual`, `members`.`ini_bodytype` AS `biotipo_inicial`, `members`.`curr_bodytype` AS `biotipo_actual`, `members`.`progress_date` AS `fecha_progreso`, `members`.`reminder` AS `recordatorio` FROM `members` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `personal`
--
DROP TABLE IF EXISTS `personal`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `personal`  AS SELECT `staffs`.`user_id` AS `id_usuario`, `staffs`.`username` AS `usuario`, `staffs`.`password` AS `clave`, `staffs`.`email` AS `correo`, `staffs`.`fullname` AS `nombre_completo`, `staffs`.`address` AS `direccion`, `staffs`.`designation` AS `cargo`, `staffs`.`gender` AS `genero`, `staffs`.`contact` AS `contacto` FROM `staffs` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `recordatorios`
--
DROP TABLE IF EXISTS `recordatorios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recordatorios`  AS SELECT `reminder`.`id` AS `id`, `reminder`.`name` AS `nombre`, `reminder`.`message` AS `mensaje`, CASE WHEN `reminder`.`status` = 'unread' THEN 'no leído' WHEN `reminder`.`status` = 'read' THEN 'leído' ELSE `reminder`.`status` END AS `estado`, `reminder`.`date` AS `fecha_hora`, `reminder`.`user_id` AS `id_usuario` FROM `reminder` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `tarifas`
--
DROP TABLE IF EXISTS `tarifas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tarifas`  AS SELECT `rates`.`id` AS `id`, `rates`.`name` AS `nombre`, `rates`.`charge` AS `monto` FROM `rates` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`user_id`);

--
-- Indices de la tabla `admin_inbox`
--
ALTER TABLE `admin_inbox`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_day` (`user_id`,`curr_date`) USING HASH;

--
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_modulo` (`modulo`),
  ADD KEY `idx_accion` (`accion`),
  ADD KEY `idx_fecha` (`created_at`),
  ADD KEY `idx_usuario` (`user_id`);

--
-- Indices de la tabla `automation_logs`
--
ALTER TABLE `automation_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clases_reservas`
--
ALTER TABLE `clases_reservas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sesion_cliente` (`sesion_id`,`cliente_id`),
  ADD UNIQUE KEY `unique_reserva` (`sesion_id`,`cliente_id`),
  ADD KEY `idx_cliente_estado` (`cliente_id`,`estado`),
  ADD KEY `idx_sesion_estado` (`sesion_id`,`estado`);

--
-- Indices de la tabla `clases_sesiones`
--
ALTER TABLE `clases_sesiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_clase_id` (`tipo_clase_id`),
  ADD KEY `idx_entrenador_fecha` (`entrenador_id`,`fecha`),
  ADD KEY `idx_fecha_estado` (`fecha`,`estado`);

--
-- Indices de la tabla `clase_tipos`
--
ALTER TABLE `clase_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logros`
--
ALTER TABLE `logros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uq_members_username` (`username`),
  ADD UNIQUE KEY `uq_members_ci` (`ci`),
  ADD UNIQUE KEY `uq_members_correo` (`correo`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `ci` (`ci`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD UNIQUE KEY `ci_2` (`ci`),
  ADD UNIQUE KEY `correo_2` (`correo`),
  ADD KEY `idx_members_fullname` (`fullname`),
  ADD KEY `idx_members_contact` (`contact`),
  ADD KEY `idx_members_gender` (`gender`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`paid_date`),
  ADD KEY `idx_payments_user` (`user_id`),
  ADD KEY `idx_payments_date` (`paid_date`),
  ADD KEY `idx_payments_status` (`status`),
  ADD KEY `fk_payments_plan` (`plan_id`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `portal_config`
--
ALTER TABLE `portal_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `portal_features`
--
ALTER TABLE `portal_features`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `portal_horarios`
--
ALTER TABLE `portal_horarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `portal_instructores`
--
ALTER TABLE `portal_instructores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `portal_mensajes`
--
ALTER TABLE `portal_mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `portal_planes`
--
ALTER TABLE `portal_planes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `portal_plan_beneficios`
--
ALTER TABLE `portal_plan_beneficios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indices de la tabla `recordatorios_pago`
--
ALTER TABLE `recordatorios_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_alert` (`user_id`,`payment_id`,`tipo_alerta`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indices de la tabla `reminder`
--
ALTER TABLE `reminder`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `admin_inbox`
--
ALTER TABLE `admin_inbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `automation_logs`
--
ALTER TABLE `automation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `clases_reservas`
--
ALTER TABLE `clases_reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clases_sesiones`
--
ALTER TABLE `clases_sesiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clase_tipos`
--
ALTER TABLE `clase_tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `logros`
--
ALTER TABLE `logros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `members`
--
ALTER TABLE `members`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `portal_config`
--
ALTER TABLE `portal_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `portal_features`
--
ALTER TABLE `portal_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `portal_horarios`
--
ALTER TABLE `portal_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `portal_instructores`
--
ALTER TABLE `portal_instructores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `portal_mensajes`
--
ALTER TABLE `portal_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `portal_planes`
--
ALTER TABLE `portal_planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `portal_plan_beneficios`
--
ALTER TABLE `portal_plan_beneficios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `recordatorios_pago`
--
ALTER TABLE `recordatorios_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `reminder`
--
ALTER TABLE `reminder`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `staffs`
--
ALTER TABLE `staffs`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clases_reservas`
--
ALTER TABLE `clases_reservas`
  ADD CONSTRAINT `clases_reservas_ibfk_1` FOREIGN KEY (`sesion_id`) REFERENCES `clases_sesiones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clases_reservas_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `members` (`user_id`);

--
-- Filtros para la tabla `clases_sesiones`
--
ALTER TABLE `clases_sesiones`
  ADD CONSTRAINT `clases_sesiones_ibfk_1` FOREIGN KEY (`tipo_clase_id`) REFERENCES `clase_tipos` (`id`),
  ADD CONSTRAINT `clases_sesiones_ibfk_2` FOREIGN KEY (`entrenador_id`) REFERENCES `staffs` (`user_id`);

--
-- Filtros para la tabla `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_plan` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `portal_plan_beneficios`
--
ALTER TABLE `portal_plan_beneficios`
  ADD CONSTRAINT `portal_plan_beneficios_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `portal_planes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
