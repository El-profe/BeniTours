document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('modalPromocion');
    const form = document.getElementById('formPromo');
    const btnNuevaPromo = document.getElementById('btnNuevaPromo');
    const promoId = document.getElementById('promoId');
    const title = document.getElementById('promoTitulo');
    const discount = document.getElementById('promoDescuento');
    const description = document.getElementById('promoDescripcion');
    const tiktokUrl = document.getElementById('promoTiktokUrl');
    const facebookUrl = document.getElementById('promoFacebookUrl');
    const instagramUrl = document.getElementById('promoInstagramUrl');
    const fechaInicio = document.getElementById('promoFechaInicio');
    const fechaFin = document.getElementById('promoFechaFin');
    const flyer = document.getElementById('promoFlyer');
    const quitarImg = document.getElementById('promoQuitarImagen');

    // UI modal elements
    const modalTitle = document.getElementById('promoModalTitulo');
    const modalSubtitle = document.getElementById('promoModalSubtitulo');
    const modalHeaderIcon = document.getElementById('promoHeaderIcon');
    const btnSubmitText = document.getElementById('promoBtnSubmitText');
    const btnIcon = document.getElementById('promoBtnIcon');

    // Preview elements
    const previewTitle = document.getElementById('promoPreviewTitle');
    const previewDiscount = document.getElementById('promoPreviewDiscount');
    const previewDescription = document.getElementById('promoPreviewDescription');
    const previewImage = document.getElementById('promoPreviewImage');
    const previewImageIcon = document.getElementById('promoPreviewImageIcon');
    const uploadZone = document.getElementById('promoUploadZone');
    const uploadLabel = document.getElementById('promoUploadLabel');
    const characterCount = document.getElementById('promoDescriptionCount');

    // Box de imagen actual
    const imgActualBox = document.getElementById('promoImgActualBox');
    const imgActualNombre = document.getElementById('promoImgActualNombre');
    const btnQuitarImgActual = document.getElementById('btnQuitarImgActual');

    if (!form || !modalEl) return;

    const modalInstance = window.bootstrap?.Modal ? window.bootstrap.Modal.getOrCreateInstance(modalEl) : null;

    const updatePreview = () => {
        if (previewTitle) previewTitle.textContent = title.value.trim() || 'Título de la promoción';
        if (previewDiscount) previewDiscount.textContent = discount.value.trim() || 'Oferta especial';
        if (previewDescription) previewDescription.textContent = description.value.trim() || 'Descripción de la promoción...';
        if (characterCount) characterCount.textContent = `${description.value.length}/500`;
    };

    [title, discount, description].forEach((field) => {
        if (field) field.addEventListener('input', updatePreview);
    });

    const resetFormParaNuevo = () => {
        form.reset();
        if (promoId) promoId.value = '';
        if (quitarImg) quitarImg.value = '0';
        if (tiktokUrl) tiktokUrl.value = '';
        if (facebookUrl) facebookUrl.value = '';
        if (instagramUrl) instagramUrl.value = '';
        
        // Fechas por defecto: hoy y en 15 días
        const hoy = new Date().toISOString().split('T')[0];
        const en15dias = new Date(Date.now() + 15 * 86400000).toISOString().split('T')[0];
        if (fechaInicio) fechaInicio.value = hoy;
        if (fechaFin) fechaFin.value = en15dias;

        if (modalTitle) modalTitle.textContent = 'Lanzar Promoción';
        if (modalSubtitle) modalSubtitle.textContent = 'Publica ofertas que atraigan visitantes y aumenten tus ventas.';
        if (modalHeaderIcon) modalHeaderIcon.innerHTML = '<i class="bi bi-stars"></i>';
        if (btnSubmitText) btnSubmitText.textContent = 'Publicar promoción';
        if (btnIcon) btnIcon.className = 'bi bi-megaphone-fill me-2';

        // Reset vista previa de imagen
        if (previewImage) {
            previewImage.src = '';
            previewImage.classList.add('d-none');
        }
        if (previewImageIcon) previewImageIcon.classList.remove('d-none');
        if (uploadZone) uploadZone.classList.remove('is-selected');
        if (uploadLabel) uploadLabel.textContent = 'Haz clic para subir un flyer o fotografía';
        
        if (imgActualBox) imgActualBox.classList.add('d-none');
        if (flyer) flyer.value = '';

        updatePreview();
    };

    if (btnNuevaPromo) {
        btnNuevaPromo.addEventListener('click', (e) => {
            e.preventDefault();
            resetFormParaNuevo();
            modalInstance?.show();
        });
    }

    // Botones "Editar" en las tarjetas de promociones
    document.querySelectorAll('.btn-editar-promo').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const rawData = btn.getAttribute('data-promo');
            if (!rawData) return;

            let data;
            try {
                data = JSON.parse(rawData);
            } catch (err) {
                console.error('Error parseando datos:', err);
                return;
            }

            // Cambiar modo a Edición
            if (promoId) promoId.value = data.id_promocion;
            if (quitarImg) quitarImg.value = '0';
            if (title) title.value = data.titulo || '';
            if (discount) discount.value = data.descuento_texto || '';
            if (description) description.value = data.descripcion || '';
            if (tiktokUrl) tiktokUrl.value = data.tiktok_url || '';
            if (facebookUrl) facebookUrl.value = data.facebook_url || '';
            if (instagramUrl) instagramUrl.value = data.instagram_url || '';
            if (fechaInicio) fechaInicio.value = data.fecha_inicio || '';
            if (fechaFin) fechaFin.value = data.fecha_fin || '';

            if (modalTitle) modalTitle.textContent = 'Editar Promoción';
            if (modalSubtitle) modalSubtitle.textContent = 'Modifica los datos, condiciones o imagen de tu promoción.';
            if (modalHeaderIcon) modalHeaderIcon.innerHTML = '<i class="bi bi-pencil-square"></i>';
            if (btnSubmitText) btnSubmitText.textContent = 'Guardar Cambios';
            if (btnIcon) btnIcon.className = 'bi bi-check-circle-fill me-2';

            // Manejo de imagen existente
            if (data.imagen_archivo) {
                const baseUrl = form.dataset.baseUrl || window.location.origin;
                const imgUrl = data.imagen_url || `${baseUrl}/imagen?f=${encodeURIComponent(data.imagen_archivo)}`;
                if (previewImage) {
                    previewImage.src = imgUrl;
                    previewImage.classList.remove('d-none');
                    previewImage.onerror = () => {
                        previewImage.removeAttribute('src');
                        previewImage.classList.add('d-none');
                        if (previewImageIcon) previewImageIcon.classList.remove('d-none');
                    };
                }
                if (previewImageIcon) previewImageIcon.classList.add('d-none');
                
                if (imgActualBox) {
                    imgActualBox.classList.remove('d-none');
                    if (imgActualNombre) imgActualNombre.textContent = data.imagen_archivo;
                }
                if (uploadLabel) uploadLabel.textContent = 'Haz clic si deseas reemplazar la imagen actual';
            } else {
                if (previewImage) {
                    previewImage.src = '';
                    previewImage.classList.add('d-none');
                }
                if (previewImageIcon) previewImageIcon.classList.remove('d-none');
                if (imgActualBox) imgActualBox.classList.add('d-none');
                if (uploadLabel) uploadLabel.textContent = 'Haz clic para subir un flyer o fotografía';
            }

            if (uploadZone) uploadZone.classList.remove('is-selected');
            if (flyer) flyer.value = '';

            updatePreview();
            modalInstance?.show();
        });
    });

    // Acción para quitar la imagen actual guardada
    if (btnQuitarImgActual) {
        btnQuitarImgActual.addEventListener('click', () => {
            if (quitarImg) quitarImg.value = '1';
            if (imgActualBox) imgActualBox.classList.add('d-none');
            if (previewImage) {
                previewImage.src = '';
                previewImage.classList.add('d-none');
            }
            if (previewImageIcon) previewImageIcon.classList.remove('d-none');
            if (uploadLabel) uploadLabel.textContent = 'Haz clic para subir un nuevo flyer o fotografía';
            if (uploadZone) uploadZone.classList.remove('is-selected');
            if (flyer) flyer.value = '';
        });
    }

    // Evento al seleccionar un archivo local
    if (flyer) {
        flyer.addEventListener('change', () => {
            const [file] = flyer.files;
            if (!file) return;

            if (quitarImg) quitarImg.value = '0';
            const imageUrl = URL.createObjectURL(file);
            if (previewImage) {
                previewImage.src = imageUrl;
                previewImage.classList.remove('d-none');
            }
            if (previewImageIcon) previewImageIcon.classList.add('d-none');
            if (uploadZone) uploadZone.classList.add('is-selected');
            if (uploadLabel) uploadLabel.textContent = `Archivo seleccionado: ${file.name}`;
            if (imgActualBox) imgActualBox.classList.add('d-none');
        });
    }

    updatePreview();
});
