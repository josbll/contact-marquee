jQuery(document).ready(function($) {

    let lastWindowWidth = window.innerWidth;
    let lastWindowHeight = window.innerHeight;

    // Función para calcular el ancho total del contenido
    function calculateContentWidth($container) {
        let totalWidth = 0;
        $container.find('.imp-marquee-item').each(function() {
            const $item = $(this);
            const itemWidth = $item.outerWidth(true); // incluye margin
            totalWidth += itemWidth;
        });
        return totalWidth;
    }
    
    // Función para recalcular animación considerando anchos individuales
    function recalculateAnimation($container) {
        const speed = parseFloat($container.data('speed')) || parseFloat(imp_ajax.marquee_speed) || 50;
        const direction = $container.data('direction') || imp_ajax.marquee_direction || 'left';
        
        // Obtener dimensiones
        const containerWidth = $container.width();
        const $content = $container.find('.imp-marquee-content');
        
        // Esperar a que las imágenes se carguen completamente
        const images = $content.find('img');
        let loadedImages = 0;
        const totalImages = images.length;
        
        if (totalImages === 0) {
            return;
        }
        
        function checkAllImagesLoaded() {
            loadedImages++;
            if (loadedImages === totalImages) {
                calculateAndApplyAnimation();
            }
        }
        
        function calculateAndApplyAnimation() {
            // Calcular ancho total del contenido
            const contentWidth = calculateContentWidth($container);
            
            // La distancia total que debe recorrer es el ancho del contenedor + ancho del contenido
            // para que la última imagen desaparezca completamente
            const totalDistance = containerWidth + contentWidth;
            
            // Calcular duración basada en la velocidad (píxeles por segundo)
            const duration = totalDistance / speed;
            
            // Limpiar animaciones existentes
            $content.css('animation', 'none');
            
            // Forzar reflow
            $content[0].offsetHeight;
            
            // Aplicar nueva animación
            const animationName = direction === 'right' ? 'marqueeRight' : 'marqueeLeft';
            $content.css({
                'animation': `${animationName} ${duration}s linear infinite`
            });
            
            // Crear keyframes dinámicos si no existen
            createDynamicKeyframes(containerWidth, contentWidth, direction);

            // Escuchar cada iteración de la animación
			$content.on('animationiteration', function () {
                        // Calcular ancho total del contenido
                    let contentWidth = calculateContentWidth($container);
                    
                    // La distancia total que debe recorrer es el ancho del contenedor + ancho del contenido
                    // para que la última imagen desaparezca completamente
                    const totalDistance = containerWidth + contentWidth;
                    
                    // Calcular duración basada en la velocidad (píxeles por segundo)
                    const duration = totalDistance / speed;
                    
                    // Limpiar animaciones existentes
                    $content.css('animation', 'none');
                    
                    // Forzar reflow
                    $content[0].offsetHeight;
                    
                    // Aplicar nueva animación
                    const animationName = direction === 'right' ? 'marqueeRight' : 'marqueeLeft';
                    $content.css({
                        'animation': `${animationName} ${duration}s linear infinite`
                    });
                    
                    // Crear keyframes dinámicos si no existen
                    createDynamicKeyframes(containerWidth, contentWidth, direction);
                    
			});
        }
        
        // Verificar si las imágenes ya están cargadas
        images.each(function() {
            if (this.complete) {
                checkAllImagesLoaded();
            } else {
                $(this).on('load', checkAllImagesLoaded);
                $(this).on('error', checkAllImagesLoaded); // También contar errores

            }
        });
    }
    
    // Función para crear keyframes dinámicos
    function createDynamicKeyframes(containerWidth, contentWidth, direction) {
        const styleId = 'imp-dynamic-keyframes';
        let $style = $('#' + styleId);
        
        if ($style.length === 0) {
            $style = $('<style id="' + styleId + '"></style>').appendTo('head');
        }
               
        let keyframes = '';
        
        if (direction === 'right') {
            keyframes = `
                @keyframes marqueeRight {
                    0% { transform: translateX(-${contentWidth}px); }
                    100% { transform: translateX(${containerWidth}px); }
                }
            `;
        } else {
            keyframes = `
                @keyframes marqueeLeft {
                    0% { transform: translateX(${containerWidth}px); }
                    100% { transform: translateX(-${contentWidth}px); }
                }
            `;
        }
        
        $style.html(keyframes);
    }
    
    // Configurar animación para cada marquesina
    $('.imp-marquee-container').each(function() {
        const $container = $(this);
        
        // Configurar posición inicial del contenido
        const $content = $container.find('.imp-marquee-content');
        const direction = $container.data('direction') || imp_ajax.marquee_direction || 'left';
        
        if (direction === 'right') {
            $content.css('transform', 'translateX(-100%)');
        } else {
            $content.css('transform', 'translateX(100%)');
        }
        
        // Calcular animación inicial
        recalculateAnimation($container);
    });
    
    // Event listener para recalcular animación
    $('.imp-marquee-container').on('recalculate-animation', function() {
        recalculateAnimation($(this));
    });
    
    // Funcionalidad de pausa en hover
    $('.imp-marquee-container').hover(
        function() {
            $(this).find('.imp-marquee-content').css('animation-play-state', 'paused');
        },
        function() {
            $(this).find('.imp-marquee-content').css('animation-play-state', 'running');
        }
    );
    
    // Recalcular animaciones cuando cambie el tamaño de la ventana
   /* $(window).on('resize', function() {
        // Debounce para evitar múltiples cálculos
        clearTimeout(window.marqueeResizeTimeout);
        window.marqueeResizeTimeout = setTimeout(function() {
            $('.imp-marquee-container').each(function() {
                recalculateAnimation($(this));
            });
        }, 250);
    }); */

    $(window).on('orientationchange', function() {
    setTimeout(function() {
        lastWindowWidth = window.innerWidth;
        lastWindowHeight = window.innerHeight;

        $('.imp-marquee-container').each(function() {
            recalculateAnimation($(this));
        });
    }, 400);
  });

     
    $(window).on('resize', function() {
            const currentWidth = window.innerWidth;
            const currentHeight = window.innerHeight;

            //  Ignorar resize causado solo por scroll (altura cambia, ancho no)
            if (currentWidth === lastWindowWidth) {
                return;
            }

            lastWindowWidth = currentWidth;
            lastWindowHeight = currentHeight;

            clearTimeout(window.marqueeResizeTimeout);
            window.marqueeResizeTimeout = setTimeout(function() {
                $('.imp-marquee-container').each(function() {
                    recalculateAnimation($(this));
                });
            }, 250);
 });

    
    // Función para actualizar configuración desde el Customizer
    window.updateMarqueeConfig = function(setting, value) {
        $('.imp-marquee-container').each(function() {
            const $container = $(this);
            
            if (setting === 'speed') {
                $container.data('speed', value);
            } else if (setting === 'direction') {
                $container.data('direction', value);
            } else if (setting === 'height') {
                $container.css('height', value + 'px');
                $container.find('.imp-marquee-item img').css('height', value + 'px');
            }
            
            // Recalcular animación
            setTimeout(function() {
                recalculateAnimation($container);
            }, 100);
        });
    };
    
    // Aplicar estilos dinámicos desde las opciones
    function applyDynamicStyles() {
        const titleFontSize = imp_ajax.title_font_size || '16';
        const titleColor = imp_ajax.title_color || '#ffffff';
        const titleFontWeight = imp_ajax.title_font_weight || '600';
        const subtitleFontSize = imp_ajax.subtitle_font_size || '14';
        const subtitleColor = imp_ajax.subtitle_color || '#e6e6e6';
        const subtitleFontWeight = imp_ajax.subtitle_font_weight || '400';
        const contactIconSize = imp_ajax.contact_icon_size || '32';
        const showContactInfo = imp_ajax.show_contact_info;
        
        // Crear estilos dinámicos
        let dynamicCSS = `
            .imp-marquee-text h3 {
                font-size: ${titleFontSize}px !important;
                color: ${titleColor} !important;
                font-weight: ${titleFontWeight} !important;
            }
            .imp-marquee-text p {
                font-size: ${subtitleFontSize}px !important;
                color: ${subtitleColor} !important;
                font-weight: ${subtitleFontWeight} !important;
            }
            .imp-contact-link {
                width: ${contactIconSize}px !important;
                height: ${contactIconSize}px !important;
            }
            .imp-contact-link span {
                font-size: ${contactIconSize * 0.5}px !important;
            }
        `;
        
        if (!showContactInfo) {
            dynamicCSS += `
                .imp-contact-links {
                    display: none !important;
                }
            `;
        }
        
        // Aplicar estilos
        const styleId = 'imp-dynamic-styles';
        let $style = $('#' + styleId);
        
        if ($style.length === 0) {
            $style = $('<style id="' + styleId + '"></style>').appendTo('head');
        }
        
        $style.html(dynamicCSS);
    }


    function getCurrentTranslateX($el) {
        const matrix = window.getComputedStyle($el[0]).transform;
        if (matrix === 'none') return 0;
        return parseFloat(matrix.split(',')[4]);
   }
   function continueMarqueeFromCurrentPosition($container) {
        const $content = $container.find('.imp-marquee-content');

        const speed = parseFloat($container.data('speed')) || 50;
        const direction = $container.data('direction') || 'left';

        let containerWidth = $container.width();
        let contentWidth = calculateContentWidth($container);

        let currentX = getCurrentTranslateX($content);

        let remainingDistance;

        if (direction === 'left') {
            remainingDistance = Math.abs(currentX + contentWidth);
        } else {
            remainingDistance = Math.abs(containerWidth - currentX);
        }

        const duration = remainingDistance / speed;

        // Reset limpio
        $content.css('animation', 'none');
        $content[0].offsetHeight;

        const animationName = direction === 'right' ? 'marqueeRight' : 'marqueeLeft';

        // Keyframes desde posición actual
        const styleId = 'imp-dynamic-keyframes';
        let $style = $('#' + styleId);
        if (!$style.length) {
            $style = $('<style id="' + styleId + '"></style>').appendTo('head');
        }

        $style.html(`
            @keyframes marqueeLeft {
                0% { transform: translateX(${currentX}px); }
                100% { transform: translateX(-${contentWidth}px); }
            }
            @keyframes marqueeRight {
                0% { transform: translateX(${currentX}px); }
                100% { transform: translateX(${containerWidth}px); }
            }
        `);

        $content.css({
            animation: `${animationName} ${duration}s linear infinite`
        });
  }

   function accelerateMarquee($container, direction) {
    const $content = $container.find('.imp-marquee-content');

    const baseSpeed = parseFloat($container.data('speed')) || 50;
    //const boostSpeed = baseSpeed * 3; // aceleración
    const BOOST_MULTIPLIER = 20;   // 🔥 más rápido
    const BOOST_DURATION   = 350; // ⏱️ más corto

    const boostSpeed = baseSpeed * BOOST_MULTIPLIER;   
	
    
    let containerWidth = $container.width();
    let contentWidth = calculateContentWidth($container);

    let currentX = getCurrentTranslateX($content);


    const totalDistance = containerWidth + contentWidth;
    const duration = totalDistance / boostSpeed;

    // Reset animación
    $content.css('animation', 'none');
    $content[0].offsetHeight;

    const animationName = direction === 'right' ? 'marqueeRight' : 'marqueeLeft';

    // Ajustar keyframes desde posición actual
    const styleId = 'imp-dynamic-keyframes';
    let $style = $('#' + styleId);
    if (!$style.length) {
        $style = $('<style id="' + styleId + '"></style>').appendTo('head');
    }

    $style.html(`
        @keyframes marqueeLeft {
            0% { transform: translateX(${currentX}px); }
            100% { transform: translateX(-${contentWidth}px); }
        }
        @keyframes marqueeRight {
            0% { transform: translateX(${currentX}px); }
            100% { transform: translateX(${containerWidth}px); }
        }
    `);

    $content.css({
        animation: `${animationName} ${duration}s linear infinite`
    });

    
     setTimeout(() => {
        continueMarqueeFromCurrentPosition($container);
    }, BOOST_DURATION);


}

$(document).on('click', '.imp-marquee-btn.imp-next', function () {
    const $container = $(this).closest('.imp-marquee-container');
    accelerateMarquee($container, 'left');
});

$(document).on('click', '.imp-marquee-btn.imp-prev', function () {
    const $container = $(this).closest('.imp-marquee-container');
    accelerateMarquee($container, 'right');
});
 
    // Aplicar estilos al cargar la página
    applyDynamicStyles();


});


document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('imp-mail-modal');
    const overlay = document.getElementById('imp-mail-overlay');
    const closeBtn = document.getElementById('imp-mail-close');
    const recipientBox = document.getElementById('imp-mail-recipient');

    let recipientEmail = '';

    // Abrir modal al hacer click en botones "Enviar correo"
    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest('.imp-open-mail');
        if (!btn) return;

        recipientEmail = btn.dataset.email || '';
        recipientBox.textContent = `${btn.dataset.name || ''} (${recipientEmail})`;

        modal.classList.add('active');
        overlay.classList.add('active');
    });

    // Cerrar modal
    [overlay, closeBtn].forEach(el => {
        if (!el) return;
        el.addEventListener('click', () => {
            modal.classList.remove('active');
            overlay.classList.remove('active');
        });
    });

    // Enviar formulario usando fetch hacia email_send.php
    const mailForm = document.getElementById('imp-mail-form');
    if (mailForm) {
        mailForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const sender = mailForm.querySelector('#imp-sender-name').value || '';
            const senderEmail = mailForm.querySelector('#imp-sender-email').value || '';
            const message = mailForm.querySelector('#imp-mail-message').value || '';
            // usar la variable externa recipientEmail (se setea al abrir el modal)
            const toEmail = recipientEmail || '';

            const submitBtn = mailForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const data = {
                action: 'imp_send_email',
                senderName: sender,
                senderEmail: senderEmail,
                recipientEmail: toEmail,
                message: message
            };

            if (typeof imp_ajax !== 'undefined' && imp_ajax.nonce) {
                data.nonce = imp_ajax.nonce;
            }

            // Usar jQuery.ajax para enviar al admin-ajax.php
            if (typeof jQuery !== 'undefined' && typeof imp_ajax !== 'undefined' && imp_ajax.ajax_url) {
                jQuery.post(imp_ajax.ajax_url, data, function(response) {
                    if (response && response.success) {
                        alert(response.data || 'Correo enviado correctamente');
                    } else {
                        const msg = (response && response.data) ? response.data : 'Error al enviar correo';
                        alert('Error: ' + msg);
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    alert('Error al enviar el correo. Revisa la consola.');
                }).always(function() {
                    if (submitBtn) submitBtn.disabled = false;
                    mailForm.reset();
                    if (modal) modal.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                });
            } else {
                // Fallback a fetch si no está imp_ajax
                const fd = new FormData();
                Object.keys(data).forEach(k => fd.append(k, data[k]));
                const url = (typeof imp_ajax !== 'undefined' && imp_ajax.ajax_url) ? imp_ajax.ajax_url : '/wp-admin/admin-ajax.php';
                fetch(url, { method: 'POST', credentials: 'same-origin', body: fd })
                    .then(r => r.json())
                    .then(response => {
                        if (response && response.success) {
                            alert(response.data || 'Correo enviado correctamente');
                        } else {
                            const msg = (response && response.data) ? response.data : 'Error al enviar correo';
                            alert('Error: ' + msg);
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        alert('Error al enviar el correo. Revisa la consola.');
                    })
                    .finally(() => {
                        if (submitBtn) submitBtn.disabled = false;
                        mailForm.reset();
                        if (modal) modal.classList.remove('active');
                        if (overlay) overlay.classList.remove('active');
                    });
            }

        });
    }
});


