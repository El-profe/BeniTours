-- Migración para soporte de Baja Voluntaria de Comercios
ALTER TABLE `publicaciones`
ADD COLUMN `baja_voluntaria` TINYINT(1) NOT NULL DEFAULT 0 AFTER `motivo_suspension`,
ADD COLUMN `baja_fecha` DATETIME NULL AFTER `baja_voluntaria`,
ADD COLUMN `baja_motivo` VARCHAR(100) NULL AFTER `baja_fecha`,
ADD COLUMN `baja_comentario` TEXT NULL AFTER `baja_motivo`;
