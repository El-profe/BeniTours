     MAPA REGIONAL INTERACTIVO DEL BENI (UN DEPARTAMENTO, CUATRO MARAVILLAS)
     ========================================================================== -->
<section class="seccion-mapa-regional-beni py-5 position-relative text-white overflow-hidden" id="regiones-beni">
    <div class="container py-4 position-relative z-index-2">
        <div class="row align-items-center gy-5">
            <!-- Columna Izquierda: Información Editorial y Llamado a la Acción -->
            <div class="col-lg-5 text-center text-lg-start">
                <span class="badge hero-badge-glass fw-bold px-3 py-2 rounded-pill text-uppercase mb-3 shadow-sm">
                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> Mapa Departamental
                </span>
                <h2 class="display-5 fw-extrabold mb-3 text-white titulo-mapa-regional">
                    Descubre el Beni, <br class="d-none d-lg-block">
                    <span class="text-teal-gradient">Tierra de Encanto</span>
                </h2>
                <p class="lead text-white-50 mb-4 fs-6 max-w-480">
                    Explora la riqueza natural, cultural e histórica de nuestro departamento a través de sus provincias. Conoce sus atractivos turísticos, tradiciones, ríos y reservas naturales en un solo lugar.
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start mb-4">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold btn-sm d-inline-flex align-items-center gap-2 btn-explorar-mapa" onclick="document.getElementById('galeria').scrollIntoView({ behavior: 'smooth' });">
                        <span>Ver Galería Turística</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                <!-- Mini Leyenda Rápida -->
                <div class="p-3 rounded-4 bg-dark bg-opacity-40 border border-white border-opacity-10 max-w-440">
                    <div class="extra-small text-white-50 text-uppercase fw-bold mb-2">
                        <i class="bi bi-hand-index-thumb me-1 text-warning"></i> Pasa el mouse o presiona sobre cada provincia para explorar:
                    </div>
                    <div class="d-flex flex-wrap gap-2 extra-small text-white-50">
                        <span><i class="bi bi-check2-circle text-success me-1"></i> 8 Provincias</span>
                        <span class="mx-1">•</span>
                        <span><i class="bi bi-camera-fill text-warning me-1"></i> Fotografías reales</span>
                        <span class="mx-1">•</span>
                        <span><i class="bi bi-compass-fill text-info me-1"></i> Destinos turísticos</span>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Mapa SVG Interactivo con Revelación Fotográfica -->
            <div class="col-lg-7 text-center">
                <div class="mapa-beni-wrapper mx-auto position-relative">
                    <!-- Mapa Geográfico Oficial y Estilizado del Departamento del Beni -->
                    <svg viewBox="0 0 520 520" class="mapa-beni-svg w-100 h-auto" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                            <!-- Patrones fotográficos dinámicos administrables para cada una de las 8 provincias del Beni -->
                            <?php
                                $obtenerUrlFotoMapa = function($codigo, $fallbackImg) use ($fotosMapa, $baseUrl) {
                                    $foto = $fotosMapa[$codigo]['imagen'] ?? null;
                                    if (!empty($foto)) {
                                        if (str_starts_with($foto, 'hero-')) {
                                            return "{$baseUrl}/assets/img/{$foto}";
                                        }
                                        return "{$baseUrl}/imagen?f=" . urlencode($foto);
                                    }
                                    return $fallbackImg;
                                };

                                $imgVacaDiez = $obtenerUrlFotoMapa('vacadiez', "{$baseUrl}/imagen?f=lugar_083829094a198208_1792908099.jpg");
                                $imgBallivian = $obtenerUrlFotoMapa('ballivian', "{$baseUrl}/assets/img/hero-poster.webp");
                                $imgYacuma = $obtenerUrlFotoMapa('yacuma', "{$baseUrl}/imagen?f=lugar_93f0c8ef051a2dda_1790312766.png");
                                $imgMamore = $obtenerUrlFotoMapa('mamore', "{$baseUrl}/imagen?f=lugar_2cf4c818d0c03cd8_1790319478.jpg");
                                $imgItenez = $obtenerUrlFotoMapa('itenez', "{$baseUrl}/imagen?f=lugar_2cf4c818d0c03cd8_1790319478.jpg");
                                $imgCercado = $obtenerUrlFotoMapa('cercado', "{$baseUrl}/imagen?f=lugar_9998d4fdbb4f31c5_1791967750.jpg");
                                $imgMoxos = $obtenerUrlFotoMapa('moxos', "{$baseUrl}/imagen?f=lugar_8570b95699c5b12b_1790310877.png");
                                $imgMarban = $obtenerUrlFotoMapa('marban', "{$baseUrl}/imagen?f=lugar_8570b95699c5b12b_1790310877.png");
                            ?>
                            <!-- 1. Vaca Díez (Riberalta / Guayaramerín) -->
                            <pattern id="patronVacaDiez" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgVacaDiez) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 2. Gral. José Ballivián (Rurrenabaque / Pampas / Pilón Lajas) -->
                            <pattern id="patronBallivian" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgBallivian) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 3. Yacuma (Santa Rosa de Yacuma / Bufeos) -->
                            <pattern id="patronYacuma" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgYacuma) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 4. Mamoré (San Joaquín / San Ramón / Puerto Siles) -->
                            <pattern id="patronMamore" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgMamore) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 5. Iténez (Magdalena / Baures / Río Iténez / Versalles) -->
                            <pattern id="patronItenez" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgItenez) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 6. Cercado (Trinidad Capital / Laguna Suárez / Loma Suárez) -->
                            <pattern id="patronCercado" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgCercado) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 7. Moxos (San Ignacio de Moxos / Laguna Isireri) -->
                            <pattern id="patronMoxos" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgMoxos) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 8. Marbán (Loreto / San Andrés / Santuario de Loreto) -->
                            <pattern id="patronMarban" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgMarban) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>

                            <!-- Sombra suave para el mapa completo -->
                            <filter id="sombraMapaBeni" x="-10%" y="-10%" width="120%" height="120%">
                                <feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#021417" flood-opacity="0.55" />
                            </filter>
                        </defs>

                        <!-- MAPA GEOGRÁFICO DEPARTAMENTO DEL BENI (8 PROVINCIAS ENCAJADAS) -->
                        <g filter="url(#sombraMapaBeni)">
                            
                            <!-- CAPA 1: CUERPO Y FORMAS DE LAS 8 PROVINCIAS -->
                            <g class="capa-cuerpos-provincias">
                                <!-- 1. Antonio Vaca Díez -->
                                <g class="region-beni-grupo" data-provincia="vacadiez" data-region="norte" tabindex="0" role="button" aria-label="Provincia Vaca Díez: Riberalta y Guayaramerín">
                                    <path class="region-path region-vacadiez" 
                                          d="M 189 12 L 195 12 L 197 14 L 198 29 L 202 34 L 202 43 L 208 52 L 208 61 L 200 76 L 200 80 L 204 86 L 204 99 L 210 101 L 213 116 L 220 116 L 222 118 L 225 138 L 219 140 L 161 140 L 155 142 L 131 143 L 111 143 L 102 141 L 84 141 L 75 143 L 75 139 L 70 132 L 72 129 L 77 128 L 79 120 L 84 120 L 89 117 L 92 103 L 95 98 L 102 93 L 108 94 L 111 91 L 122 91 L 122 89 L 126 88 L 127 81 L 132 77 L 134 71 L 138 69 L 138 66 L 142 62 L 144 56 L 143 48 L 146 45 L 154 45 L 154 47 L 157 47 L 157 40 L 166 39 L 170 36 L 170 33 L 165 30 L 166 24 L 179 23 L 189 12 Z" 
                                          fill="#c1bba4" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 2. Gral. José Ballivián -->
                                <g class="region-beni-grupo" data-provincia="ballivian" data-region="pampas" tabindex="0" role="button" aria-label="Provincia Gral. Ballivián: Rurrenabaque y San Borja">
                                    <path class="region-path region-ballivian" 
                                          d="M 84 141 L 125 143 L 122 151 L 125 168 L 118 179 L 113 183 L 104 206 L 104 284 L 106 285 L 106 302 L 104 303 L 102 314 L 102 345 L 104 347 L 104 367 L 102 368 L 102 428 L 87 442 L 78 444 L 72 432 L 68 430 L 66 423 L 61 418 L 59 407 L 50 395 L 47 387 L 33 381 L 31 376 L 33 362 L 29 356 L 21 351 L 21 348 L 26 341 L 24 336 L 20 333 L 20 329 L 26 325 L 25 320 L 23 319 L 23 313 L 25 311 L 23 292 L 26 289 L 29 289 L 27 280 L 32 276 L 32 272 L 38 266 L 36 251 L 43 245 L 45 238 L 52 234 L 56 225 L 61 223 L 66 211 L 70 207 L 71 193 L 69 188 L 72 182 L 70 181 L 69 174 L 74 170 L 73 152 L 76 148 L 75 143 L 84 141 Z" 
                                          fill="#f2e415" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 3. Yacuma -->
                                <g class="region-beni-grupo" data-provincia="yacuma" data-region="pampas" tabindex="0" role="button" aria-label="Provincia Yacuma: Santa Rosa del Yacuma">
                                    <path class="region-path region-yacuma" 
                                          d="M 202 139 L 219 140 L 221 145 L 220 151 L 214 154 L 216 163 L 214 174 L 219 184 L 223 187 L 222 196 L 226 200 L 226 212 L 224 213 L 225 220 L 222 229 L 213 230 L 214 238 L 210 246 L 210 257 L 206 265 L 205 279 L 216 292 L 218 299 L 223 305 L 223 311 L 215 314 L 210 313 L 197 316 L 186 347 L 178 355 L 170 359 L 171 366 L 169 372 L 171 373 L 171 376 L 167 380 L 166 389 L 158 396 L 158 399 L 154 404 L 142 404 L 141 406 L 134 407 L 119 420 L 107 427 L 102 427 L 102 368 L 104 367 L 102 314 L 104 303 L 106 302 L 106 285 L 104 284 L 104 206 L 113 183 L 118 179 L 125 168 L 122 151 L 125 143 L 131 142 L 202 139 Z" 
                                          fill="#ef66e6" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 4. Mamoré -->
                                <g class="region-beni-grupo" data-provincia="mamore" data-region="misiones" tabindex="0" role="button" aria-label="Provincia Mamoré: San Joaquín">
                                    <path class="region-path region-mamore" 
                                          d="M 224 138 L 232 139 L 241 144 L 242 149 L 247 149 L 252 155 L 264 158 L 266 162 L 268 162 L 269 171 L 280 179 L 277 186 L 285 209 L 283 211 L 283 219 L 286 223 L 286 230 L 280 239 L 279 248 L 276 251 L 275 312 L 267 312 L 239 303 L 221 303 L 216 292 L 205 279 L 206 265 L 210 257 L 210 246 L 214 238 L 213 230 L 221 230 L 225 220 L 224 213 L 226 212 L 226 200 L 222 196 L 223 187 L 219 184 L 214 174 L 214 169 L 216 168 L 214 154 L 221 149 L 219 139 L 224 138 Z" 
                                          fill="#f2efbc" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 5. Iténez -->
                                <g class="region-beni-grupo" data-provincia="itenez" data-region="misiones" tabindex="0" role="button" aria-label="Provincia Iténez: Magdalena, Baures y Versalles">
                                    <path class="region-path region-itenez" 
                                          d="M 323 173 L 339 177 L 344 184 L 348 185 L 354 191 L 363 193 L 368 191 L 376 192 L 382 203 L 389 207 L 393 207 L 403 221 L 413 220 L 419 225 L 427 226 L 430 229 L 440 228 L 442 230 L 451 230 L 455 234 L 455 239 L 461 248 L 469 250 L 475 257 L 475 262 L 480 262 L 482 264 L 483 262 L 490 262 L 493 259 L 497 259 L 499 266 L 492 271 L 481 273 L 474 278 L 470 278 L 459 285 L 446 288 L 422 299 L 387 310 L 386 312 L 366 317 L 362 321 L 358 321 L 356 324 L 349 326 L 339 327 L 333 323 L 275 312 L 276 251 L 279 248 L 280 239 L 286 230 L 286 223 L 283 219 L 283 211 L 285 209 L 277 183 L 279 180 L 285 180 L 286 178 L 292 178 L 294 181 L 310 180 L 323 173 Z" 
                                          fill="#e194bc" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 6. Cercado -->
                                <g class="region-beni-grupo" data-provincia="cercado" data-region="cercado" tabindex="0" role="button" aria-label="Provincia Cercado: Ciudad de Trinidad">
                                    <path class="region-path region-cercado" 
                                          d="M 224 302 L 239 303 L 267 312 L 277 312 L 280 314 L 295 315 L 306 319 L 333 323 L 339 326 L 340 330 L 319 352 L 317 352 L 314 359 L 302 361 L 294 366 L 282 368 L 278 371 L 258 374 L 238 383 L 231 383 L 229 382 L 229 374 L 225 362 L 230 352 L 233 335 L 231 333 L 231 326 L 227 322 L 227 313 L 222 310 L 222 303 L 224 302 Z" 
                                          fill="#57e261" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 7. Moxos -->
                                <g class="region-beni-grupo" data-provincia="moxos" data-region="misiones" tabindex="0" role="button" aria-label="Provincia Moxos: San Ignacio de Moxos">
                                    <path class="region-path region-moxos" 
                                          d="M 222 311 L 226 311 L 227 313 L 227 322 L 231 326 L 233 340 L 228 358 L 225 362 L 229 374 L 229 383 L 225 393 L 215 402 L 212 410 L 213 416 L 216 417 L 219 422 L 220 432 L 217 441 L 210 448 L 206 448 L 208 454 L 206 459 L 208 493 L 203 499 L 195 502 L 156 503 L 153 500 L 137 499 L 136 496 L 120 483 L 115 472 L 107 464 L 82 455 L 79 450 L 79 445 L 87 442 L 95 436 L 102 427 L 107 427 L 119 420 L 132 408 L 141 406 L 142 404 L 154 404 L 157 401 L 158 396 L 166 389 L 167 380 L 171 376 L 171 373 L 169 372 L 171 366 L 170 359 L 178 355 L 186 347 L 197 316 L 220 313 L 222 311 Z" 
                                          fill="#56e4dd" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 8. Marbán -->
                                <g class="region-beni-grupo" data-provincia="marban" data-region="cercado" tabindex="0" role="button" aria-label="Provincia Marbán: Loreto y San Andrés">
                                    <path class="region-path region-marban" 
                                          d="M 308 359 L 314 359 L 314 370 L 312 372 L 314 378 L 313 391 L 320 400 L 322 411 L 333 427 L 342 434 L 349 436 L 350 440 L 353 442 L 356 454 L 353 456 L 344 456 L 343 454 L 298 449 L 285 451 L 280 450 L 279 448 L 237 447 L 230 450 L 225 456 L 225 467 L 222 471 L 223 481 L 221 488 L 212 493 L 208 493 L 208 473 L 206 471 L 206 459 L 208 454 L 206 448 L 210 448 L 217 441 L 220 432 L 219 422 L 216 417 L 213 416 L 212 410 L 215 402 L 225 393 L 228 383 L 238 383 L 252 376 L 278 371 L 308 359 Z" 
                                          fill="#9eeea4" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>
                            </g>

                            <!-- CAPA 2 (OVERLAY SUPERIOR ABSOLUTO): PINES Y ETIQUETAS FLOTANTES -->
                            <!-- Renderizado siempre después de todas las provincias para que jamás queden cubiertas -->
                            <g class="capa-overlays-provincias pointer-events-none">
                                <!-- 1. Vaca Díez -->
                                <g class="provincia-overlay" data-overlay="vacadiez">
                                    <g class="region-pin-btn" transform="translate(163, 95)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(85, 120)">
                                        <rect x="0" y="0" width="155" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="77" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Vaca Díez (Riberalta) ➔</text>
                                    </g>
                                </g>

                                <!-- 2. Gral. Ballivián -->
                                <g class="provincia-overlay" data-overlay="ballivian">
                                    <g class="region-pin-btn" transform="translate(76, 295)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(10, 325)">
                                        <rect x="0" y="0" width="155" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="77" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Gral. Ballivián (Pampas) ➔</text>
                                    </g>
                                </g>

                                <!-- 3. Yacuma -->
                                <g class="provincia-overlay" data-overlay="yacuma">
                                    <g class="region-pin-btn" transform="translate(154, 255)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(85, 285)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Yacuma & Santa Rosa ➔</text>
                                    </g>
                                </g>

                                <!-- 4. Mamoré -->
                                <g class="provincia-overlay" data-overlay="mamore">
                                    <g class="region-pin-btn" transform="translate(246, 225)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(180, 255)">
                                        <rect x="0" y="0" width="135" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="67" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Provincia Mamoré ➔</text>
                                    </g>
                                </g>

                                <!-- 5. Iténez -->
                                <g class="provincia-overlay" data-overlay="itenez">
                                    <g class="region-pin-btn" transform="translate(365, 252)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(295, 280)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Iténez & Magdalena ➔</text>
                                    </g>
                                </g>

                                <!-- 6. Cercado -->
                                <g class="provincia-overlay" data-overlay="cercado">
                                    <g class="region-pin-btn" transform="translate(267, 342)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(200, 372)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Cercado (Trinidad) ➔</text>
                                    </g>
                                </g>

                                <!-- 7. Moxos -->
                                <g class="provincia-overlay" data-overlay="moxos">
                                    <g class="region-pin-btn" transform="translate(165, 435)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(95, 465)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Moxos (San Ignacio) ➔</text>
                                    </g>
                                </g>

                                <!-- 8. Marbán -->
                                <g class="provincia-overlay" data-overlay="marban">
                                    <g class="region-pin-btn" transform="translate(275, 420)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(205, 450)">
                                        <rect x="0" y="0" width="140" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="70" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Marbán (Loreto) ➔</text>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tigre Amazónico / Jaguar Beniano (Panthera onca) Vigilante en la Esquina -->
    <div class="tigre-mapa-box d-none d-md-block" title="Tigre Amazónico / Jaguar (Panthera onca) - El rey de la fauna y las reservas del Beni">
        <svg viewBox="0 0 180 140" width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <!-- Pelaje dorado/ámbar del Jaguar -->
                <linearGradient id="gradPielTigre" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#f59e0b" />
                    <stop offset="60%" stop-color="#d97706" />
                    <stop offset="100%" stop-color="#b45309" />
                </linearGradient>
                <!-- Hocico y mentón blanco crema -->
                <linearGradient id="gradHocicoTigre" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#ffffff" />
                    <stop offset="100%" stop-color="#fef3c7" />
                </linearGradient>
                <!-- Ojos ámbar / esmeralda brillante -->
                <linearGradient id="gradOjoTigre" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fef08a" />
                    <stop offset="60%" stop-color="#a3e635" />
                    <stop offset="100%" stop-color="#65a30d" />
                </linearGradient>
            </defs>

            <!-- Hojas de selva de fondo en la esquina -->
            <g opacity="0.75">
                <path d="M 120 140 C 135 105 160 85 180 80 C 180 115 160 135 120 140 Z" fill="#047857" />
                <path d="M 100 140 C 120 115 140 100 165 98 C 160 125 135 138 100 140 Z" fill="#10b981" opacity="0.6" />
            </g>

            <!-- Cola curvada y animada con anillos oscuros -->
            <g class="tigre-cola-animada">
                <path d="M 32 96 C 18 90 8 75 14 58 C 18 48 28 46 32 52 C 34 56 30 64 24 68 C 19 72 22 84 34 90 Z" fill="url(#gradPielTigre)" stroke="#78350f" stroke-width="0.8" />
                <!-- Manchas de la cola -->
                <circle cx="16" cy="62" r="3" fill="#1e1b18" />
                <circle cx="21" cy="74" r="3.2" fill="#1e1b18" />
                <circle cx="26" cy="85" r="3.5" fill="#1e1b18" />
                <!-- Punta oscura -->
                <path d="M 28 48 C 32 50 33 54 30 56 C 26 58 20 54 22 50 Z" fill="#1e1b18" />
            </g>

            <!-- Cuerpo del Jaguar en reposo felino -->
            <g class="tigre-cuerpo">
                <path d="M 30 96 C 30 76 48 65 72 65 C 102 65 120 74 135 90 C 142 98 145 112 144 125 C 138 128 50 130 30 125 C 24 116 28 104 30 96 Z" fill="url(#gradPielTigre)" />
                <!-- Rosetas / Manchas características del Jaguar en el lomo -->
                <g fill="#1e1b18">
                    <!-- Roseta 1 -->
                    <circle cx="52" cy="78" r="4.5" fill="none" stroke="#1e1b18" stroke-width="2.2" />
                    <circle cx="52" cy="78" r="1.5" />
                    <!-- Roseta 2 -->
                    <circle cx="74" cy="75" r="5" fill="none" stroke="#1e1b18" stroke-width="2.5" />
                    <circle cx="74" cy="75" r="1.8" />
                    <!-- Roseta 3 -->
                    <circle cx="95" cy="80" r="4.8" fill="none" stroke="#1e1b18" stroke-width="2.3" />
                    <circle cx="95" cy="80" r="1.6" />
                    <!-- Roseta 4 -->
                    <circle cx="62" cy="94" r="5.2" fill="none" stroke="#1e1b18" stroke-width="2.4" />
                    <circle cx="62" cy="94" r="1.8" />
                    <!-- Roseta 5 -->
                    <circle cx="86" cy="96" r="4.6" fill="none" stroke="#1e1b18" stroke-width="2.2" />
                    <circle cx="86" cy="96" r="1.6" />
                </g>
                <!-- Pata delantera visible descansando -->
                <path d="M 112 102 C 118 102 124 108 125 118 C 125 126 120 128 110 128 C 102 128 98 124 99 116 C 100 106 106 102 112 102 Z" fill="url(#gradPielTigre)" />
                <path d="M 125 116 C 132 116 138 120 138 128 C 135 130 115 130 112 128 C 112 124 118 116 125 116 Z" fill="#fef3c7" />
                <!-- Líneas de garras retráctiles -->
                <path d="M 122 123 L 122 128" stroke="#78350f" stroke-width="1.2" stroke-linecap="round" />
                <path d="M 128 123 L 128 128" stroke="#78350f" stroke-width="1.2" stroke-linecap="round" />
                <path d="M 134 123 L 134 128" stroke="#78350f" stroke-width="1.2" stroke-linecap="round" />
            </g>

            <!-- Cabeza majestuosa del Tigre Amazónico / Jaguar (Con animación sutil de mirada) -->
            <g class="tigre-cabeza-animada">
                <!-- Oreja Izquierda -->
                <path d="M 104 42 C 100 30 110 24 116 30 C 120 34 118 42 114 45 Z" fill="url(#gradPielTigre)" />
                <path d="M 106 40 C 104 33 110 28 113 32 C 115 35 114 40 111 42 Z" fill="#1e1b18" />
                <circle cx="110" cy="35" r="1.5" fill="#ffffff" opacity="0.9" />

                <!-- Oreja Derecha -->
                <path d="M 148 44 C 154 32 164 28 168 34 C 170 39 166 47 160 48 Z" fill="url(#gradPielTigre)" />
                <path d="M 152 42 C 156 34 162 32 164 36 C 165 40 162 45 158 46 Z" fill="#1e1b18" />
                <circle cx="160" cy="38" r="1.5" fill="#ffffff" opacity="0.9" />

                <!-- Cráneo y Mandíbula poderosa del felino -->
                <path d="M 108 52 C 104 68 112 90 134 94 C 156 94 168 76 166 54 C 164 42 148 38 135 38 C 122 38 110 44 108 52 Z" fill="url(#gradPielTigre)" />

                <!-- Marcas faciales y rosetas en la frente -->
                <path d="M 130 43 C 132 40 138 40 140 43" stroke="#1e1b18" stroke-width="2.2" stroke-linecap="round" />
                <path d="M 126 48 C 129 46 133 46 135 48" stroke="#1e1b18" stroke-width="2" stroke-linecap="round" />
                <path d="M 137 48 C 139 46 143 46 146 48" stroke="#1e1b18" stroke-width="2" stroke-linecap="round" />
                <circle cx="121" cy="52" r="2.2" fill="#1e1b18" />
                <circle cx="151" cy="53" r="2.2" fill="#1e1b18" />

                <!-- Ojos hipnóticos verdes-dorados de jaguar con parpadeo perfecto en su propio eje -->
                <!-- Ojo Izquierdo -->
                <g class="tigre-ojo-izq">
                    <ellipse cx="125" cy="58" rx="5.5" ry="4" fill="url(#gradOjoTigre)" stroke="#1e1b18" stroke-width="1.8" />
                    <ellipse cx="125" cy="58" rx="2" ry="3.5" fill="#0f172a" />
                    <circle cx="126" cy="56.5" r="0.9" fill="#ffffff" />
                    <path d="M 119 55 C 123 54 128 54 131 56" stroke="#1e1b18" stroke-width="1.6" stroke-linecap="round" />
                </g>

                <!-- Ojo Derecho -->
                <g class="tigre-ojo-der">
                    <ellipse cx="147" cy="59" rx="5.5" ry="4" fill="url(#gradOjoTigre)" stroke="#1e1b18" stroke-width="1.8" />
                    <ellipse cx="147" cy="59" rx="2" ry="3.5" fill="#0f172a" />
                    <circle cx="148" cy="57.5" r="0.9" fill="#ffffff" />
                    <path d="M 141 56 C 145 54 150 54 153 56" stroke="#1e1b18" stroke-width="1.6" stroke-linecap="round" />
                </g>

                <!-- Hocico blanco y mofletes de fiera -->
                <path d="M 128 66 C 122 72 120 84 135 84 C 150 84 150 72 144 66 C 140 62 132 62 128 66 Z" fill="url(#gradHocicoTigre)" />
                <!-- Nariz rosada oscura felina -->
                <path d="M 132 68 C 134 66 138 66 140 68 C 141 71 138 74 136 74 C 134 74 131 71 132 68 Z" fill="#be185d" />
                <!-- División del labio -->
                <path d="M 136 74 L 136 79" stroke="#1e1b18" stroke-width="1.6" stroke-linecap="round" />
                <path d="M 131 78 C 133 80 139 80 141 78" stroke="#1e1b18" stroke-width="1.4" stroke-linecap="round" />
                
                <!-- Puntos de bigotes -->
                <circle cx="128" cy="74" r="1" fill="#1e1b18" />
                <circle cx="126" cy="77" r="1" fill="#1e1b18" />
                <circle cx="130" cy="77" r="1" fill="#1e1b18" />
                <circle cx="144" cy="74" r="1" fill="#1e1b18" />
                <circle cx="142" cy="77" r="1" fill="#1e1b18" />
                <circle cx="146" cy="77" r="1" fill="#1e1b18" />

                <!-- Bigotes blancos finos y elegantes -->
                <path d="M 124 75 C 114 74 104 77 96 82" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
                <path d="M 125 78 C 116 80 107 85 100 92" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
                <path d="M 148 76 C 158 75 168 78 176 83" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
                <path d="M 147 79 C 156 82 165 87 172 94" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
            </g>

            <!-- Hojas de selva decorativas en primer plano -->
            <path d="M 140 140 C 150 120 170 115 180 118 C 178 135 160 140 140 140 Z" fill="#10b981" />
            <path d="M 155 140 C 162 128 175 125 180 128" stroke="#34d399" stroke-width="1" stroke-linecap="round" />
        </svg>
    </div>
</section>


<!-- Banner CTA Rediseñado con Identidad Amazónica y Paraba Beniana -->
