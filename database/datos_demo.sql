-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 27-11-2026 a las 07:18:29
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
-- Base de datos: `hegiastock`
--

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`, `descripcion`, `activo`) VALUES
(1, 'Construccion', 'Materiales de construccion', 1),
(2, 'Herramientas', 'Herramientas de trabajo', 1),
(3, 'Electricidad', 'Materiales electricos', 1);

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `sku`, `nombre_comercial`, `id_categoria`, `id_unidad`, `cantidad_disponible`, `activo`, `actualizado_en`) VALUES
(1, 'HEG-001', 'Cemento Portland', 1, 2, 120, 1, '2026-10-09 19:50:46'),
(2, 'HEG-002', 'Ladrillo King Kong', 1, 1, 850, 1, '2026-10-09 19:50:46'),
(3, 'HEG-003', 'Taladro electrico', 2, 1, 15, 1, '2026-10-09 19:50:46'),
(4, 'HEG-004', 'Cable electrico', 3, 3, 300, 1, '2026-10-09 19:50:46'),
(5, 'HEG-005', 'Martillo', 2, 1, 24, 1, '2026-10-09 19:50:46'),
(6, 'HEG-006', 'Arena fina', 1, 2, 200, 1, '2026-11-26 23:31:09'),
(7, 'HEG-007', 'Fierro corrugado 1/2', 1, 1, 85, 1, '2026-11-26 23:31:09'),
(8, 'HEG-008', 'Tubo PVC 1/2', 1, 1, 60, 1, '2026-11-26 23:31:09'),
(9, 'HEG-009', 'Pintura blanca', 1, 1, 18, 1, '2026-11-26 23:31:09'),
(10, 'HEG-010', 'Alicate universal', 2, 1, 12, 1, '2026-11-26 23:31:09'),
(11, 'HEG-011', 'Destornillador estrella', 2, 1, 25, 1, '2026-11-26 23:31:09'),
(12, 'HEG-012', 'Llave francesa', 2, 1, 8, 1, '2026-11-26 23:31:09'),
(13, 'HEG-013', 'Disco de corte', 2, 1, 4, 1, '2026-11-26 23:31:09'),
(14, 'HEG-014', 'Tornillo autorroscante', 1, 1, 500, 1, '2026-11-26 23:31:09'),
(15, 'HEG-015', 'Clavos de 2 pulgadas', 1, 1, 150, 1, '2026-11-26 23:31:09'),
(16, 'HEG-016', 'Interruptor simple', 3, 1, 35, 1, '2026-11-26 23:31:09'),
(17, 'HEG-017', 'Tomacorriente doble', 3, 1, 40, 1, '2026-11-26 23:31:09'),
(18, 'HEG-018', 'Cinta aislante', 3, 1, 3, 1, '2026-11-26 23:31:09'),
(19, 'HEG-019', 'Silicona transparente', 1, 1, 0, 1, '2026-11-26 23:31:09'),
(20, 'HEG-020', 'Casco de seguridad', 2, 1, 20, 1, '2026-11-26 23:31:09');

--
-- Volcado de datos para la tabla `ubicacion`
--

INSERT INTO `ubicacion` (`id_ubicacion`, `id_producto`, `numero_pasillo`, `codigo_estante`, `observacion`) VALUES
(1, 1, 1, 'A-01', NULL),
(2, 2, 1, 'A-02', 'Ubicacion de demostracion'),
(3, 3, 2, 'B-03', 'Ubicacion de demostracion'),
(4, 4, 3, 'C-04', 'Ubicacion de demostracion'),
(5, 5, 2, 'B-05', 'Ubicacion de demostracion'),
(6, 6, 1, 'A-06', 'Ubicacion de demostracion'),
(7, 7, 1, 'A-07', 'Ubicacion de demostracion'),
(8, 8, 1, 'A-08', 'Ubicacion de demostracion'),
(9, 9, 1, 'A-09', 'Ubicacion de demostracion'),
(10, 10, 2, 'B-10', 'Ubicacion de demostracion'),
(11, 11, 2, 'B-11', 'Ubicacion de demostracion'),
(12, 12, 2, 'B-12', 'Ubicacion de demostracion'),
(13, 13, 2, 'B-13', 'Ubicacion de demostracion'),
(14, 14, 1, 'A-14', 'Ubicacion de demostracion'),
(15, 15, 1, 'A-15', 'Ubicacion de demostracion'),
(16, 16, 3, 'C-16', 'Ubicacion de demostracion'),
(17, 17, 3, 'C-17', 'Ubicacion de demostracion'),
(18, 18, 3, 'C-18', 'Ubicacion de demostracion'),
(19, 19, 1, 'A-19', 'Ubicacion de demostracion'),
(20, 20, 2, 'B-20', 'Ubicacion de demostracion');

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id_unidad`, `simbolo`, `descripcion`) VALUES
(1, 'und', 'Unidad'),
(2, 'bolsa', 'Bolsa'),
(3, 'm', 'Metro');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
