<?php
namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Servicio de Rutas Turísticas Inteligentes - BeniTours
 * Combina algoritmos de popularidad real (Me Gusta / Favoritos)
 * con optimización geográfica (Nearest Neighbor Heuristic)
 * para trazar recorridos turísticos continuos, lógicos y atractivos.
 */
class RutaTuristicaService {

    /**
     * Coordenadas por defecto del Casco Cívico / Plaza Principal de Trinidad
     */
    private const LAT_DEFECTO_TRINIDAD = -14.8348;
    private const LNG_DEFECTO_TRINIDAD = -64.9042;

    /**
     * Radio medio de la Tierra en kilómetros (Fórmula de Haversine)
     */
    private const RADIO_TIERRA_KM = 6371;

    /**
     * Calcula la distancia ortodrómica en kilómetros entre dos coordenadas GPS
     */
    public static function calcularDistanciaKm(float $lat1, float $lng1, float $lat2, float $lng2): float {
        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLng = $lng2Rad - $lng1Rad;

        $a = sin($deltaLat / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;
        $c = 2 * asin(min(1.0, sqrt($a)));

        return self::RADIO_TIERRA_KM * $c;
    }

    /**
     * Obtiene los lugares con coordenadas válidas y su conteo de 'Me Gusta' (FAVORITO)
     *
     * @param int|null $idMunicipio Filtro opcional por municipio (ej. Trinidad)
     * @param array $categoriasNombres Filtro opcional por nombres de categoría
     * @param int $limiteCandidatos Cuántos lugares con más votos evaluar
     * @return array
     */
    public static function obtenerCandidatosPorPopularidad(?int $idMunicipio = null, array $categoriasNombres = [], int $limiteCandidatos = 20): array {
        $db = Database::getConnection();

        $where = ["l.latitud IS NOT NULL", "l.longitud IS NOT NULL", "l.latitud != 0", "l.longitud != 0"];
        $params = [];

        if ($idMunicipio !== null && $idMunicipio > 0) {
            $where[] = "l.id_municipio = :id_municipio";
            $params[':id_municipio'] = $idMunicipio;
        }

        if (!empty($categoriasNombres)) {
            $inPlaceholders = [];
            foreach ($categoriasNombres as $idx => $catNombre) {
                $ph = ":cat_{$idx}";
                $inPlaceholders[] = $ph;
                $params[$ph] = $catNombre;
            }
            $where[] = "c.nombre IN (" . implode(', ', $inPlaceholders) . ")";
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT 
                l.id_lugar,
                l.nombre,
                l.slug,
                l.tipo_lugar,
                l.direccion,
                l.referencia_ubicacion,
                l.latitud,
                l.longitud,
                l.telefono_contacto,
                l.whatsapp_contacto,
                l.horario_atencion,
                c.id_categoria,
                c.nombre AS categoria_nombre,
                c.icono AS categoria_icono,
                m.nombre AS municipio_nombre,
                (SELECT f.nombre_archivo 
                 FROM fotografias f 
                 WHERE f.id_lugar = l.id_lugar 
                 ORDER BY f.es_principal DESC, f.orden ASC, f.id_fotografia ASC 
                 LIMIT 1) AS imagen,
                COUNT(DISTINCT CASE WHEN i.tipo_evento = 'FAVORITO' THEN i.id_interaccion END) AS total_favoritos,
                COUNT(DISTINCT i.id_interaccion) AS total_interacciones
            FROM lugares l
            JOIN categorias c ON l.id_categoria = c.id_categoria
            LEFT JOIN municipios m ON l.id_municipio = m.id_municipio
            LEFT JOIN interacciones_lugar i ON l.id_lugar = i.id_lugar
            WHERE {$whereSql}
            GROUP BY l.id_lugar
            ORDER BY total_favoritos DESC, total_interacciones DESC, l.id_lugar ASC
            LIMIT {$limiteCandidatos}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Algoritmo Heurístico del Vecino Más Próximo (Nearest Neighbor)
     * Ordena las paradas para que el recorrido sea continuo y sin regresiones.
     *
     * @param array $candidatos Lista de lugares a ordenar
     * @param int $maxParadas Cantidad máxima de paradas (3 a 5)
     * @param float|null $inicioLat Coordenada inicial opcional (ej. GPS turista)
     * @param float|null $inicioLng Coordenada inicial opcional
     * @return array Array ordenado con paradas, distancia total en km y tiempo estimado
     */
    public static function optimizarCircuito(array $candidatos, int $maxParadas = 4, ?float $inicioLat = null, ?float $inicioLng = null): array {
        if (empty($candidatos)) {
            return [
                'paradas'         => [],
                'distancia_km'    => 0,
                'tiempo_estimado' => '0 min',
                'puntos_coords'   => []
            ];
        }

        // Si tenemos más candidatos de los requeridos, priorizamos el pool de mejores
        $pool = array_slice($candidatos, 0, min(count($candidatos), max($maxParadas * 2, $maxParadas)));

        $paradasOrdenadas = [];
        $visitados = [];

        // 1. Determinar el punto de partida (Parada 1)
        if ($inicioLat !== null && $inicioLng !== null) {
            // El candidato más cercano a la posición del usuario
            $mejorIndice = 0;
            $distMin = PHP_FLOAT_MAX;
            foreach ($pool as $idx => $c) {
                $d = self::calcularDistanciaKm($inicioLat, $inicioLng, (float)$c['latitud'], (float)$c['longitud']);
                if ($d < $distMin) {
                    $distMin = $d;
                    $mejorIndice = $idx;
                }
            }
            $paradaActual = $pool[$mejorIndice];
            $visitados[$mejorIndice] = true;
            $paradasOrdenadas[] = $paradaActual;
        } else {
            // El lugar con más Me Gusta / Más emblemático (el primer elemento del pool)
            $paradaActual = $pool[0];
            $visitados[0] = true;
            $paradasOrdenadas[] = $paradaActual;
        }

        // 2. Encadenar cada siguiente parada buscando el vecino no visitado más cercano
        while (count($paradasOrdenadas) < $maxParadas && count($paradasOrdenadas) < count($pool)) {
            $mejorVecino = null;
            $mejorVecinoIdx = -1;
            $distMinima = PHP_FLOAT_MAX;

            $latActual = (float)$paradaActual['latitud'];
            $lngActual = (float)$paradaActual['longitud'];

            foreach ($pool as $idx => $candidato) {
                if (isset($visitados[$idx])) {
                    continue;
                }

                $d = self::calcularDistanciaKm($latActual, $lngActual, (float)$candidato['latitud'], (float)$candidato['longitud']);
                if ($d < $distMinima) {
                    $distMinima = $d;
                    $mejorVecino = $candidato;
                    $mejorVecinoIdx = $idx;
                }
            }

            if ($mejorVecino !== null) {
                $visitados[$mejorVecinoIdx] = true;
                $paradasOrdenadas[] = $mejorVecino;
                $paradaActual = $mejorVecino;
            } else {
                break;
            }
        }

        // 3. Calcular distancias acumuladas y waypoints
        $distanciaTotalKm = 0.0;
        $puntosCoords = [];
        $googleWaypoints = [];

        foreach ($paradasOrdenadas as $orden => &$parada) {
            $parada['numero_parada'] = $orden + 1;
            $parada['es_inicio'] = ($orden === 0);
            $parada['es_fin'] = ($orden === count($paradasOrdenadas) - 1);

            $lat = (float)$parada['latitud'];
            $lng = (float)$parada['longitud'];
            $puntosCoords[] = [$lat, $lng];
            $googleWaypoints[] = "{$lat},{$lng}";

            if ($orden > 0) {
                $latPrev = (float)$paradasOrdenadas[$orden - 1]['latitud'];
                $lngPrev = (float)$paradasOrdenadas[$orden - 1]['longitud'];
                $distanciaTotalKm += self::calcularDistanciaKm($latPrev, $lngPrev, $lat, $lng);
            }
        }
        unset($parada);

        // Estimar tiempo: Velocidad media urbana en mototaxi ~22 km/h + 35 min de estadía por punto
        $minutosTraslado = ($distanciaTotalKm / 22) * 60;
        $minutosEstadia = count($paradasOrdenadas) * 35;
        $totalMinutos = round($minutosTraslado + $minutosEstadia);

        if ($totalMinutos >= 60) {
            $horas = floor($totalMinutos / 60);
            $minsRestantes = $totalMinutos % 60;
            $tiempoFormateado = $minsRestantes > 0 ? "{$horas} h {$minsRestantes} min" : "{$horas} h";
        } else {
            $tiempoFormateado = "{$totalMinutos} min";
        }

        // Generar enlace universal de Google Maps con navegación multipunto
        $urlGoogleMaps = '';
        if (count($googleWaypoints) >= 2) {
            $origen = array_shift($googleWaypoints);
            $destino = array_pop($googleWaypoints);
            $waypointsParam = !empty($googleWaypoints) ? '&waypoints=' . urlencode(implode('|', $googleWaypoints)) : '';
            $urlGoogleMaps = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($origen) . "&destination=" . urlencode($destino) . $waypointsParam . "&travelmode=driving";
        } elseif (count($googleWaypoints) === 1) {
            $urlGoogleMaps = "https://www.google.com/maps/search/?api=1&query=" . urlencode($googleWaypoints[0]);
        }

        return [
            'paradas'         => $paradasOrdenadas,
            'distancia_km'    => round($distanciaTotalKm, 1),
            'tiempo_estimado' => $tiempoFormateado,
            'puntos_coords'   => $puntosCoords,
            'google_maps_url' => $urlGoogleMaps
        ];
    }

    /**
     * Genera el catálogo completo de rutas recomendadas para la portada
     */
    public static function obtenerRutasRecomendadas(?int $idMunicipio = null, ?float $userLat = null, ?float $userLng = null): array {
        // 1. Ruta: Lo Más Votado / Favoritos de Trinidad
        $candidatosTop = self::obtenerCandidatosPorPopularidad($idMunicipio, [], 15);
        $circuitoTop = self::optimizarCircuito($candidatosTop, 4, $userLat, $userLng);

        // 2. Ruta: Sabores & Gastronomía
        $candidatosGastro = self::obtenerCandidatosPorPopularidad($idMunicipio, ['Gastronomía y Sabores', 'Bares y Vida Nocturna'], 10);
        $circuitoGastro = self::optimizarCircuito($candidatosGastro, 3, $userLat, $userLng);

        // 3. Ruta: Naturaleza, Museos & Atardecer
        $candidatosCultura = self::obtenerCandidatosPorPopularidad($idMunicipio, ['Atractivos y Naturaleza', 'Museos y Patrimonio Cultural'], 10);
        $circuitoCultura = self::optimizarCircuito($candidatosCultura, 3, $userLat, $userLng);

        return [
            [
                'id'              => 'ruta-favoritos',
                'titulo'          => 'Ruta Lo Más Votado de Trinidad',
                'subtitulo'       => 'Los destinos preferidos por la comunidad, organizados de forma continua',
                'icono'           => 'bi-star-fill',
                'color'           => '#0d9488', // Teal BeniTours
                'badge'           => '🔥 Más Popular',
                'distancia_km'    => $circuitoTop['distancia_km'],
                'tiempo_estimado' => $circuitoTop['tiempo_estimado'],
                'google_maps_url' => $circuitoTop['google_maps_url'],
                'puntos_coords'   => $circuitoTop['puntos_coords'],
                'paradas'         => $circuitoTop['paradas']
            ],
            [
                'id'              => 'ruta-sabores',
                'titulo'          => 'Ruta de Sabores & Gastronomía',
                'subtitulo'       => 'Platos típicos benianos, delicias y refrescantes paradas',
                'icono'           => 'bi-cup-hot-fill',
                'color'           => '#ea580c', // Naranja cálido
                'badge'           => '🍽️ Imperdible',
                'distancia_km'    => $circuitoGastro['distancia_km'],
                'tiempo_estimado' => $circuitoGastro['tiempo_estimado'],
                'google_maps_url' => $circuitoGastro['google_maps_url'],
                'puntos_coords'   => $circuitoGastro['puntos_coords'],
                'paradas'         => $circuitoGastro['paradas']
            ],
            [
                'id'              => 'ruta-cultura-naturaleza',
                'titulo'          => 'Ruta Naturaleza & Tradición',
                'subtitulo'       => 'Historia moxeña, fauna amazónica y atardeceres de postal',
                'icono'           => 'bi-tree-fill',
                'color'           => '#15803d', // Verde selva
                'badge'           => '🌿 Ecoturismo',
                'distancia_km'    => $circuitoCultura['distancia_km'],
                'tiempo_estimado' => $circuitoCultura['tiempo_estimado'],
                'google_maps_url' => $circuitoCultura['google_maps_url'],
                'puntos_coords'   => $circuitoCultura['puntos_coords'],
                'paradas'         => $circuitoCultura['paradas']
            ]
        ];
    }
}
