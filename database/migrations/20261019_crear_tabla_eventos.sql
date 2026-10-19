-- Migración para gestión dinámica de eventos y agenda del Beni desde el panel administrativo
CREATE TABLE IF NOT EXISTS `eventos_agenda` (
  `id_evento` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(180) NOT NULL,
  `subtitulo_lugar` varchar(180) NOT NULL DEFAULT 'Trinidad · Beni',
  `detalle_fechas` varchar(180) NOT NULL,
  `imagen_archivo` varchar(255) NOT NULL,
  `enlace_externo` varchar(255) NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrar los 2 eventos que ya tenías en código
INSERT INTO `eventos_agenda` (`titulo`, `subtitulo_lugar`, `detalle_fechas`, `imagen_archivo`, `fecha_inicio`, `fecha_fin`, `activo`, `orden`) 
SELECT 'XIII Entrada Folklórica Universitaria Nacional', 'Trinidad · Beni', '13 al 16 de noviembre de 2026', 'EntradaFolklórica2026.jpeg', '2026-10-01', '2026-11-16', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `eventos_agenda` WHERE `titulo` LIKE '%Entrada Folklórica%');

INSERT INTO `eventos_agenda` (`titulo`, `subtitulo_lugar`, `detalle_fechas`, `imagen_archivo`, `fecha_inicio`, `fecha_fin`, `activo`, `orden`) 
SELECT 'FexpoBeni 2026', 'Trinidad · Beni', '14 al 18 de noviembre de 2026', 'FexpoBeni2026.jpeg', '2026-10-01', '2026-11-18', 1, 2
WHERE NOT EXISTS (SELECT 1 FROM `eventos_agenda` WHERE `titulo` LIKE '%FexpoBeni%');
