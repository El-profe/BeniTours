-- Mejoras operativas administrativas de BeniTours.
-- Ejecutar una sola vez sobre la base de datos existente.

CREATE TABLE IF NOT EXISTS bitacora_admin (
    id_bitacora INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_lugar INT NULL,
    id_administrador INT NULL,
    tipo_evento VARCHAR(60) NOT NULL,
    descripcion VARCHAR(500) NOT NULL,
    referencia_tipo VARCHAR(40) NULL,
    referencia_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_bitacora_lugar_fecha (id_lugar, created_at),
    CONSTRAINT fk_bitacora_lugar FOREIGN KEY (id_lugar) REFERENCES lugares(id_lugar) ON DELETE SET NULL,
    CONSTRAINT fk_bitacora_admin FOREIGN KEY (id_administrador) REFERENCES administradores(id_administrador) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS seguimientos_cobro (
    id_seguimiento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_lugar INT NOT NULL,
    id_administrador INT NULL,
    canal ENUM('WHATSAPP','LLAMADA','OTRO') NOT NULL DEFAULT 'WHATSAPP',
    resultado ENUM('ENVIADO','RESPONDIO','SIN_RESPUESTA','PROMETIO_PAGO','NO_INTERESADO') NOT NULL DEFAULT 'ENVIADO',
    nota VARCHAR(1000) NULL,
    proximo_seguimiento DATE NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_seguimiento_lugar_fecha (id_lugar, created_at),
    INDEX idx_seguimiento_proximo (proximo_seguimiento),
    CONSTRAINT fk_seguimiento_lugar FOREIGN KEY (id_lugar) REFERENCES lugares(id_lugar) ON DELETE CASCADE,
    CONSTRAINT fk_seguimiento_admin FOREIGN KEY (id_administrador) REFERENCES administradores(id_administrador) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
