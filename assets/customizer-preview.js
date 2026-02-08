/**
 * Script para vista previa en tiempo real del Customizer
 */
(function($) {
    'use strict';
    
    // Actualizar altura de marquesina en tiempo real
    wp.customize('imp_marquee_height', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateMarqueeConfig === 'function') {
                window.updateMarqueeConfig('height', newval);
            }
        });
    });
    
    // Actualizar velocidad en tiempo real
    wp.customize('imp_marquee_speed', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateMarqueeConfig === 'function') {
                window.updateMarqueeConfig('speed', newval);
            }
        });
    });
    
    // Actualizar dirección en tiempo real
    wp.customize('imp_marquee_direction', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateMarqueeConfig === 'function') {
                window.updateMarqueeConfig('direction', newval);
            }
        });
    });
    
    // Actualizar estilos de texto en tiempo real
    wp.customize('imp_title_font_size', function(value) {
        value.bind(function(newval) {
            $('.imp-marquee-text h3').css('font-size', newval + 'px');
        });
    });
    
    wp.customize('imp_title_color', function(value) {
        value.bind(function(newval) {
            $('.imp-marquee-text h3').css('color', newval);
        });
    });
    
    wp.customize('imp_title_font_weight', function(value) {
        value.bind(function(newval) {
            $('.imp-marquee-text h3').css('font-weight', newval);
        });
    });
    
    wp.customize('imp_subtitle_font_size', function(value) {
        value.bind(function(newval) {
            $('.imp-marquee-text p').css('font-size', newval + 'px');
        });
    });
    
    wp.customize('imp_subtitle_color', function(value) {
        value.bind(function(newval) {
            $('.imp-marquee-text p').css('color', newval);
        });
    });
    
    wp.customize('imp_subtitle_font_weight', function(value) {
        value.bind(function(newval) {
            $('.imp-marquee-text p').css('font-weight', newval);
        });
    });
    
    wp.customize('imp_contact_icon_size', function(value) {
        value.bind(function(newval) {
            $('.imp-contact-link').css({
                'width': newval + 'px',
                'height': newval + 'px'
            });
            $('.imp-contact-link span').css('font-size', (newval * 0.5) + 'px');
        });
    });
    
    wp.customize('imp_show_contact_info', function(value) {
        value.bind(function(newval) {
            if (newval) {
                $('.imp-contact-links').show();
            } else {
                $('.imp-contact-links').hide();
            }
        });
    });
    
})(jQuery);