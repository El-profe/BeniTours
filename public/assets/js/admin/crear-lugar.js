document.addEventListener('DOMContentLoaded', () => {
    const inputFotos = document.getElementById('fotografias');
    const previews = document.getElementById('previsualizacionFotos');
    const inputPortada = document.getElementById('fotoPortada');
    const inputCoordenadas = document.getElementById('coordenadasGps');
    const panelManual = document.getElementById('ubicacionManual');
    const panelMapa = document.getElementById('ubicacionMapa');
    const estadoMapa = document.getElementById('estadoMapaNuevaFicha');
    const botonesModo = document.querySelectorAll('[data-location-mode]');
    const objectUrlsFotos = new Set();
    let mapa;
    let marcador;

    let archivosFotos = [];

    const elegirPortada = (indice) => {
        inputPortada.value = String(indice);
        previews.querySelectorAll('.image-preview-card').forEach((elemento) => {
            elemento.classList.toggle('is-cover', Number(elemento.dataset.fotoIndice) === indice);
        });
    };

    const renderizarGaleria = () => {
        objectUrlsFotos.forEach((url) => URL.revokeObjectURL(url));
        objectUrlsFotos.clear();
        previews.replaceChildren();

        if (!archivosFotos.length) {
            actualizarPreviewPortada('');
            if (inputPortada) inputPortada.value = '0';
            return;
        }

        archivosFotos.forEach((archivo, indice) => {
            const tarjeta = document.createElement('div');
            tarjeta.className = 'image-preview-card';
            tarjeta.dataset.fotoIndice = String(indice);
            tarjeta.setAttribute('role', 'button');
            tarjeta.setAttribute('tabindex', '0');
            tarjeta.setAttribute('aria-label', `Foto ${indice + 1}: ${archivo.name}. Clic para portada.`);

            const imagen = document.createElement('img');
            imagen.alt = `Previsualización de ${archivo.name}`;
            const objectUrl = URL.createObjectURL(archivo);
            objectUrlsFotos.add(objectUrl);
            imagen.src = objectUrl;

            // Badge de Portada
            const etiqueta = document.createElement('span');
            etiqueta.innerHTML = '<i class="bi bi-star-fill"></i> Portada';

            // Botón flotante para eliminar foto
            const btnEliminar = document.createElement('button');
            btnEliminar.type = 'button';
            btnEliminar.className = 'btn-remove-foto';
            btnEliminar.title = 'Quitar esta fotografía';
            btnEliminar.setAttribute('aria-label', `Quitar fotografía ${archivo.name}`);
            btnEliminar.innerHTML = '<i class="bi bi-x-lg"></i>';
            btnEliminar.addEventListener('click', (e) => {
                e.stopPropagation();
                eliminarFoto(indice);
            });

            // Al hacer clic en la tarjeta, seleccionar como portada
            tarjeta.addEventListener('click', () => {
                elegirPortadaConPreview(indice);
            });

            tarjeta.append(imagen, etiqueta, btnEliminar);
            previews.append(tarjeta);
        });

        // Asegurar que la portada actual sea válida
        let portadaActual = parseInt(inputPortada.value, 10);
        if (isNaN(portadaActual) || portadaActual < 0 || portadaActual >= archivosFotos.length) {
            portadaActual = 0;
        }
        elegirPortadaConPreview(portadaActual);
    };

    const sincronizarInputFotos = () => {
        if (typeof DataTransfer !== 'undefined') {
            const dt = new DataTransfer();
            archivosFotos.forEach(file => dt.items.add(file));
            inputFotos.files = dt.files;
        }
    };

    const eliminarFoto = (indiceAEliminar) => {
        archivosFotos.splice(indiceAEliminar, 1);
        sincronizarInputFotos();
        renderizarGaleria();
    };

    inputFotos?.addEventListener('change', async () => {
        let nuevosArchivos = Array.from(inputFotos.files || []).slice(0, 6);
        if (!nuevosArchivos.length) {
            return;
        }

        // Si CompresorImagen está disponible, optimizar en el cliente antes de la subida
        if (window.CompresorImagen) {
            previews.innerHTML = '<div class="col-12 py-3 text-center text-muted small"><span class="spinner-border spinner-border-sm me-2 text-success"></span> Optimizando fotos en el navegador para subida rápida...</div>';
            const resultados = await Promise.all(nuevosArchivos.map(a => window.CompresorImagen.comprimir(a, { maxDimension: 1280, calidad: 0.82 })));
            if (typeof DataTransfer !== 'undefined') {
                const dt = new DataTransfer();
                resultados.forEach(r => dt.items.add(r.file));
                inputFotos.files = dt.files;
            }
            nuevosArchivos = Array.from(inputFotos.files || []);
        }

        archivosFotos = nuevosArchivos;
        renderizarGaleria();
    });

    const colocarMarcador = (latitud, longitud, actualizarCampo = true) => {
        if (!mapa || !window.L) return;
        if (marcador) marcador.setLatLng([latitud, longitud]);
        else marcador = L.marker([latitud, longitud], { draggable: true }).addTo(mapa);
        marcador.on('dragend', () => {
            const punto = marcador.getLatLng();
            colocarMarcador(punto.lat, punto.lng);
        });
        if (actualizarCampo) inputCoordenadas.value = `${Number(latitud).toFixed(6)}, ${Number(longitud).toFixed(6)}`;
        estadoMapa.textContent = `Ubicación seleccionada: ${Number(latitud).toFixed(6)}, ${Number(longitud).toFixed(6)}.`;
    };

    const iniciarMapa = () => {
        if (mapa || !window.L) return;
        mapa = L.map('mapaNuevaFicha').setView([-14.8333, -64.9000], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(mapa);
        mapa.on('click', (evento) => colocarMarcador(evento.latlng.lat, evento.latlng.lng));
        const coincidencia = (inputCoordenadas.value || '').match(/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/);
        if (coincidencia) colocarMarcador(Number(coincidencia[1]), Number(coincidencia[2]), false);
    };

    botonesModo.forEach((boton) => boton.addEventListener('click', () => {
        const mapaActivo = boton.dataset.locationMode === 'mapa';
        botonesModo.forEach((item) => item.classList.toggle('active', item === boton));
        panelManual.classList.toggle('d-none', mapaActivo);
        panelMapa.classList.toggle('d-none', !mapaActivo);
        if (mapaActivo) {
            iniciarMapa();
            setTimeout(() => mapa?.invalidateSize(), 0);
        }
    }));

    inputCoordenadas?.addEventListener('change', () => {
        const coincidencia = inputCoordenadas.value.match(/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/);
        if (coincidencia && mapa) colocarMarcador(Number(coincidencia[1]), Number(coincidencia[2]), false);
    });

    // Control de Aprovisionamiento Comercial
    const radiosTipo = document.querySelectorAll('input[name="tipo_lugar"]');
    const seccionComercial = document.getElementById('seccionAprovisionamientoComercial');
    const switchCrear = document.getElementById('switchCrearCuenta');
    const camposAprov = document.getElementById('camposAprovisionamiento');

    function actualizarVisibilidadComercial() {
        const esComercial = document.querySelector('input[name="tipo_lugar"]:checked')?.value === 'COMERCIAL';
        if (seccionComercial) {
            seccionComercial.classList.toggle('d-none', !esComercial);
        }
        // Actualizar Badge en Live Preview
        const previewBadge = document.getElementById('previewTypeBadge');
        if (previewBadge) {
            if (esComercial) {
                previewBadge.className = 'live-preview-badge-type bg-purple text-white';
                previewBadge.style.backgroundColor = '#6f42c1';
                previewBadge.innerHTML = '<i class="bi bi-shop me-1"></i>Comercial';
            } else {
                previewBadge.className = 'live-preview-badge-type bg-success text-white';
                previewBadge.style.backgroundColor = '#2A9D8F';
                previewBadge.innerHTML = '<i class="bi bi-tree me-1"></i>Público';
            }
        }
    }

    radiosTipo.forEach(radio => {
        radio.addEventListener('change', actualizarVisibilidadComercial);
    });
    actualizarVisibilidadComercial();

    switchCrear?.addEventListener('change', () => {
        if (camposAprov) {
            camposAprov.classList.toggle('d-none', !switchCrear.checked);
        }
    });

    // Sincronización en Tiempo Real con la Live Preview Card
    const inputNombre = document.getElementById('inputNombreLugar');
    const previewTitle = document.getElementById('previewTitle');
    const selectMunicipio = document.getElementById('selectMunicipio');
    const previewMunicipio = document.getElementById('previewMunicipio');
    const inputDireccion = document.getElementById('inputDireccion');
    const previewDireccion = document.getElementById('previewDireccion');
    const inputDesc = document.getElementById('inputDescripcionLugar');
    const previewDesc = document.getElementById('previewDesc');
    const radiosCat = document.querySelectorAll('input[name="id_categoria"]');
    const previewCatBadge = document.getElementById('previewCatBadge');
    const previewPlaceholder = document.getElementById('previewPlaceholderIcon');
    const previewImg = document.getElementById('previewImgElement');

    inputNombre?.addEventListener('input', () => {
        const val = inputNombre.value.trim();
        if (previewTitle) previewTitle.textContent = val || 'Nombre del Establecimiento';
    });

    selectMunicipio?.addEventListener('change', () => {
        const txt = selectMunicipio.options[selectMunicipio.selectedIndex]?.text.split('(')[0]?.trim();
        if (previewMunicipio) previewMunicipio.textContent = txt || 'Trinidad';
    });

    inputDireccion?.addEventListener('input', () => {
        const val = inputDireccion.value.trim();
        if (previewDireccion) previewDireccion.textContent = val || 'Dirección pendiente';
    });

    inputDesc?.addEventListener('input', () => {
        const val = inputDesc.value.trim();
        if (previewDesc) previewDesc.textContent = val || 'Descripción preliminar de la ficha turística...';
    });

    radiosCat.forEach(radio => {
        radio.addEventListener('change', () => {
            const catNombre = radio.dataset.nombreCat || 'General';
            if (previewCatBadge) previewCatBadge.textContent = catNombre;
            if (typeof window.alternarTarifasPorCategoria === 'function') {
                window.alternarTarifasPorCategoria(radio.value);
            }
        });
    });

    const catSeleccionadaInicial = document.querySelector('input[name="id_categoria"]:checked')?.value;
    if (catSeleccionadaInicial && typeof window.alternarTarifasPorCategoria === 'function') {
        window.alternarTarifasPorCategoria(catSeleccionadaInicial);
    }

    // Actualizar foto en la preview al seleccionar portada
    const actualizarPreviewPortada = (imgSrc) => {
        if (!previewImg) return;
        if (imgSrc) {
            previewImg.src = imgSrc;
            previewImg.style.display = 'block';
            if (previewPlaceholder) previewPlaceholder.style.display = 'none';
        } else {
            previewImg.style.display = 'none';
            if (previewPlaceholder) previewPlaceholder.style.display = 'block';
        }
    };

    // Enganchar con la selección de fotos
    const originalElegirPortada = elegirPortada;
    const elegirPortadaConPreview = (indice) => {
        originalElegirPortada(indice);
        const cardTarget = previews.querySelector(`[data-foto-indice="${indice}"] img`);
        if (cardTarget && cardTarget.src) {
            actualizarPreviewPortada(cardTarget.src);
        }
    };

    // ==========================================
    // Constructor Visual de Días y Horarios
    // ==========================================
    const inputHorario = document.getElementById('inputHorarioAtencion');
    const previewHorario = document.getElementById('previewHorarioSintetizado');
    const panelConstructor = document.getElementById('panelHorarioConstructor');
    const btnModoManual = document.getElementById('btnAlternarModoHorarioManual');
    const txtModoHorario = document.getElementById('txtModoHorario');
    const switch24h = document.getElementById('switchHorario24h');
    const checkboxesDias = document.querySelectorAll('.btn-dia-check');
    const presetDiasBtns = document.querySelectorAll('[data-preset-dias]');
    const horaApertura1 = document.getElementById('horaApertura1');
    const horaCierre1 = document.getElementById('horaCierre1');
    const filaTurno2 = document.getElementById('filaHorasTurno2');
    const btnToggleTurno2 = document.getElementById('btnToggleTurno2');
    const btnQuitarTurno2 = document.getElementById('btnQuitarTurno2');
    const horaApertura2 = document.getElementById('horaApertura2');
    const horaCierre2 = document.getElementById('horaCierre2');

    let modoHorarioManual = false;
    let turno2Activo = false;

    const DIAS_ORDEN = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

    const formatearRangoDias = (diasSeleccionados) => {
        if (!diasSeleccionados.length) return 'Cerrado temporalmente';
        if (diasSeleccionados.length === 7) return 'Todos los días';

        // Ordenar según el orden de la semana
        const diasOrdenados = diasSeleccionados.slice().sort((a, b) => DIAS_ORDEN.indexOf(a) - DIAS_ORDEN.indexOf(b));

        // Casos comunes
        const str = diasOrdenados.join(',');
        if (str === 'Lun,Mar,Mié,Jue,Vie') return 'Lun - Vie';
        if (str === 'Lun,Mar,Mié,Jue,Vie,Sáb') return 'Lun - Sáb';
        if (str === 'Sáb,Dom') return 'Sáb y Dom';

        // Verificar si es un rango continuo
        const indices = diasOrdenados.map(d => DIAS_ORDEN.indexOf(d));
        let esContinuo = true;
        for (let i = 1; i < indices.length; i++) {
            if (indices[i] !== indices[i - 1] + 1) {
                esContinuo = false;
                break;
            }
        }

        if (esContinuo && diasOrdenados.length >= 3) {
            return `${diasOrdenados[0]} - ${diasOrdenados[diasOrdenados.length - 1]}`;
        }

        // Días salteados o no continuos
        return diasOrdenados.join(', ');
    };

    const actualizarHorarioSintetizado = () => {
        if (modoHorarioManual) return;

        if (switch24h?.checked) {
            const texto = 'Abierto las 24 horas';
            if (inputHorario) inputHorario.value = texto;
            if (previewHorario) previewHorario.textContent = texto;
            return;
        }

        const diasSeleccionados = [];
        checkboxesDias.forEach(cb => {
            if (cb.checked) diasSeleccionados.push(cb.value);
        });

        const diasTexto = formatearRangoDias(diasSeleccionados);
        if (diasSeleccionados.length === 0) {
            const texto = 'Sin horario definido';
            if (inputHorario) inputHorario.value = texto;
            if (previewHorario) previewHorario.textContent = texto;
            return;
        }

        const hIni1 = horaApertura1?.value || '08:00';
        const hFin1 = horaCierre1?.value || '22:00';
        let rangoHoras = `${hIni1} - ${hFin1}`;

        if (turno2Activo) {
            const hIni2 = horaApertura2?.value || '15:00';
            const hFin2 = horaCierre2?.value || '22:00';
            rangoHoras = `${hIni1} - ${hFin1}, ${hIni2} - ${hFin2}`;
        }

        const textoFinal = `${diasTexto}: ${rangoHoras}`;
        if (inputHorario) inputHorario.value = textoFinal;
        if (previewHorario) previewHorario.textContent = textoFinal;
    };

    // Listeners de checkboxes de días
    checkboxesDias.forEach(cb => {
        cb.addEventListener('change', actualizarHorarioSintetizado);
    });

    // Listeners de horas
    horaApertura1?.addEventListener('change', actualizarHorarioSintetizado);
    horaCierre1?.addEventListener('change', actualizarHorarioSintetizado);
    horaApertura2?.addEventListener('change', actualizarHorarioSintetizado);
    horaCierre2?.addEventListener('change', actualizarHorarioSintetizado);

    // Atajos de presets
    presetDiasBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const preset = btn.dataset.presetDias;
            checkboxesDias.forEach(cb => {
                if (preset === 'L-V') {
                    cb.checked = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'].includes(cb.value);
                } else if (preset === 'L-S') {
                    cb.checked = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'].includes(cb.value);
                } else if (preset === 'S-D') {
                    cb.checked = ['Sáb', 'Dom'].includes(cb.value);
                } else if (preset === 'TODOS') {
                    cb.checked = true;
                }
            });
            actualizarHorarioSintetizado();
        });
    });

    // Turno 2
    btnToggleTurno2?.addEventListener('click', () => {
        turno2Activo = true;
        if (filaTurno2) filaTurno2.classList.remove('d-none');
        if (btnToggleTurno2) btnToggleTurno2.classList.add('d-none');
        actualizarHorarioSintetizado();
    });

    btnQuitarTurno2?.addEventListener('click', () => {
        turno2Activo = false;
        if (filaTurno2) filaTurno2.classList.add('d-none');
        if (btnToggleTurno2) btnToggleTurno2.classList.remove('d-none');
        actualizarHorarioSintetizado();
    });

    // Switch 24 horas
    switch24h?.addEventListener('change', () => {
        if (panelConstructor) {
            panelConstructor.style.opacity = switch24h.checked ? '0.35' : '1';
            panelConstructor.style.pointerEvents = switch24h.checked ? 'none' : 'auto';
        }
        actualizarHorarioSintetizado();
    });

    // Alternar modo manual
    btnModoManual?.addEventListener('click', () => {
        modoHorarioManual = !modoHorarioManual;
        if (modoHorarioManual) {
            if (panelConstructor) panelConstructor.classList.add('d-none');
            if (inputHorario) inputHorario.classList.remove('d-none');
            if (txtModoHorario) txtModoHorario.textContent = 'Usar constructor';
        } else {
            if (panelConstructor) panelConstructor.classList.remove('d-none');
            if (inputHorario) inputHorario.classList.add('d-none');
            if (txtModoHorario) txtModoHorario.textContent = 'Modo manual';
            actualizarHorarioSintetizado();
        }
    });

    inputHorario?.addEventListener('input', () => {
        if (modoHorarioManual && previewHorario) {
            previewHorario.textContent = inputHorario.value || 'Sin horario definido';
        }
    });

    // Inicializar síntesis de horario
    actualizarHorarioSintetizado();

    window.addEventListener('beforeunload', () => {
        objectUrlsFotos.forEach((url) => URL.revokeObjectURL(url));
    });
});


