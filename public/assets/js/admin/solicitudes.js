document.addEventListener('DOMContentLoaded', () => {
    const mensaje = document.getElementById('solicitudAdminMensaje');
    const pendientes = new Set();
    const modalCredencialesEl = document.getElementById('modalCredencialesGeneradas');
    const modalCredenciales = modalCredencialesEl && window.bootstrap ? new bootstrap.Modal(modalCredencialesEl) : null;
    const modalCobroEl = document.getElementById('modalConfirmarCobroEfectivo');
    const modalCobro = modalCobroEl && window.bootstrap ? new bootstrap.Modal(modalCobroEl) : null;

    // Manejo de botones de cobro en efectivo
    document.querySelectorAll('.btn-cobro-efectivo').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const nombre = btn.dataset.nombre || 'Comercio';
            const plan = btn.dataset.plan || 'Plan';
            const monto = Number(btn.dataset.monto || 0).toLocaleString('es-BO', {minimumFractionDigits: 2});

            document.getElementById('cobroEfectivoIdSolicitud').value = id;
            document.getElementById('cobroEfectivoNombre').textContent = nombre;
            document.getElementById('cobroEfectivoPlan').textContent = `Plan Solicitado: ${plan}`;
            document.getElementById('cobroEfectivoMonto').value = `Bs ${monto}`;
            document.getElementById('cobroEfectivoRecibo').value = '';

            modalCobro?.show();
        });
    });

    // Formulario del modal de cobro en efectivo
    const formCobro = document.getElementById('formCobroEfectivoModal');
    if (formCobro) {
        formCobro.addEventListener('submit', async event => {
            event.preventDefault();
            const id = document.getElementById('cobroEfectivoIdSolicitud').value;
            if (!id || pendientes.has(id)) return;
            pendientes.add(id);

            const btnSubmit = document.getElementById('btnConfirmarCobroEfectivo');
            if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.textContent = 'Aprovisionando…'; }

            try {
                const formData = new FormData(formCobro);
                const baseUrl = window.location.pathname.split('/admin/')[0];
                const response = await fetch(`${baseUrl}/admin/solicitudes/aprovisionar`, {
                    method: 'POST',
                    body: formData,
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.error || 'No se pudo completar la operación.');

                modalCobro?.hide();
                actualizarFilaAceptada(id, data);
                mostrarModalCredenciales(data);

            } catch (error) {
                alert('Error al registrar cobro: ' + (error.message || 'No se pudo completar.'));
            } finally {
                pendientes.delete(id);
                if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Cobrar y Generar Credenciales'; }
            }
        });
    }

    // Formularios estándar de aprobación y rechazo
    document.querySelectorAll('[data-solicitud-accion]').forEach(form => {
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const id = form.elements.id_solicitud.value;
            if (pendientes.has(id)) return;
            const aceptar = form.dataset.solicitudAccion === 'aprovisionar';
            if (!window.confirm(aceptar ? '¿Confirmas la activación y aprovisionamiento de este negocio?' : '¿Rechazar esta solicitud?')) return;
            pendientes.add(id);
            const relacionados = [...document.querySelectorAll('[data-solicitud-accion]')].filter(f => f.elements.id_solicitud.value === id);
            relacionados.forEach(f => { const b = f.querySelector('button[type="submit"]'); if (b) b.disabled = true; });
            mensaje.className = 'alert alert-info';
            mensaje.textContent = aceptar ? 'Aprovisionando negocio y generando credenciales…' : 'Guardando resolución…';
            try {
                const response = await fetch(form.action, {method:'POST',body:new FormData(form),headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.error || 'No se pudo completar la operación.');
                
                actualizarFilaAceptada(id, data);

                if (aceptar) {
                    mostrarModalCredenciales(data);
                }

                mensaje.className = 'alert alert-success';
                mensaje.textContent = data.message;
                const modal = form.closest('.modal');
                if (modal) window.bootstrap?.Modal.getInstance(modal)?.hide();
            } catch (error) {
                mensaje.className = 'alert alert-danger';
                mensaje.textContent = error.message || 'No se pudo completar la operación. Revisa el estado antes de reintentar.';
            } finally {
                pendientes.delete(id);
                relacionados.forEach(f => { const b = f.querySelector('button[type="submit"]'); if (b) b.disabled = false; });
                mensaje.focus();
            }
        });
    });

    function actualizarFilaAceptada(id, data) {
        const fila = document.querySelector(`[data-solicitud="${id}"]`);
        if (!fila) return;
        const estadoCelda = fila.querySelector('[data-estado]');
        const estadoClase = String(data.estado || '').toLowerCase();
        const iconoEstado = data.estado === 'ACEPTADA' ? 'bi-check-circle-fill' : (data.estado === 'RECHAZADA' ? 'bi-x-circle-fill' : 'bi-hourglass-split');
        estadoCelda.replaceChildren();
        const insignia = document.createElement('span');
        insignia.className = `solicitud-status solicitud-status-${estadoClase}`;
        const icono = document.createElement('i');
        icono.className = `bi ${iconoEstado}`;
        insignia.append(icono, document.createTextNode(` ${data.estado}`));
        estadoCelda.append(insignia);

        // Remover botones pendientes
        fila.querySelectorAll('form[data-solicitud-accion], .btn-cobro-efectivo').forEach(el => el.remove());

        if (data.whatsapp_url) {
            const enlace = document.createElement('a');
            enlace.href = data.whatsapp_url;
            enlace.target = '_blank'; enlace.rel = 'noopener noreferrer';
            enlace.className = 'btn btn-success btn-sm mt-2 d-inline-flex align-items-center gap-1 w-100 justify-content-center';
            enlace.innerHTML = '<i class="bi bi-whatsapp"></i> <span>WhatsApp</span>';
            fila.querySelector('[data-acciones]').append(enlace);
        }
    }

    function mostrarModalCredenciales(data) {
        if (window.BeniTursModalCredenciales) {
            window.BeniTursModalCredenciales.mostrar(data);
        }
    }

    // ==========================================
    // BUSCADOR EN TIEMPO REAL (LIVE SEARCH)
    // ==========================================
    const inputSearch = document.getElementById('inputBuscadorSolicitudes');
    const btnClear = document.getElementById('btnClearSearchSolicitudes');
    const btnRestablecerLive = document.getElementById('btnRestablecerLiveSolicitudes');
    const filaSinResultados = document.getElementById('filaSinResultadosSolicitudes');
    const textoBusquedaFallida = document.getElementById('textoBusquedaFallidaSolicitudes');
    const filasSolicitudes = Array.from(document.querySelectorAll('tr.solicitud-row'));

    function filtrarSolicitudesEnVivo() {
        if (!inputSearch) return;

        const query = (inputSearch.value || '').trim().toLowerCase();

        // Mostrar / ocultar botón de limpieza rápida
        if (btnClear) {
            btnClear.style.display = query !== '' ? 'block' : 'none';
        }

        let visibles = 0;

        filasSolicitudes.forEach(fila => {
            const dataSearch = fila.getAttribute('data-search') || '';
            const dataId = fila.getAttribute('data-solicitud') || '';

            let coincide = true;
            if (query !== '') {
                const queryLimpia = query.replace(/^#/, '');
                coincide = dataSearch.includes(query) || (queryLimpia !== '' && dataId === queryLimpia);
            }

            if (coincide) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        // Manejar fila de sin resultados
        if (filaSinResultados) {
            if (visibles === 0 && filasSolicitudes.length > 0) {
                filaSinResultados.style.display = '';
                if (textoBusquedaFallida) {
                    textoBusquedaFallida.textContent = query !== '' 
                        ? `No se encontraron resultados para "${inputSearch.value}".`
                        : 'No hay solicitudes que coincidan.';
                }
            } else {
                filaSinResultados.style.display = 'none';
            }
        }
    }

    if (inputSearch) {
        inputSearch.addEventListener('input', filtrarSolicitudesEnVivo);
        // Si al cargar ya tenía valor inicial por GET, sincronizar estado del botón
        if (inputSearch.value.trim() !== '') {
            if (btnClear) btnClear.style.display = 'block';
        }
    }

    if (btnClear) {
        btnClear.addEventListener('click', () => {
            if (inputSearch) {
                inputSearch.value = '';
                inputSearch.focus();
                filtrarSolicitudesEnVivo();
            }
        });
    }

    if (btnRestablecerLive) {
        btnRestablecerLive.addEventListener('click', () => {
            if (inputSearch) {
                inputSearch.value = '';
                inputSearch.focus();
                filtrarSolicitudesEnVivo();
            }
        });
    }
});

