-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-01-2025 a las 07:07:16
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
-- Base de datos: `rh`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestacion_lentes`
--

CREATE TABLE `prestacion_lentes` (
  `id` int(11) NOT NULL,
  `trabajador_id` int(11) NOT NULL,
  `tipo_solicitud` enum('trabajador','hijo') NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT 2500.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestacion_lentes`
--

INSERT INTO `prestacion_lentes` (`id`, `trabajador_id`, `tipo_solicitud`, `fecha_solicitud`, `monto`) VALUES
(1, 1, 'trabajador', '2024-02-10', 2500.00),
(2, 1, 'hijo', '2024-03-12', 2500.00),
(4, 4, 'hijo', '2024-05-20', 2500.00),
(5, 2, 'trabajador', '2025-01-18', 2500.00),
(6, 5, 'trabajador', '2025-01-18', 2500.00),
(8, 5, 'hijo', '2025-01-18', 2500.00),
(9, 1, 'trabajador', '2025-01-18', 2500.00),
(10, 1, 'hijo', '2025-01-18', 2500.00),
(11, 2, 'hijo', '2025-01-18', 2500.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_ingreso` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `nombre`, `fecha_ingreso`) VALUES
(1, 'Juan Pérez', '2022-01-15'),
(2, 'María López', '2023-06-10'),
(3, 'Carlos Ramírez', '2024-08-01'),
(4, 'Ana Torres', '2021-03-20'),
(5, 'Luis Sánchez', '2020-12-05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `prestacion_lentes`
--
ALTER TABLE `prestacion_lentes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trabajador_id` (`trabajador_id`,`tipo_solicitud`,`fecha_solicitud`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `prestacion_lentes`
--
ALTER TABLE `prestacion_lentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `prestacion_lentes`
--
ALTER TABLE `prestacion_lentes`
  ADD CONSTRAINT `prestacion_lentes_ibfk_1` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
