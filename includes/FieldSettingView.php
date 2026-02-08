<?php

class FieldSettingView {
    public $page_slug = 'imp-field-settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_imp_save_field_settings', array($this, 'imp_save_field_settings'));
        // Registrar el hook de encolado en el constructor para que esté disponible
        // durante la fase correcta del ciclo de vida de administración.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'));
    }

    public function add_admin_menu() {
        add_submenu_page(
            'contact-marquee-list',
            'Configuración de Campos',
            'Configuración de Campos',
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        if (isset($_GET['saved']) && $_GET['saved'] == 1) {
            echo '<div class="updated"><p>Configuración guardada correctamente.</p></div>';
        }
        // Obtener las marquesinas existentes
        global $wpdb;
        $table = $wpdb->prefix . 'image_marquees_contacts';
        $existing_marquees = $wpdb->get_col("SELECT DISTINCT marquee_name FROM $table");

        include CMP_PLUGIN_PATH . 'templates/admin-campo-configuracion.php';
    }

    public function enqueue_admin_script($hook) {
        if (isset($_GET['page']) && $_GET['page'] === $this->page_slug) {
            wp_enqueue_script('imp-field-admin-script', CMP_PLUGIN_URL . 'assets/field-setting-script.js');   
        }
    }

    public function get_field_settings() {
        global $wpdb;
        $table_settings = $wpdb->prefix . 'image_marquees_field_settings';

        $marquee_name = isset($_POST['marquee_name']) ? sanitize_text_field($_POST['marquee_name']) : '';

        if (empty($marquee_name)) {
            return [];
        }

        $field_settings = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_settings WHERE marquee_name = %s",
                $marquee_name
            ),
            ARRAY_A
        );
        return $field_settings ?: [];
    }

    public function get_field_settingsbyMarquee($marquee_name) {
        global $wpdb;
        $table_settings = $wpdb->prefix . 'image_marquees_field_settings';

        if (empty($marquee_name)) {
            return [];
        }

        $field_settings = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_settings WHERE marquee_name = %s",
                $marquee_name
            ),
            ARRAY_A
        );

        return $field_settings ?: [];
    }

    public function imp_save_field_settings() {
            check_admin_referer('imp_field_settings', 'imp_field_settings_nonce');

            if (!current_user_can('manage_options')) {
                wp_die('Sin permisos');
            }

            global $wpdb;
            $table = $wpdb->prefix . 'image_marquees_field_settings';

            $marquee = isset($_POST['marquee_name']) ? sanitize_text_field($_POST['marquee_name']) : '';

            if (empty($marquee)) {
                wp_redirect(admin_url('admin.php?page=imp-field-settings&saved=0'));
                exit;
            }

            $fields = [
                'show_title',
                'show_subtitle',
                'show_phone',
                'show_email',
                'show_whatsapp',
                'show_instagram',
                'show_facebook',
                'show_tiktok',
                'show_telegram',
                'show_linkedin',
            ];

            $data = ['marquee_name' => $marquee];

            foreach ($fields as $field) {
                $data[$field] = isset($_POST[$field]) ? 1 : 0;
            }

            // Comprobar existencia por marquee_name y usar insert o update
            $existing_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE marquee_name = %s", $marquee));
            
            if ($existing_id) {
                $wpdb->update($table, $data, array('id' => $existing_id));
                
            } else {
                $wpdb->insert($table, $data);
                
            }

            wp_redirect(admin_url('admin.php?page=imp-field-settings&saved=1'));
            exit;
        }
}






?>