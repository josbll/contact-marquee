<?php
if (!defined('ABSPATH')) {
    exit;
}

class EmailSettingView {
    private $page_slug = 'imp-email-settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'register_menu'));
        add_action('admin_post_imp_save_email_settings', array($this, 'handle_save'));
    
    }


    public function register_menu() {
        // Añadir como subpágina bajo el menú del plugin (si existe)
        $parent = 'image-contacts';
        add_submenu_page(
            $parent,
            'Configuración de Correo',
            'Email Settings',
            'manage_options',
            $this->page_slug,
            array($this, 'render')
        );
    }

    public function enqueue_admin_assets($hook) {
        // Encolar estilos/JS sólo en nuestra página
        if (isset($_GET['page']) && $_GET['page'] === $this->page_slug) {
            // Tailwind y Feather ya se usan en frontend; en admin los encolamos solo para esta página
            wp_enqueue_style('imp-email-admin-style', CMP_PLUGIN_URL . 'assets/admin-style.css');
            wp_enqueue_script('imp-tailwind', 'https://cdn.tailwindcss.com', array(), null, false);
            wp_enqueue_script('feather', 'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js', array(), null, true);
        }
    }

    public function render() {
        if (!current_user_can('manage_options')) {
            wp_die('Permisos insuficientes');
        }

        // Encolar assets para esta página
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Cargar valores actuales desde la tabla Email_setting
        global $wpdb;
        $table = $wpdb->prefix . 'Email_setting';
        $row = $wpdb->get_row("SELECT * FROM $table LIMIT 1", ARRAY_A);

        $values = array(
            'subject' => $row['subject'] ?? 'Nuevo mensaje de contacto',
            'from' => $row['from_email'] ?? get_option('admin_email'),
            'reply_to' => $row['reply_to'] ?? '',
            'charset' => $row['charset'] ?? 'UTF-8',
            'html_template' => $row['html_template'] ?? '',
            'text_template' => $row['text_template'] ?? '',
            'strategy' => $row['strategy'] ?? 'phpmail',
            'success_message' => $row['success_message'] ?? '¡Mensaje enviado correctamente!',
            'error_message' => $row['error_message'] ?? 'Hubo un error al enviar el mensaje',
            'spam_check' => isset($row['spam_check']) ? $row['spam_check'] : '1',
            'email_validation' => isset($row['email_validation']) ? $row['email_validation'] : '1',
            'required_fields' => isset($row['required_fields']) ? $row['required_fields'] : '0',
            'honeypot' => isset($row['honeypot']) ? $row['honeypot'] : '0',
            'template_active' => isset($row['active']) ? $row['active'] : 'no'
        );

        // Renderizar plantilla (usa template del plugin)
        include CMP_PLUGIN_PATH . 'templates/email-settings.php';
    }

    public function handle_save() {
        if (!current_user_can('manage_options')) {
            wp_die('Permisos insuficientes');
        }

        check_admin_referer('imp_email_settings', 'imp_email_settings_nonce');


        // Guardar en la tabla Email_setting
        global $wpdb;
        $table = $wpdb->prefix . 'Email_setting';

        if(!isset($_POST['template_active'])){
            $option = isset($_POST['template_active']) ? 'yes' : 'no';
        }

        $data = array(
            'subject' => sanitize_text_field($_POST['subject'] ?? ''),
            'from_email' => sanitize_email($_POST['from'] ?? ''),
            'reply_to' => sanitize_email($_POST['reply_to'] ?? ''),
            'charset' => sanitize_text_field($_POST['charset'] ?? 'UTF-8'),
            'html_template' => wp_kses_post($_POST['html_template'] ?? ''),
            'text_template' => sanitize_textarea_field($_POST['text_template'] ?? ''),
            'strategy' => sanitize_text_field($_POST['strategy'] ?? 'phpmail'),
            'success_message' => sanitize_text_field($_POST['success_message'] ?? ''),
            'error_message' => sanitize_text_field($_POST['error_message'] ?? ''),
            'spam_check' => isset($_POST['spam_check']) ? 1 : 0,
            'email_validation' => isset($_POST['email_validation']) ? 1 : 0,
            'required_fields' => isset($_POST['required_fields']) ? 1 : 0,
            'honeypot' => isset($_POST['honeypot']) ? 1 : 0,
            'active' => $option ?? 'no'
        );

        // Comprobar si existe fila
        $exists = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if (intval($exists) > 0) {
            // Actualizar primera fila
            $row = $wpdb->get_row("SELECT id FROM $table LIMIT 1");
            if ($row) {
                $wpdb->update($table, $data, array('id' => $row->id));
            }
        } else {
            $wpdb->insert($table, $data);
        }

        $redirect = admin_url('admin.php?page=' . $this->page_slug . '&updated=1');
        wp_redirect($redirect);
        exit;
    }
}

// Inicializar la vista en admin
add_action('plugins_loaded', function() {
    if (is_admin()) {
        new EmailSettingView();
    }
});
