-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-05-2025 a las 20:40:29
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
-- Base de datos: `sige`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrativos`
--

CREATE TABLE `administrativos` (
  `id_administrativo` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrativos`
--

INSERT INTO `administrativos` (`id_administrativo`, `persona_id`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(1, 4, '2024-10-27 00:00:00', NULL, '1'),
(2, 22, '2024-12-26 00:00:00', NULL, '1'),
(4, 24, '2025-01-24 00:00:00', NULL, '0'),
(5, 26, '2025-01-24 00:00:00', '2025-02-08 00:00:00', '1'),
(6, 27, '2025-01-24 00:00:00', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carnets_emitidos`
--

CREATE TABLE `carnets_emitidos` (
  `id_emision` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_diseno` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_expiracion` date NOT NULL,
  `codigo_qr` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carnets_estudiantiles`
--

CREATE TABLE `carnets_estudiantiles` (
  `id_carnet` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_plantilla` int(11) NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `qr_code` varchar(50) DEFAULT NULL,
  `fecha_emision` datetime DEFAULT current_timestamp(),
  `fecha_vencimiento` date DEFAULT NULL,
  `estatus` enum('activo','vencido','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_instituciones`
--

CREATE TABLE `configuracion_instituciones` (
  `id_config_institucion` int(11) NOT NULL,
  `nombre_institucion` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `fondo` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `celular` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_instituciones`
--

INSERT INTO `configuracion_instituciones` (`id_config_institucion`, `nombre_institucion`, `logo`, `fondo`, `direccion`, `telefono`, `celular`, `correo`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(1, 'U.E.N ROBERTO MARTINEZ CENTENO', '2025-05-03-15-01-46logo.jpg', '', 'Parroquia Caricuao, Avenida Este 0, Caracas, Distrito Capital, adscrito a la Zona Educativa del Estado Distrito Capital', '02124331080', '', 'admin@gmail.com', '2023-12-28 20:29:10', '2025-05-03 00:00:00', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_carnets`
--

CREATE TABLE `config_carnets` (
  `id_config` int(11) NOT NULL,
  `universidad_linea1` varchar(100) NOT NULL,
  `universidad_linea2` varchar(100) NOT NULL,
  `nombre_universidad` varchar(100) NOT NULL,
  `sede` varchar(100) NOT NULL,
  `siglas` varchar(20) NOT NULL,
  `sedes` varchar(100) NOT NULL,
  `texto_pie1` text NOT NULL,
  `texto_pie2` text NOT NULL,
  `firma_nombre` varchar(100) NOT NULL,
  `firma_cargo` varchar(100) NOT NULL,
  `telefono_emergencia` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_institucion`
--

CREATE TABLE `datos_institucion` (
  `id_institucion` int(11) NOT NULL,
  `nombre_institucion` varchar(100) NOT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sitio_web` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diseno_carnets`
--

CREATE TABLE `diseno_carnets` (
  `id_diseno` int(11) NOT NULL,
  `nombre_diseno` varchar(100) NOT NULL DEFAULT 'Predeterminado',
  `logo_path` varchar(255) DEFAULT NULL,
  `logo_pos_x` int(11) DEFAULT 10,
  `logo_pos_y` int(11) DEFAULT 10,
  `logo_width` int(11) DEFAULT 30,
  `logo_height` int(11) DEFAULT 30,
  `foto_estudiante_pos_x` int(11) DEFAULT 15,
  `foto_estudiante_pos_y` int(11) DEFAULT 50,
  `foto_estudiante_width` int(11) DEFAULT 25,
  `foto_estudiante_height` int(11) DEFAULT 30,
  `qr_pos_x` int(11) DEFAULT 60,
  `qr_pos_y` int(11) DEFAULT 50,
  `qr_size` int(11) DEFAULT 25,
  `color_fondo` varchar(20) DEFAULT '#FFFFFF',
  `color_texto` varchar(20) DEFAULT '#000000',
  `fuente_principal` varchar(50) DEFAULT 'Arial',
  `mostrar_qr` tinyint(1) DEFAULT 1,
  `texto_superior` text DEFAULT NULL,
  `texto_inferior` text DEFAULT NULL,
  `firma_path` varchar(255) DEFAULT NULL,
  `firma_pos_x` int(11) DEFAULT 50,
  `firma_pos_y` int(11) DEFAULT 80,
  `firma_width` int(11) DEFAULT 30,
  `firma_height` int(11) DEFAULT 15,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `diseno_carnets`
--

INSERT INTO `diseno_carnets` (`id_diseno`, `nombre_diseno`, `logo_path`, `logo_pos_x`, `logo_pos_y`, `logo_width`, `logo_height`, `foto_estudiante_pos_x`, `foto_estudiante_pos_y`, `foto_estudiante_width`, `foto_estudiante_height`, `qr_pos_x`, `qr_pos_y`, `qr_size`, `color_fondo`, `color_texto`, `fuente_principal`, `mostrar_qr`, `texto_superior`, `texto_inferior`, `firma_path`, `firma_pos_x`, `firma_pos_y`, `firma_width`, `firma_height`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Predeterminado', NULL, 10, 10, 30, 30, 15, 50, 25, 30, 60, 50, 25, '#FFFFFF', '#000000', 'Arial', 1, 'Universidad Nacional Experimental|de los Llanos Occidentales|Ezequiel Zamora|UNELLEZ', 'Credencial Estudiantil|ViceRectorado de Producción Agrícola|Carnet válido hasta: {fecha_expiracion}', NULL, 50, 80, 30, 15, 1, '2025-05-12 01:31:00', '2025-05-12 01:31:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `id_docente` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `especialidad` varchar(255) NOT NULL,
  `antiguedad` varchar(255) NOT NULL,
  `fyh_creacion` date DEFAULT NULL,
  `fyh_actualizacion` date DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`id_docente`, `persona_id`, `especialidad`, `antiguedad`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(2, 3, 'INFORMATICA', '2 años', '2024-10-27', '2024-10-30', '1'),
(3, 14, 'Licenciado ed', '5', '2024-10-28', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `archivo` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id_estudiante` int(11) NOT NULL,
  `tipo_cedula` varchar(50) NOT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `cedula_escolar` varchar(255) DEFAULT NULL,
  `posicion_hijo` int(11) DEFAULT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('masculino','femenino') NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `numeros_telefonicos` varchar(20) NOT NULL,
  `id_representante` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `estatus` enum('activo','inactivo') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo_discapacidad` varchar(50) DEFAULT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id_estudiante`, `tipo_cedula`, `cedula`, `cedula_escolar`, `posicion_hijo`, `nombres`, `apellidos`, `fecha_nacimiento`, `genero`, `correo_electronico`, `direccion`, `numeros_telefonicos`, `id_representante`, `turno_id`, `estatus`, `created_at`, `updated_at`, `tipo_discapacidad`, `foto`) VALUES
(1, 'V', '30045678', NULL, 0, 'Carlos Eduardo', 'Pérez López', '2015-05-10', 'masculino', 'carloseduardo15@gmail.com', 'San Juan de Los Morros', '0412-1234567', 1, 1, 'inactivo', '2025-01-24 12:27:15', '2025-01-25 01:57:29', 'ninguna', ''),
(2, 'V', '30045679', NULL, 0, 'Ana Lucía', 'Pérez López', '2016-06-15', 'femenino', 'analucia15@gmail.com', 'El Junquito', '0414-1234568', 1, 1, 'inactivo', '2025-01-24 12:27:15', '2025-01-25 01:57:56', 'ninguna', ''),
(3, 'V', '30045680', NULL, 0, 'Luis Miguel', 'Pérez López', '2017-07-20', 'masculino', 'luismiguel15@gmail.com', 'La Candelaria', '0416-1234569', 1, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(4, 'V', '31234567', NULL, 0, 'María Fernanda', 'González Torres', '2015-08-25', 'femenino', 'mariafernanda31@gmail.com', 'Santa Teresa', '0424-1234570', 2, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(5, 'V', '31234568', '', 0, 'Diego Alejandro', 'González Torres', '2016-09-30', 'masculino', 'diegoalejandro31@gmail.com', 'Los Palos Grandes', '04164634936', 2, 1, 'activo', '2025-01-24 12:27:15', '2025-02-08 03:28:44', 'ninguna', ''),
(6, 'V', '31234569', NULL, 0, 'Sofía Alejandra', 'González Torres', '2017-10-10', 'femenino', 'sofiaalejandra31@gmail.com', 'El Hatillo', '0412-1234572', 2, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(7, 'V', '32135798', NULL, 0, 'Javier Alejandro', 'Martínez Ruiz', '2015-11-10', 'masculino', 'javieralejandro32@gmail.com', 'Caricuao', '0414-1234580', 3, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(8, 'V', '32135799', NULL, 0, 'Lucía Fernanda', 'Martínez Ruiz', '2016-12-15', 'femenino', 'luciafernanda32@gmail.com', 'Los Rosales', '0416-1234581', 3, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(9, 'V', '32135800', NULL, 0, 'María José', 'Martínez Ruiz', '2017-01-20', 'femenino', 'mariajose32@gmail.com', 'Coche', '0424-1234582', 3, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(10, 'V', '33345678', NULL, 0, 'Carlos Andrés', 'Díaz López', '2015-05-30', 'masculino', 'carlosandres34@gmail.com', 'La Vega', '0426-1234590', 4, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(11, 'V', '33345679', NULL, 0, 'Isabella', 'Díaz López', '2016-06-25', 'femenino', 'isabelladiaz34@gmail.com', 'Los Teques', '0412-1234591', 4, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(12, 'V', '33345680', NULL, 0, 'Santiago', 'Díaz López', '2017-07-15', 'masculino', 'santiagodiaz34@gmail.com', 'Chacao', '0414-1234592', 4, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(13, 'V', '30167891', NULL, 0, 'Valentina', 'Hernández García', '2015-08-25', 'femenino', 'valentinahernandez30@gmail.com', 'Los Dos Caminos', '0416-1234593', 5, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(14, 'V', '30167892', NULL, 0, 'Fernando', 'Hernández García', '2016-09-20', 'masculino', 'fernandohernandez30@gmail.com', 'El Paraíso', '0424-1234501', 5, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(15, 'V', '30167893', NULL, 0, 'Gabriel', 'Hernández García', '2017-10-15', 'masculino', 'gabrielhernandez30@gmail.com', 'Las Mercedes', '0426-1234502', 5, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(16, 'V', '31890123', NULL, 0, 'Mateo', 'Ramírez Fernández', '2015-11-30', 'masculino', 'mateoramirez31@gmail.com', 'Sabana Grande', '0412-1234503', 6, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(17, 'V', '31890124', NULL, 0, 'Camila', 'Ramírez Fernández', '2016-12-31', 'femenino', 'camilaramirez31@gmail.com', 'Los Chaguaramos', '0414-1234504', 6, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(18, 'V', '31890125', NULL, 0, 'Diego', 'Ramírez Fernández', '2017-01-15', 'masculino', 'diegoramirez31@gmail.com', 'Catia', '0416-1234505', 6, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(19, 'V', '32678910', NULL, 0, 'Camilo', 'Morales López', '2015-02-25', 'masculino', 'camilomorales32@gmail.com', 'La Urbina', '0424-1234506', 7, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(20, 'V', '32678911', NULL, 0, 'Natalia', 'Morales López', '2016-03-16', 'femenino', 'nataliamorales32@gmail.com', 'Boleíta', '0426-1234507', 7, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(21, 'V', '32678912', NULL, 0, 'Arturo', 'Morales López', '2017-04-17', 'masculino', 'arturomorales32@gmail.com', 'El Cafetal', '0412-1234508', 7, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(22, 'V', '33789012', NULL, 0, 'Leo', 'Ortega Medina', '2015-05-18', 'masculino', 'leootegamedina33@gmail.com', 'Catedral', '0414-1234509', 8, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(23, 'V', '33789013', NULL, 0, 'Valeria', 'Ortega Medina', '2016-06-29', 'femenino', 'valeriaortegamedina33@gmail.com', 'Calle Real', '0416-1234510', 8, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(24, 'V', '33789014', NULL, 0, 'Esteban', 'Ortega Medina', '2017-07-10', 'masculino', 'estebanortegamedina33@gmail.com', 'San Bernardino', '0424-1234511', 8, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(25, 'V', '30231456', NULL, 0, 'Claudia', 'Chapman Ruiz', '2015-08-26', 'femenino', 'claudiachapman30@gmail.com', 'Los Palos Grandes', '0426-1234512', 9, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(26, 'V', '30231457', NULL, 0, 'Felipe', 'Chapman Ruiz', '2016-09-12', 'masculino', 'felipechapman30@gmail.com', 'Plaza Venezuela', '0412-1234513', 9, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(27, 'V', '30231458', NULL, 0, 'Juan', 'Chapman Ruiz', '2017-10-05', 'masculino', 'juanchapman30@gmail.com', 'Miranda', '0414-1234514', 9, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(28, 'V', '31567890', NULL, 0, 'Simón', 'Salazar Pérez', '2015-11-11', 'masculino', 'simonsalazar31@gmail.com', 'Catia La Mar', '0416-1234515', 10, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(29, 'V', '31567891', NULL, 0, 'María', 'Salazar Pérez', '2016-12-12', 'femenino', 'mariasalazar31@gmail.com', 'Tarqui', '0424-1234516', 10, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(30, 'V', '31567892', NULL, 0, 'Leonardo', 'Salazar Pérez', '2017-01-13', 'masculino', 'leonardosalazar31@gmail.com', 'La Grita', '0426-1234517', 10, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(31, 'V', '32987654', NULL, 0, 'Estefanía', 'Carrillo Martínez', '2015-02-14', 'femenino', 'estefaniacarrillo32@gmail.com', 'El Valle', '0412-1234518', 11, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(32, 'V', '32987655', NULL, 0, 'Diego', 'Carrillo Martínez', '2016-03-15', 'masculino', 'diegocarrillo32@gmail.com', 'La Bandera', '0414-1234519', 11, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(33, 'V', '32987656', NULL, 0, 'Gabriela', 'Carrillo Martínez', '2017-04-16', 'femenino', 'gabrielacarrillo32@gmail.com', 'Antímano', '0416-1234520', 11, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(34, 'V', '33555555', NULL, 0, 'Pablo', 'García López', '2015-02-14', 'masculino', 'pablogarcia33@gmail.com', 'Río de Janeiro', '0412-1234571', 12, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(35, 'V', '33555556', NULL, 0, 'Laura', 'García López', '2016-03-15', 'femenino', 'lauragarcia33@gmail.com', 'Avenida Bolívar', '0414-1234572', 12, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(36, 'V', '33555557', NULL, 0, 'Ricardo', 'García López', '2017-04-16', 'masculino', 'ricardogarcia33@gmail.com', 'Bello Campo', '0416-1234573', 12, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(37, 'V', '30654321', NULL, 0, 'Martina', 'Blanco Rodríguez', '2015-05-18', 'femenino', 'martinablancor33@gmail.com', 'La Yaguara', '0424-1234581', 13, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(38, 'V', '30654322', NULL, 0, 'Santiago', 'Blanco Rodríguez', '2016-06-21', 'masculino', 'santiagoblanco33@gmail.com', 'Tamanaco', '0426-1234582', 13, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(39, 'V', '30654323', NULL, 0, 'Gabriela', 'Blanco Rodríguez', '2017-07-24', 'femenino', 'gabrielablanco33@gmail.com', 'Montalbán', '0412-1234583', 13, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(40, 'V', '31112233', NULL, 0, 'Fernando', 'Castillo Mendoza', '2015-08-26', 'masculino', 'fernandocastillo33@gmail.com', 'Las Acacias', '0414-1234591', 14, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(41, 'V', '31112234', NULL, 0, 'Maria', 'Castillo Mendoza', '2016-09-27', 'femenino', 'mariacastillo33@gmail.com', 'Palo Verde', '0416-1234592', 14, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(42, 'V', '31112235', NULL, 0, 'Javier', 'Castillo Mendoza', '2017-10-18', 'masculino', 'javiercastillo33@gmail.com', 'Cerro Verde', '0424-1234593', 14, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(43, 'V', '32443322', NULL, 0, 'Esteban', 'Rivas Araujo', '2015-11-30', 'masculino', 'estebanrivas33@gmail.com', 'Los Teques', '0426-1234501', 15, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(44, 'V', '32443323', NULL, 0, 'Anabella', 'Rivas Araujo', '2016-12-12', 'femenino', 'anabellarivas33@gmail.com', 'Baruta', '0412-1234502', 15, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(45, 'V', '32443324', NULL, 0, 'Joaquín', 'Rivas Araujo', '2017-01-18', 'masculino', 'joaquinrivas33@gmail.com', 'Guarenas', '0414-1234503', 15, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(46, 'V', '33334455', NULL, 0, 'Marisol', 'Soto Castillo', '2015-02-17', 'femenino', 'marisolsotoc33@gmail.com', 'Santa Fe', '0416-1234501', 16, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(47, 'V', '33334456', NULL, 0, 'Ramón', 'Soto Castillo', '2016-03-31', 'masculino', 'ramonsotoc33@gmail.com', 'La Trinidad', '0424-1234502', 16, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(48, 'V', '33334457', NULL, 0, 'Virginia', 'Soto Castillo', '2017-05-18', 'femenino', 'virginiasotoc33@gmail.com', 'La Candelaria', '0426-1234503', 16, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(49, 'V', '30112233', NULL, 0, 'Óscar', 'Vásquez Pérez', '2015-06-30', 'masculino', 'oscargasquez33@gmail.com', 'Peñalver', '0412-1234501', 17, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(50, 'V', '30112234', NULL, 0, 'Evelyn', 'Vásquez Pérez', '2016-07-14', 'femenino', 'evelynvasquez33@gmail.com', 'Antímano', '0414-1234502', 17, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(51, 'V', '30112235', NULL, 0, 'Mateo', 'Vásquez Pérez', '2017-08-19', 'masculino', 'mateovasquez33@gmail.com', 'Cantaura', '0416-1234503', 17, 1, 'activo', '2025-01-24 12:27:15', '2025-01-24 12:47:31', 'ninguna', ''),
(56, 'V', '33200918', NULL, 0, 'Misael David', 'Marquez Cruz', '2019-05-24', 'masculino', 'misaelmarquez@gmail.com', 'Parroquia Caricuao Ud1', '04121988817', 113, 0, 'activo', '2025-01-24 11:23:20', '2025-01-24 11:23:20', 'ninguna', ''),
(61, 'V', '33200919', NULL, 0, 'Juan Carlos', 'Pérez López', '2018-04-15', 'masculino', 'juancarlos@gmail.com', 'Parroquia Caricuao Ud1', '04121234567', 113, 0, 'activo', '2025-01-24 11:30:25', '2025-01-24 11:30:25', 'ninguna', ''),
(62, 'V', '33200920', NULL, 0, 'Ana María', 'González Torres', '2017-03-10', 'femenino', 'anagonzalez@gmail.com', 'Parroquia Caricuao Ud1', '04121234568', 113, 0, 'activo', '2025-01-24 11:30:25', '2025-01-24 11:30:25', 'ninguna', ''),
(63, 'V', '33200921', NULL, 0, 'Luis Fernando', 'Martínez Ruiz', '2016-02-20', 'masculino', 'luismartinez@gmail.com', 'Parroquia Caricuao Ud1', '04121234569', 113, 0, 'activo', '2025-01-24 11:30:25', '2025-01-24 11:30:25', 'ninguna', ''),
(64, 'V', '', 'V21914756124', 2, 'Sofía Isabel', 'Ramírez López', '2019-01-15', 'femenino', 'sofiaramirez@gmail.com', 'Parroquia Caricuao Ud1', '04121234570', 113, 0, 'activo', '2025-01-24 11:30:25', '2025-01-24 11:33:09', 'ninguna', ''),
(65, 'V', '33200922', NULL, 0, 'Carlos Alberto', 'Hernández Pérez', '2018-05-10', 'masculino', 'carloshp@gmail.com', 'Parroquia Caricuao Ud1', '04121234571', 105, 0, 'activo', '2025-01-24 11:47:17', '2025-01-24 11:47:17', 'ninguna', ''),
(66, 'V', '33200923', NULL, 0, 'María José', 'López García', '2017-06-15', 'femenino', 'mariajose@gmail.com', 'Parroquia Caricuao Ud1', '04121234572', 105, 0, 'activo', '2025-01-24 11:47:17', '2025-01-24 11:47:17', 'ninguna', ''),
(67, 'V', '33200924', NULL, 0, 'Andrés Felipe', 'Martínez Torres', '2016-07-20', 'masculino', 'andresfelipe@gmail.com', 'Parroquia Caricuao Ud1', '04121234573', 105, 0, 'activo', '2025-01-24 11:47:17', '2025-01-24 11:47:17', 'ninguna', ''),
(68, 'V', '33200925', NULL, 0, 'Isabella', 'Ramírez López', '2019-08-25', 'femenino', 'isabellar@gmail.com', 'Parroquia Caricuao Ud1', '04121234574', 105, 0, 'activo', '2025-01-24 11:47:17', '2025-01-24 11:47:17', 'ninguna', ''),
(69, 'V', '33200926', NULL, 0, 'Diego Alejandro', 'González Ruiz', '2015-09-30', 'masculino', 'diegoalejandro@gmail.com', 'Parroquia Caricuao Ud1', '04121234575', 105, 0, 'activo', '2025-01-24 11:47:17', '2025-01-24 11:47:17', 'ninguna', ''),
(75, 'V', '31982330', NULL, 0, 'Fernando José', 'Pérez Martínez', '2018-01-10', 'masculino', 'fernandoj@gmail.com', 'Parroquia Caricuao Ud1', '04121234581', 114, 0, 'activo', '2025-01-24 11:54:21', '2025-01-24 11:54:21', 'ninguna', ''),
(76, 'V', '31982331', NULL, 0, 'Lucía Fernanda', 'González Torres', '2017-02-15', 'femenino', 'luciafernanda@gmail.com', 'Parroquia Caricuao Ud1', '04121234582', 114, 0, 'activo', '2025-01-24 11:54:21', '2025-01-24 11:54:21', 'ninguna', ''),
(77, 'V', '31982332', NULL, 0, 'Javier Alejandro', 'Martínez López', '2016-03-20', 'masculino', 'javieralejandro@gmail.com', 'Parroquia Caricuao Ud1', '04121234583', 114, 0, 'activo', '2025-01-24 11:54:21', '2025-01-24 11:54:21', 'ninguna', ''),
(78, 'V', '', 'V21911985583', 2, 'Sofía Valentina', 'Ramírez Pérez', '2019-04-25', 'femenino', 'sofiavalentina@gmail.com', 'Parroquia Caricuao Ud1', '04121234584', 114, 0, 'activo', '2025-01-24 11:54:21', '2025-01-24 11:54:38', 'ninguna', ''),
(79, 'V', '', 'V11511985583', 1, 'Diego Armando', 'Hernández Ruiz', '2015-05-30', 'masculino', 'diegoarmando@gmail.com', 'Parroquia Caricuao Ud1', '04121234585', 114, 0, 'activo', '2025-01-24 11:54:21', '2025-01-24 11:55:23', 'ninguna', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestiones`
--

CREATE TABLE `gestiones` (
  `id_gestion` int(11) NOT NULL,
  `desde` date NOT NULL,
  `hasta` date NOT NULL,
  `fyh_creacion` date DEFAULT NULL,
  `fyh_actualizacion` date DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gestiones`
--

INSERT INTO `gestiones` (`id_gestion`, `desde`, `hasta`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(1, '2025-07-13', '2026-05-18', '2023-12-28', '2025-01-24', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

CREATE TABLE `grados` (
  `id_grado` int(11) NOT NULL,
  `nivel` varchar(20) NOT NULL,
  `grado` varchar(20) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `fyh_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `trayecto` varchar(20) NOT NULL,
  `trimestre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grados`
--

INSERT INTO `grados` (`id_grado`, `nivel`, `grado`, `estado`, `fyh_creacion`, `trayecto`, `trimestre`) VALUES
(19, 'Inicial', 'Primer Nivel', 1, '2025-01-21 09:00:55', '', ''),
(20, 'Inicial', 'Segundo Nivel', 1, '2025-01-21 09:00:55', '', ''),
(21, 'Inicial', 'Tercer Nivel', 1, '2025-01-21 09:00:55', '', ''),
(22, 'Primaria', 'Primer Grado', 1, '2025-01-21 09:00:55', '', ''),
(23, 'Primaria', 'Segundo Grado', 1, '2025-01-21 09:00:55', '', ''),
(24, 'Primaria', 'Tercer Grado', 1, '2025-01-21 09:00:55', '', ''),
(25, 'Primaria', 'Cuarto Grado', 1, '2025-01-21 09:00:55', '', ''),
(26, 'Primaria', 'Quinto Grado', 1, '2025-01-21 09:00:55', '', ''),
(27, 'Primaria', 'Sexto Grado', 1, '2025-01-21 09:00:55', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados_materias`
--

CREATE TABLE `grados_materias` (
  `id` int(11) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_gestion` int(11) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `id_seccion` int(11) NOT NULL,
  `aula` varchar(20) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id_horario`, `id_gestion`, `id_grado`, `id_seccion`, `aula`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 1, 19, 45, 'Aula 101', '2025-05-04', '2025-06-04'),
(2, 1, 19, 45, 'Aula 101', '2025-05-04', '2025-06-04'),
(3, 1, 19, 45, 'Aula 101', '2025-05-04', '2025-06-04'),
(4, 1, 19, 45, 'Aula 101', '2025-05-04', '2025-06-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario_detalle`
--

CREATE TABLE `horario_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_horario` int(11) NOT NULL,
  `dia_semana` varchar(10) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_profesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horario_detalle`
--

INSERT INTO `horario_detalle` (`id_detalle`, `id_horario`, `dia_semana`, `hora_inicio`, `hora_fin`, `id_materia`, `id_profesor`) VALUES
(1, 1, 'Lunes', '07:50:00', '08:30:00', 3, 1),
(3, 2, 'Lunes', '07:50:00', '08:30:00', 3, 1),
(5, 3, 'Lunes', '07:50:00', '08:30:00', 3, 1),
(7, 4, 'Lunes', '07:50:00', '08:30:00', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `id_gestion` varchar(50) NOT NULL,
  `nivel_id` varchar(50) NOT NULL,
  `grado` varchar(50) NOT NULL,
  `nombre_seccion` varchar(50) NOT NULL,
  `turno_id` varchar(50) NOT NULL,
  `talla_camisa` varchar(10) DEFAULT NULL,
  `talla_pantalon` varchar(10) DEFAULT NULL,
  `talla_zapatos` varchar(10) DEFAULT NULL,
  `id_estudiante` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` varchar(255) DEFAULT NULL,
  `id_seccion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id`, `id_gestion`, `nivel_id`, `grado`, `nombre_seccion`, `turno_id`, `talla_camisa`, `talla_pantalon`, `talla_zapatos`, `id_estudiante`, `created_at`, `updated_at`, `estado`, `id_seccion`) VALUES
(190, '1', 'Primaria', '22', 'A', 'M', 'S', '10', '24', 1, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 48),
(191, '1', 'Primaria', '22', 'A', 'M', 'M', '14', '26', 2, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 48),
(192, '1', 'Primaria', '22', 'B', 'M', 'L', '16', '28', 3, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 49),
(193, '1', 'Primaria', '22', 'B', 'M', 'XL', '26', '30', 4, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 49),
(194, '1', 'Primaria', '22', 'C', 'M', 'XS', '28', '32', 5, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 50),
(195, '1', 'Primaria', '22', 'C', 'M', 'SS', '30', '24', 6, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 50),
(196, '1', 'Primaria', '23', 'A', 'M', 'S', '10', '24', 7, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 54),
(197, '1', 'Primaria', '23', 'A', 'M', 'M', '14', '26', 8, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 54),
(198, '1', 'Primaria', '23', 'B', 'M', 'L', '16', '28', 9, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 55),
(199, '1', 'Primaria', '23', 'B', 'M', 'XL', '26', '30', 10, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 55),
(200, '1', 'Primaria', '23', 'C', 'M', 'XS', '28', '32', 11, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 56),
(201, '1', 'Primaria', '23', 'C', 'M', 'SS', '30', '24', 12, '2025-01-24 18:38:16', '2025-01-24 18:38:16', '1', 56),
(202, '1', 'Primaria', '24', 'A', 'M', 'S', '10', '24', 13, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 60),
(203, '1', 'Primaria', '24', 'A', 'M', 'M', '14', '26', 14, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 60),
(204, '1', 'Primaria', '24', 'B', 'M', 'L', '16', '28', 15, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 61),
(205, '1', 'Primaria', '24', 'B', 'M', 'XL', '26', '30', 16, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 61),
(206, '1', 'Primaria', '24', 'C', 'M', 'XS', '28', '32', 17, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 62),
(207, '1', 'Primaria', '24', 'C', 'M', 'SS', '30', '24', 18, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 62),
(208, '1', 'Primaria', '25', 'A', 'M', 'S', '10', '24', 19, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 66),
(209, '1', 'Primaria', '25', 'A', 'M', 'M', '14', '26', 20, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 66),
(210, '1', 'Primaria', '25', 'B', 'M', 'L', '16', '28', 21, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 67),
(211, '1', 'Primaria', '25', 'B', 'M', 'XL', '26', '30', 22, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 67),
(212, '1', 'Primaria', '25', 'C', 'M', 'XS', '28', '32', 23, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 68),
(213, '1', 'Primaria', '25', 'C', 'M', 'SS', '30', '24', 24, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 68),
(214, '1', 'Primaria', '26', 'A', 'M', 'S', '10', '24', 25, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 72),
(215, '1', 'Primaria', '26', 'A', 'M', 'M', '14', '26', 26, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 72),
(216, '1', 'Primaria', '26', 'B', 'M', 'L', '16', '28', 27, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 73),
(217, '1', 'Primaria', '26', 'B', 'M', 'XL', '26', '30', 28, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 73),
(218, '1', 'Primaria', '26', 'C', 'M', 'XS', '28', '32', 29, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 74),
(219, '1', 'Primaria', '26', 'C', 'M', 'SS', '30', '24', 30, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 74),
(220, '1', 'Primaria', '27', 'A', 'M', 'S', '10', '24', 31, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 78),
(221, '1', 'Primaria', '27', 'A', 'M', 'M', '14', '26', 32, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 78),
(222, '1', 'Primaria', '27', 'B', 'M', 'L', '16', '28', 33, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 79),
(223, '1', 'Primaria', '27', 'B', 'M', 'XL', '26', '30', 34, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 79),
(224, '1', 'Primaria', '27', 'C', 'M', 'XS', '28', '32', 35, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 80),
(225, '1', 'Primaria', '27', 'C', 'M', 'SS', '30', '24', 36, '2025-01-24 18:38:17', '2025-01-24 18:38:17', '1', 80);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lapsos`
--

CREATE TABLE `lapsos` (
  `id_lapso` int(11) NOT NULL,
  `nombre_lapso` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `id_gestion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lapsos`
--

INSERT INTO `lapsos` (`id_lapso`, `nombre_lapso`, `fecha_inicio`, `fecha_fin`, `id_gestion`) VALUES
(1, 'Primer lapso', '2024-10-01', '2025-02-07', 1),
(2, 'Segundo lapso', '2025-02-17', '2025-04-25', 1),
(3, 'Tercer lapso', '2025-05-05', '2025-07-25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre_materia` varchar(100) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `nivel_educativo` enum('Preescolar','Primaria','Secundaria') NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `codigo` varchar(20) NOT NULL,
  `abreviatura` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id_materia`, `nombre_materia`, `id_grado`, `nivel_educativo`, `estado`, `codigo`, `abreviatura`) VALUES
(1, 'Matemáticas', 19, 'Preescolar', 1, '', ''),
(3, 'Educación Física', 20, 'Preescolar', 1, '', ''),
(4, 'Ingles', 27, 'Preescolar', 1, '', ''),
(5, 'Física', 21, 'Preescolar', 1, '', ''),
(6, 'Lenguaje y Comunicación', 21, 'Preescolar', 1, '', ''),
(7, 'Química', 22, 'Preescolar', 1, '', ''),
(8, 'Orientación y convivencia', 23, 'Preescolar', 1, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id_nivel` int(11) NOT NULL,
  `gestion_id` int(11) NOT NULL,
  `nivel` varchar(255) NOT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`id_nivel`, `gestion_id`, `nivel`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(3, 1, 'PRIMARIA', '2024-10-27 00:00:00', '2024-10-27 00:00:00', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_estudiantes`
--

CREATE TABLE `notas_estudiantes` (
  `id_nota` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_lapso` int(11) NOT NULL,
  `calificacion` decimal(4,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas_estudiantes`
--

INSERT INTO `notas_estudiantes` (`id_nota`, `id_estudiante`, `id_materia`, `id_lapso`, `calificacion`, `observaciones`, `fecha_registro`) VALUES
(1, 1, 1, 1, 18.00, NULL, '2025-04-15 02:20:07'),
(2, 10, 1, 1, 14.00, NULL, '2025-04-15 03:40:00'),
(3, 1, 3, 1, 5.00, NULL, '2025-05-11 18:11:48'),
(4, 1, 1, 2, 12.00, NULL, '2025-05-11 18:12:36'),
(5, 1, 3, 3, 20.00, NULL, '2025-05-11 18:12:43'),
(6, 1, 5, 1, 10.00, NULL, '2025-05-11 18:18:06'),
(7, 1, 4, 1, 1.00, NULL, '2025-05-11 18:18:07'),
(8, 1, 6, 1, 12.00, NULL, '2025-05-11 18:18:07'),
(9, 1, 8, 1, 11.00, NULL, '2025-05-11 18:18:07'),
(10, 1, 7, 1, 19.00, NULL, '2025-05-11 18:18:07'),
(11, 1, 3, 2, 10.00, NULL, '2025-05-11 18:19:42'),
(12, 1, 4, 2, 20.00, NULL, '2025-05-11 18:19:42'),
(13, 1, 5, 2, 13.00, NULL, '2025-05-11 18:19:42'),
(14, 1, 6, 2, 17.00, NULL, '2025-05-11 18:19:43'),
(15, 1, 7, 2, 18.00, NULL, '2025-05-11 18:19:43'),
(16, 1, 8, 2, 19.00, NULL, '2025-05-11 18:19:43'),
(17, 1, 1, 3, 16.00, NULL, '2025-05-11 18:21:28'),
(18, 1, 4, 3, 18.00, NULL, '2025-05-11 18:21:28'),
(19, 1, 5, 3, 20.00, NULL, '2025-05-11 18:21:28'),
(20, 1, 6, 3, 15.00, NULL, '2025-05-11 18:21:28'),
(21, 1, 7, 3, 16.00, NULL, '2025-05-11 18:21:28'),
(22, 1, 8, 3, 14.00, NULL, '2025-05-11 18:21:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_url` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_url`, `url`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(1, 'Configuraciones', 'admin/configuraciones/institucion/', '2024-10-26 18:50:54', NULL, '1'),
(2, 'Periodo academico', 'admin/configuraciones/gestion/', '2024-10-26 18:51:45', NULL, '1'),
(3, 'Panel administrador', 'admin/', '2024-10-26 18:52:18', NULL, '1'),
(4, 'Inscripción', 'admin/inscripciones/', '2024-10-26 18:52:56', '2024-10-26 18:53:37', '1'),
(5, 'Lista de estudiante', 'admin/estudiantes/', '2024-10-26 18:54:02', NULL, '1'),
(6, 'Lista de turnos', 'admin/niveles/', '2024-10-26 18:55:34', NULL, '1'),
(7, 'Grados', 'admin/grados/', '2024-10-26 18:55:56', NULL, '1'),
(8, 'Secciones', 'http://localhost/Daniel/SIGE/admin/seccion/', '2024-10-26 18:56:15', NULL, '1'),
(9, 'Roles', 'admin/roles/', '2024-10-26 18:56:35', NULL, '1'),
(10, 'Permisos del sistema', 'admin/roles/permisos.php', '2024-10-26 18:57:11', NULL, '1'),
(11, 'Registro de usuarios', 'admin/usuarios/', '2024-10-26 18:57:58', NULL, '1'),
(12, 'Personal administrativo', 'admin/administrativos/', '2024-10-26 18:58:23', NULL, '1'),
(13, 'Personal docente', 'admin/docentes/', '2024-10-26 18:58:47', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `ci` varchar(20) NOT NULL,
  `fecha_nacimiento` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `fyh_creacion` date DEFAULT NULL,
  `fyh_actualizacion` date DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id_persona`, `usuario_id`, `nombres`, `apellidos`, `ci`, `fecha_nacimiento`, `direccion`, `celular`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(3, 8, 'Carlos Jose', 'Peñas Rivas', '123456756565', '2000-04-06', 'Distrito Capital, Calle Circunvalacion, Urb San Maritn I, Frente a la plaza San Martin.', '04124564199', '2024-10-27', '2024-10-30', '1'),
(4, 10, 'Daniel Jesus', 'Duartes Quintero', '16023866', '1992-12-12', 'Caracas, el valle', '04124564199', '2024-10-27', NULL, '1'),
(14, 56, 'heldyn david', 'diaz daboin', '2', '2024-10-23', 'San Martin', '656', '2024-10-28', NULL, '1'),
(21, 65, 'heldyn david', 'Aray', '555', '2024-10-09', ' caracas', '656', '2024-10-30', NULL, '1'),
(22, 68, 'heldyn david', 'Diaz Daboin', '27985583', '2024-12-18', 'Caricuao', '04124331080', '2024-12-26', NULL, '1'),
(24, 71, 'JENNIFER MARIA', 'GIMÉNEZ', '16562458', '1985-06-23', 'Av. Principal San Martin', '04242268486', '2025-01-24', NULL, '1'),
(26, 73, 'YAGERVI DEL CASTILLO', 'LOPEZ', '12798500', '1975-05-13', 'San Martin', '04124331080', '2025-01-24', '2025-02-08', '1'),
(27, 74, 'JENNIFER MARIA', 'GIMÉNEZ', '16562458', '1976-07-23', 'Av. Principal San Martin', '04242268486', '2025-01-24', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantillas_carnet`
--

CREATE TABLE `plantillas_carnet` (
  `id_plantilla` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `archivo_plantilla` varchar(255) DEFAULT NULL,
  `ancho` int(11) DEFAULT 85,
  `alto` int(11) DEFAULT 54,
  `margen_superior` int(11) DEFAULT 5,
  `margen_inferior` int(11) DEFAULT 5,
  `margen_izquierdo` int(11) DEFAULT 5,
  `margen_derecho` int(11) DEFAULT 5,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `estatus` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id_profesor` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `especialidad` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id_profesor`, `cedula`, `nombres`, `apellidos`, `email`, `telefono`, `especialidad`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `usuario`, `password`) VALUES
(1, '1234567890', 'Juan', 'Pérez', 'juan.perez@example.com', '0987654321', 'Matemáticas', 1, '2025-05-13 14:15:38', NULL, 1, 'e10adc3949ba59abbe56e057f20f883e');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id_reporte` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `periodo_academico` varchar(50) NOT NULL,
  `nivel_id` varchar(50) NOT NULL,
  `grado` varchar(50) NOT NULL,
  `nombre_seccion` varchar(50) NOT NULL,
  `turno_id` varchar(50) NOT NULL,
  `talla_camisa` varchar(50) NOT NULL,
  `talla_pantalon` varchar(50) NOT NULL,
  `talla_zapatos` varchar(50) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representantes`
--

CREATE TABLE `representantes` (
  `id_representante` int(11) NOT NULL,
  `tipo_cedula` enum('V','E') DEFAULT NULL,
  `cedula` int(8) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `estado_civil` enum('Soltero','Casado','Viudo') NOT NULL,
  `afinidad` enum('mama','papa','abuelo','tio') NOT NULL,
  `genero` varchar(50) NOT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `tipo_sangre` enum('A+','A-','AB+','AB-','B+','B-','O+','O-') NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `numeros_telefonicos` varchar(20) NOT NULL,
  `estatus` enum('Activo','Inactivo') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `representantes`
--

INSERT INTO `representantes` (`id_representante`, `tipo_cedula`, `cedula`, `nombres`, `apellidos`, `fecha_nacimiento`, `estado_civil`, `afinidad`, `genero`, `correo_electronico`, `tipo_sangre`, `direccion`, `numeros_telefonicos`, `estatus`, `created_at`) VALUES
(1, 'V', 14023560, 'Carlos Alberto', 'Pérez López', '1980-05-10', 'Casado', '', 'masculino', 'carlosperez@gmail.com', 'O+', 'Caracas', '04121234501', 'Inactivo', '2025-01-24 11:59:04'),
(2, 'V', 14023561, 'Ana María', 'González Torres', '1985-06-15', 'Soltero', '', 'femenino', 'anamaria@gmail.com', 'A+', 'Caracas', '04121234502', 'Inactivo', '2025-01-24 11:59:04'),
(3, 'V', 13023562, 'Luis Fernando', 'Martínez Ruiz', '1990-07-20', 'Casado', '', 'masculino', 'luisfernando@gmail.com', 'B+', 'Caracas', '04121234503', 'Activo', '2025-01-24 11:59:04'),
(4, 'V', 13023563, 'Sofía Valentina', 'Ramírez Pérez', '1995-08-25', 'Soltero', '', 'femenino', 'sofiaramirez@gmail.com', 'AB+', 'Caracas', '04121234504', 'Activo', '2025-01-24 11:59:04'),
(5, 'V', 10023564, 'Diego Armando', 'Hernández Ruiz', '1988-09-30', 'Casado', '', 'masculino', 'diegohernandez@gmail.com', 'O-', 'Caracas', '04121234505', 'Activo', '2025-01-24 11:59:04'),
(6, 'V', 10023565, 'María José', 'López García', '1992-10-05', 'Soltero', '', 'femenino', 'mariajose@gmail.com', 'B-', 'Caracas', '04121234506', 'Activo', '2025-01-24 11:59:04'),
(7, 'V', 7202356, 'Fernando José', 'Cruz Mierez', '1983-11-10', 'Casado', '', 'masculino', 'fernandoj@gmail.com', 'O+', 'Caracas', '04121234507', 'Activo', '2025-01-24 11:59:04'),
(8, 'V', 7202567, 'Lucía Fernanda', 'Daboin Rodriguez', '1987-12-15', 'Soltero', '', 'femenino', 'luciafernanda@gmail.com', 'A+', 'Caracas', '04121234508', 'Activo', '2025-01-24 11:59:04'),
(9, 'V', 8023568, 'Javier Alejandro', 'Martínez López', '1991-01-20', 'Casado', '', 'masculino', 'javieralejandro@gmail.com', 'B+', 'Caracas', '04121234509', 'Activo', '2025-01-24 11:59:04'),
(10, 'V', 8202569, 'Isabella', 'Ramírez López', '1994-02-25', 'Soltero', '', 'femenino', 'isabellar@gmail.com', 'AB+', 'Caracas', '04121234510', 'Activo', '2025-01-24 11:59:04'),
(11, 'V', 14023570, 'Carlos Eduardo', 'González Torres', '1980-03-10', 'Casado', '', 'masculino', 'carloseduardo@gmail.com', 'O+', 'Caracas', '04121234511', 'Activo', '2025-01-24 11:59:04'),
(12, 'V', 13023571, 'María Fernanda', 'Pérez López', '1985-04-15', 'Soltero', '', 'femenino', 'mariafernanda@gmail.com', 'A+', 'Caracas', '04121234512', 'Activo', '2025-01-24 11:59:04'),
(13, 'V', 10023572, 'Luis Miguel', 'Martínez Ruiz', '1990-05-20', 'Casado', '', 'masculino', 'luismiguel@gmail.com', 'B+', 'Caracas', '04121234513', 'Activo', '2025-01-24 11:59:04'),
(14, 'V', 6203573, 'Sofía Alejandra', 'Hernández Ruiz', '1995-06-25', 'Soltero', '', 'femenino', 'sofiaalejandra@gmail.com', 'AB+', 'Caracas', '04121234514', 'Activo', '2025-01-24 11:59:04'),
(15, 'V', 9202574, 'Diego Alejandro', 'Cruz Mierez', '1988-07-30', 'Casado', '', 'masculino', 'diegoalejandro@gmail.com', 'O-', 'Caracas', '04121234515', 'Activo', '2025-01-24 11:59:04'),
(16, 'V', 14023575, 'María Elena', 'Daboin Rodriguez', '1992-08-05', 'Soltero', '', 'femenino', 'mariaelena@gmail.com', 'B-', 'Caracas', '04121234516', 'Activo', '2025-01-24 11:59:04'),
(17, 'V', 13023576, 'Fernando Andrés', 'Martínez López', '1995-09-10', 'Casado', '', 'masculino', 'fernandoandres@gmail.com', 'O+', 'Caracas', '04121234517', 'Activo', '2025-01-24 11:59:04'),
(105, 'V', 27985583, 'Marcos José', 'Cruz Mierez', '2006-12-20', 'Soltero', 'mama', 'masculino', 'marcos1904@gmail.com', 'O+', 'Caricuao', '04121988817', 'Activo', '2025-01-09 21:23:26'),
(113, 'V', 14756124, 'Marilyn del Carmen', 'Daboin Rodriguez', '2007-01-16', 'Soltero', 'mama', 'femenino', 'mary@gmail.com', 'B+', 'Parroquia Caricuao Ud1', '04164655292', 'Activo', '2025-01-16 21:07:44'),
(114, 'V', 11985583, 'Maria Lupita', 'Aray Acosta', '2007-01-22', 'Casado', 'mama', 'masculino', 'marialupita@gmail.com', 'O+', 'Parroquia Caricuao Ud1', '04121988817', 'Activo', '2025-01-22 16:50:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(255) NOT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(1, 'ADMINISTRADOR', '2024-10-26 19:22:07', '2024-11-12 00:00:00', '1'),
(2, 'DIRECTOR', '2024-10-26 19:23:06', NULL, '1'),
(3, 'SUBDIRETOR', '2024-10-26 19:23:14', NULL, '1'),
(4, 'PERSONAL ADMINISTRATIVO', '2024-10-26 19:23:33', NULL, '1'),
(5, 'DOCENTE', '2024-10-26 19:23:43', NULL, '1'),
(7, 'REPRESENTANTE', '2024-10-27 00:00:00', '2025-01-17 00:00:00', '1'),
(8, 'ADMINISTRATIVOS', '2024-10-27 00:00:00', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_permisos`
--

CREATE TABLE `roles_permisos` (
  `id_rol_permiso` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles_permisos`
--

INSERT INTO `roles_permisos` (`id_rol_permiso`, `rol_id`, `permiso_id`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(1, 1, 1, '2024-10-26 19:22:12', NULL, '1'),
(2, 1, 7, '2024-10-26 19:22:14', NULL, '1'),
(3, 1, 4, '2024-10-26 19:22:19', NULL, '1'),
(4, 1, 5, '2024-10-26 19:22:22', NULL, '1'),
(5, 1, 6, '2024-10-26 19:22:25', NULL, '1'),
(6, 1, 3, '2024-10-26 19:22:29', NULL, '1'),
(7, 1, 2, '2024-10-26 19:22:31', NULL, '1'),
(8, 1, 10, '2024-10-26 19:22:39', NULL, '1'),
(9, 1, 12, '2024-10-26 19:22:41', NULL, '1'),
(10, 1, 13, '2024-10-26 19:22:46', NULL, '1'),
(11, 1, 11, '2024-10-26 19:22:49', NULL, '1'),
(12, 1, 9, '2024-10-26 19:22:52', NULL, '1'),
(13, 1, 8, '2024-10-26 19:22:55', NULL, '1'),
(14, 4, 1, '2025-01-17 00:00:00', NULL, '1'),
(15, 4, 4, '2025-01-17 00:00:00', NULL, '1'),
(16, 7, 1, '2025-01-17 00:00:00', NULL, '1'),
(18, 7, 4, '2025-01-17 00:00:00', NULL, '1'),
(19, 7, 3, '2025-01-17 00:00:00', NULL, '1'),
(20, 7, 10, '2025-01-17 00:00:00', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sangre`
--

CREATE TABLE `sangre` (
  `sangre_id` int(30) NOT NULL,
  `tipo_sangre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sangre`
--

INSERT INTO `sangre` (`sangre_id`, `tipo_sangre`) VALUES
(1, 'A+'),
(2, 'A-'),
(3, 'B+'),
(4, 'B-'),
(5, 'AB+'),
(6, 'AB-'),
(7, 'O+'),
(8, 'O-'),
(1, 'A+'),
(2, 'A-'),
(3, 'B+'),
(4, 'B-'),
(5, 'AB+'),
(6, 'AB-'),
(7, 'O+'),
(8, 'O-');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

CREATE TABLE `secciones` (
  `id_seccion` int(11) NOT NULL,
  `turno` char(1) DEFAULT NULL,
  `capacidad` int(11) NOT NULL,
  `id_gestion` int(11) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `nombre_seccion` varchar(255) NOT NULL,
  `fyh_creacion` datetime DEFAULT current_timestamp(),
  `cupo_actual` int(11) DEFAULT 0,
  `aula` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id_seccion`, `turno`, `capacidad`, `id_gestion`, `id_grado`, `estado`, `nombre_seccion`, `fyh_creacion`, `cupo_actual`, `aula`) VALUES
(45, 'M', 25, 1, 19, 1, 'A', '2025-01-24 13:07:51', 0, ''),
(46, 'T', 25, 1, 20, 1, 'B', '2025-01-24 13:11:22', 0, ''),
(47, 'T', 25, 1, 21, 1, 'C', '2025-01-24 13:12:11', 0, ''),
(48, 'M', 25, 1, 22, 1, 'A', '2025-01-24 13:17:23', 3, ''),
(49, 'M', 25, 1, 22, 1, 'B', '2025-01-24 13:17:23', 1, ''),
(50, 'M', 25, 1, 22, 1, 'C', '2025-01-24 13:17:23', 1, ''),
(51, 'T', 25, 1, 22, 1, 'D', '2025-01-24 13:17:23', 0, ''),
(52, 'T', 25, 1, 22, 1, 'E', '2025-01-24 13:17:23', 0, ''),
(53, 'T', 25, 1, 22, 1, 'F', '2025-01-24 13:17:23', 0, ''),
(54, 'M', 35, 1, 23, 1, 'A', '2025-01-24 13:17:23', 1, ''),
(55, 'M', 35, 1, 23, 1, 'B', '2025-01-24 13:17:23', 1, ''),
(56, 'M', 35, 1, 23, 1, 'C', '2025-01-24 13:17:23', 1, ''),
(57, 'T', 35, 1, 23, 1, 'D', '2025-01-24 13:17:23', 0, ''),
(58, 'T', 35, 1, 23, 1, 'E', '2025-01-24 13:17:23', 0, ''),
(59, 'T', 35, 1, 23, 1, 'F', '2025-01-24 13:17:23', 0, ''),
(60, 'M', 35, 1, 24, 1, 'A', '2025-01-24 13:17:23', 1, ''),
(61, 'M', 35, 1, 24, 1, 'B', '2025-01-24 13:17:23', 1, ''),
(62, 'M', 35, 1, 24, 1, 'C', '2025-01-24 13:17:23', 1, ''),
(63, 'T', 35, 1, 24, 1, 'D', '2025-01-24 13:17:23', 0, ''),
(64, 'T', 35, 1, 24, 1, 'E', '2025-01-24 13:17:23', 0, ''),
(65, 'T', 35, 1, 24, 1, 'F', '2025-01-24 13:17:23', 0, ''),
(66, 'M', 35, 1, 25, 1, 'A', '2025-01-24 13:17:23', 1, ''),
(67, 'M', 35, 1, 25, 1, 'B', '2025-01-24 13:17:23', 1, ''),
(68, 'M', 35, 1, 25, 1, 'C', '2025-01-24 13:17:23', 1, ''),
(69, 'T', 35, 1, 25, 1, 'D', '2025-01-24 13:17:23', 0, ''),
(70, 'T', 35, 1, 25, 1, 'E', '2025-01-24 13:17:23', 0, ''),
(71, 'T', 35, 1, 25, 1, 'F', '2025-01-24 13:17:23', 0, ''),
(72, 'M', 35, 1, 26, 1, 'A', '2025-01-24 13:17:23', 1, ''),
(73, 'M', 35, 1, 26, 1, 'B', '2025-01-24 13:17:23', 1, ''),
(74, 'M', 35, 1, 26, 1, 'C', '2025-01-24 13:17:23', 1, ''),
(75, 'T', 35, 1, 26, 1, 'D', '2025-01-24 13:17:23', 0, ''),
(76, 'T', 35, 1, 26, 1, 'E', '2025-01-24 13:17:23', 0, ''),
(77, 'T', 35, 1, 26, 1, 'F', '2025-01-24 13:17:23', 0, ''),
(78, 'M', 35, 1, 27, 1, 'A', '2025-01-24 13:17:24', 1, ''),
(79, 'M', 35, 1, 27, 1, 'B', '2025-01-24 13:17:24', 1, ''),
(80, 'M', 35, 1, 27, 1, 'C', '2025-01-24 13:17:24', 1, ''),
(81, 'T', 35, 1, 27, 1, 'D', '2025-01-24 13:17:24', 0, ''),
(82, 'T', 35, 1, 27, 1, 'E', '2025-01-24 13:17:24', 0, ''),
(83, 'T', 35, 1, 27, 1, 'F', '2025-01-24 13:17:24', 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sexos`
--

CREATE TABLE `sexos` (
  `sexo_id` int(11) NOT NULL,
  `sexo` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sexos`
--

INSERT INTO `sexos` (`sexo_id`, `sexo`) VALUES
(1, 'Masculino'),
(2, 'Femenino'),
(1, 'Masculino'),
(2, 'Femenino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_constancias`
--

CREATE TABLE `solicitudes_constancias` (
  `id_solicitud` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `cedula_estudiante` varchar(20) NOT NULL,
  `nombre_estudiante` varchar(100) NOT NULL,
  `grado_seccion` varchar(50) NOT NULL,
  `id_tipo_constancia` int(11) NOT NULL,
  `nombre_representante` varchar(100) NOT NULL,
  `cedula_representante` varchar(20) NOT NULL,
  `parentesco` varchar(50) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada','Entregada') NOT NULL DEFAULT 'Pendiente',
  `id_usuario_aprobador` int(11) DEFAULT NULL,
  `id_usuario_entrega` int(11) DEFAULT NULL,
  `ruta_pdf` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_constancias`
--

INSERT INTO `solicitudes_constancias` (`id_solicitud`, `id_estudiante`, `cedula_estudiante`, `nombre_estudiante`, `grado_seccion`, `id_tipo_constancia`, `nombre_representante`, `cedula_representante`, `parentesco`, `observaciones`, `fecha_solicitud`, `fecha_aprobacion`, `fecha_entrega`, `estado`, `id_usuario_aprobador`, `id_usuario_entrega`, `ruta_pdf`, `created_at`, `updated_at`) VALUES
(1, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:17:54', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:17:54', '2025-04-20 04:17:54'),
(2, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:17:54', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:17:54', '2025-04-20 04:17:54'),
(3, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:22:52', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:22:52', '2025-04-20 04:22:52'),
(4, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:22:52', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:22:52', '2025-04-20 04:22:52'),
(5, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Representante Legal', '', '2025-04-20 06:33:53', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:33:53', '2025-04-20 04:33:53'),
(6, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Representante Legal', '', '2025-04-20 06:33:55', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:33:55', '2025-04-20 04:33:55'),
(7, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:39:03', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:39:03', '2025-04-20 04:39:03'),
(8, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:39:06', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:39:06', '2025-04-20 04:39:06'),
(9, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:40:36', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:40:36', '2025-04-20 04:40:36'),
(10, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:40:37', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:40:37', '2025-04-20 04:40:37'),
(11, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:41:25', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:41:25', '2025-04-20 04:41:25'),
(12, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:41:26', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:41:26', '2025-04-20 04:41:26'),
(13, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:42:52', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:42:52', '2025-04-20 04:42:52'),
(14, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:48:49', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:48:49', '2025-04-20 04:48:49'),
(15, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:48:51', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:48:51', '2025-04-20 04:48:51'),
(16, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:49:59', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:49:59', '2025-04-20 04:49:59'),
(17, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:50:00', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:50:00', '2025-04-20 04:50:00'),
(18, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:51:53', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:51:53', '2025-04-20 04:51:53'),
(19, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:51:55', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:51:55', '2025-04-20 04:51:55'),
(20, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:53:35', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:53:35', '2025-04-20 04:53:35'),
(21, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:53:37', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:53:37', '2025-04-20 04:53:37'),
(22, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:54:28', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:54:28', '2025-04-20 04:54:28'),
(23, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:54:31', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:54:31', '2025-04-20 04:54:31'),
(24, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:55:55', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:55:55', '2025-04-20 04:55:55'),
(25, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:55:57', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:55:57', '2025-04-20 04:55:57'),
(26, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:57:15', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:57:15', '2025-04-20 04:57:15'),
(27, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:57:17', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:57:17', '2025-04-20 04:57:17'),
(28, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:59:46', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:59:46', '2025-04-20 04:59:46'),
(29, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 06:59:48', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 04:59:48', '2025-04-20 04:59:48'),
(30, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 07:00:29', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 05:00:29', '2025-04-20 05:00:29'),
(31, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 07:00:31', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 05:00:31', '2025-04-20 05:00:31'),
(32, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 07:03:35', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 05:03:35', '2025-04-20 05:03:35'),
(33, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 07:03:37', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 05:03:37', '2025-04-20 05:03:37'),
(34, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Representante Legal', '', '2025-04-20 18:20:45', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 16:20:45', '2025-04-20 16:20:45'),
(35, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Representante Legal', '', '2025-04-20 18:20:45', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 16:20:45', '2025-04-20 16:20:45'),
(36, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 18:41:09', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 16:41:09', '2025-04-20 16:41:09'),
(37, 1, 'V-30045678', 'Carlos Eduardo Pérez López', 'N/A - N/A', 1, 'Carlos Alberto Pérez López', 'V-14023560', 'Padre', '', '2025-04-20 18:41:11', NULL, NULL, 'Pendiente', NULL, NULL, NULL, '2025-04-20 16:41:11', '2025-04-20 16:41:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tallas`
--

CREATE TABLE `tallas` (
  `talla_id` int(30) NOT NULL,
  `talla` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tallas`
--

INSERT INTO `tallas` (`talla_id`, `talla`) VALUES
(1, 'XS'),
(2, 'S'),
(3, 'M'),
(4, 'L'),
(5, 'XL'),
(6, 'XXL'),
(1, 'XS'),
(2, 'S'),
(3, 'M'),
(4, 'L'),
(5, 'XL'),
(6, 'XXL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_constancia`
--

CREATE TABLE `tipos_constancia` (
  `id_tipo_constancia` int(11) NOT NULL,
  `nombre_tipo_constancia` varchar(255) NOT NULL,
  `descripcion_tipo_constancia` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_constancia`
--

INSERT INTO `tipos_constancia` (`id_tipo_constancia`, `nombre_tipo_constancia`, `descripcion_tipo_constancia`, `fecha_creacion`) VALUES
(1, 'Constancia de Estudio', NULL, '2025-04-20 03:35:35'),
(2, 'Constancia de Conducta', NULL, '2025-04-20 03:35:35'),
(3, 'Constancia de Notas', NULL, '2025-04-20 03:35:35'),
(4, 'Constancia de Matrícula', NULL, '2025-04-20 03:35:35'),
(5, 'Constancia de Regularidad', NULL, '2025-04-20 03:35:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id_turno` int(11) NOT NULL,
  `nombre_turno` varchar(50) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id_turno`, `nombre_turno`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Mañana', 'activo', '2025-01-09 21:28:38', '2025-01-09 21:28:38'),
(2, 'Tarde', 'activo', '2025-01-09 21:28:38', '2025-01-09 21:28:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `fyh_creacion` datetime DEFAULT NULL,
  `fyh_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `rol_id`, `email`, `password`, `fyh_creacion`, `fyh_actualizacion`, `estado`) VALUES
(1, 1, 'admin@gmail.com', '$2y$10$NVhkeupcyKUPFqx.l7t7n.qELV7X5LxKjmOV3WwyRQ3CfJquHF0P2', '2023-12-28 20:29:10', '2024-11-12 00:00:00', '1');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrativos`
--
ALTER TABLE `administrativos`
  ADD PRIMARY KEY (`id_administrativo`),
  ADD KEY `persona_id` (`persona_id`);

--
-- Indices de la tabla `carnets_emitidos`
--
ALTER TABLE `carnets_emitidos`
  ADD PRIMARY KEY (`id_emision`),
  ADD UNIQUE KEY `codigo_qr` (`codigo_qr`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_diseno` (`id_diseno`);

--
-- Indices de la tabla `carnets_estudiantiles`
--
ALTER TABLE `carnets_estudiantiles`
  ADD PRIMARY KEY (`id_carnet`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_plantilla` (`id_plantilla`);

--
-- Indices de la tabla `configuracion_instituciones`
--
ALTER TABLE `configuracion_instituciones`
  ADD PRIMARY KEY (`id_config_institucion`);

--
-- Indices de la tabla `config_carnets`
--
ALTER TABLE `config_carnets`
  ADD PRIMARY KEY (`id_config`);

--
-- Indices de la tabla `datos_institucion`
--
ALTER TABLE `datos_institucion`
  ADD PRIMARY KEY (`id_institucion`);

--
-- Indices de la tabla `diseno_carnets`
--
ALTER TABLE `diseno_carnets`
  ADD PRIMARY KEY (`id_diseno`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`id_docente`),
  ADD KEY `persona_id` (`persona_id`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `id_representante` (`id_representante`),
  ADD KEY `turno_id` (`turno_id`);

--
-- Indices de la tabla `gestiones`
--
ALTER TABLE `gestiones`
  ADD PRIMARY KEY (`id_gestion`);

--
-- Indices de la tabla `grados`
--
ALTER TABLE `grados`
  ADD PRIMARY KEY (`id_grado`);

--
-- Indices de la tabla `grados_materias`
--
ALTER TABLE `grados_materias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_grado` (`id_grado`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_gestion` (`id_gestion`),
  ADD KEY `id_grado` (`id_grado`),
  ADD KEY `id_seccion` (`id_seccion`);

--
-- Indices de la tabla `horario_detalle`
--
ALTER TABLE `horario_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_horario` (`id_horario`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_seccion` (`id_seccion`);

--
-- Indices de la tabla `lapsos`
--
ALTER TABLE `lapsos`
  ADD PRIMARY KEY (`id_lapso`),
  ADD KEY `id_gestion` (`id_gestion`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id_nivel`),
  ADD UNIQUE KEY `gestion_id_2` (`gestion_id`),
  ADD KEY `gestion_id` (`gestion_id`);

--
-- Indices de la tabla `notas_estudiantes`
--
ALTER TABLE `notas_estudiantes`
  ADD PRIMARY KEY (`id_nota`),
  ADD UNIQUE KEY `id_estudiante` (`id_estudiante`,`id_materia`,`id_lapso`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_lapso` (`id_lapso`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id_persona`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `plantillas_carnet`
--
ALTER TABLE `plantillas_carnet`
  ADD PRIMARY KEY (`id_plantilla`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id_profesor`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `id_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `representantes`
--
ALTER TABLE `representantes`
  ADD PRIMARY KEY (`id_representante`),
  ADD KEY `correo_electrónico` (`correo_electronico`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD PRIMARY KEY (`id_rol_permiso`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD PRIMARY KEY (`id_seccion`),
  ADD KEY `id_gestion` (`id_gestion`),
  ADD KEY `id_grado` (`id_grado`);

--
-- Indices de la tabla `solicitudes_constancias`
--
ALTER TABLE `solicitudes_constancias`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_tipo_constancia` (`id_tipo_constancia`),
  ADD KEY `id_usuario_aprobador` (`id_usuario_aprobador`),
  ADD KEY `id_usuario_entrega` (`id_usuario_entrega`);

--
-- Indices de la tabla `tipos_constancia`
--
ALTER TABLE `tipos_constancia`
  ADD PRIMARY KEY (`id_tipo_constancia`),
  ADD UNIQUE KEY `nombre_tipo_constancia` (`nombre_tipo_constancia`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id_turno`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrativos`
--
ALTER TABLE `administrativos`
  MODIFY `id_administrativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `carnets_emitidos`
--
ALTER TABLE `carnets_emitidos`
  MODIFY `id_emision` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carnets_estudiantiles`
--
ALTER TABLE `carnets_estudiantiles`
  MODIFY `id_carnet` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_instituciones`
--
ALTER TABLE `configuracion_instituciones`
  MODIFY `id_config_institucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `config_carnets`
--
ALTER TABLE `config_carnets`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `datos_institucion`
--
ALTER TABLE `datos_institucion`
  MODIFY `id_institucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `diseno_carnets`
--
ALTER TABLE `diseno_carnets`
  MODIFY `id_diseno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `gestiones`
--
ALTER TABLE `gestiones`
  MODIFY `id_gestion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `grados`
--
ALTER TABLE `grados`
  MODIFY `id_grado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `grados_materias`
--
ALTER TABLE `grados_materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `horario_detalle`
--
ALTER TABLE `horario_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT de la tabla `lapsos`
--
ALTER TABLE `lapsos`
  MODIFY `id_lapso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id_nivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `notas_estudiantes`
--
ALTER TABLE `notas_estudiantes`
  MODIFY `id_nota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `plantillas_carnet`
--
ALTER TABLE `plantillas_carnet`
  MODIFY `id_plantilla` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `representantes`
--
ALTER TABLE `representantes`
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `id_rol_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `secciones`
--
ALTER TABLE `secciones`
  MODIFY `id_seccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `solicitudes_constancias`
--
ALTER TABLE `solicitudes_constancias`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `tipos_constancia`
--
ALTER TABLE `tipos_constancia`
  MODIFY `id_tipo_constancia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id_turno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administrativos`
--
ALTER TABLE `administrativos`
  ADD CONSTRAINT `administrativos_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id_persona`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `carnets_emitidos`
--
ALTER TABLE `carnets_emitidos`
  ADD CONSTRAINT `carnets_emitidos_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`),
  ADD CONSTRAINT `carnets_emitidos_ibfk_2` FOREIGN KEY (`id_diseno`) REFERENCES `diseno_carnets` (`id_diseno`);

--
-- Filtros para la tabla `carnets_estudiantiles`
--
ALTER TABLE `carnets_estudiantiles`
  ADD CONSTRAINT `carnets_estudiantiles_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`),
  ADD CONSTRAINT `carnets_estudiantiles_ibfk_2` FOREIGN KEY (`id_plantilla`) REFERENCES `plantillas_carnet` (`id_plantilla`);

--
-- Filtros para la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD CONSTRAINT `docentes_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id_persona`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`id_representante`) REFERENCES `representantes` (`id_representante`);

--
-- Filtros para la tabla `grados_materias`
--
ALTER TABLE `grados_materias`
  ADD CONSTRAINT `grados_materias_ibfk_1` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`),
  ADD CONSTRAINT `grados_materias_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_gestion`) REFERENCES `gestiones` (`id_gestion`),
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`),
  ADD CONSTRAINT `horarios_ibfk_3` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id_seccion`);

--
-- Filtros para la tabla `horario_detalle`
--
ALTER TABLE `horario_detalle`
  ADD CONSTRAINT `horario_detalle_ibfk_1` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id_horario`),
  ADD CONSTRAINT `horario_detalle_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `horario_detalle_ibfk_3` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `fk_id_seccion` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id_seccion`);

--
-- Filtros para la tabla `lapsos`
--
ALTER TABLE `lapsos`
  ADD CONSTRAINT `lapsos_ibfk_1` FOREIGN KEY (`id_gestion`) REFERENCES `gestiones` (`id_gestion`);

--
-- Filtros para la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD CONSTRAINT `niveles_ibfk_1` FOREIGN KEY (`gestion_id`) REFERENCES `gestiones` (`id_gestion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notas_estudiantes`
--
ALTER TABLE `notas_estudiantes`
  ADD CONSTRAINT `notas_estudiantes_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`),
  ADD CONSTRAINT `notas_estudiantes_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `notas_estudiantes_ibfk_3` FOREIGN KEY (`id_lapso`) REFERENCES `lapsos` (`id_lapso`);

--
-- Filtros para la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD CONSTRAINT `secciones_ibfk_1` FOREIGN KEY (`id_gestion`) REFERENCES `gestiones` (`id_gestion`) ON DELETE CASCADE,
  ADD CONSTRAINT `secciones_ibfk_2` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
