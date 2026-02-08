<!-- Modal overlay -->
<div id="imp-mail-overlay" class="imp-mail-overlay"></div>

<!-- Modal -->
<div id="imp-mail-modal" class="imp-mail-modal">
    <button class="imp-mail-close" id="imp-mail-close">
        ✕
    </button>

    <div class="imp-mail-content">
        <h2>Enviar mensaje</h2>

        <form id="imp-mail-form">
            <div class="imp-mail-group">
                <label>Tu nombre</label>
                <input type="text" id="imp-sender-name" required>
            </div>

            <div class="imp-mail-group">
                <label>Tu correo</label>
                <input type="email" id="imp-sender-email" required>
            </div>

            <div class="imp-mail-group">
                <label>Destinatario</label>
                <div class="imp-mail-recipient" id="imp-mail-recipient">
                    —
                </div>
            </div>

            <div class="imp-mail-group">
                <label>Mensaje</label>
                <textarea id="imp-mail-message" rows="5" required></textarea>
            </div>

            <button type="submit" class="imp-mail-send">
                Enviar
            </button>
        </form>
    </div>
</div>
