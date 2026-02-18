<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table_contacts = $wpdb->prefix . 'image_marquees_contacts';
$table_settings = $wpdb->prefix . 'image_marquees_field_settings';

$existing_marquees = $wpdb->get_col(
    "SELECT DISTINCT marquee_name FROM $table_contacts"
);

//Obtiene los campos activados de la marquesina seleccionada marquee_name en la tabla imp_marquee_field_settings
$field_settings = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM $table_settings WHERE marquee_name = %s",
        $existing_marquees[0] ?? ''
    ),
    ARRAY_A
);

?>

<div class="wrap">
    <h1>Configuración de Campos por Marquesina</h1>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('imp_field_settings', 'imp_field_settings_nonce'); ?>
        <input type="hidden" name="action" value="imp_save_field_settings">

        <table class="form-table">
            <tr>
                <th>Marquesina</th>
                <td>
                    <select name="marquee_name" required>
                        <option value="">Seleccione una marquesina</option>
                        <?php foreach ($existing_marquees as $name): ?>
                            <option value="<?php echo esc_attr($name); ?>">
                                <?php echo esc_html($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <h2>Campos de Texto</h2>
        <fieldset>
            <label>
                <input type="checkbox" name="show_title" checked>
                Mostrar Título
            </label><br>

            <label>
                <input type="checkbox" name="show_subtitle" checked>
                Mostrar Subtítulo
            </label>
            <label>
                <input type="checkbox" name="show_story" checked>
                Mostrar Descripción o Historia
            </label>
        </fieldset>

        <h2>Información de Contacto</h2>
        <fieldset>
            <?php
            $contacts = [
                'phone' => 'Teléfono',
                'email' => 'Correo',
                'whatsapp' => 'WhatsApp',
                'instagram' => 'Instagram',
                'facebook' => 'Facebook',
                'tiktok' => 'TikTok',
                'telegram' => 'Telegram',
                'linkedin' => 'LinkedIn',
            ];

            foreach ($contacts as $key => $label):
            ?>
                <label>
                    <input type="checkbox" name="show_<?php echo $key; ?>" checked>
                    <?php echo $label; ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset>

        <p>
            <button class="button button-primary">
                Guardar configuración
            </button>
        </p>
    </form>
</div>
