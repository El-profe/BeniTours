<?php
use App\Controllers\Publico\CatalogoController;
use App\Controllers\Publico\SolicitudController;

/**
 * Rutas JSON / Fetch
 */
$router->get('/api/lugares/buscar', [CatalogoController::class, 'apiBuscar']);
$router->post('/api/solicitudes/enviar', [SolicitudController::class, 'apiEnviar']);
$router->post('/solicitudes/enviar', [SolicitudController::class, 'apiEnviar']);
