<?php
namespace App\Services;

use DateTime;
use DateTimeZone;

/**
 * Helper para interpretar horarios de atención en Trinidad y determinar
 * si un establecimiento se encuentra actualmente abierto o cerrado.
 */
class HorarioHelper {
    public static function evaluarEstado(?string $horario, ?string $estadoOperativo = null, ?string $motivo = null, ?string $reapertura = null): ?array {
        // 0. Comprobación prioritaria de Cierre Temporal / Pausa Operativa
        if ($estadoOperativo === 'CERRADO_TEMPORAL') {
            $textoMotivo = !empty($motivo) ? trim($motivo) : 'Cerrado temporalmente';
            if (!empty($reapertura)) {
                $fechaFmt = date('d/m/Y', strtotime($reapertura));
                $textoBadge = "Cerrado hasta {$fechaFmt}";
            } else {
                $textoBadge = 'Cerrado temporalmente';
            }

            return [
                'abierto'          => false,
                'cerrado_temporal' => true,
                'texto'            => $textoBadge,
                'motivo'           => $textoMotivo,
                'reapertura'       => $reapertura,
                'badge'            => 'bg-danger-subtle text-danger border border-danger-subtle'
            ];
        }

        if (empty($horario)) {
            return null;
        }

        $normalizado = mb_strtolower(trim($horario), 'UTF-8');

        // 1. Casos 24 horas
        if (str_contains($normalizado, '24 hora') || str_contains($normalizado, '24 horas') || str_contains($normalizado, '24h')) {
            return [
                'abierto' => true,
                'texto'   => 'Abierto 24h',
                'badge'   => 'bg-success-subtle text-success border border-success-subtle'
            ];
        }

        // 2. Extraer rangos de hora en formato HH:MM a HH:MM o HH:MM - HH:MM
        preg_match_all('/(\d{1,2}:\d{2})\s*(?:a|-)\s*(\d{1,2}:\d{2})/i', $horario, $coincidencias, PREG_SET_ORDER);

        if (empty($coincidencias)) {
            return null;
        }

        try {
            $zonaBolivia = new DateTimeZone('America/La_Paz');
            $ahora = new DateTime('now', $zonaBolivia);
            $minutosAhora = ((int)$ahora->format('H') * 60) + (int)$ahora->format('i');

            $estaAbierto = false;
            foreach ($coincidencias as $rango) {
                [$hInicio, $mInicio] = array_map('intval', explode(':', $rango[1]));
                [$hFin, $mFin] = array_map('intval', explode(':', $rango[2]));

                $minutosInicio = ($hInicio * 60) + $mInicio;
                $minutosFin = ($hFin * 60) + $mFin;

                if ($minutosFin < $minutosInicio) {
                    // Turno que pasa la medianoche (ej. 19:00 a 02:00)
                    if ($minutosAhora >= $minutosInicio || $minutosAhora <= $minutosFin) {
                        $estaAbierto = true;
                        break;
                    }
                } else {
                    if ($minutosAhora >= $minutosInicio && $minutosAhora <= $minutosFin) {
                        $estaAbierto = true;
                        break;
                    }
                }
            }

            if ($estaAbierto) {
                return [
                    'abierto' => true,
                    'texto'   => 'Abierto ahora',
                    'badge'   => 'bg-teal-subtle text-teal border border-teal-subtle'
                ];
            } else {
                return [
                    'abierto' => false,
                    'texto'   => 'Cerrado ahora',
                    'badge'   => 'bg-secondary-subtle text-secondary border border-secondary-subtle'
                ];
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clasifica los turnos gastronómicos (desayuno, almuerzo, merienda, cena)
     * a partir del texto de horario o el tipo de establecimiento.
     *
     * @param string|null $horario
     * @param string|null $nombre
     * @return array Lista de turnos: ['desayuno', 'almuerzo', 'merienda', 'cena']
     */
    public static function clasificarTurnos(?string $horario, ?string $nombre = null): array {
        $turnos = [];
        $texto = mb_strtolower(trim(($horario ?? '') . ' ' . ($nombre ?? '')), 'UTF-8');

        if (empty($texto)) {
            return ['almuerzo', 'cena']; // Valor predeterminado para restaurantes sin horario
        }

        // Casos 24 horas o siempre abierto
        if (str_contains($texto, '24 hora') || str_contains($texto, '24h') || str_contains($texto, 'continuo')) {
            return ['desayuno', 'almuerzo', 'merienda', 'cena'];
        }

        // 1. Detección por palabras clave directas
        if (str_contains($texto, 'desayun') || str_contains($texto, 'salteñ') || str_contains($texto, 'mañan')) {
            $turnos[] = 'desayuno';
        }
        if (str_contains($texto, 'almuerz')) {
            $turnos[] = 'almuerzo';
        }
        if (str_contains($texto, 'meriend') || str_contains($texto, 'caf') || str_contains($texto, 'masita') || str_contains($texto, 'pasteler')) {
            $turnos[] = 'merienda';
        }
        if (str_contains($texto, 'cena') || str_contains($texto, 'noche') || str_contains($texto, 'pub') || str_contains($texto, 'bar')) {
            $turnos[] = 'cena';
        }

        // 2. Detección por rangos de horas (Inicio y Fin)
        preg_match_all('/(\d{1,2}):(\d{2})\s*(?:a|-)\s*(\d{1,2}):(\d{2})/i', $horario ?? '', $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            foreach ($matches as $m) {
                $hIni = (int)$m[1];
                $hFin = (int)$m[3];

                // Turno cruza el día o noche (ej. 18:00 a 02:00)
                if ($hFin < $hIni) {
                    $turnos[] = 'cena';
                }
                // Desayuno: Abre entre las 06:00 y las 10:59
                if ($hIni >= 6 && $hIni <= 10) {
                    $turnos[] = 'desayuno';
                }
                // Almuerzo: Horario cubre entre 11:30 y 14:30
                if (($hIni <= 13 && $hFin >= 12) || ($hIni >= 11 && $hIni <= 14)) {
                    $turnos[] = 'almuerzo';
                }
                // Merienda / Café: Horario cubre entre 15:00 y 18:30
                if (($hIni <= 17 && $hFin >= 16) || ($hIni >= 15 && $hIni <= 17)) {
                    $turnos[] = 'merienda';
                }
                // Cena: Horario cubre a partir de las 18:30 o noche
                if ($hFin >= 20 || $hIni >= 18 || $hFin < $hIni) {
                    $turnos[] = 'cena';
                }
            }
        }

        // Si es una churrasquería o restaurante tradicional típico
        if (str_contains($texto, 'churrasquer') || str_contains($texto, 'restauran') || str_contains($texto, 'pizz')) {
            $turnos[] = 'almuerzo';
            $turnos[] = 'cena';
        }

        // Si es cafetería
        if (str_contains($texto, 'cafeter') || str_contains($texto, 'café')) {
            $turnos[] = 'desayuno';
            $turnos[] = 'merienda';
        }

        $turnos = array_unique($turnos);
        if (empty($turnos)) {
            $turnos = ['almuerzo', 'cena'];
        }

        return array_values($turnos);
    }
}
