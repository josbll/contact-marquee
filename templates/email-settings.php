<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap imp-email-settings-wrap">
    <h1 class="wp-heading-inline">Configuración de Envío Correo Electrónico</h1>
    <?php if (isset($_GET['updated'])): ?>
        <div id="message" class="updated notice is-dismissible"><p>Configuración guardada.</p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('imp_email_settings', 'imp_email_settings_nonce'); ?>
        <input type="hidden" name="action" value="imp_save_email_settings">

        <h2 class="imp-section">Configuración Básica</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="template_active">Usar Plantilla</label></th>
                <td><input name="template_active" type="checkbox" value="yes" <?php checked($values['template_active'], 'yes'); ?> class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="subject">Asunto del correo</label></th>
                <td><input name="subject" id="subject" type="text" value="<?php echo esc_attr($values['subject']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="from">Remitente (From)</label></th>
                <td><input name="from" id="from" type="email" value="<?php echo esc_attr($values['from']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="reply_to">Respuesta a (Reply-To)</label></th>
                <td><input name="reply_to" id="reply_to" type="email" value="<?php echo esc_attr($values['reply_to']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="charset">Charset</label></th>
                <td>
                    <select name="charset" id="charset">
                        <option value="UTF-8" <?php selected($values['charset'], 'UTF-8'); ?>>UTF-8</option>
                        <option value="ISO-8859-1" <?php selected($values['charset'], 'ISO-8859-1'); ?>>ISO-8859-1</option>
                        <option value="Windows-1252" <?php selected($values['charset'], 'Windows-1252'); ?>>Windows-1252</option>
                    </select>
                </td>
            </tr>
        </table>

        <h2 class="imp-section">Plantilla del Mensaje</h2>
        <p><label for="html_template">HTML</label></p>
        <p><textarea name="html_template" id="html_template" rows="10" cols="80" class="large-text code"><?php echo esc_textarea($values['html_template']); ?></textarea></p>
        <p><label for="text_template">Texto plano</label></p>
        <p><textarea name="text_template" id="text_template" rows="6" cols="80" class="large-text code"><?php echo esc_textarea($values['text_template']); ?></textarea></p>

        <h2 class="imp-section">Estrategia de Envío</h2>
        <p>
            <label><input type="radio" name="strategy" value="phpmail" <?php checked($values['strategy'], 'phpmail'); ?>> PHP mail()</label><br>
            <label><input type="radio" name="strategy" value="smtp" <?php checked($values['strategy'], 'smtp'); ?>> SMTP</label><br>
            <label><input type="radio" name="strategy" value="api" <?php checked($values['strategy'], 'api'); ?>> API Externa</label>
        </p>

        <h2 class="imp-section">Mensajes del Sistema</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="success_message">Mensaje de éxito</label></th>
                <td><input name="success_message" id="success_message" type="text" value="<?php echo esc_attr($values['success_message']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="error_message">Mensaje de error</label></th>
                <td><input name="error_message" id="error_message" type="text" value="<?php echo esc_attr($values['error_message']); ?>" class="regular-text"></td>
            </tr>
        </table>

        <h2 class="imp-section">Validaciones</h2>
        <p>
            <label><input type="checkbox" name="spam_check" value="1" <?php checked($values['spam_check'], '1'); ?>> Protección contra spam</label><br>
            <label><input type="checkbox" name="email_validation" value="1" <?php checked($values['email_validation'], '1'); ?>> Validación de formato de email</label><br>
            <label><input type="checkbox" name="required_fields" value="1" <?php checked($values['required_fields'], '1'); ?>> Validar campos requeridos</label><br>
            <label><input type="checkbox" name="honeypot" value="1" <?php checked($values['honeypot'], '1'); ?>> Honeypot (trampa para bots)</label>
        </p>

        <p class="submit">
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=image-contacts')); ?>">Cancelar</a>
            <button type="submit" class="button button-primary">Guardar Configuración</button>
        </p>
    </form>
</div>
