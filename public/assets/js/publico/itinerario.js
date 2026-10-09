/**
 * Sistema de Itinerario y Favoritos del Turista - BeniTours
 * Permite a los visitantes guardar destinos, armar su ruta del día
 * y compartirla fácilmente por WhatsApp sin necesidad de registro.
 */
(() => {
    'use strict';

    const STORAGE_KEY = 'beniturs_itinerario_v1';

    // Obtener lista de favoritos guardados
    function obtenerItinerario() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            console.warn('Error leyendo itinerario de localStorage:', e);
            return [];
        }
    }

    // Guardar lista en localStorage
    function guardarItinerario(items) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
            actualizarUI();
        } catch (e) {
            console.error('Error guardando en localStorage:', e);
        }
    }

    // Comprobar si un lugar ya está guardado
    function estaEnItinerario(idLugar) {
        const idNum = parseInt(idLugar, 10);
        return obtenerItinerario().some(item => parseInt(item.id, 10) === idNum);
    }

    // Alternar lugar (agregar / quitar)
    function alternarFavorito(lugarData) {
        const idNum = parseInt(lugarData.id, 10);
        let items = obtenerItinerario();
        const existe = items.some(item => parseInt(item.id, 10) === idNum);

        if (existe) {
            items = items.filter(item => parseInt(item.id, 10) !== idNum);
            mostrarToastFeedback(`Se quitó "${lugarData.nombre}" de tu ruta`, 'secondary');
        } else {
            items.push({
                id: idNum,
                nombre: lugarData.nombre || 'Lugar',
                slug: lugarData.slug || '',
                categoria: lugarData.categoria || 'Turismo',
                imagen: lugarData.imagen || '',
                municipio: lugarData.municipio || 'Trinidad',
                url: lugarData.url || (window.location.origin + window.location.pathname)
            });
            mostrarToastFeedback(`¡"${lugarData.nombre}" añadido a tu ruta!`, 'success');
            enviarTelemetria(idNum, 'FAVORITO');
        }

        guardarItinerario(items);
        return !existe;
    }

    // Envío silencioso de telemetría sin bloquear la experiencia
    function enviarTelemetria(idLugar, tipoEvento) {
        try {
            const baseUrl = window.APP_CONFIG?.baseUrl || '';
            const url = `${baseUrl}/api/telemetria/evento`;
            const payload = JSON.stringify({ id_lugar: parseInt(idLugar, 10), tipo_evento: tipoEvento });
            if (navigator.sendBeacon) {
                const blob = new Blob([payload], { type: 'application/json' });
                navigator.sendBeacon(url, blob);
            } else {
                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: payload,
                    keepalive: true
                }).catch(() => {});
            }
        } catch (e) {}
    }

    // Eliminar por ID
    function eliminarDeItinerario(idLugar) {
        const idNum = parseInt(idLugar, 10);
        let items = obtenerItinerario().filter(item => parseInt(item.id, 10) !== idNum);
        guardarItinerario(items);
        mostrarToastFeedback('Lugar eliminado de tu ruta', 'secondary');
    }

    // Vaciar lista completa
    function vaciarItinerario() {
        if (!confirm('¿Deseas vaciar todos los lugares de tu itinerario?')) return;
        localStorage.removeItem(STORAGE_KEY);
        actualizarUI();
        mostrarToastFeedback('Itinerario vaciado', 'info');
    }

    // Compartir por WhatsApp
    function compartirRutaWhatsApp() {
        const items = obtenerItinerario();
        if (items.length === 0) {
            alert('Añade al menos un lugar a tu ruta para compartirla.');
            return;
        }

        let texto = `*Mi Ruta Turística en Trinidad y el Beni* 🌴🗺️\n`;
        texto += `He planificado este recorrido en la Guía BeniTours:\n\n`;

        items.forEach((item, index) => {
            texto += `*${index + 1}. ${item.nombre}* (${item.categoria})\n`;
            if (item.municipio) texto += `📍 ${item.municipio}\n`;
            texto += `👉 Ver ficha: ${item.url}\n\n`;
        });

        texto += `Descubre más atractivos y gastronomía en: ${window.location.origin}`;

        const waUrl = `https://wa.me/?text=${encodeURIComponent(texto)}`;
        window.open(waUrl, '_blank', 'noopener,noreferrer');
    }

    // Actualizar estados visuales de botones, contadores y modal
    function actualizarUI() {
        const items = obtenerItinerario();
        const cantidad = items.length;

        // 1. Actualizar contadores
        document.querySelectorAll('.contador-itinerario').forEach(el => {
            el.textContent = cantidad;
            el.classList.toggle('d-none', cantidad === 0);
        });

        // 2. Botón flotante de ruta
        const btnFlotante = document.getElementById('btnFlotanteItinerario');
        if (btnFlotante) {
            if (cantidad > 0) {
                btnFlotante.classList.remove('d-none');
                btnFlotante.classList.add('d-flex');
            } else {
                btnFlotante.classList.add('d-none');
                btnFlotante.classList.remove('d-flex');
            }
        }

        // 3. Sincronizar todos los botones de corazón en página
        document.querySelectorAll('[data-btn-favorito]').forEach(btn => {
            const id = btn.getAttribute('data-id');
            const activo = estaEnItinerario(id);
            const icono = btn.querySelector('i');

            if (icono) {
                if (activo) {
                    icono.className = 'bi bi-heart-fill text-danger';
                    btn.classList.add('btn-favorito-activo');
                    btn.setAttribute('title', 'Quitar de mi itinerario');
                    btn.setAttribute('aria-pressed', 'true');
                } else {
                    icono.className = 'bi bi-heart text-secondary';
                    btn.classList.remove('btn-favorito-activo');
                    btn.setAttribute('title', 'Guardar en mi itinerario');
                    btn.setAttribute('aria-pressed', 'false');
                }
            }

            // Si tiene texto descriptivo (como en detalle.php)
            const labelEl = btn.querySelector('.label-favorito');
            if (labelEl) {
                labelEl.textContent = activo ? 'En mi itinerario ♥' : 'Añadir a mi itinerario';
            }
        });

        // 4. Renderizar contenido dentro del modal si existe
        renderizarModalItinerario(items);
    }

    // Renderizar cuerpo del modal
    function renderizarModalItinerario(items) {
        const contenedor = document.getElementById('listaItinerarioContenido');
        const vacio = document.getElementById('itinerarioVacioMensaje');
        const footerAcciones = document.getElementById('itinerarioFooterAcciones');
        if (!contenedor) return;

        if (items.length === 0) {
            contenedor.innerHTML = '';
            if (vacio) vacio.classList.remove('d-none');
            if (footerAcciones) footerAcciones.classList.add('d-none');
            return;
        }

        if (vacio) vacio.classList.add('d-none');
        if (footerAcciones) footerAcciones.classList.remove('d-none');

        const baseUrl = window.APP_CONFIG?.baseUrl || '';
        let html = '<div class="list-group list-group-flush gap-2">';

        items.forEach((item, idx) => {
            const imgHtml = item.imagen
                ? `<img src="${baseUrl}/imagen?f=${encodeURIComponent(item.imagen)}" alt="${item.nombre}" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 54px; height: 54px;">`
                : `<div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px;"><i class="bi bi-geo-alt-fill fs-4"></i></div>`;

            html += `
                <div class="list-group-item bg-light rounded-4 border-0 p-3 d-flex align-items-center justify-content-between gap-3 shadow-sm">
                    <div class="d-flex align-items-center gap-3 overflow-hidden">
                        <span class="badge bg-success rounded-pill extra-small px-2 py-1">${idx + 1}</span>
                        ${imgHtml}
                        <div class="text-truncate">
                            <h6 class="mb-0 fw-bold text-dark text-truncate">${item.nombre}</h6>
                            <small class="text-muted extra-small d-block text-truncate">
                                <i class="bi bi-tag me-1"></i>${item.categoria} ${item.municipio ? `&bull; <i class="bi bi-geo-alt text-danger"></i> ${item.municipio}` : ''}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <a href="${item.url}" class="btn btn-outline-success btn-sm rounded-pill px-3 extra-small fw-semibold" title="Ver ficha">
                            Ver
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1 d-flex align-items-center justify-content-center btn-eliminar-favorito" 
                                data-id="${item.id}" title="Quitar de la lista" style="width: 32px; height: 32px;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        contenedor.innerHTML = html;

        // Escuchar clics de eliminación en el modal
        contenedor.querySelectorAll('.btn-eliminar-favorito').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = btn.getAttribute('data-id');
                eliminarDeItinerario(id);
            });
        });
    }

    // Feedback Toast flotante ligero
    function mostrarToastFeedback(mensaje, tipo = 'success') {
        let toastCont = document.getElementById('benitursToastContainer');
        if (!toastCont) {
            toastCont = document.createElement('div');
            toastCont.id = 'benitursToastContainer';
            toastCont.className = 'toast-container position-fixed bottom-0 start-50 translate-middle-x p-3';
            toastCont.style.zIndex = '9999';
            document.body.appendChild(toastCont);
        }

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-${tipo} border-0 shadow-lg rounded-pill`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.innerHTML = `
            <div class="d-flex px-3 py-2">
                <div class="toast-body small fw-semibold py-1">
                    <i class="bi ${tipo === 'success' ? 'bi-heart-fill' : 'bi-info-circle'} me-2"></i> ${mensaje}
                </div>
            </div>
        `;

        toastCont.appendChild(toastEl);
        if (window.bootstrap?.Toast) {
            const toast = new window.bootstrap.Toast(toastEl, { delay: 2500 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        } else {
            setTimeout(() => toastEl.remove(), 2500);
        }
    }

    // Inicialización al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        // Delegación de eventos para botones de favorito
        document.body.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-btn-favorito]');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();

            const lugarData = {
                id: btn.getAttribute('data-id'),
                nombre: btn.getAttribute('data-nombre'),
                slug: btn.getAttribute('data-slug'),
                categoria: btn.getAttribute('data-categoria'),
                imagen: btn.getAttribute('data-imagen'),
                municipio: btn.getAttribute('data-municipio'),
                url: btn.getAttribute('data-url') || window.location.href
            };

            alternarFavorito(lugarData);
        });

        // Escuchar botón de compartir por WhatsApp
        const btnCompartir = document.getElementById('btnCompartirItinerarioWa');
        if (btnCompartir) {
            btnCompartir.addEventListener('click', compartirRutaWhatsApp);
        }

        // Escuchar botón de vaciar
        const btnVaciar = document.getElementById('btnVaciarItinerario');
        if (btnVaciar) {
            btnVaciar.addEventListener('click', vaciarItinerario);
        }

        actualizarUI();
    });

    // Exponer API mínima para integración
    window.BeniTursItinerario = {
        obtener: obtenerItinerario,
        alternar: alternarFavorito,
        eliminar: eliminarDeItinerario,
        vaciar: vaciarItinerario,
        compartir: compartirRutaWhatsApp,
        actualizar: actualizarUI,
        registrarEvento: enviarTelemetria
    };
})();
