<?php
namespace App\Services;

use DateTime;
use DateTimeZone;

/**
 * Helper para interpretar horarios de atención en Trinidad y determinar
 * si un establecimiento se encuentra actualmente abierto o cerrado.
 */
class HorarioHelper {
    public static function evaluarEstado(?string $horario): ?array {
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
                    'badge'   => 'bg-success-subtle text-success border border-success-subtle'
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
}
