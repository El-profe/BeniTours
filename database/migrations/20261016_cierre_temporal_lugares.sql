-- Migración para soporte de Cierre Temporal y Avisos Operativos Especiales en Comercios
ALTER TABLE `lugares` 
ADD COLUMN `estado_operativo` ENUM('ABIERTO_HABITUAL', 'CERRADO_TEMPORAL') NOT NULL DEFAULT 'ABIERTO_HABITUAL' AFTER `horario_atencion`,
ADD COLUMN `aviso_temporal_motivo` VARCHAR(255) NULL AFTER `estado_operativo`,
ADD COLUMN `aviso_temporal_reapertura` DATE NULL AFTER `aviso_temporal_motivo`;
