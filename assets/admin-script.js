jQuery(document).ready(function($) {
    let currentMarquee = '';
    let imageCounter = 0;
    
    // Inicializar sortable
    function initSortable() {
        $('#images-container').sortable({
            handle: '.imp-drag-handle',
            placeholder: 'ui-state-highlight',
            tolerance: 'pointer',
            axis: 'y'
        });
    }
    
    // Cargar marquesina
    $('#load-marquee').click(function() {
        const selectedMarquee = $('#marquee-name-select').val();
        const newMarqueeName = $('#new-marquee-name').val().trim();
        
        if (newMarqueeName) {
            currentMarquee = newMarqueeName;
            $('#current-marquee-name').text(newMarqueeName);
            clearImages();
        } else if (selectedMarquee) {
            currentMarquee = selectedMarquee;
            $('#current-marquee-name').text(selectedMarquee);
            loadMarqueeImages(selectedMarquee);
        } else {
            alert('Selecciona una marquesina o introduce el nombre de una nueva.');
            return;
        }
        
        $('#new-marquee-name').val('');
    });
    
    // Eliminar marquesina
    $('#delete-marquee').click(function() {
        const selectedMarquee = $('#marquee-name-select').val();
        
        if (!selectedMarquee) {
            alert('Selecciona una marquesina para eliminar.');
            return;
        }
        
        if (!confirm('¿Estás seguro de que deseas eliminar esta marquesina? Esta acción no se puede deshacer.')) {
            return;
        }
        
        $.ajax({
            url: imp_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_marquee',
                marquee_name: selectedMarquee,
                nonce: imp_admin_ajax.nonce
            },
            beforeSend: function() {
                $('#imp-admin-container').addClass('imp-loading');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    // Recargar la página para actualizar la lista
                    location.reload();
                } else {
                    alert('Error al eliminar la marquesina.');
                }
            },
            error: function() {
                alert('Error de conexión.');
            },
            complete: function() {
                $('#imp-admin-container').removeClass('imp-loading');
            }
        });
    });
    
    // Agregar imagen
    $('#add-image').click(function(e) {
        e.preventDefault();
        
        if (!currentMarquee) {
            alert('Primero debes cargar o crear una marquesina.');
            return;
        }
        
        const mediaUploader = wp.media({
            title: 'Seleccionar Imagen',
            button: {
                text: 'Usar esta imagen'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            addImageItem(attachment.url, '', '', ++imageCounter);
        });
        
        mediaUploader.open();
    });
    
    // Guardar marquesina
    $('#save-marquee').click(function() {
        if (!currentMarquee) {
            alert('No hay ninguna marquesina cargada.');
            return;
        }
        
        const imagesData = [];
        $('#images-container .imp-image-item').each(function(index) {
            const $item = $(this);
            imagesData.push({
               // url: $item.find('img').attr('src'),
                url: $item.find('.image-url').val(),
                title: $item.find('.image-title').val(),
                subtitle: $item.find('.image-subtitle').val(),
                story: $item.find('.image-story').val(),
                email: $item.find('.image-email').val(),
                phone: $item.find('.image-phone').val(),
                whatsapp: $item.find('.image-whatsapp').val(),
                instagram: $item.find('.image-instagram').val(),
                facebook: $item.find('.image-facebook').val(),
                tiktok: $item.find('.image-tiktok').val(),
                telegram: $item.find('.image-telegram').val(),
                linkedin: $item.find('.image-linkedin').val()
            });
        });
        
        if (imagesData.length === 0) {
            alert('No hay imágenes para guardar.');
            return;
        }
        
        $.ajax({
            url: imp_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'save_marquee_contacts',
                marquee_name: currentMarquee,
                images_data: JSON.stringify(imagesData),
                nonce: imp_admin_ajax.nonce
            },
            beforeSend: function() {
                $('#save-marquee').prop('disabled', true).text('Guardando...');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    // Actualizar la lista de marquesinas si es nueva
                    updateMarqueeSelect();
                } else {
                    alert('Error al guardar la marquesina.');
                }
            },
            error: function() {
                alert('Error de conexión.');
            },
            complete: function() {
                $('#save-marquee').prop('disabled', false).text('Guardar Marquesina');
            }
        });
    });
    
    // Cargar imágenes de marquesina
    function loadMarqueeImages(marqueeName) {
        $.ajax({
            url: imp_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_marquee_contacts',
                marquee_name: marqueeName,
                nonce: imp_admin_ajax.nonce
            },
            beforeSend: function() {
                $('#images-container').addClass('imp-loading');
            },
            success: function(response) {
                if (response.success && response.data) {
                    clearImages();
                    response.data.forEach(function(image, index) {
                        const contactData = {
                            phone: image.phone,
                            whatsapp: image.whatsapp,
                            instagram: image.instagram,
                            facebook: image.facebook,
                            tiktok: image.tiktok,
                            telegram: image.telegram,
                            linkedin: image.linkedin,
                            story: image.story
                        };
                        addImageItem(image.image_url, image.title, image.subtitle, index + 1, contactData);
                    });
                    imageCounter = response.data.length;
                } else {
                    clearImages();
                }
            },
            error: function() {
                alert('Error al cargar las imágenes.');
            },
            complete: function() {
                $('#images-container').removeClass('imp-loading');
            }
        });
    }
    
    // Agregar elemento de imagen al contenedor
    function addImageItem(url, title, subtitle, id, contactData) {
        contactData = contactData || {};
        hideNoImagesMessage();
        
        const template = $('#image-item-template').html();
        const html = template
            .replace(/{{id}}/g, id)
            .replace(/{{url}}/g, url)
            .replace(/{{title}}/g, title || '')
            .replace(/{{subtitle}}/g, subtitle || '')
            .replace(/{{phone}}/g, contactData.phone || '')
            .replace(/{{whatsapp}}/g, contactData.whatsapp || '')
            .replace(/{{instagram}}/g, contactData.instagram || '')
            .replace(/{{facebook}}/g, contactData.facebook || '')
            .replace(/{{tiktok}}/g, contactData.tiktok || '')
            .replace(/{{telegram}}/g, contactData.telegram || '')
            .replace(/{{linkedin}}/g, contactData.linkedin || '')
            .replace(/{{story}}/g, contactData.story || '');

        $('#images-container').append(html);
        initSortable();
    }
    
    // Eliminar imagen
    $(document).on('click', '.remove-image', function() {
        $(this).closest('.imp-image-item').remove();
        
        if ($('#images-container .imp-image-item').length === 0) {
            showNoImagesMessage();
        }
    });
    
    // Limpiar imágenes
    function clearImages() {
        $('#images-container').empty();
        showNoImagesMessage();
        imageCounter = 0;
    }
    
    // Mostrar mensaje de no hay imágenes
    function showNoImagesMessage() {
        $('#images-container').html('<div class="imp-no-images"><p>No hay imágenes en esta marquesina. Haz clic en "Agregar Imagen" para comenzar.</p></div>');
    }
    
    // Ocultar mensaje de no hay imágenes
    function hideNoImagesMessage() {
        $('#images-container .imp-no-images').remove();
    }
    
    // Actualizar select de marquesinas
    function updateMarqueeSelect() {
        // Esta función se puede implementar para recargar la lista dinámicamente
        // Por simplicidad, se puede recargar la página
    }

    // Cambiar imagen individual
    $(document).on('click', '.imp-change-image', function (e) {
        e.preventDefault();

        const $item = $(this).closest('.imp-image-item');
        const $img = $item.find('img');
        const $input = $item.find('.image-url');

        const mediaUploader = wp.media({
            title: 'Cambiar imagen del contacto',
            button: {
                text: 'Usar esta imagen'
            },
            multiple: false
        });

        mediaUploader.on('select', function () {
            const attachment = mediaUploader
                .state()
                .get('selection')
                .first()
                .toJSON();

            $img.attr('src', attachment.url);
            $input.val(attachment.url);
        });

        mediaUploader.open();
    });

    
    // Inicializar sortable al cargar
    initSortable();
});