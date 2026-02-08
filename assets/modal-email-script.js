document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('imp-mail-modal');
    const overlay = document.getElementById('imp-mail-overlay');
    const closeBtn = document.getElementById('imp-mail-close');
    const recipientBox = document.getElementById('imp-mail-recipient');

    let recipientEmail = '';

    // Abrir modal (botón correo en tarjeta)
    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest('.imp-open-mail');
        if (!btn) return;

        const name = btn.dataset.name;
        const email = btn.dataset.email;

        recipientBox.textContent = `${name} (${email})`;
        recipientEmail = email;

        modal.classList.add('active');
        overlay.classList.add('active');
    });

    // Cerrar modal
    [overlay, closeBtn].forEach(el => {
        el.addEventListener('click', () => {
            modal.classList.remove('active');
            overlay.classList.remove('active');
        });
    });

    // Enviar formulario
    document.getElementById('imp-mail-form').addEventListener('submit', (e) => {
        e.preventDefault();

        const data = {
            senderName: document.getElementById('imp-sender-name').value,
            senderEmail: document.getElementById('imp-sender-email').value,
            message: document.getElementById('imp-mail-message').value,
            recipientEmail: recipientEmail,
        };

        console.log('Enviar correo:', data);

        alert('Mensaje enviado (simulado)');
        modal.classList.remove('active');
        overlay.classList.remove('active');
        e.target.reset();
    });
});
