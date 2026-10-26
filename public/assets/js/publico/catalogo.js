document.addEventListener('DOMContentLoaded', () => {
    // 1. Manejo del menú de navegación, enlaces activos y ScrollSpy
    const navLinks = Array.from(document.querySelectorAll('#navbarMain .nav-link'));
    const menu = document.getElementById('navbarMain');

    function activarEnlace(enlace) {
        if (!enlace) return;
        navLinks.forEach(l => l.classList.remove('active'));
        enlace.classList.add('active');
    }

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const catSlug = link.getAttribute('data-categoria-slug');
            const destHash = link.getAttribute('href')?.split('#')[1];

            // Si es Hoteles en la misma página de catálogo, activar filtro dinámico
            if (catSlug && (location.pathname.endsWith('/') || location.pathname.endsWith('/catalogo') || location.pathname.includes('index.php'))) {
                const chipHotel = document.querySelector(`#explorar button[data-categoria="2"]`) ||
                                  Array.from(document.querySelectorAll('#explorar button[data-categoria]')).find(c => {
                                      const title = (c.getAttribute('title') || '').toLowerCase();
                                      return title.includes('hotel') || title.includes('alojam');
                                  });
                if (chipHotel) {
                    e.preventDefault();
                    chipHotel.click();
                    document.getElementById('explorar')?.scrollIntoView({ behavior: 'smooth' });
                    activarEnlace(link);
                    if (menu?.classList.contains('show') && window.bootstrap?.Collapse) {
                        window.bootstrap.Collapse.getOrCreateInstance(menu).hide();
                    }
                    return;
                }
            }

            activarEnlace(link);

            // Manejo de navegación suave y cierre de menú colapsable en móvil
            if (destHash && document.getElementById(destHash)) {
                if (menu?.classList.contains('show') && window.bootstrap?.Collapse) {
                    e.preventDefault();
                    menu.addEventListener('hidden.bs.collapse', () => {
                        document.getElementById(destHash)?.scrollIntoView({ behavior: 'smooth' });
                    }, { once: true });
                    window.bootstrap.Collapse.getOrCreateInstance(menu).hide();
                }
            }
        });
    });

    // ScrollSpy interactivo para actualizar el enlace activo al desplazarse
    const seccionesObservadas = [
        { id: 'inicio', linkAttr: 'inicio' },
        { id: 'explorar', linkAttr: 'explorar' },
        { id: 'promociones', linkAttr: 'promociones' },
        { id: 'guia-trinidad', linkAttr: 'guia-trinidad' },
        { id: 'transporte', linkAttr: 'transporte' },
        { id: 'galeria', linkAttr: 'galeria' }
    ];

    let tickingScroll = false;
    window.addEventListener('scroll', () => {
        if (tickingScroll) return;
        tickingScroll = true;
        requestAnimationFrame(() => {
            tickingScroll = false;
            const scrollPos = window.scrollY + 220;
            let seccionActual = 'inicio';

            for (const sec of seccionesObservadas) {
                const el = document.getElementById(sec.id);
                if (el && el.offsetTop <= scrollPos) {
                    seccionActual = sec.linkAttr;
                }
            }

            const targetLink = document.querySelector(`#navbarMain .nav-link[data-nav-link="${seccionActual}"]`);
            if (targetLink && !targetLink.classList.contains('active')) {
                activarEnlace(targetLink);
            }
        });
    }, { passive: true });

    // 2. Elementos del DOM del catálogo
    const inputBuscar = document.getElementById('inputBuscarHero');
    const inputBuscarLugar = document.getElementById('inputBuscarLugar');
    const chips = Array.from(document.querySelectorAll('#explorar button[data-categoria]:not(#btnCercaDeMi)'));
    const contenedor = document.getElementById('gridLugaresCatalogo') || document.getElementById('contenedorLugares');
    const contador = document.getElementById('contadorResultados');
    const btnLimpiar = document.getElementById('btnLimpiarFiltros');
    const selectMunicipio = document.getElementById('selectMunicipio');

    if (!contenedor) return;

    const btnCerca = document.getElementById('btnCercaDeMi');
    const gridLugares = contenedor;
    const alertaGeo = document.getElementById('alertaGeolocalizacion');
    const alertaGeoTexto = document.getElementById('alertaGeoTexto');

    // Elementos de la Fase 3: Selector de Vistas y Mapa
    const btnVistaGrid = document.getElementById('btnVistaGrid');
    const btnVistaMapa = document.getElementById('btnVistaMapa');
    const mapaWrapper = document.getElementById('mapaCatalogoWrapper');
    const btnCentrarTrinidad = document.getElementById('btnMapaCentrarTrinidad');
    const btnMapaMiUbi = document.getElementById('btnMapaMiUbicacion');
    const mapaConteoEl = document.getElementById('mapaConteoLugares');
    const mapaBadgeRadio = document.getElementById('mapaBadgeRadio');

    // Variables de estado
    let geolocalizacionActiva = false;
    let ubicacionUsuario = null;
    let precisionUsuario = 0;
    let solicitudUbicacion = 0;
    let ordenOriginalTarjetas = Array.from(contenedor.querySelectorAll('.tarjeta-lugar-col'));
    let modoVistaActual = 'grid'; // 'grid' | 'mapa'

    // Control de límite de 2 filas iniciales (6 tarjetas) y botón Ver más
    const LIMITE_FILAS_INICIAL = 6;
    let catalogoExpandido = false;
    const contenedorVerMas = document.getElementById('contenedorVerMasLugares');
    const btnVerMas = document.getElementById('btnVerMasLugares');
    const textoVerMas = document.getElementById('textoVerMasLugares');
    const iconoVerMas = document.getElementById('iconoVerMasLugares');

    function actualizarVisibilidadPorFilas() {
        if (!contenedor) return;
        const tarjetas = Array.from(contenedor.querySelectorAll('.tarjeta-lugar-col'));
        const tarjetasCandidatas = tarjetas.filter(t => t.dataset.ocultaRadio !== '1');
        const total = tarjetasCandidatas.length;

        if (total <= LIMITE_FILAS_INICIAL) {
            if (contenedorVerMas) contenedorVerMas.classList.add('d-none');
            tarjetasCandidatas.forEach(t => {
                t.classList.remove('d-none', 'tarjeta-oculta-fila', 'card-fade-in');
            });
            return;
        }

        if (modoVistaActual !== 'mapa' && contenedorVerMas) {
            contenedorVerMas.classList.remove('d-none');
        }

        if (catalogoExpandido) {
            tarjetasCandidatas.forEach(t => {
                const estabaOculta = t.classList.contains('d-none') || t.classList.contains('tarjeta-oculta-fila');
                t.classList.remove('d-none', 'tarjeta-oculta-fila');
                if (estabaOculta) {
                    t.classList.add('card-fade-in');
                }
            });
            if (textoVerMas) textoVerMas.textContent = 'Ver menos lugares';
            if (iconoVerMas) iconoVerMas.className = 'bi bi-chevron-up me-2';
        } else {
            tarjetasCandidatas.forEach((t, idx) => {
                t.classList.remove('card-fade-in');
                if (idx < LIMITE_FILAS_INICIAL) {
                    t.classList.remove('d-none', 'tarjeta-oculta-fila');
                } else {
                    t.classList.add('d-none', 'tarjeta-oculta-fila');
                }
            });
            const restantes = total - LIMITE_FILAS_INICIAL;
            if (textoVerMas) textoVerMas.textContent = `Ver más lugares (${restantes} restantes)`;
            if (iconoVerMas) iconoVerMas.className = 'bi bi-chevron-down me-2';
        }
    }

    btnVerMas?.addEventListener('click', () => {
        catalogoExpandido = !catalogoExpandido;
        actualizarVisibilidadPorFilas();
        if (!catalogoExpandido) {
            contenedor.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    // Aplicar límite inicial de 3 filas
    actualizarVisibilidadPorFilas();
    const RADIO_BUSQUEDA_KM = 1.0; // Radio de búsqueda: 1 km

    // Variables de Leaflet
    let mapa = null;
    let marcadoresLayer = null;
    let marcadorUsuario = null;
    let circuloPrecisionUsuario = null;
    let circuloRadio1Km = null;
    let rutaPolylineLayer = null; // Capa de línea de ruta turística
    let rutaMarcadoresLayer = null; // Capa de marcadores numerados de la ruta
    let idRutaActiva = 'todos';
    const COORDENADAS_TRINIDAD = [-14.8333, -64.9000];

    // Cargar datos de rutas inteligentes embebidas
    let rutasDisponibles = [];
    const scriptRutasJson = document.getElementById('rutasInicialesJson');
    if (scriptRutasJson) {
        try {
            rutasDisponibles = JSON.parse(scriptRutasJson.textContent || '[]');
        } catch (e) {
            console.error('Error parseando rutas inteligentes:', e);
        }
    }

    // Elementos del panel de rutas inteligentes
    const selectorRutasContainer = document.getElementById('selectorRutasTuristicas');
    const panelInfoRuta = document.getElementById('panelInfoRutaActiva');
    const rutaInfoBadge = document.getElementById('rutaInfoBadge');
    const rutaInfoTitulo = document.getElementById('rutaInfoTitulo');
    const rutaInfoSubtitulo = document.getElementById('rutaInfoSubtitulo');
    const rutaInfoParadas = document.getElementById('rutaInfoParadas');
    const rutaInfoDistancia = document.getElementById('rutaInfoDistancia');
    const rutaInfoTiempo = document.getElementById('rutaInfoTiempo');
    const rutaBtnGoogleMaps = document.getElementById('rutaBtnGoogleMaps');
    const rutaBtnVolverGeneral = document.getElementById('rutaBtnVolverGeneral');

    // Cargar datos iniciales del catálogo desde el script JSON embebido
    let lugaresActuales = [];
    const scriptLugaresJson = document.getElementById('lugaresInicialesJson');
    if (scriptLugaresJson) {
        try {
            lugaresActuales = JSON.parse(scriptLugaresJson.textContent || '[]');
        } catch (e) {
            console.error('Error parseando lugares iniciales:', e);
        }
    }

    // 3. Funciones de Geolocalización y Haversine
    function actualizarBotonCerca(cargando = false) {
        if (!btnCerca) return;
        btnCerca.disabled = cargando;
        btnCerca.classList.toggle('active', geolocalizacionActiva);
        btnCerca.setAttribute('aria-pressed', String(geolocalizacionActiva));
        btnCerca.setAttribute('aria-busy', String(cargando));
        const etiqueta = cargando ? 'Buscando en radio de 1 km…'
            : geolocalizacionActiva ? 'Quitar filtro de 1 km (Ver todos)' : 'Lugares a menos de 1 km';
        btnCerca.title = etiqueta;
        btnCerca.setAttribute('aria-label', etiqueta);
        btnCerca.innerHTML = cargando
            ? '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>'
            : '<i class="bi bi-geo-alt-fill" aria-hidden="true"></i>';
    }

    actualizarBotonCerca();

    // Fórmula de Haversine
    function calcularDistanciaHaversine(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radio de la Tierra en kilómetros
        const dLat = (lat2 - lat1) * (Math.PI / 180);
        const dLon = (lon2 - lon1) * (Math.PI / 180);

        const valor = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const a = Math.min(1, Math.max(0, valor));
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function formatearDistancia(km) {
        if (km < 1) {
            const metros = Math.round(km * 1000);
            return `A ${metros} m`;
        }
        return `A ${km.toFixed(1)} km`;
    }

    // Parseo robusto de coordenadas con corrección para Trinidad / Beni
    function parsearCoordenadas(coordsStr) {
        if (!coordsStr || typeof coordsStr !== 'string') return null;
        const partes = coordsStr.split(',');
        if (partes.length !== 2) return null;
        let lat = parseFloat(partes[0].trim());
        let lng = parseFloat(partes[1].trim());
        if (isNaN(lat) || isNaN(lng)) return null;

        // Autocorrección si la latitud se registró positiva en Trinidad/Bolivia (10° a 25° S)
        if (lat > 10 && lat < 25 && lng < -55 && lng > -75) {
            lat = -lat;
        }

        if (Math.abs(lat) > 90 || Math.abs(lng) > 180) return null;
        return { lat, lng };
    }

    function mostrarAlerta(mensaje, tipo = 'info') {
        if (!alertaGeo || !alertaGeoTexto) return;
        alertaGeo.className = `alert alert-${tipo} alert-dismissible fade show rounded-4 small mb-4 border-0 shadow-sm mx-auto text-center`;
        alertaGeoTexto.textContent = mensaje;
        alertaGeo.classList.remove('d-none');
    }

    function ordenarYFiltrarLugaresPorRadio(userLat, userLng) {
        const tarjetas = Array.from(contenedor.querySelectorAll('.tarjeta-lugar-col'));
        let conCoordenadas = 0;
        let lugaresEnRadio = 0;

        tarjetas.forEach(tarjeta => {
            const coordsRaw = tarjeta.getAttribute('data-coordenadas') || '';
            const contenedorBadge = tarjeta.querySelector('.badge-distancia-container');
            const coords = parsearCoordenadas(coordsRaw);

            if (coords) {
                conCoordenadas++;
                const distanciaKm = calcularDistanciaHaversine(userLat, userLng, coords.lat, coords.lng);
                tarjeta.dataset.distancia = distanciaKm;

                if (distanciaKm <= RADIO_BUSQUEDA_KM) {
                    lugaresEnRadio++;
                    tarjeta.dataset.ocultaRadio = '0';
                    tarjeta.classList.remove('d-none');
                    if (contenedorBadge) {
                        contenedorBadge.innerHTML = `
                            <span class="badge bg-danger text-white shadow-sm rounded-pill px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                <i class="bi bi-cursor-fill me-1"></i>${formatearDistancia(distanciaKm)}
                            </span>
                        `;
                    }
                } else {
                    tarjeta.dataset.ocultaRadio = '1';
                    tarjeta.classList.add('d-none');
                    if (contenedorBadge) contenedorBadge.innerHTML = '';
                }
                return;
            }

            tarjeta.dataset.distancia = 999999;
            tarjeta.dataset.ocultaRadio = '1';
            tarjeta.classList.add('d-none');
            if (contenedorBadge) contenedorBadge.innerHTML = '';
        });

        // Ordenar tarjetas visibles de menor a mayor distancia
        tarjetas.sort((a, b) => {
            return parseFloat(a.dataset.distancia || 999999) - parseFloat(b.dataset.distancia || 999999);
        });

        tarjetas.forEach(t => gridLugares.appendChild(t));
        catalogoExpandido = false;
        actualizarVisibilidadPorFilas();

        // Aviso en caso de no haber lugares dentro del radio de 1 km
        let avisoVacio = document.getElementById('avisoSinLugaresRadio');
        if (lugaresEnRadio === 0) {
            if (!avisoVacio) {
                avisoVacio = document.createElement('div');
                avisoVacio.id = 'avisoSinLugaresRadio';
                avisoVacio.className = 'col-12 text-center py-5';
                avisoVacio.innerHTML = `
                    <div class="card border-0 shadow-sm rounded-4 p-4 mx-auto" style="max-width: 520px; background-color: #fff9f0; border: 1px solid #ffe8cc !important;">
                        <i class="bi bi-broadcast-pin display-4 text-warning mb-2 d-block"></i>
                        <h5 class="fw-bold text-dark mb-1">Sin lugares a menos de 1 km</h5>
                        <p class="text-secondary small mb-3">No encontramos lugares turísticos ni comerciales registrados en un radio de 1 kilómetro de tu ubicación actual.</p>
                        <button type="button" id="btnRestaurarDesdeAviso" class="btn btn-outline-success rounded-pill btn-sm px-4 fw-semibold">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Ver todos los lugares
                        </button>
                    </div>
                `;
                gridLugares.appendChild(avisoVacio);
                document.getElementById('btnRestaurarDesdeAviso')?.addEventListener('click', () => {
                    restaurarOrdenOriginal();
                });
            } else {
                avisoVacio.classList.remove('d-none');
            }
        } else if (avisoVacio) {
            avisoVacio.classList.add('d-none');
        }

        if (contador) {
            contador.textContent = `${lugaresEnRadio} lugares encontrados (en radio de 1 km)`;
        }

        if (mapaBadgeRadio) {
            mapaBadgeRadio.classList.remove('d-none');
        }

        mostrarAlerta(lugaresEnRadio > 0
            ? `Se encontraron ${lugaresEnRadio} lugares en un radio de 1 km a tu alrededor. Pulsa de nuevo el icono GPS para ver todos.`
            : 'No se encontraron lugares en un radio de 1 km desde tu ubicación. Pulsa el botón GPS para ver todos los lugares.',
            lugaresEnRadio > 0 ? 'success' : 'warning');
    }

    function restaurarOrdenOriginal() {
        solicitudUbicacion++;
        ordenOriginalTarjetas.forEach(tarjeta => {
            tarjeta.classList.remove('d-none');
            const contenedorBadge = tarjeta.querySelector('.badge-distancia-container');
            if (contenedorBadge) contenedorBadge.innerHTML = '';
            delete tarjeta.dataset.distancia;
            delete tarjeta.dataset.ocultaRadio;
            gridLugares.appendChild(tarjeta);
        });

        catalogoExpandido = false;
        actualizarVisibilidadPorFilas();

        const avisoVacio = document.getElementById('avisoSinLugaresRadio');
        if (avisoVacio) avisoVacio.classList.add('d-none');

        if (alertaGeo) alertaGeo.classList.add('d-none');
        geolocalizacionActiva = false;
        ubicacionUsuario = null;
        precisionUsuario = 0;
        actualizarBotonCerca();

        if (mapaBadgeRadio) {
            mapaBadgeRadio.classList.add('d-none');
        }

        if (contador) {
            contador.textContent = `${ordenOriginalTarjetas.length} lugares encontrados`;
        }

        // Limpiar marcador GPS del usuario y círculo de radio en el mapa
        if (mapa) {
            if (marcadorUsuario) {
                mapa.removeLayer(marcadorUsuario);
                marcadorUsuario = null;
            }
            if (circuloPrecisionUsuario) {
                mapa.removeLayer(circuloPrecisionUsuario);
                circuloPrecisionUsuario = null;
            }
            if (circuloRadio1Km) {
                mapa.removeLayer(circuloRadio1Km);
                circuloRadio1Km = null;
            }
        }
        // Refrescar popups del mapa para retirar la distancia y mostrar todos los lugares
        actualizarMarcadoresMapa(lugaresActuales);
    }

    // Manejador del botón GPS
    btnCerca?.addEventListener('click', () => {
        if (btnCerca.disabled) return;
        if (geolocalizacionActiva) {
            restaurarOrdenOriginal();
            return;
        }

        if (!navigator.geolocation) {
            mostrarAlerta('Tu navegador no admite la función de geolocalización.', 'warning');
            return;
        }

        const solicitud = ++solicitudUbicacion;
        actualizarBotonCerca(true);

        navigator.geolocation.getCurrentPosition(
            (posicion) => {
                if (solicitud !== solicitudUbicacion) return;
                const userLat = posicion.coords.latitude;
                const userLng = posicion.coords.longitude;
                precisionUsuario = posicion.coords.accuracy || 0;

                ubicacionUsuario = { lat: userLat, lng: userLng };
                geolocalizacionActiva = true;
                actualizarBotonCerca();

                ordenarYFiltrarLugaresPorRadio(userLat, userLng);

                // Actualizar posición en el mapa interactivo con círculo de 1 km
                actualizarMarcadorGpsUsuario(userLat, userLng, precisionUsuario);
                actualizarMarcadoresMapa(lugaresActuales);

                if (modoVistaActual === 'mapa' && mapa) {
                    if (circuloRadio1Km) {
                        mapa.fitBounds(circuloRadio1Km.getBounds(), { padding: [35, 35] });
                    } else {
                        mapa.flyTo([userLat, userLng], 15, { animate: true, duration: 0.8 });
                    }
                }
            },
            (error) => {
                if (solicitud !== solicitudUbicacion) return;
                actualizarBotonCerca();

                let mensajeError = 'No se pudo obtener tu ubicación.';
                if (error.code === error.PERMISSION_DENIED) {
                    mensajeError = 'Permiso de ubicación denegado. Permite el acceso a la ubicación en tu navegador para ver distancias y centrar el mapa.';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    mensajeError = 'La señal GPS o de red no está disponible en este momento.';
                } else if (error.code === error.TIMEOUT) {
                    mensajeError = 'Se agotó el tiempo para obtener tu ubicación. Inténtalo de nuevo.';
                }

                mostrarAlerta(mensajeError, 'warning');
            },
            {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            }
        );
    });

    alertaGeo?.addEventListener('close.bs.alert', event => {
        event.preventDefault();
        alertaGeo.classList.add('d-none');
        alertaGeo.classList.remove('show');
    });

    // 4. MAPA INTERACTIVO DE TRINIDAD (Leaflet)
    function obtenerClasePinPorCategoria(nombreCat, icono) {
        const cat = (nombreCat || '').toLowerCase();
        const ico = (icono || '').toLowerCase();

        // 1. Museos y Patrimonio Cultural (Dorado/Ámbar) - Comprobar antes de building
        if (cat.includes('museo') || cat.includes('patrimon') || cat.includes('cultur') || ico.includes('bank') || ico.includes('landmark')) {
            return 'pin-museum';
        }
        // 2. Bares y Vida Nocturna (Violeta/Morado)
        if (cat.includes('bar') || cat.includes('nocturn') || cat.includes('pub') || cat.includes('karaoke') || ico.includes('straw') || ico.includes('moon') || ico.includes('martini') || ico.includes('wine')) {
            return 'pin-night';
        }
        // 3. Gastronomía y Sabores (Naranja Ámbar)
        if (cat.includes('restauran') || cat.includes('gastro') || cat.includes('comida') || cat.includes('sabor') || ico.includes('egg') || ico.includes('cup-hot') || ico.includes('utensil')) {
            return 'pin-gastro';
        }
        // 4. Hoteles y Hospedajes (Azul Cielo)
        if (cat.includes('hotel') || cat.includes('hospedaj') || cat.includes('alojam') || ico.includes('building') || ico.includes('houses') || ico.includes('bed')) {
            return 'pin-hotel';
        }
        // 5. Balnearios y Recreación Acuática (Turquesa / Cian)
        if (cat.includes('balneari') || cat.includes('recreac') || cat.includes('piscina') || cat.includes('laguna') || ico.includes('water')) {
            return 'pin-water';
        }
        // 6. Atractivos y Naturaleza (Verde Esmeralda)
        if (cat.includes('atractiv') || cat.includes('naturaleza') || cat.includes('turist') || ico.includes('compass') || ico.includes('tree')) {
            return 'pin-nature';
        }
        // 7. Radiomóviles y Transporte (Amarillo Taxi)
        if (cat.includes('transporte') || cat.includes('movil') || cat.includes('móvil') || cat.includes('taxi') || ico.includes('taxi') || ico.includes('car')) {
            return 'pin-taxi';
        }
        // 8. Comercio / Tiendas (Rosa Coral)
        if (cat.includes('artesan') || cat.includes('comercio') || cat.includes('tienda') || ico.includes('shop') || ico.includes('bag')) {
            return 'pin-shop';
        }
        return 'pin-default';
    }

    function inicializarMapa() {
        if (mapa || !window.L) return;
        const mapDiv = document.getElementById('mapaCatalogo');
        if (!mapDiv) return;

        mapa = L.map('mapaCatalogo', {
            center: COORDENADAS_TRINIDAD,
            zoom: 14,
            zoomControl: true,
            scrollWheelZoom: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> | BeniTours Trinidad'
        }).addTo(mapa);

        marcadoresLayer = L.featureGroup().addTo(mapa);
        rutaMarcadoresLayer = L.featureGroup().addTo(mapa);

        // Si el usuario ya tenía ubicación activa previa
        if (ubicacionUsuario) {
            actualizarMarcadorGpsUsuario(ubicacionUsuario.lat, ubicacionUsuario.lng, precisionUsuario);
        }

        actualizarMarcadoresMapa(lugaresActuales);
    }

    function actualizarMarcadorGpsUsuario(lat, lng, accuracy = 0) {
        if (!mapa || !window.L) return;

        if (!marcadorUsuario) {
            const userIcon = L.divIcon({
                className: 'user-gps-marker',
                html: '<div class="user-gps-dot" title="Tu posición GPS"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            marcadorUsuario = L.marker([lat, lng], { icon: userIcon, zIndexOffset: 2000 }).addTo(mapa);
            marcadorUsuario.bindPopup('<strong class="small text-success"><i class="bi bi-geo-fill me-1"></i>¡Estás aquí!</strong><br><span class="extra-small text-muted"><i class="bi bi-broadcast-pin me-1"></i>Buscando en radio de 1 km</span>');
        } else {
            marcadorUsuario.setLatLng([lat, lng]);
        }

        // Círculo visual de 1 km (1000 m) de radio de búsqueda
        if (!circuloRadio1Km) {
            circuloRadio1Km = L.circle([lat, lng], {
                radius: RADIO_BUSQUEDA_KM * 1000,
                color: '#198754',
                weight: 2,
                dashArray: '6, 8',
                fillColor: '#10b981',
                fillOpacity: 0.10
            }).addTo(mapa);
            circuloRadio1Km.bindTooltip('<strong>Radio de búsqueda: 1 km</strong>', { permanent: false, direction: 'top' });
        } else {
            circuloRadio1Km.setLatLng([lat, lng]);
            circuloRadio1Km.setRadius(RADIO_BUSQUEDA_KM * 1000);
        }
    }

    function actualizarMarcadoresMapa(lugares) {
        if (!window.L) return;
        if (!mapa) {
            inicializarMapa();
            if (!mapa) return;
        }

        marcadoresLayer.clearLayers();
        let lugaresConGps = 0;
        const baseUrl = window.APP_CONFIG?.baseUrl || '';

        // Si la geolocalización está activa, filtrar marcadores al radio de 1 km
        let lugaresAMostrar = lugares;
        if (geolocalizacionActiva && ubicacionUsuario) {
            lugaresAMostrar = lugares.filter(lug => {
                const coords = parsearCoordenadas(lug.coordenadas_gps);
                if (!coords) return false;
                const d = calcularDistanciaHaversine(ubicacionUsuario.lat, ubicacionUsuario.lng, coords.lat, coords.lng);
                return d <= RADIO_BUSQUEDA_KM;
            });
        }

        lugaresAMostrar.forEach(lug => {
            const coords = parsearCoordenadas(lug.coordenadas_gps);
            if (!coords) return;
            lugaresConGps++;

            let pinClass = obtenerClasePinPorCategoria(lug.categoria, lug.categoria_icono);
            let icono = escapeHtml(lug.categoria_icono || 'bi-geo-alt');

            // Asegurar icono de museo correcto (bi-bank) para museos y patrimonio
            if (pinClass === 'pin-museum' && (icono === 'bi-building' || icono === 'bi-geo-alt')) {
                icono = 'bi-bank';
            }

            const iconoTag = icono.startsWith('fa-') ? `<i class="${icono}"></i>` : `<i class="bi ${icono}"></i>`;
            const customIcon = L.divIcon({
                className: 'beniturs-map-marker',
                html: `<div class="beniturs-marker-pin ${pinClass}">${iconoTag}</div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -34]
            });

            // Distancia si la ubicación del turista está activa
            let distHtml = '';
            if (ubicacionUsuario) {
                const distKm = calcularDistanciaHaversine(ubicacionUsuario.lat, ubicacionUsuario.lng, coords.lat, coords.lng);
                distHtml = `<span class="badge bg-danger text-white rounded-pill px-2 py-1 extra-small"><i class="bi bi-cursor-fill me-1"></i>${formatearDistancia(distKm)}</span>`;
            }

            const imgHtml = lug.imagen
                ? `<img src="${baseUrl}/imagen?f=${encodeURIComponent(lug.imagen)}" alt="${escapeHtml(lug.nombre)}" class="beniturs-popup-img" loading="lazy" decoding="async">`
                : `<div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #092611 0%, #1b7a42 100%);"><i class="bi ${icono} fs-1 text-warning"></i></div>`;

            const direccionHtml = lug.direccion
                ? `<p class="extra-small text-secondary mb-1 text-truncate" title="${escapeHtml(lug.direccion)}"><i class="bi bi-geo-alt me-1 text-danger"></i>${escapeHtml(lug.direccion)}</p>`
                : '';

            const horarioHtml = lug.horario_atencion
                ? `<p class="extra-small text-muted mb-2 text-truncate" title="${escapeHtml(lug.horario_atencion)}"><i class="bi bi-clock me-1 text-warning"></i>${escapeHtml(lug.horario_atencion)}</p>`
                : '';

            let whatsappRaw = (lug.whatsapp_contacto || '').replace(/\D/g, '');
            if (whatsappRaw.length === 8) whatsappRaw = '591' + whatsappRaw;
            const msgWa = encodeURIComponent(`Hola, vi la información de ${lug.nombre} en BeniTours y quisiera hacer una consulta.`);
            const waBtnHtml = whatsappRaw
                ? `<a href="https://wa.me/${whatsappRaw}?text=${msgWa}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-teal rounded-pill px-2 py-1 extra-small" title="Contactar por WhatsApp"><i class="bi bi-whatsapp"></i></a>`
                : '';

            const googleMapsDir = `https://www.google.com/maps/dir/?api=1&destination=${coords.lat},${coords.lng}`;

            const popupContent = `
                <div class="beniturs-popup-card">
                    ${imgHtml}
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-light text-teal border border-teal-subtle rounded-pill extra-small px-2 py-1">
                                <i class="bi ${icono} me-1"></i>${escapeHtml(lug.categoria || '')}
                            </span>
                            ${distHtml}
                        </div>
                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="${escapeHtml(lug.nombre)}">${escapeHtml(lug.nombre)}</h6>
                        ${direccionHtml}
                        ${horarioHtml}
                        <div class="d-flex gap-1 mt-2">
                            <a href="${baseUrl}/catalogo/detalle?slug=${encodeURIComponent(lug.slug)}" 
                               class="btn btn-sm btn-teal-action text-white rounded-pill px-3 py-1 flex-grow-1 fw-semibold extra-small"
                               style="color: #ffffff !important; text-decoration: none;">
                                Ver Ficha <i class="bi bi-arrow-right ms-1 text-white"></i>
                            </a>
                            <a href="${googleMapsDir}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-teal rounded-pill px-2 py-1 extra-small" title="Cómo llegar (Google Maps)">
                                <i class="bi bi-map-fill"></i>
                            </a>
                            ${waBtnHtml}
                        </div>
                    </div>
                </div>
            `;

            const marker = L.marker([coords.lat, coords.lng], { icon: customIcon });
            marker.bindPopup(popupContent, { maxWidth: 300 });
            marcadoresLayer.addLayer(marker);
        });

        if (mapaConteoEl) {
            mapaConteoEl.textContent = (geolocalizacionActiva && ubicacionUsuario)
                ? `${lugaresConGps} en radio 1 km`
                : `${lugaresConGps} en mapa`;
        }

        // Si la vista mapa está visible y hay marcadores, encuadrar
        if (modoVistaActual === 'mapa') {
            if (geolocalizacionActiva && circuloRadio1Km) {
                try {
                    mapa.fitBounds(circuloRadio1Km.getBounds(), { padding: [35, 35] });
                } catch (e) {}
            } else if (lugaresConGps > 0) {
                try {
                    mapa.fitBounds(marcadoresLayer.getBounds(), { padding: [50, 50], maxZoom: 16 });
                } catch (e) {}
            }
        }
    }

    // 5. Selector de Vistas: Cuadrícula vs Mapa
    function cambiarModoVista(nuevoModo) {
        modoVistaActual = nuevoModo;
        if (nuevoModo === 'mapa') {
            gridLugares.classList.add('d-none');
            contenedorVerMas?.classList.add('d-none');
            mapaWrapper?.classList.remove('d-none');
            btnVistaGrid?.classList.remove('active');
            btnVistaGrid?.classList.add('text-secondary');
            btnVistaMapa?.classList.add('active');
            btnVistaMapa?.classList.remove('text-secondary');

            inicializarMapa();
            setTimeout(() => {
                if (mapa) {
                    mapa.invalidateSize();
                    if (marcadoresLayer && marcadoresLayer.getLayers().length > 0) {
                        mapa.fitBounds(marcadoresLayer.getBounds(), { padding: [50, 50], maxZoom: 16 });
                    }
                }
            }, 120);
        } else {
            mapaWrapper?.classList.add('d-none');
            gridLugares.classList.remove('d-none');
            btnVistaMapa?.classList.remove('active');
            btnVistaMapa?.classList.add('text-secondary');
            btnVistaGrid?.classList.add('active');
            btnVistaGrid?.classList.remove('text-secondary');
            actualizarVisibilidadPorFilas();
        }
    }

    btnVistaGrid?.addEventListener('click', () => cambiarModoVista('grid'));
    btnVistaMapa?.addEventListener('click', () => cambiarModoVista('mapa'));

    // 5.1. Manejo del Algoritmo de Rutas Turísticas Inteligentes
    function activarRutaTuristica(idRuta) {
        if (!mapa || !window.L) {
            inicializarMapa();
            if (!mapa) return;
        }

        idRutaActiva = idRuta;

        // Actualizar estados visuales de los botones del selector
        if (selectorRutasContainer) {
            selectorRutasContainer.querySelectorAll('.btn-filtro-ruta').forEach(btn => {
                const coincide = btn.getAttribute('data-ruta-id') === idRuta;
                btn.classList.toggle('active', coincide);
                btn.classList.toggle('btn-teal-action', coincide);
                btn.classList.toggle('btn-white', !coincide);
            });
        }

        // Limpiar trazado y marcadores anteriores de ruta
        if (rutaPolylineLayer) {
            mapa.removeLayer(rutaPolylineLayer);
            rutaPolylineLayer = null;
        }
        if (rutaMarcadoresLayer) {
            rutaMarcadoresLayer.clearLayers();
        }

        // Si se selecciona "Todos los lugares", restaurar vista normal
        if (idRuta === 'todos') {
            panelInfoRuta?.classList.add('d-none');
            marcadoresLayer.addTo(mapa);
            actualizarMarcadoresMapa(lugaresActuales);
            return;
        }

        // Buscar la ruta en el set disponible
        const ruta = rutasDisponibles.find(r => r.id === idRuta);
        if (!ruta || !ruta.paradas || ruta.paradas.length === 0) {
            return;
        }

        // Ocultar marcadores generales para enfocar la ruta
        if (mapa.hasLayer(marcadoresLayer)) {
            mapa.removeLayer(marcadoresLayer);
        }

        // Mostrar panel informativo
        if (panelInfoRuta) {
            panelInfoRuta.classList.remove('d-none');
            if (rutaInfoBadge) {
                rutaInfoBadge.textContent = ruta.badge || 'Ruta Inteligente';
                rutaInfoBadge.style.backgroundColor = ruta.color || '#0d9488';
            }
            if (rutaInfoTitulo) rutaInfoTitulo.textContent = ruta.titulo;
            if (rutaInfoSubtitulo) rutaInfoSubtitulo.textContent = ruta.subtitulo;
            if (rutaInfoParadas) rutaInfoParadas.textContent = ruta.paradas.length;
            if (rutaInfoDistancia) rutaInfoDistancia.textContent = `${ruta.distancia_km} km`;
            if (rutaInfoTiempo) rutaInfoTiempo.textContent = ruta.tiempo_estimado;
            if (rutaBtnGoogleMaps) {
                rutaBtnGoogleMaps.href = ruta.google_maps_url || '#';
            }
        }

        // 1. Trazar la polilínea conectando los puntos ordenados
        const latLngs = ruta.puntos_coords || [];
        if (latLngs.length >= 2) {
            rutaPolylineLayer = L.polyline(latLngs, {
                color: ruta.color || '#0d9488',
                weight: 5,
                opacity: 0.85,
                dashArray: '8, 8',
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(mapa);
        }

        // 2. Colocar pines numerados (Inicio, Intermedios, Fin)
        const baseUrl = window.APP_CONFIG?.baseUrl || '';
        ruta.paradas.forEach((parada, idx) => {
            const lat = parseFloat(parada.latitud);
            const lng = parseFloat(parada.longitud);
            if (isNaN(lat) || isNaN(lng)) return;

            const esInicio = (idx === 0);
            const esFin = (idx === ruta.paradas.length - 1);
            let badgeTexto = `${parada.numero_parada}`;
            let pinBg = ruta.color || '#0d9488';
            let pinLabel = `Parada ${parada.numero_parada}`;

            if (esInicio) {
                badgeTexto = '<i class="bi bi-play-fill"></i>';
                pinBg = '#15803d'; // Verde inicio
                pinLabel = '🟢 1. INICIO';
            } else if (esFin) {
                badgeTexto = '<i class="bi bi-flag-fill"></i>';
                pinBg = '#b91c1c'; // Rojo meta
                pinLabel = `🏁 ${parada.numero_parada}. META / FIN`;
            }

            const customRutaIcon = L.divIcon({
                className: 'beniturs-ruta-pin-wrapper',
                html: `
                    <div class="beniturs-ruta-pin" style="background-color: ${pinBg};">
                        <span class="ruta-pin-numero">${badgeTexto}</span>
                    </div>
                `,
                iconSize: [38, 38],
                iconAnchor: [19, 38],
                popupAnchor: [0, -36]
            });

            const imgHtml = parada.imagen
                ? `<img src="${baseUrl}/imagen?f=${encodeURIComponent(parada.imagen)}" alt="${escapeHtml(parada.nombre)}" class="beniturs-popup-img" loading="lazy">`
                : '';

            const popupContent = `
                <div class="beniturs-map-popup">
                    ${imgHtml}
                    <div class="p-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge rounded-pill px-2 py-1 extra-small text-white fw-bold" style="background-color: ${pinBg};">
                                ${pinLabel}
                            </span>
                            <span class="badge bg-light text-dark border rounded-pill extra-small">
                                <i class="bi bi-heart-fill text-danger me-1"></i>${parada.total_favoritos || 0} votos
                            </span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">${escapeHtml(parada.nombre)}</h6>
                        <p class="extra-small text-muted mb-2">${escapeHtml(parada.categoria_nombre || '')}</p>
                        ${parada.direccion ? `<p class="extra-small text-secondary mb-2 text-truncate"><i class="bi bi-geo-alt me-1 text-danger"></i>${escapeHtml(parada.direccion)}</p>` : ''}
                        <div class="d-flex gap-2 pt-2 border-top">
                            <a href="${baseUrl}/catalogo/detalle?slug=${encodeURIComponent(parada.slug)}" 
                               class="btn btn-sm btn-teal-action rounded-pill flex-grow-1 extra-small py-1 fw-semibold">
                                Ver Ficha
                            </a>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" rel="noopener noreferrer" 
                               class="btn btn-sm btn-outline-secondary rounded-pill extra-small px-2 py-1" title="Cómo llegar">
                                <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;

            const marker = L.marker([lat, lng], { icon: customRutaIcon, zIndexOffset: 1500 - idx }).addTo(rutaMarcadoresLayer);
            marker.bindPopup(popupContent, { maxWidth: 280, minWidth: 240, className: 'beniturs-leaflet-popup' });
        });

        // 3. Ajustar el mapa al circuito completo
        if (rutaMarcadoresLayer.getLayers().length > 0) {
            mapa.fitBounds(rutaMarcadoresLayer.getBounds(), { padding: [60, 60], maxZoom: 16 });
        }
    }

    // Eventos del selector de rutas
    if (selectorRutasContainer) {
        selectorRutasContainer.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-filtro-ruta');
            if (!btn) return;
            const idRuta = btn.getAttribute('data-ruta-id');
            if (idRuta) {
                activarRutaTuristica(idRuta);
            }
        });
    }

    rutaBtnVolverGeneral?.addEventListener('click', () => {
        activarRutaTuristica('todos');
    });

    btnCentrarTrinidad?.addEventListener('click', () => {
        if (mapa) {
            mapa.flyTo(COORDENADAS_TRINIDAD, 14, { animate: true, duration: 0.8 });
        }
    });

    btnMapaMiUbi?.addEventListener('click', () => {
        if (ubicacionUsuario && mapa) {
            mapa.flyTo([ubicacionUsuario.lat, ubicacionUsuario.lng], 15, { animate: true, duration: 0.8 });
            marcadorUsuario?.openPopup();
        } else {
            btnCerca?.click();
        }
    });

    // 6. Filtros y Búsqueda en Vivo
    let categoriaSeleccionada = chips.find(chip => chip.classList.contains('active'))?.getAttribute('data-categoria') || '';
    let debounceTimer = null;
    let ultimaConsulta = 0;

    const filtrarLugares = async () => {
        clearTimeout(debounceTimer);
        const consulta = ++ultimaConsulta;
        const query = encodeURIComponent((inputBuscar?.value || inputBuscarLugar?.value || '').trim());
        const cat = encodeURIComponent(categoriaSeleccionada);
        const mun = selectMunicipio?.value ? encodeURIComponent(selectMunicipio.value) : '';
        const baseUrl = window.APP_CONFIG?.baseUrl || '';
        const endpoint = `${baseUrl}/api/lugares/buscar?q=${query}&categoria=${cat}&municipio=${mun}`;

        try {
            const data = await http.get(endpoint);
            if (consulta !== ultimaConsulta) return;

            lugaresActuales = data.resultados || [];
            renderizarCuadricula(lugaresActuales);
            ordenOriginalTarjetas = Array.from(contenedor.querySelectorAll('.tarjeta-lugar-col'));
            if (geolocalizacionActiva && ubicacionUsuario) {
                ordenarYFiltrarLugaresPorRadio(ubicacionUsuario.lat, ubicacionUsuario.lng);
            } else if (contador) {
                contador.textContent = `${data.total} lugares encontrados`;
            }
            actualizarMarcadoresMapa(lugaresActuales);
        } catch (error) {
            console.error('Error al consultar lugares:', error);
        }
    };

    [inputBuscar, inputBuscarLugar].filter(Boolean).forEach(input => {
        input.addEventListener('input', () => {
            if (inputBuscar) inputBuscar.value = input.value;
            if (inputBuscarLugar) inputBuscarLugar.value = input.value;
            ultimaConsulta++;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(filtrarLugares, 300);
        });
    });

    selectMunicipio?.addEventListener('change', () => {
        restaurarOrdenOriginal();
        filtrarLugares();
        const opt = selectMunicipio.selectedOptions[0];
        const lat = parseFloat(opt?.getAttribute('data-lat'));
        const lng = parseFloat(opt?.getAttribute('data-lng'));
        if (!isNaN(lat) && !isNaN(lng) && mapa) {
            mapa.flyTo([lat, lng], 13, { animate: true, duration: 0.8 });
        }
    });

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            restaurarOrdenOriginal();
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            categoriaSeleccionada = chip.getAttribute('data-categoria') || '';
            filtrarLugares();
        });
    });

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => {
            restaurarOrdenOriginal();
            if (inputBuscar) inputBuscar.value = '';
            if (inputBuscarLugar) inputBuscarLugar.value = '';
            if (selectMunicipio) selectMunicipio.value = '';
            categoriaSeleccionada = '';
            chips.forEach(c => c.classList.remove('active'));
            const primerChip = chips.find(chip => chip.getAttribute('data-categoria') === '');
            if (primerChip) primerChip.classList.add('active');
            filtrarLugares();
        });
    }

    document.querySelector('[data-catalogo-todos]')?.addEventListener('click', () => {
        if (!location.search) btnLimpiar?.click();
    });

    // 7. Renderizado de la cuadrícula de tarjetas
    function renderizarCuadricula(lugares) {
        contenedor.innerHTML = '';

        if (lugares.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-4 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted fw-bold">No se encontraron resultados</h5>
                    <p class="text-secondary small">Intenta buscar con otra palabra clave o selecciona otra categoría.</p>
                </div>
            `;
            if (contenedorVerMas) contenedorVerMas.classList.add('d-none');
            return;
        }

        const baseUrl = window.APP_CONFIG?.baseUrl || '';

        lugares.forEach(lug => {
            let catIco = lug.categoria_icono || 'bi-geo-alt';
            if ((lug.categoria || '').toLowerCase().includes('museo') && (catIco === 'bi-building' || catIco === 'bi-geo-alt')) {
                catIco = 'bi-bank';
            }
            lug.categoria_icono = escapeHtml(catIco);

            let desc = lug.descripcion || '';
            if (desc.length > 140) {
                desc = desc.substring(0, 140) + '...';
            }

            const horarioHtml = lug.horario_atencion ? `
                <div class="text-muted extra-small text-truncate">
                    <i class="bi bi-clock me-1 text-warning"></i> ${escapeHtml(lug.horario_atencion)}
                </div>
            ` : '';
            const estadoHorarioHtml = lug.estado_horario ? `
                <span class="badge ${lug.estado_horario.abierto ? 'bg-teal-subtle text-teal border border-teal-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle'} rounded-pill extra-small flex-shrink-0">
                    ${escapeHtml(lug.estado_horario.texto)}
                </span>
            ` : '';

            const munBadge = lug.municipio ? ` · <i class="bi bi-geo-alt text-warning"></i> ${escapeHtml(lug.municipio)}` : '';
            const munHeader = lug.municipio ? ` · ${escapeHtml(lug.municipio)}` : '';
            const catIcoStr = lug.categoria_icono || 'bi-geo-alt';
            const catIcoTag = catIcoStr.startsWith('fa-') ? `<i class="${catIcoStr} me-1"></i>` : `<i class="bi ${catIcoStr} me-1"></i>`;
            const catIcoBubble = catIcoStr.startsWith('fa-') ? `<i class="${catIcoStr} fs-3 text-success"></i>` : `<i class="bi ${catIcoStr} fs-3 text-success"></i>`;

            const urlFicha = `${baseUrl}/catalogo/detalle?slug=${encodeURIComponent(lug.slug)}`;

            // Redes sociales compactas si existen
            let redesSocialesHtml = '';
            if (lug.tiktok_url || lug.facebook_url || lug.instagram_url) {
                redesSocialesHtml = `<div class="d-flex align-items-center gap-1 ms-1">`;
                if (lug.tiktok_url) {
                    redesSocialesHtml += `
                        <a href="${escapeHtml(lug.tiktok_url)}" target="_blank" rel="noopener noreferrer" 
                           class="btn btn-sm btn-dark rounded-circle p-0 d-flex align-items-center justify-content-center" 
                           style="width: 28px; height: 28px;" title="Ver en TikTok" onclick="event.stopPropagation();">
                            <i class="bi bi-tiktok text-white" style="font-size: 0.75rem;"></i>
                        </a>
                    `;
                }
                if (lug.facebook_url) {
                    redesSocialesHtml += `
                        <a href="${escapeHtml(lug.facebook_url)}" target="_blank" rel="noopener noreferrer" 
                           class="btn btn-sm text-white rounded-circle p-0 d-flex align-items-center justify-content-center" 
                           style="width: 28px; height: 28px; background-color: #1877f2;" title="Ver en Facebook" onclick="event.stopPropagation();">
                            <i class="bi bi-facebook" style="font-size: 0.75rem;"></i>
                        </a>
                    `;
                }
                if (lug.instagram_url) {
                    redesSocialesHtml += `
                        <a href="${escapeHtml(lug.instagram_url)}" target="_blank" rel="noopener noreferrer" 
                           class="btn btn-sm text-white rounded-circle p-0 d-flex align-items-center justify-content-center" 
                           style="width: 28px; height: 28px; background: linear-gradient(45deg, #f09433, #dc2743, #bc1888);" title="Ver en Instagram" onclick="event.stopPropagation();">
                            <i class="bi bi-instagram" style="font-size: 0.75rem;"></i>
                        </a>
                    `;
                }
                redesSocialesHtml += `</div>`;
            }

            const mediaHtml = lug.imagen ? `
                <img src="${baseUrl}/imagen?f=${encodeURIComponent(lug.imagen)}" 
                     alt="${escapeHtml(lug.nombre)}" 
                     class="w-100 h-100 object-fit-cover place-card-img"
                     loading="lazy"
                     decoding="async">
            ` : `
                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #09252a 0%, #0d9488 100%); min-height: 280px;">
                    <div class="place-icon-bubble shadow-sm mb-2">
                        ${catIcoBubble}
                    </div>
                    <span class="small text-white-50">${escapeHtml(lug.categoria || 'Lugar de Interés')}</span>
                </div>
            `;

            const cardHtml = `
                <div class="col-md-6 col-lg-4 item-lugar tarjeta-lugar-col"
                     data-id="${escapeHtml(lug.id_lugar)}"
                     data-nombre="${escapeHtml((lug.nombre || '').toLowerCase())}"
                     data-categoria="${escapeHtml(lug.id_categoria)}"
                     data-municipio="${escapeHtml(lug.id_municipio || 1)}"
                     data-coordenadas="${escapeHtml(lug.coordenadas_gps || '')}">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden position-relative place-card place-card-editorial">
                        <!-- Contenedor de Imagen Inmersiva -->
                        <div class="place-card-media-wrapper position-relative">
                            ${mediaHtml}
                            <!-- Badges Superiores Flotantes: Categoría y Municipio -->
                            <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1" style="z-index: 4;">
                                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill fw-semibold shadow-sm extra-small">
                                    ${catIcoTag} ${escapeHtml(lug.categoria)}${munBadge}
                                </span>
                            </div>
                            ${lug.promo_activa_texto ? `
                                <span class="badge bg-danger text-white position-absolute top-0 end-0 m-3 rounded-pill fw-bold shadow-sm extra-small" style="z-index: 5;">
                                    <i class="bi bi-tag-fill me-1"></i> Promo
                                </span>
                            ` : ''}
                            <div class="badge-distancia-container distance-badge-position position-absolute end-0 top-0 mt-5 me-3" style="z-index: 4;"></div>
                        </div>

                        <!-- Panel Flotante Curvado Estilo Editorial (Inspirado en la Referencia) -->
                        <div class="place-card-panel-editorial position-relative p-3 p-sm-4 d-flex flex-column justify-content-between">
                            <div class="place-card-header-info mb-2">
                                <h5 class="fw-bold text-dark mb-1 place-card-title-link">
                                    <a href="${urlFicha}" class="text-decoration-none text-dark stretched-link">
                                        ${escapeHtml(lug.nombre)}
                                    </a>
                                </h5>
                                <p class="text-muted extra-small mb-0 line-clamp-2 place-card-desc-preview">
                                    ${escapeHtml(desc)}
                                </p>
                            </div>

                            <!-- Barra de Acciones Revelables en Hover -->
                            <div class="place-card-hover-actions d-flex align-items-center justify-content-between pt-2">
                                <div class="d-flex align-items-center gap-2 position-relative" style="z-index: 6;">
                                    <button type="button" 
                                            class="btn btn-white bg-white rounded-circle shadow-xs place-btn-icon" 
                                            data-btn-favorito
                                            data-id="${escapeHtml(lug.id_lugar)}"
                                            data-nombre="${escapeHtml(lug.nombre)}"
                                            data-slug="${escapeHtml(lug.slug)}"
                                            data-categoria="${escapeHtml(lug.categoria)}"
                                            data-imagen="${escapeHtml(lug.imagen || '')}"
                                            data-municipio="${escapeHtml(lug.municipio || '')}"
                                            data-url="${urlFicha}"
                                            title="Guardar en mi itinerario"
                                            aria-label="Guardar en mi itinerario">
                                        <i class="bi bi-heart text-danger fs-6"></i>
                                    </button>

                                    ${estadoHorarioHtml || `
                                        <span class="badge bg-light text-secondary border rounded-pill extra-small" title="Información disponible">
                                            <i class="bi bi-info-circle text-teal"></i> Info
                                        </span>
                                    `}

                                    ${redesSocialesHtml}
                                </div>

                                <a href="${urlFicha}" 
                                   class="btn-place-arrow-circle d-flex align-items-center justify-content-center text-white shadow-sm position-relative" 
                                   style="z-index: 6;"
                                   title="Ver todos los detalles">
                                    <i class="bi bi-arrow-right fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            contenedor.insertAdjacentHTML('beforeend', cardHtml);
        });

        catalogoExpandido = false;
        actualizarVisibilidadPorFilas();

        // Las tarjetas se recrean al filtrar; recuperar de inmediato el estado de cada corazón.
        window.BeniTursItinerario?.actualizar();
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[char]));
    }

    // =========================================================================
    // Protección de Imágenes contra Descarga (Anti-Save)
    // =========================================================================
    // Carrusel de promociones: cada tarjeta ocupa su propia posición.
    const promoCarousel = document.querySelector('[data-promo-carousel]');
    if (promoCarousel) {
        const cards = Array.from(promoCarousel.querySelectorAll('[data-promo-card]'));
        const indicators = Array.from(promoCarousel.querySelectorAll('[data-promo-to]'));
        let activeIndex = 0;
        let timerId;
        const autoPlayDelay = 4000;

        const modulo = (value, total) => ((value % total) + total) % total;
        const renderPromo = (nextIndex) => {
            activeIndex = modulo(nextIndex, cards.length);
            cards.forEach((card, index) => {
                const offset = modulo(index - activeIndex, cards.length);
                card.classList.toggle('is-active', index === activeIndex);
                card.classList.toggle('is-before', offset === cards.length - 1 && index !== activeIndex);
                card.classList.toggle('is-after', offset === 1 && index !== activeIndex);
                card.classList.toggle('is-back-before', cards.length >= 5 && offset === cards.length - 2);
                card.classList.toggle('is-back-after', cards.length >= 5 && offset === 2);
                card.setAttribute('aria-hidden', index === activeIndex ? 'false' : 'true');
            });
            indicators.forEach((indicator, index) => {
                const selected = index === activeIndex;
                indicator.classList.toggle('is-active', selected);
                indicator.setAttribute('aria-current', selected ? 'true' : 'false');
            });
        };
        const stopAutoPlay = () => window.clearInterval(timerId);
        const startAutoPlay = () => {
            stopAutoPlay();
            const modalEl = document.getElementById('modalDetallePromocion');
            const isModalOpen = modalEl && (modalEl.classList.contains('show') || modalEl.style.display === 'block');
            if (cards.length > 1 && !document.hidden && !isModalOpen) {
                timerId = window.setInterval(() => renderPromo(activeIndex + 1), autoPlayDelay);
            }
        };

        promoCarousel.closest('.seccion-promociones')?.querySelectorAll('[data-promo-direction]').forEach((button) => {
            button.addEventListener('click', () => {
                renderPromo(activeIndex + (button.dataset.promoDirection === 'next' ? 1 : -1));
                startAutoPlay();
            });
        });
        indicators.forEach((indicator) => indicator.addEventListener('click', () => {
            renderPromo(Number(indicator.dataset.promoTo));
            startAutoPlay();
        }));
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoPlay();
            } else {
                startAutoPlay();
            }
        });
        renderPromo(0);
        startAutoPlay();

        // =========================================================================
        // Modal interactivo de Detalle de Promoción
        // =========================================================================
        const modalPromoEl = document.getElementById('modalDetallePromocion');
        let promoMap = null;
        let promoMarker = null;

        function abrirModalPromocion(cardEl) {
            if (!modalPromoEl || !cardEl) return;
            const rawData = cardEl.getAttribute('data-promo-data');
            if (!rawData) return;

            let data;
            try {
                data = JSON.parse(rawData);
            } catch (err) {
                console.error('Error al leer datos de la promoción:', err);
                return;
            }
            if (data.id_lugar) {
                window.BeniTursItinerario?.registrarEvento(data.id_lugar, 'PROMOCION');
            }

            // 1. Fotografía completa y fondo envolvente
            const imgEl = document.getElementById('modalPromoImg');
            const backdropEl = document.getElementById('modalPromoImgBackdrop');
            const noImgEl = document.getElementById('modalPromoNoImg');
            const placeholderNombre = document.getElementById('modalPromoPlaceholderNombre');

            if (data.imagen) {
                if (imgEl) {
                    imgEl.src = data.imagen;
                    imgEl.alt = data.titulo || 'Promoción';
                    imgEl.classList.remove('d-none');
                    imgEl.onerror = () => {
                        imgEl.classList.add('d-none');
                        if (backdropEl) backdropEl.classList.add('d-none');
                        if (noImgEl) noImgEl.classList.remove('d-none');
                        if (placeholderNombre) placeholderNombre.textContent = data.lugar_nombre || '';
                    };
                }
                if (backdropEl) {
                    backdropEl.style.backgroundImage = `url('${data.imagen}')`;
                    backdropEl.classList.remove('d-none');
                }
                if (noImgEl) noImgEl.classList.add('d-none');
            } else {
                if (imgEl) imgEl.classList.add('d-none');
                if (backdropEl) backdropEl.classList.add('d-none');
                if (noImgEl) noImgEl.classList.remove('d-none');
                if (placeholderNombre) placeholderNombre.textContent = data.lugar_nombre || '';
            }

            // 2. Textos e insignias informativas
            const catIconEl = document.getElementById('modalPromoCatIcono');
            if (catIconEl) catIconEl.className = `bi ${data.categoria_icono || 'bi-tag'} me-1`;
            const catTxtEl = document.getElementById('modalPromoCatTexto');
            if (catTxtEl) catTxtEl.textContent = data.categoria_nombre || 'Promoción';

            const descTagEl = document.getElementById('modalPromoDescuentoTag');
            const descTxtEl = document.getElementById('modalPromoDescuentoTexto');
            if (data.descuento_texto) {
                if (descTxtEl) descTxtEl.textContent = data.descuento_texto;
                if (descTagEl) descTagEl.classList.remove('d-none');
            } else if (descTagEl) {
                descTagEl.classList.add('d-none');
            }

            const tituloEl = document.getElementById('modalPromoTitulo');
            if (tituloEl) tituloEl.textContent = data.titulo || '';

            const lugarNombreEl = document.getElementById('modalPromoLugarNombre');
            if (lugarNombreEl) lugarNombreEl.textContent = data.lugar_nombre || '';

            const lugarMunEl = document.getElementById('modalPromoLugarMunicipio');
            if (lugarMunEl) lugarMunEl.textContent = data.municipio_nombre || 'Trinidad';

            const fechaFinEl = document.getElementById('modalPromoFechaFin');
            const vigenciaContainer = document.getElementById('modalPromoVigenciaContainer');
            if (data.fecha_fin_fmt) {
                if (fechaFinEl) fechaFinEl.textContent = data.fecha_fin_fmt;
                if (vigenciaContainer) vigenciaContainer.classList.remove('d-none');
            } else if (vigenciaContainer) {
                vigenciaContainer.classList.add('d-none');
            }

            const descEl = document.getElementById('modalPromoDescripcion');
            if (descEl) descEl.textContent = data.descripcion || '';

            const dirEl = document.getElementById('modalPromoLugarDireccion');
            if (dirEl) dirEl.textContent = data.lugar_direccion || '';

            // 3. Botones de acción
            const btnWa = document.getElementById('modalPromoBtnWa');
            if (btnWa) {
                if (data.wa_contacto) {
                    btnWa.href = `https://wa.me/${data.wa_contacto}?text=${data.wa_texto || ''}`;
                    btnWa.onclick = () => {
                        window.BeniTursItinerario?.registrarEvento(data.id_lugar, 'WHATSAPP');
                    };
                    btnWa.classList.remove('d-none');
                } else {
                    btnWa.classList.add('d-none');
                }
            }

            // Redes Sociales de la Promoción (Solo íconos circulares)
            const redesContainer = document.getElementById('modalPromoRedesContainer');
            const btnTiktok = document.getElementById('modalPromoBtnTiktok');
            const btnFacebook = document.getElementById('modalPromoBtnFacebook');
            const btnInstagram = document.getElementById('modalPromoBtnInstagram');

            let tieneRedes = false;
            if (btnTiktok) {
                if (data.tiktok_url) {
                    btnTiktok.href = data.tiktok_url;
                    btnTiktok.classList.remove('d-none');
                    tieneRedes = true;
                } else {
                    btnTiktok.classList.add('d-none');
                }
            }
            if (btnFacebook) {
                if (data.facebook_url) {
                    btnFacebook.href = data.facebook_url;
                    btnFacebook.classList.remove('d-none');
                    tieneRedes = true;
                } else {
                    btnFacebook.classList.add('d-none');
                }
            }
            if (btnInstagram) {
                if (data.instagram_url) {
                    btnInstagram.href = data.instagram_url;
                    btnInstagram.classList.remove('d-none');
                    tieneRedes = true;
                } else {
                    btnInstagram.classList.add('d-none');
                }
            }
            if (redesContainer) {
                if (tieneRedes) {
                    redesContainer.classList.remove('d-none');
                } else {
                    redesContainer.classList.add('d-none');
                }
            }

            const btnFicha = document.getElementById('modalPromoBtnFicha');
            if (btnFicha) {
                btnFicha.href = data.url_ficha || `${baseUrl}/catalogo/detalle?slug=${encodeURIComponent(data.lugar_slug || '')}`;
            }

            const btnComoLlegar = document.getElementById('modalPromoBtnComoLlegar');
            if (btnComoLlegar) {
                const latNum = parseFloat(data.latitud);
                const lngNum = parseFloat(data.longitud);
                if (!isNaN(latNum) && !isNaN(lngNum) && (latNum !== 0 || lngNum !== 0)) {
                    btnComoLlegar.href = `https://www.google.com/maps/dir/?api=1&destination=${latNum},${lngNum}`;
                    btnComoLlegar.classList.remove('d-none');
                } else if (data.lugar_direccion) {
                    btnComoLlegar.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent((data.lugar_nombre || '') + ' ' + (data.lugar_direccion || '') + ' ' + (data.municipio_nombre || 'Trinidad'))}`;
                    btnComoLlegar.classList.remove('d-none');
                } else {
                    btnComoLlegar.classList.add('d-none');
                }
            }

            // 4. Mapa interactivo de ubicación con Leaflet
            const mapaContainer = document.getElementById('modalPromoMapa');
            const fallbackContainer = document.getElementById('modalPromoMapaFallback');
            const dirAltEl = document.getElementById('modalPromoLugarDireccionAlt');

            const latNum = parseFloat(data.latitud);
            const lngNum = parseFloat(data.longitud);
            const tieneCoords = !isNaN(latNum) && !isNaN(lngNum) && (latNum !== 0 || lngNum !== 0);

            if (tieneCoords && window.L) {
                if (mapaContainer) mapaContainer.classList.remove('d-none');
                if (fallbackContainer) fallbackContainer.classList.add('d-none');

                if (!promoMap) {
                    promoMap = L.map('modalPromoMapa', {
                        zoomControl: true,
                        scrollWheelZoom: false
                    }).setView([latNum, lngNum], 16);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(promoMap);

                    const customIcon = L.divIcon({
                        className: 'beniturs-map-marker',
                        html: `<div class="beniturs-marker-pin pin-hotel"><i class="bi ${data.categoria_icono || 'bi-geo-alt'}"></i></div>`,
                        iconSize: [36, 36],
                        iconAnchor: [18, 36],
                        popupAnchor: [0, -34]
                    });

                    promoMarker = L.marker([latNum, lngNum], { icon: customIcon }).addTo(promoMap);
                } else {
                    promoMap.setView([latNum, lngNum], 16);
                    if (promoMarker) {
                        promoMarker.setLatLng([latNum, lngNum]);
                    } else {
                        promoMarker = L.marker([latNum, lngNum]).addTo(promoMap);
                    }
                }

                // El mini mapa no abre un popup: en un contenedor de 180px este se
                // recortaba y ocultaba la ubicación. El nombre ya se muestra arriba.
                if (promoMarker) {
                    promoMarker.unbindPopup();
                    promoMap.closePopup();
                }
            } else {
                if (mapaContainer) mapaContainer.classList.add('d-none');
                if (fallbackContainer) {
                    fallbackContainer.classList.remove('d-none');
                    if (dirAltEl) dirAltEl.textContent = data.lugar_direccion || 'Dirección no registrada';
                }
            }

            // 5. Pausar autoplay del carrusel y mostrar el modal
            stopAutoPlay();
            if (window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalPromoEl).show();
            }
        }

        // Delegación de clic para abrir el modal desde tarjetas del carrusel
        promoCarousel.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-promo-trigger="modal"]');
            if (trigger) {
                e.preventDefault();
                const card = trigger.closest('[data-promo-card]');
                if (card) {
                    abrirModalPromocion(card);
                }
            }
        });

        // Eventos del modal para recalcular dimensiones del mapa y reanudar autoplay al cerrar
        if (modalPromoEl) {
            modalPromoEl.addEventListener('shown.bs.modal', () => {
                if (promoMap) {
                    promoMap.invalidateSize();
                    if (promoMarker) {
                        promoMap.panTo(promoMarker.getLatLng(), { animate: false });
                    }
                }
            });
            modalPromoEl.addEventListener('hidden.bs.modal', () => {
                startAutoPlay();
            });
        }
    }

    // Manejador del Modal de Solicitud de Radiomóviles en Portada
    let radioActualModal = { nombre: '', wa: '', id: 0 };
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-pedir-radio');
        if (!btn) return;
        radioActualModal = {
            nombre: btn.getAttribute('data-nombre') || 'Radiomóvil',
            wa: btn.getAttribute('data-wa') || '',
            id: parseInt(btn.getAttribute('data-id') || '0', 10)
        };
        const titleNombre = document.getElementById('modalPedirRadioIndexNombre');
        if (titleNombre) {
            titleNombre.textContent = radioActualModal.nombre;
        }
    });

    const btnRadioIndexUsarGps = document.getElementById('btnRadioIndexUsarGps');
    const radioIndexMiUbi = document.getElementById('radioIndexMiUbicacion');
    const radioIndexDest = document.getElementById('radioIndexDestino');
    const radioIndexServ = document.getElementById('radioIndexTipoServicio');
    const btnEnviarPedidoIndex = document.getElementById('btnEnviarPedidoRadioIndex');

    btnRadioIndexUsarGps?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('La geolocalización no está disponible en tu navegador.');
            return;
        }
        btnRadioIndexUsarGps.disabled = true;
        btnRadioIndexUsarGps.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Obteniendo...';
        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            radioIndexMiUbi.value = `Ubicación GPS: https://maps.google.com/?q=${lat},${lng}`;
            btnRadioIndexUsarGps.disabled = false;
            btnRadioIndexUsarGps.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Ubicación lista';
        }, () => {
            btnRadioIndexUsarGps.disabled = false;
            btnRadioIndexUsarGps.innerHTML = '<i class="bi bi-cursor-fill me-1"></i> Mi GPS';
            alert('No se pudo obtener tu ubicación actual.');
        });
    });

    btnEnviarPedidoIndex?.addEventListener('click', () => {
        const servicio = radioIndexServ?.value || 'Carrera Urbana';
        const origen = radioIndexMiUbi?.value.trim() || 'Mi ubicación actual';
        const destino = radioIndexDest?.value.trim() || 'Por definir con el conductor';

        const texto = `Hola Central de ${radioActualModal.nombre} 🚕\nSolicito un móvil:\n📍 *Punto de recogida:* ${origen}\n🏁 *Destino:* ${destino}\n🛠️ *Tipo de servicio:* ${servicio}\n\n¿Me confirman disponibilidad y tarifa por favor?`;
        
        if (radioActualModal.id > 0) {
            window.BeniTursItinerario?.registrarEvento(radioActualModal.id, 'WHATSAPP');
        }
        window.open(`https://wa.me/${radioActualModal.wa}?text=${encodeURIComponent(texto)}`, '_blank');

        const modalEl = document.getElementById('modalPedirRadiomovilIndex');
        if (modalEl && window.bootstrap?.Modal) {
            window.bootstrap.Modal.getInstance(modalEl)?.hide();
        }
    });

    // =========================================================================
    // 7. MAPA DE PARADAS FIJAS DE RADIOMÓVILES (Leaflet)
    // =========================================================================
    let mapaTransporte = null;
    let marcadoresTransporte = [];
    let grupoTransporte = null;

    function inicializarMapaTransporte() {
        const contenedor = document.getElementById('mapaTransporte');
        if (!contenedor || !window.L || mapaTransporte) return;

        const paradas = window.BeniToursRadiomoviles || [];
        if (!Array.isArray(paradas) || paradas.length === 0) return;

        // Coordenadas base (Trinidad Casco Viejo)
        const latInicial = -14.8333;
        const lngInicial = -64.9000;

        mapaTransporte = L.map('mapaTransporte', {
            zoomControl: true,
            scrollWheelZoom: false,
            attributionControl: true
        }).setView([latInicial, lngInicial], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapaTransporte);

        grupoTransporte = L.featureGroup().addTo(mapaTransporte);

        const paletaColoresSindicatos = [
            { bg: '#f59e0b', text: '#111827', border: '#d97706' }, // Amarillo
            { bg: '#0284c7', text: '#ffffff', border: '#0369a1' }, // Azul
            { bg: '#10b981', text: '#ffffff', border: '#047857' }, // Verde
            { bg: '#ea580c', text: '#ffffff', border: '#c2410c' }, // Naranja
            { bg: '#8b5cf6', text: '#ffffff', border: '#6d28d9' }, // Violeta
            { bg: '#e11d48', text: '#ffffff', border: '#be123c' }  // Rojo
        ];

        // Mapeo determinístico de color por id_lugar
        const idsUnicosSindicatos = [...new Set(paradas.map(p => p.id_lugar))];
        const colorPorLugar = {};
        idsUnicosSindicatos.forEach((id, idx) => {
            colorPorLugar[id] = paletaColoresSindicatos[idx % paletaColoresSindicatos.length];
        });

        marcadoresTransporte = [];

        paradas.forEach((rm) => {
            if (!rm.latitud || !rm.longitud) return;

            const lat = parseFloat(rm.latitud);
            const lng = parseFloat(rm.longitud);
            if (isNaN(lat) || isNaN(lng)) return;

            const idLug = parseInt(rm.id_lugar, 10);
            const idPar = parseInt(rm.id_parada || '0', 10);
            const color = colorPorLugar[idLug] || paletaColoresSindicatos[0];
            const esCentral = (rm.tipo_parada === 'CENTRAL' || idPar === 0);

            // Icono temático con el color específico del sindicato
            const iconoTaxi = L.divIcon({
                className: `beniturs-map-marker marker-parada-taxi ${!esCentral ? 'marker-parada-secundaria' : ''}`,
                html: `<div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;border-radius:50%;background-color:${color.bg};color:${color.text};box-shadow:0 3px 10px rgba(0,0,0,0.35);border:2.5px solid ${esCentral ? '#ffffff' : color.border};">
                        <i class="bi bi-taxi-front-fill" style="font-size:1.1rem;color:${color.text};"></i>
                       </div>`,
                iconSize: [38, 38],
                iconAnchor: [19, 19],
                popupAnchor: [0, -22]
            });

            const marker = L.marker([lat, lng], { icon: iconoTaxi });

            const nombreSindicato = rm.nombre_sindicato || rm.nombre || 'Radiomóvil';
            const nombreParada = rm.nombre_parada || 'Parada Oficial';
            const tipoParada = rm.tipo_parada || (esCentral ? 'CENTRAL' : 'PARADA');

            const popupHtml = `
                <div class="beniturs-map-popup p-1" style="min-width: 240px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 34px; height: 34px; flex-shrink: 0; background-color:${color.bg}; color:${color.text};">
                            <i class="bi bi-taxi-front-fill fs-6"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="badge extra-small rounded-pill px-2 py-0 fw-bold border" style="background-color:${color.bg}; color:${color.text}; border-color:${color.border}!important;">${tipoParada}</span>
                            <h6 class="fw-bold mb-0 text-dark text-truncate" style="font-size: 0.90rem;">${nombreParada}</h6>
                            <small class="extra-small fw-bold d-block text-truncate" style="color:${color.border};">${nombreSindicato}</small>
                        </div>
                    </div>
                    <p class="extra-small text-muted mb-2 lh-sm">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>${rm.direccion || 'Trinidad, Beni'}
                    </p>
                    <div class="d-grid gap-1 pt-1 border-top">
                        ${rm.whatsapp ? `
                            <button type="button" 
                                    class="btn btn-sm btn-success rounded-pill fw-bold extra-small py-1 btn-pedir-radio d-flex align-items-center justify-content-center gap-1 shadow-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalPedirRadiomovilIndex"
                                    data-nombre="${nombreSindicato} (${nombreParada})"
                                    data-wa="${rm.whatsapp}"
                                    data-id="${idLug}">
                                <i class="bi bi-whatsapp"></i> Pedir a esta Parada
                            </button>
                        ` : ''}
                        <div class="d-flex gap-1 mt-1">
                            ${rm.telefono ? `
                                <a href="tel:${rm.telefono}" class="btn btn-xs btn-outline-dark rounded-pill flex-grow-1 extra-small py-1 text-center text-decoration-none">
                                    <i class="bi bi-telephone-fill me-1"></i> Llamar
                                </a>
                            ` : ''}
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" rel="noopener" class="btn btn-xs btn-outline-secondary rounded-pill flex-grow-1 extra-small py-1 text-center text-decoration-none">
                                <i class="bi bi-navigation-fill me-1"></i> Cómo llegar
                            </a>
                        </div>
                    </div>
                </div>
            `;

            marker.bindPopup(popupHtml, { maxWidth: 280, minWidth: 230, className: 'beniturs-leaflet-popup' });
            marker.idLugar = idLug;
            marker.idParada = idPar;

            // En móvil: al tocar un marcador, desplazar el carrusel horizontal a la tarjeta correspondiente
            marker.on('click', () => {
                const targetCard = document.getElementById(`card-radio-${idLug}`);
                if (targetCard && window.innerWidth < 992) {
                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            });

            grupoTransporte.addLayer(marker);
            marcadoresTransporte.push(marker);
        });

        // Ajustar vista a las paradas existentes
        if (grupoTransporte.getLayers().length > 0) {
            mapaTransporte.fitBounds(grupoTransporte.getBounds(), { padding: [50, 50], maxZoom: 16 });
        }

        // Recentrar mapa al hacer clic en "Ver todo"
        const btnRecentrar = document.getElementById('btnRecentrarMapaTransporte');
        btnRecentrar?.addEventListener('click', () => {
            if (grupoTransporte && grupoTransporte.getLayers().length > 0) {
                mapaTransporte.fitBounds(grupoTransporte.getBounds(), { padding: [50, 50], maxZoom: 16 });
            }
        });

        // Conectar botones "Ver en mapa" de las tarjetas (Central o Parada específica)
        document.querySelectorAll('.btn-ver-parada-mapa').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const lat = parseFloat(btn.getAttribute('data-lat'));
                const lng = parseFloat(btn.getAttribute('data-lng'));
                const id = parseInt(btn.getAttribute('data-id') || '0', 10);
                const idPar = parseInt(btn.getAttribute('data-parada-id') || '0', 10);

                if (!isNaN(lat) && !isNaN(lng) && mapaTransporte) {
                    mapaTransporte.flyTo([lat, lng], 16, { animate: true, duration: 0.8 });
                    let targetMarker = marcadoresTransporte.find(m => m.idLugar === id && m.idParada === idPar);
                    if (!targetMarker) {
                        targetMarker = marcadoresTransporte.find(m => m.idLugar === id);
                    }
                    if (targetMarker) {
                        setTimeout(() => targetMarker.openPopup(), 850);
                    }
                }
            });
        });
    }

    // Inicializar mapa de transporte cuando la sección esté visible o el DOM esté listo
    if (document.getElementById('mapaTransporte')) {
        // Observer para cargar o recalcular tamaño solo cuando el usuario se acerque a la sección
        const secTransporte = document.getElementById('transporte');
        if ('IntersectionObserver' in window && secTransporte) {
            const observerTransporte = new IntersectionObserver((entries, obs) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        if (!mapaTransporte) {
                            inicializarMapaTransporte();
                        } else {
                            mapaTransporte.invalidateSize();
                        }
                        obs.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '200px' });
            observerTransporte.observe(secTransporte);
        } else {
            setTimeout(inicializarMapaTransporte, 600);
        }
    }

    // =========================================================================
    // 8. LIGHTBOX DE GALERÍA TURÍSTICA
    // =========================================================================
    const modalLightbox = document.getElementById('modalLightboxGaleria');
    if (modalLightbox) {
        modalLightbox.addEventListener('show.bs.modal', (e) => {
            const trigger = e.relatedTarget;
            if (!trigger) return;

            const foto = trigger.getAttribute('data-foto') || '';
            const titulo = trigger.getAttribute('data-titulo') || 'Lugar Turístico';
            const categoria = trigger.getAttribute('data-categoria') || 'Turismo';
            const credito = trigger.getAttribute('data-credito') || '';
            const fuente = trigger.getAttribute('data-fuente') || '';
            const url = trigger.getAttribute('data-url') || '#';

            const imgEl = document.getElementById('lightboxImg');
            const tituloEl = document.getElementById('lightboxTituloLugar');
            const catBadgeEl = document.getElementById('lightboxCatBadge');
            const btnFichaEl = document.getElementById('lightboxBtnFicha');
            const creditoContainerEl = document.getElementById('lightboxCreditoContainer');
            const creditoNombreEl = document.getElementById('lightboxCreditoNombre');
            const fuenteLinkEl = document.getElementById('lightboxFuenteLink');

            if (imgEl) imgEl.src = foto;
            if (tituloEl) tituloEl.textContent = titulo;
            if (catBadgeEl) catBadgeEl.textContent = categoria;

            if (creditoContainerEl && creditoNombreEl) {
                if (credito) {
                    creditoNombreEl.textContent = credito;
                    creditoContainerEl.classList.remove('d-none');
                    if (fuenteLinkEl) {
                        if (fuente) {
                            fuenteLinkEl.href = fuente;
                            fuenteLinkEl.classList.remove('d-none');
                        } else {
                            fuenteLinkEl.classList.add('d-none');
                        }
                    }
                } else {
                    creditoContainerEl.classList.add('d-none');
                }
            }

            if (btnFichaEl) {
                if (url && url !== '#') {
                    btnFichaEl.href = url;
                    btnFichaEl.classList.remove('d-none');
                } else {
                    btnFichaEl.classList.add('d-none');
                }
            }
        });

        modalLightbox.addEventListener('hidden.bs.modal', () => {
            const imgEl = document.getElementById('lightboxImg');
            if (imgEl) imgEl.src = '';
        });
    }

    document.addEventListener('contextmenu', (e) => {
        if (e.target.closest('img, video, .place-card, .hero-video, .gallery-item, .beniturs-popup-img')) {
            e.preventDefault();
            return false;
        }
    }, { passive: false });

    document.addEventListener('dragstart', (e) => {
        if (e.target.closest('img, video, .place-card, .hero-video, .gallery-item, .beniturs-popup-img')) {
            e.preventDefault();
            return false;
        }
    }, { passive: false });
});

/**
 * Función global para aplicar búsquedas directas desde las píldoras del Hero
 */
window.aplicarFiltroRapidoHero = function(termino) {
    const inputHero = document.getElementById('inputBuscarHero');
    if (inputHero) {
        inputHero.value = termino;
        inputHero.dispatchEvent(new Event('input', { bubbles: true }));
        document.getElementById('explorar')?.scrollIntoView({ behavior: 'smooth' });
    }
};

/**
 * Interactividad del Mapa Departamental Regional del Beni
 * Efecto Hover Revelador fotográfico y Clic con desplazamiento suave hacia la Galería
 */
document.addEventListener('DOMContentLoaded', () => {
    const gruposRegiones = Array.from(document.querySelectorAll('.region-beni-grupo'));
    if (!gruposRegiones.length) return;

    const overlays = Array.from(document.querySelectorAll('.provincia-overlay'));
    const overlayMap = new Map();
    overlays.forEach(overlay => {
        const id = overlay.dataset.overlay;
        if (id) overlayMap.set(id, overlay);
    });

    gruposRegiones.forEach(grupo => {
        const provId = grupo.dataset.provincia;
        const overlay = overlayMap.get(provId);

        // Al pasar el mouse sobre la provincia, activar su etiqueta y pin en la capa superior
        grupo.addEventListener('mouseenter', () => {
            if (overlay) overlay.classList.add('is-hover');
        });

        grupo.addEventListener('mouseleave', () => {
            if (overlay) overlay.classList.remove('is-hover');
        });

        grupo.addEventListener('focus', () => {
            if (overlay) overlay.classList.add('is-hover');
        });

        grupo.addEventListener('blur', () => {
            if (overlay) overlay.classList.remove('is-hover');
        });

        // Al hacer clic sobre cualquier provincia
        grupo.addEventListener('click', (e) => {
            e.preventDefault();

            // Resaltar brevemente la provincia seleccionada
            grupo.classList.add('is-active');
            if (overlay) overlay.classList.add('is-active');

            // Desplazar suavemente a la sección Galería de Fotos
            const seccionGaleria = document.getElementById('galeria');
            if (seccionGaleria) {
                seccionGaleria.scrollIntoView({ behavior: 'smooth' });
            }

            // Remover la clase is-active para que cuando el usuario vuelva a ver el mapa
            // no quede congelado con la foto revelada ni la etiqueta pegada
            setTimeout(() => {
                gruposRegiones.forEach(g => g.classList.remove('is-active'));
                overlays.forEach(o => o.classList.remove('is-active', 'is-hover'));
            }, 600);
        });

        // Soporte de accesibilidad para teclado (Enter o Espacio)
        grupo.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                grupo.click();
            }
        });
    });
});

