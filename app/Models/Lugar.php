<?php
namespace App\Models;

use App\Core\Database;
use App\Services\PublicacionService;
use PDO;

class Lugar {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lista lugares públicos visibles con filtros opcionales de categoría, búsqueda textual y municipio.
     */
    public function listarPublicos(?int $idCategoria = null, ?string $termino = null, ?int $idMunicipio = null): array {
        $condicionVisibilidad = PublicacionService::getSqlCondicionVisibilidad('l', 'pub');
        
        $sql = "SELECT l.id_lugar, l.id_municipio, l.nombre, l.slug, l.descripcion, l.direccion, 
                       l.referencia_ubicacion, l.horario_atencion, l.tipo_lugar,
                       l.estado_operativo, l.aviso_temporal_motivo, l.aviso_temporal_reapertura,
                       l.whatsapp_contacto, l.telefono_contacto, l.facebook_url, l.instagram_url, l.tiktok_url, l.id_categoria,
                       l.latitud, l.longitud,
                       CONCAT(COALESCE(l.latitud, ''), ',', COALESCE(l.longitud, '')) AS coordenadas_gps,
                       c.nombre AS categoria, c.icono AS categoria_icono,
                       m.nombre AS municipio, m.provincia AS municipio_provincia, m.slug AS municipio_slug,
                       (SELECT f.nombre_archivo FROM fotografias f WHERE f.id_lugar = l.id_lugar AND f.es_principal = 1 LIMIT 1) AS imagen,
                       (SELECT p.descuento_texto FROM promociones p 
                        WHERE p.id_lugar = l.id_lugar AND p.activo = 1 
                          AND CURDATE() BETWEEN p.fecha_inicio AND p.fecha_fin 
                        ORDER BY p.id_promocion DESC LIMIT 1) AS promo_activa_texto
                FROM lugares l
                INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                INNER JOIN municipios m ON l.id_municipio = m.id_municipio
                INNER JOIN publicaciones pub ON l.id_lugar = pub.id_lugar
                WHERE {$condicionVisibilidad}";

        $params = [];

        if ($idCategoria !== null && $idCategoria > 0) {
            $sql .= " AND l.id_categoria = :id_categoria";
            $params[':id_categoria'] = $idCategoria;
        }

        if ($idMunicipio !== null && $idMunicipio > 0) {
            $sql .= " AND l.id_municipio = :id_municipio";
            $params[':id_municipio'] = $idMunicipio;
        }

        if (!empty($termino)) {
            $sql .= " AND (l.nombre LIKE :nombre OR l.descripcion LIKE :descripcion OR l.direccion LIKE :direccion OR m.nombre LIKE :municipio)";
            foreach ([':nombre', ':descripcion', ':direccion', ':municipio'] as $param) {
                $params[$param] = '%' . $termino . '%';
            }
        }

        $sql .= " ORDER BY l.id_lugar DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Busca la ficha pública visible de un lugar por su slug.
     */
    public function buscarPublicoPorSlug(string $slug): ?array {
        $condicionVisibilidad = PublicacionService::getSqlCondicionVisibilidad('l', 'pub');

        $sql = "SELECT l.*,
                       CONCAT(COALESCE(l.latitud, ''), ',', COALESCE(l.longitud, '')) AS coordenadas_gps,
                       c.nombre AS categoria, c.icono AS categoria_icono,
                       m.nombre AS municipio, m.provincia AS municipio_provincia, m.slug AS municipio_slug,
                       (SELECT f.nombre_archivo FROM fotografias f WHERE f.id_lugar = l.id_lugar AND f.es_principal = 1 LIMIT 1) AS imagen
                FROM lugares l
                INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                INNER JOIN municipios m ON l.id_municipio = m.id_municipio
                INNER JOIN publicaciones pub ON l.id_lugar = pub.id_lugar
                WHERE l.slug = :slug AND {$condicionVisibilidad}
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Lista todos los lugares para el panel administrativo con soporte de filtros combinados.
     */
    public function listarParaAdmin(
        ?string $buscar = null,
        ?string $tipoLugar = null,
        ?int $idCategoria = null,
        ?int $idMunicipio = null,
        ?int $habilitado = null,
        ?int $esVisible = null
    ): array {
        $visibilidad = PublicacionService::getSqlCondicionVisibilidad('l', 'pub');
        $sql = "SELECT l.id_lugar, l.id_municipio, l.id_categoria, l.nombre, l.slug, l.tipo_lugar, l.created_at,
                       l.latitud, l.longitud, l.telefono_contacto, l.whatsapp_contacto,
                       CONCAT(COALESCE(l.latitud, ''), ',', COALESCE(l.longitud, '')) AS coordenadas_gps,
                       c.nombre AS categoria, c.icono AS categoria_icono,
                       m.nombre AS municipio, m.provincia AS municipio_provincia,
                       pub.aprobado, pub.habilitado, pub.motivo_suspension,
                       pub.baja_voluntaria, pub.baja_fecha, pub.baja_motivo, pub.baja_comentario,
                       v.fecha_vencimiento,
                       (SELECT f.nombre_archivo FROM fotografias f WHERE f.id_lugar = l.id_lugar AND f.es_principal = 1 LIMIT 1) AS imagen,
                       CASE WHEN {$visibilidad} THEN 1 ELSE 0 END AS es_visible
                FROM lugares l
                INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                INNER JOIN municipios m ON l.id_municipio = m.id_municipio
                LEFT JOIN publicaciones pub ON l.id_lugar = pub.id_lugar
                LEFT JOIN (
                    SELECT vg.id_lugar, MAX(vg.fecha_vencimiento) AS fecha_vencimiento
                    FROM vigencias vg INNER JOIN pagos pg ON pg.id_pago = vg.id_pago
                    WHERE pg.estado = 'CONFIRMADO'
                    GROUP BY vg.id_lugar
                ) v ON l.id_lugar = v.id_lugar
                ";

        $where = [];
        $params = [];

        if ($buscar !== null && trim($buscar) !== '') {
            $termino = trim($buscar);
            $terminoId = ltrim($termino, '#');
            if (is_numeric($terminoId)) {
                $where[] = "(l.id_lugar = :id_buscar OR l.nombre LIKE :buscar_nom1 OR l.telefono_contacto LIKE :buscar_tel1 OR l.whatsapp_contacto LIKE :buscar_wa1)";
                $params[':id_buscar'] = (int)$terminoId;
                $val = '%' . $termino . '%';
                $params[':buscar_nom1'] = $val;
                $params[':buscar_tel1'] = $val;
                $params[':buscar_wa1'] = $val;
            } else {
                $where[] = "(l.nombre LIKE :buscar_nom OR l.slug LIKE :buscar_slug OR l.direccion LIKE :buscar_dir OR l.telefono_contacto LIKE :buscar_tel OR l.whatsapp_contacto LIKE :buscar_wa OR c.nombre LIKE :buscar_cat OR m.nombre LIKE :buscar_mun)";
                $val = '%' . $termino . '%';
                $params[':buscar_nom'] = $val;
                $params[':buscar_slug'] = $val;
                $params[':buscar_dir'] = $val;
                $params[':buscar_tel'] = $val;
                $params[':buscar_wa'] = $val;
                $params[':buscar_cat'] = $val;
                $params[':buscar_mun'] = $val;
            }
        }

        if (!empty($tipoLugar) && in_array($tipoLugar, ['PUBLICO', 'COMERCIAL'], true)) {
            $where[] = "l.tipo_lugar = :tipo_lugar";
            $params[':tipo_lugar'] = $tipoLugar;
        }

        if (!empty($idCategoria) && $idCategoria > 0) {
            $where[] = "l.id_categoria = :id_categoria";
            $params[':id_categoria'] = $idCategoria;
        }

        if (!empty($idMunicipio) && $idMunicipio > 0) {
            $where[] = "l.id_municipio = :id_municipio";
            $params[':id_municipio'] = $idMunicipio;
        }

        if ($habilitado !== null && in_array($habilitado, [0, 1], true)) {
            $where[] = "pub.habilitado = :habilitado";
            $params[':habilitado'] = $habilitado;
        }

        if ($esVisible !== null && in_array($esVisible, [0, 1], true)) {
            if ($esVisible === 1) {
                $where[] = $visibilidad;
            } else {
                $where[] = "NOT ({$visibilidad})";
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY l.id_lugar DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Retorna conteos analíticos globales para las tarjetas KPI administrativas.
     */
    public function obtenerEstadisticasAdmin(): array {
        $visibilidad = PublicacionService::getSqlCondicionVisibilidad('l', 'pub');
        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN l.tipo_lugar = 'COMERCIAL' THEN 1 ELSE 0 END) AS comerciales,
                    SUM(CASE WHEN l.tipo_lugar = 'PUBLICO' THEN 1 ELSE 0 END) AS publicos,
                    SUM(CASE WHEN {$visibilidad} THEN 1 ELSE 0 END) AS visibles,
                    SUM(CASE WHEN pub.habilitado = 0 THEN 1 ELSE 0 END) AS deshabilitados
                FROM lugares l
                LEFT JOIN publicaciones pub ON l.id_lugar = pub.id_lugar";
        $stmt = $this->db->query($sql);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        return [
            'total'          => (int)($res['total'] ?? 0),
            'comerciales'    => (int)($res['comerciales'] ?? 0),
            'publicos'       => (int)($res['publicos'] ?? 0),
            'visibles'       => (int)($res['visibles'] ?? 0),
            'deshabilitados' => (int)($res['deshabilitados'] ?? 0),
        ];
    }

    /**
     * Busca un lugar por su clave primaria.
     */
    public function buscarPorId(int $id): ?array {
        $sql = "SELECT l.*,
                       CONCAT(COALESCE(l.latitud, ''), ',', COALESCE(l.longitud, '')) AS coordenadas_gps,
                       c.nombre AS categoria,
                       m.nombre AS municipio, m.provincia AS municipio_provincia,
                       pub.aprobado, pub.habilitado, pub.motivo_suspension,
                       (SELECT f.nombre_archivo FROM fotografias f WHERE f.id_lugar = l.id_lugar AND f.es_principal = 1 LIMIT 1) AS imagen
                FROM lugares l
                LEFT JOIN categorias c ON l.id_categoria = c.id_categoria
                LEFT JOIN municipios m ON l.id_municipio = m.id_municipio
                LEFT JOIN publicaciones pub ON l.id_lugar = pub.id_lugar
                WHERE l.id_lugar = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Crea un nuevo registro de lugar con soporte para id_municipio, latitud y longitud.
     */
    public function crear(array $datos): int {
        $slugBase = $this->generarSlug($datos['nombre']);
        $slugFinal = $this->asegurarSlugUnico($slugBase);

        $coords = $this->extraerCoordenadas($datos);

        $sql = "INSERT INTO lugares (
                    id_municipio, id_categoria, nombre, slug, descripcion, direccion, 
                    referencia_ubicacion, latitud, longitud, telefono_contacto, 
                    whatsapp_contacto, facebook_url, instagram_url, tiktok_url,
                    email_contacto, horario_atencion, tipo_lugar
                ) VALUES (
                    :id_municipio, :id_categoria, :nombre, :slug, :descripcion, :direccion,
                    :referencia, :latitud, :longitud, :telefono,
                    :whatsapp, :facebook_url, :instagram_url, :tiktok_url,
                    :email, :horario, :tipo_lugar
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_municipio'   => (int)($datos['id_municipio'] ?? 1),
            ':id_categoria'   => (int)$datos['id_categoria'],
            ':nombre'         => $datos['nombre'],
            ':slug'           => $slugFinal,
            ':descripcion'    => $datos['descripcion'],
            ':direccion'      => $datos['direccion'],
            ':referencia'     => $datos['referencia_ubicacion'] ?? null,
            ':latitud'        => $coords['latitud'],
            ':longitud'       => $coords['longitud'],
            ':telefono'       => $datos['telefono_contacto'] ?? null,
            ':whatsapp'       => $datos['whatsapp_contacto'] ?? null,
            ':facebook_url'   => $datos['facebook_url'] ?? null,
            ':instagram_url'  => $datos['instagram_url'] ?? null,
            ':tiktok_url'     => $datos['tiktok_url'] ?? null,
            ':email'          => $datos['email_contacto'] ?? null,
            ':horario'        => $datos['horario_atencion'] ?? null,
            ':tipo_lugar'     => $datos['tipo_lugar']
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualiza un registro existente de lugar.
     */
    public function actualizar(int $id, array $datos): bool {
        $coords = $this->extraerCoordenadas($datos);

        $sql = "UPDATE lugares SET
                    id_municipio = :id_municipio,
                    id_categoria = :id_categoria,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    direccion = :direccion,
                    referencia_ubicacion = :referencia,
                    latitud = :latitud,
                    longitud = :longitud,
                    telefono_contacto = :telefono,
                    whatsapp_contacto = :whatsapp,
                    facebook_url = :facebook_url,
                    instagram_url = :instagram_url,
                    tiktok_url = :tiktok_url,
                    email_contacto = :email,
                    horario_atencion = :horario,
                    tipo_lugar = :tipo_lugar
                WHERE id_lugar = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_municipio'   => (int)($datos['id_municipio'] ?? 1),
            ':id_categoria'   => (int)$datos['id_categoria'],
            ':nombre'         => $datos['nombre'],
            ':descripcion'    => $datos['descripcion'],
            ':direccion'      => $datos['direccion'],
            ':referencia'     => $datos['referencia_ubicacion'] ?? null,
            ':latitud'        => $coords['latitud'],
            ':longitud'       => $coords['longitud'],
            ':telefono'       => $datos['telefono_contacto'] ?? null,
            ':whatsapp'       => $datos['whatsapp_contacto'] ?? null,
            ':facebook_url'   => $datos['facebook_url'] ?? null,
            ':instagram_url'  => $datos['instagram_url'] ?? null,
            ':tiktok_url'     => $datos['tiktok_url'] ?? null,
            ':email'          => $datos['email_contacto'] ?? null,
            ':horario'        => $datos['horario_atencion'] ?? null,
            ':tipo_lugar'     => $datos['tipo_lugar'],
            ':id'             => $id
        ]);
    }

    /**
     * Permite al comerciante actualizar su propio perfil desde el panel de autoservicio.
     */
    public function actualizarPerfilNegocio(int $id, array $datos): bool {
        $sql = "UPDATE lugares SET
                    descripcion = :descripcion,
                    direccion = :direccion,
                    referencia_ubicacion = :referencia,
                    telefono_contacto = :telefono,
                    whatsapp_contacto = :whatsapp,
                    email_contacto = :email,
                    horario_atencion = :horario,
                    enlace_menu = :enlace_menu,
                    facebook_url = :facebook_url,
                    instagram_url = :instagram_url,
                    tiktok_url = :tiktok_url";

        $params = [
            ':descripcion'   => $datos['descripcion'],
            ':direccion'     => $datos['direccion'],
            ':referencia'    => $datos['referencia_ubicacion'] ?? null,
            ':telefono'      => $datos['telefono_contacto'] ?? null,
            ':whatsapp'      => $datos['whatsapp_contacto'] ?? null,
            ':email'         => $datos['email_contacto'] ?? null,
            ':horario'       => $datos['horario_atencion'] ?? null,
            ':enlace_menu'   => $datos['enlace_menu'] ?? null,
            ':facebook_url'  => $datos['facebook_url'] ?? null,
            ':instagram_url' => $datos['instagram_url'] ?? null,
            ':tiktok_url'    => $datos['tiktok_url'] ?? null,
            ':id'            => $id
        ];

        if (isset($datos['id_municipio']) && (int)$datos['id_municipio'] > 0) {
            $sql .= ", id_municipio = :id_municipio";
            $params[':id_municipio'] = (int)$datos['id_municipio'];
        }

        $sql .= " WHERE id_lugar = :id AND tipo_lugar = 'COMERCIAL'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Extrae y normaliza latitud y longitud tanto si vienen como campos separados o como string coordenadas_gps.
     */
    private function extraerCoordenadas(array $datos): array {
        if (isset($datos['latitud']) && isset($datos['longitud']) && is_numeric($datos['latitud']) && is_numeric($datos['longitud'])) {
            return [
                'latitud'  => (float)$datos['latitud'],
                'longitud' => (float)$datos['longitud']
            ];
        }

        if (!empty($datos['coordenadas_gps']) && is_string($datos['coordenadas_gps'])) {
            $parts = explode(',', $datos['coordenadas_gps']);
            if (count($parts) === 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
                return [
                    'latitud'  => (float)trim($parts[0]),
                    'longitud' => (float)trim($parts[1])
                ];
            }
        }

        return ['latitud' => null, 'longitud' => null];
    }

    private function generarSlug(string $texto): string {
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        $texto = preg_replace('~[^\pL\d]+~u', '-', $texto);
        $texto = trim($texto, '-');
        $texto = strtolower($texto);
        return empty($texto) ? 'lugar-' . time() : $texto;
    }

    private function asegurarSlugUnico(string $slug, ?int $ignorarId = null): string {
        $slugFinal = $slug;
        $i = 1;
        while (true) {
            $sql = "SELECT id_lugar FROM lugares WHERE slug = :slug";
            if ($ignorarId) {
                $sql .= " AND id_lugar != :id";
            }
            $stmt = $this->db->prepare($sql);
            $params = [':slug' => $slugFinal];
            if ($ignorarId) {
                $params[':id'] = $ignorarId;
            }
            $stmt->execute($params);

            if (!$stmt->fetch()) {
                break;
            }
            $slugFinal = $slug . '-' . $i++;
        }
        return $slugFinal;
    }

    /**
     * Actualiza el estado operativo temporal de un lugar/comercio.
     */
    public function actualizarEstadoOperativo(int $idLugar, string $estado, ?string $motivo = null, ?string $reapertura = null): bool {
        $estadoValido = in_array($estado, ['ABIERTO_HABITUAL', 'CERRADO_TEMPORAL'], true) ? $estado : 'ABIERTO_HABITUAL';
        $motivoValido = ($estadoValido === 'CERRADO_TEMPORAL') ? (!empty($motivo) ? mb_substr(trim($motivo), 0, 255, 'UTF-8') : null) : null;
        $reaperturaValida = ($estadoValido === 'CERRADO_TEMPORAL' && !empty($reapertura)) ? $reapertura : null;

        $stmt = $this->db->prepare("UPDATE lugares 
                                    SET estado_operativo = :estado, 
                                        aviso_temporal_motivo = :motivo, 
                                        aviso_temporal_reapertura = :reapertura 
                                    WHERE id_lugar = :id");
        return $stmt->execute([
            ':estado'     => $estadoValido,
            ':motivo'     => $motivoValido,
            ':reapertura' => $reaperturaValida,
            ':id'         => $idLugar
        ]);
    }
}

