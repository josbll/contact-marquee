jQuery(document).ready(function ($) {

    const marquees = new Map();

    function calculateContentWidth($container) {
        let total = 0;
        $container.find('.imp-marquee-item').each(function () {
            total += $(this).outerWidth(true);
        });
        return total;
    }

    function initMarquee($container) {
        const $content = $container.find('.imp-marquee-content');

        const baseSpeed = parseFloat($container.data('speed')) || 50;
        const direction = $container.data('direction') || 'left';

        const contentWidth = calculateContentWidth($container);
        const containerWidth = $container.width();

        const pxPerFrame = baseSpeed / 60;

        marquees.set($container[0], {
            $container,
            $content,
            containerWidth,
            contentWidth,
            position: direction === 'left' ? containerWidth : -contentWidth,
            speed: direction === 'left' ? -pxPerFrame : pxPerFrame,
            baseSpeed: pxPerFrame,
            direction
        });
    }

    function animate() {
        
        marquees.forEach(m => {
            m.position += m.speed;

            // loop infinito limpio
            if (m.direction === 'left' && m.position <= -m.contentWidth) {
                m.position = m.containerWidth;
            }
            if (m.direction === 'right' && m.position >= m.containerWidth) {
                m.position = -m.contentWidth;
            }

            m.$content.css('transform', `translateX(${m.position}px)`);
        });

        requestAnimationFrame(animate);
      
    }

    //  Aceleración sin perder posición
    function accelerateMarquee($container, dir) {
        const m = marquees.get($container[0]);
        if (!m) return;

        const BOOST_MULTIPLIER = 20;
        const BOOST_DURATION = 350;

        const boostSpeed = m.baseSpeed * BOOST_MULTIPLIER;

        m.speed = dir === 'left' ? -boostSpeed : boostSpeed;

        setTimeout(() => {
            m.speed = m.direction === 'left' ? -m.baseSpeed : m.baseSpeed;
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


    //  Inicialización
    $('.imp-marquee-container').each(function () {
        initMarquee($(this));
    });

    //  Recalcular en orientación / resize real
    $(window).on('orientationchange resize', function () {
        setTimeout(() => {
            marquees.clear();
            $('.imp-marquee-container').each(function () {
                initMarquee($(this));
            });
        }, 300);
    });

    animate();
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

    

    const stoverlay = document.getElementById("imp-story-overlay");
    const stcloseBtn = document.getElementById("imp-story-close");
    const img = document.getElementById("imp-story-img");
    const title = document.getElementById("imp-story-title");
    const text = document.getElementById("imp-story-text");

    if (!stoverlay) return; // shortcode no está en la página

    

    document.addEventListener("click", (e) => {

        const btn = e.target.closest(".imp-open-story");
        if (!btn) return;

        title.textContent = btn.dataset.title || "";
        img.src = btn.dataset.image || "";
        console.log(btn.dataset.story);
        text.textContent = btn.dataset.story || "";

        stoverlay.classList.remove("hidden");
    });

    stcloseBtn.addEventListener("click", () => {
        stoverlay.classList.add("hidden");
    });

    stoverlay.addEventListener("click", (e) => {
        if (e.target === stoverlay) {
            stoverlay.classList.add("hidden");
        }
    });





});


