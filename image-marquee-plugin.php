<?php
/**
 * Plugin Name: Marquee Contact List Plugin
 * Plugin URI: https://example.com
 * Description: Plugin para crear marquesinas de imágenes con títulos y subtítulos, redes sociales personalizable desde el Customizer
 * Version: 2.6
 * Author: Josué Becerra Llamozas - Director de Tecnología y Comunicaciones de Fundasperven
 * License: GPL v2 or later
 * 
 * Versión 1.2 - Características principales:
 * - Interfaz de administración para gestionar imágenes con títulos y subtítulos
 * - Campos de contacto: teléfono y redes sociales (Instagram, Facebook, TikTok, WhatsApp, Telegram, LinkedIn)
 * - Sistema de múltiples marquesinas
 * - Shortcode flexible para insertar marquesinas
 * - Configuración desde el Customizer con vista previa en tiempo real
 * - Animación continua que considera anchos individuales de imágenes
 * - Diseño responsivo y efectos hover
 * Version 1.7 - Mejoras
 * - Caracteristicas de la version 1.2
 * - Se incorpora el boton siguiente y anterior
 * Version 1.8 
 * - Correccion del bug fantasma
 * Version 1.9
 * - Correccion de detalle al desplesgar subtitulo largo(cargo y rol).
 * Version 2.0
 *  - Se agrega funcionalidad para enviar correos desde el frontend usando AJAX y wp_mail
 * Version 2.1
 *  - Se agrega la configuracion para que no se envie los mensajes como spam en correo electronico.
 * Version 2.2
 * - Se agrega la tabla para la configuracion de envio de correo electronico.
 * - Se agrega la pagina de configuracion de correo electronico en el menu de administracion.
 * - Se anexa la opcion de activar o desactivar la plantilla de correo electronico.
 * - Se crea las opciones: Crear Directorio y Configuración de envío de correo.
 * Version 2.3
 * - Se agrega la funcionalidad para configurar los campos que se desean mostrar por marquesina.
 * Version 2.4
 * - Se mejora el error de altura y anchura de imagen.
 * Version 2.5
 * - Se elimina las animaciones CSS y se implementa la animación con JavaScript para un desplazamiento más fluido y preciso, considerando los anchos individuales de las imágenes. Además, se añaden botones de navegación para acelerar el desplazamiento en ambas direcciones, mejorando la experiencia del usuario. Se corrige un bug que causaba un salto al cambiar la orientación o redimensionar la ventana, asegurando que el marquee se recalcule correctamente sin perder su posición actual. También se optimiza el código para mejorar el rendimiento y la estabilidad general del plugin.
 * - Se crea el boton y la ventana de reseña historica.
 * Version 2.6
 * - Se visualiza la ventana modal de reseña historica con saltos de linea.
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('CMP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CMP_PLUGIN_PATH', plugin_dir_path(__FILE__));



class ContactMarqueePlugin {
    private $email_settings_view;
    private $field_setting_view;

    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Cargar archivos necesarios
        $this->load_dependencies();
        
        // Hooks principales
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_save_marquee_contacts', array($this, 'save_marquee_contacts'));
        add_action('wp_ajax_delete_marquee', array($this, 'delete_marquee'));
        add_action('wp_ajax_get_marquee_contacts', array($this, 'get_marquee_contacts'));
        add_action('wp_ajax_imp_get_field_settings',array($this,'get_field_settings'));
        // AJAX endpoint para enviar correos desde el frontend
        add_action('wp_ajax_imp_send_email', array($this, 'imp_send_email'));
        add_action('wp_ajax_nopriv_imp_send_email', array($this, 'imp_send_email'));
        add_action('customize_register', array($this, 'customize_register'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_tailwind_scoped'));


        // Registrar shortcode
        add_shortcode('image_marquee_contact', array($this, 'marquee_shortcode'));
        $this->email_settings_view = new EmailSettingView();
        $this->field_setting_view = new FieldSettingView();
    }
    
    private function load_dependencies() {
        // Cargar dependencias del plugin
        $email_sender_file = CMP_PLUGIN_PATH . 'emailSender/email_send.php';
        if (file_exists($email_sender_file)) {
            require_once $email_sender_file;
        }
        $email_settings_view = CMP_PLUGIN_PATH . 'includes/EmailSettingView.php';
        if (file_exists($email_settings_view)) {
            require_once $email_settings_view;
        }
        $field_setting_view = CMP_PLUGIN_PATH . 'includes/FieldSettingView.php';
        if (file_exists($field_setting_view)) {
            require_once $field_setting_view;
        }
    }
    
    public function activate() {
        // Crear tabla en la base de datos
        $this->create_tables();
        
        // Establecer opciones por defecto
        add_option('imp_marquee_speed', '50');
        add_option('imp_marquee_height', '200');
        add_option('imp_marquee_direction', 'left');
        add_option('imp_title_font_size', '16');
        add_option('imp_title_color', '#ffffff');
        add_option('imp_title_font_weight', '600');
        add_option('imp_subtitle_font_size', '14');
        add_option('imp_subtitle_color', '#e6e6e6');
        add_option('imp_subtitle_font_weight', '400');
        add_option('imp_contact_icon_size', '32');
        add_option('imp_show_contact_info', true);
    }
    
    public function deactivate() {
        // Limpiar si es necesario
    }
    
    private function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'image_marquees_contacts';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            marquee_name varchar(255) NOT NULL,
            image_url varchar(500) NOT NULL,
            title varchar(255),
            subtitle varchar(255),
            phone varchar(20),
            email varchar(50),
            story longtext,
            instagram varchar(255),
            facebook varchar(255),
            tiktok varchar(255),
            whatsapp varchar(20),
            telegram varchar(255),
            linkedin varchar(255),
            marquee_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $table_name = $wpdb->prefix . 'image_marquees_field_settings';

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            marquee_name varchar(255) NOT NULL,
            show_title tinyint(1) DEFAULT 1,
            show_subtitle tinyint(1) DEFAULT 1,
            show_phone tinyint(1) DEFAULT 1,
            show_email tinyint(1) DEFAULT 1,
            show_whatsapp tinyint(1) DEFAULT 1,
            show_instagram tinyint(1) DEFAULT 1,
            show_facebook tinyint(1) DEFAULT 1,
            show_tiktok tinyint(1) DEFAULT 1,
            show_telegram tinyint(1) DEFAULT 1,
            show_linkedin tinyint(1) DEFAULT 1,
            show_story tinyint(1) DEFAULT 1,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY marquee_name (marquee_name)
        ) $charset_collate;";

         dbDelta($sql);
         

        // Tabla para almacenar configuración de correo (una sola fila esperada)
        $email_table = $wpdb->prefix . 'Email_setting';
        $sql2 = "CREATE TABLE $email_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            subject varchar(255) DEFAULT '' NOT NULL,
            active varchar(2) DEFAULT 'no' NOT NULL,
            from_email varchar(255) DEFAULT '' NOT NULL,
            reply_to varchar(255) DEFAULT '' NOT NULL,
            charset varchar(50) DEFAULT 'UTF-8' NOT NULL,
            html_template longtext,
            text_template longtext,
            strategy varchar(50) DEFAULT 'phpmail',
            success_message varchar(255) DEFAULT '' NOT NULL,
            error_message varchar(255) DEFAULT '' NOT NULL,
            spam_check tinyint(1) DEFAULT 1,
            email_validation tinyint(1) DEFAULT 1,
            required_fields tinyint(1) DEFAULT 0,
            honeypot tinyint(1) DEFAULT 0,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql2);

       $table_name = $wpdb->prefix . 'image_marquees_contacts';

       $column = $wpdb->get_results(
            "SHOW COLUMNS FROM $table_name LIKE 'story'"
        );

        if (empty($column)) {
            $wpdb->query(
                "ALTER TABLE $table_name ADD COLUMN story longtext"
            );
        }

        $table_name = $wpdb->prefix . 'image_marquees_field_settings';

        $column = $wpdb->get_results(
            "SHOW COLUMNS FROM $table_name LIKE 'show_story'"
        );

        if (empty($column)) {
            $wpdb->query(
                "ALTER TABLE $table_name ADD COLUMN show_story tinyint(1) DEFAULT 1"
            );
        }
        
        // Insertar fila por defecto si no existe
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $email_table");
        if (intval($count) === 0) {
            $wpdb->insert($email_table, array(
                'subject' => 'Nuevo mensaje de contacto',
                'from_email' => get_option('admin_email'),
                'reply_to' => '',
                'charset' => 'UTF-8',
                'html_template' => '',
                'text_template' => '',
                'strategy' => 'phpmail',
                'success_message' => '¡Mensaje enviado correctamente!',
                'error_message' => 'Hubo un error al enviar el mensaje',
                'spam_check' => 1,
                'email_validation' => 1,
                'required_fields' => 0,
                'honeypot' => 0
            ));
        }
    }
    
    public function add_admin_menu() {
        // Top level menu
        $capability = 'manage_options';
        $menu_slug = 'contact-marquee-list';

        add_menu_page(
            'Marquesina de Contactos', // page title
            'Marquesina de Contactos', // menu title
            $capability,
            $menu_slug,
             null,
            'dashicons-images-alt2',
            2
        );

        // Submenu: Marquesina de Directorios -> admin_page
        add_submenu_page(
            $menu_slug,
            'Crear Marquesina',
            'Crear Marquesina',
            $capability,
            'image-contacts',
            array($this, 'admin_page'),
            1
        );

        // Submenu: Configuración de envío de correo -> email_setting_page
        add_submenu_page(
            $menu_slug,
            'Configuración de envío de correo',
            'Configuración de envío de correo',
            $capability,
            'imp-email-settings',
            array($this, 'email_setting_page')
        );

       // $this->field_setting_view->add_admin_menu();

        remove_submenu_page(
            $menu_slug,
            $menu_slug
        );

    }

    public function redirect_to_first_submenu() {
        wp_redirect(admin_url('admin.php?page=image-contacts'));
        exit;
   }

    
    public function enqueue_scripts() {
        wp_enqueue_style('imp-marquee-style', CMP_PLUGIN_URL . 'assets/style.css', array(), '1.0.0');
        wp_enqueue_script('imp-marquee-script', CMP_PLUGIN_URL . 'assets/script.js', array('jquery'), '1.0.0', true);
        
        // Pasar variables PHP a JavaScript
        wp_localize_script('imp-marquee-script', 'imp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('imp_nonce'),
            'marquee_speed' => get_option('imp_marquee_speed', '50'),
            'marquee_height' => get_option('imp_marquee_height', '200'),
            'marquee_direction' => get_option('imp_marquee_direction', 'left'),
            'title_font_size' => get_option('imp_title_font_size', '16'),
            'title_color' => get_option('imp_title_color', '#ffffff'),
            'title_font_weight' => get_option('imp_title_font_weight', '600'),
            'subtitle_font_size' => get_option('imp_subtitle_font_size', '14'),
            'subtitle_color' => get_option('imp_subtitle_color', '#e6e6e6'),
            'subtitle_font_weight' => get_option('imp_subtitle_font_weight', '400'),
            'contact_icon_size' => get_option('imp_contact_icon_size', '32'),
            'show_contact_info' => get_option('imp_show_contact_info', true)
        ));
        
        // Cargar script de vista previa para el Customizer
        if (is_customize_preview()) {
            wp_enqueue_script('imp-customizer-preview', CMP_PLUGIN_URL . 'assets/customizer-preview.js', array('jquery', 'customize-preview'), '1.0.0', true);
        }
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'marquesina-de-contactos_page_contact-marquee-list' && $hook !== 'marquesina-de-contactos_page_imp-email-settings' && $hook !== 'marquesina-de-contactos_page_image-contacts') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_style('imp-admin-style', CMP_PLUGIN_URL . 'assets/admin-style.css', array(), '1.0.0');
        wp_enqueue_script('imp-admin-script', CMP_PLUGIN_URL . 'assets/admin-script.js', array('jquery', 'jquery-ui-sortable'), '1.0.0', true);
        
        wp_localize_script('imp-admin-script', 'imp_admin_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('imp_nonce')
        ));
    }
    
    public function admin_page() {
        include CMP_PLUGIN_PATH . 'templates/admin-page.php';
    }

    public function email_setting_page() {  
       
       $this->email_settings_view->render();
    }

    public function field_setting_page() {
        $this->field_setting_view->render();
    }

    public function get_field_settings(){
        //retornar los campos activados de la marquesina seleccionada marquee_name
        //se coloca en formato json
        wp_send_json_success($this->field_setting_view->get_field_settings());
    }
    
public function marquee_shortcode($atts) {
        $atts = shortcode_atts(array(
            'name'      => 'default',
            'speed'     => get_option('imp_marquee_speed', '50'),
            'height'    => get_option('imp_marquee_height', '260'),
            'direction' => get_option('imp_marquee_direction', 'left')
        ), $atts);

        global $wpdb;
        $table_name = $wpdb->prefix . 'image_marquees_contacts';

        $images = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE marquee_name = %s ORDER BY marquee_order ASC",
                $atts['name']
            )
        );

        $fields=$this->field_setting_view->get_field_settingsbyMarquee($atts['name']);

        if (empty($images)) {
            return '<p>No se encontraron imágenes para esta marquesina.</p>';
        }

        $output  = '<div class="imp-tailwind-scope">';
        $output .= '<div class="imp-marquee-container overflow-hidden w-full">';

        $output .= '<div class="imp-marquee-content flex gap-6">';

        foreach ($images as $image) {

            $output .= '
            <div class="imp-marquee-item flex-shrink-0 w-[360px] bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl">

                <!-- Foto -->
                <div class="flex justify-center pt-8 px-6">
                    <img 
                        src="' . esc_url($image->image_url) . '" 
                        alt="' . esc_attr($image->title) . '" 
                        class="rounded-lg object-cover border-4 border-indigo-100 shadow-md"
                        style="max-height:' . esc_attr($atts['height']) . 'px;"
                    >
                </div>';
            $show_story = !empty($image->story) && !empty($fields['show_story']);
            
            if($show_story){
                  $output .= '
                    <div class="mt-6 text-center">
                        <button 
                            type="button"
                            id="storyId"
                            class="imp-open-story text-indigo-600 hover:text-indigo-800 font-medium"
                            data-title="' . esc_attr($image->title) . '"
                            data-image="' . esc_url($image->image_url) . '"
                            data-story="' . esc_attr($image->story) . '"
                        >
                            Ver reseña
                        </button>
                    </div>';
            }

            // Nombre y rol (mostrar según configuración)
            $show_title = !empty($fields) && !empty($fields['show_title']);
            $show_subtitle = !empty($fields) && !empty($fields['show_subtitle']);

            if ($show_title || $show_subtitle) {
                $output .= '<div class="text-center mt-6 px-6">';
                if ($show_title) {
                    $output .= '<h3 class="text-xl font-bold text-gray-800">' . esc_html($image->title) . '</h3>';
                }
                if ($show_subtitle && !empty($image->subtitle)) {
                    $output .= '<p class="text-gray-600 mt-1">' . esc_html($image->subtitle) . '</p>';
                }
                $output .= '</div>';
            }

            $output .= '
                <!-- Contactos -->
                <div class="mt-6 px-6 pb-8 space-y-4 text-center">';

            /* Teléfono */
            if (!empty($fields) && !empty($fields['show_phone']) && !empty($image->phone)) {
                $output .= '
                    <div class="flex items-center justify-center gap-2">
                        <i data-feather="phone" class="text-indigo-500"></i>
                        <a href="tel:' . esc_attr($image->phone) . '" class="text-gray-700 hover:text-indigo-600 transition-colors">
                            ' . esc_html($image->phone) . '
                        </a>
                    </div>';
            }

             if (!empty($image->email)) {
                if (!empty($fields) && !empty($fields['show_email'])) {
                    $output .= '
                        <div class="flex items-center justify-center gap-2">
                            <i data-feather="mail" class="text-indigo-500"></i>
                            <button 
                                type="button"
                                class="imp-open-mail text-gray-700 hover:text-indigo-600 transition-colors underline"
                                data-name="' . esc_attr($image->title) . '"
                                data-email="' . esc_attr($image->email) . '"
                            >
                                Enviar correo
                            </button>
                        </div>';
                }
            }


            /* WhatsApp */
            if (!empty($fields) && !empty($fields['show_whatsapp']) && !empty($image->whatsapp)) {
                $output .= '
                    <div class="flex items-center justify-center gap-2">
                        <i data-feather="message-square" class="text-green-500"></i>
                        <a href="https://wa.me/' . esc_attr($image->whatsapp) . '" target="_blank" class="text-gray-700 hover:text-green-600 transition-colors">
                            ' . esc_attr($image->whatsapp) . '
                        </a>
                    </div>';
            }

            /* Redes sociales */
            $output .= '<div class="flex justify-center gap-8 mt-6">';

            if (!empty($fields) && !empty($fields['show_instagram']) && !empty($image->instagram)) {
                $instagram_raw = trim($image->instagram);
                $username = '';
                // Si es una URL válida o contiene el dominio de Instagram, úsala tal cual
                if (filter_var($instagram_raw, FILTER_VALIDATE_URL) || strpos($instagram_raw, 'instagram.com') !== false) {
                    $instagram_url = $instagram_raw;
                } else {
                    // Si es un nombre de usuario, eliminar @ inicial si existe y construir la URL
                    $username = ltrim($instagram_raw, "@ ");
                    $rawUser = rawurlencode($username);
                    $instagram_url = 'https://www.instagram.com/' . $rawUser . '/';
                }

                $output .= '<a href="' . esc_url($instagram_url) . '" target="_blank" rel="noopener noreferrer" class="text-pink-500 hover:text-pink-700"><i data-feather="instagram"></i>' . esc_html($username) . '</a>';
            }
            if (!empty($fields) && !empty($fields['show_facebook']) && !empty($image->facebook)) {
                $output .= '<a href="' . esc_url($image->facebook) . '" target="_blank" class="text-blue-600 hover:text-blue-800"><i data-feather="facebook"></i></a>';
            }
            if (!empty($fields) && !empty($fields['show_tiktok']) && !empty($image->tiktok)) {
                $output .= '<a href="' . esc_url($image->tiktok) . '" target="_blank" class="text-black hover:text-gray-800"><i data-feather="music"></i></a>';
            }
            if (!empty($fields) && !empty($fields['show_linkedin']) && !empty($image->linkedin)) {
                $output .= '<a href="' . esc_url($image->linkedin) . '" target="_blank" class="text-blue-700 hover:text-blue-900"><i data-feather="linkedin"></i></a>';
            }

            $output .= '</div>'; // redes
            $output .= '</div>'; // contactos
            $output .= '</div>'; // card
        }

        $output .= '</div>


                    <button class="imp-marquee-btn imp-prev" aria-label="Anterior"><span>‹</span></button>
                    <button class="imp-marquee-btn imp-next" aria-label="Siguiente"><span>›</span></button>
 
                   </div>
                   </div>';
        $output .= '
        <div id="imp-mail-overlay" class="imp-mail-overlay"></div>

        <div id="imp-mail-modal" class="imp-mail-modal">
            <button class="imp-mail-close" id="imp-mail-close">✕</button>

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
                        <div class="imp-mail-recipient" id="imp-mail-recipient"></div>
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
        ';

        $output .= '
                <!-- Modal Reseña -->
                <div id="imp-story-overlay" class="imp-story-overlay hidden">
                    <div class="imp-story-modal">
                        <button class="imp-story-close" id="imp-story-close">✕</button>

                        <div class="imp-story-body">
                            <div class="imp-story-image">
                                <img id="imp-story-img" src="" alt="">
                            </div>

                            <div class="imp-story-content">
                                <h3 id="imp-story-title"></h3>
                                <div id="imp-story-text" class="imp-story-text"></div>
                            </div>
                        </div>
                    </div>
                </div>
                ';


        /* Reemplazar iconos Feather */
        $output .= '
        <script>
            if (typeof feather !== "undefined") {
                feather.replace();
            }
        </script>';


        return $output;
}


    
    public function save_marquee_contacts() {
        check_ajax_referer('imp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Permisos insuficientes');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'image_marquees_contacts';
        
        $marquee_name = sanitize_text_field($_POST['marquee_name']);
        $images_data = json_decode(stripslashes($_POST['images_data']), true);
        
        // Eliminar imágenes existentes de esta marquesina
        $wpdb->delete($table_name, array('marquee_name' => $marquee_name));
        
        // Insertar nuevas imágenes
        foreach ($images_data as $order => $image) {
            $wpdb->insert($table_name, array(
                'marquee_name' => $marquee_name,
                'image_url' => esc_url_raw($image['url']),
                'title' => sanitize_text_field($image['title']),
                'email' => sanitize_email($image['email']),
                'subtitle' => sanitize_text_field($image['subtitle']),
                'story' => sanitize_textarea_field($image['story']),
                'phone' => sanitize_text_field($image['phone']),
                'instagram' => esc_url_raw($image['instagram']),
                'facebook' => esc_url_raw($image['facebook']),
                'tiktok' => esc_url_raw($image['tiktok']),
                'whatsapp' => sanitize_text_field($image['whatsapp']),
                'telegram' => esc_url_raw($image['telegram']),
                'linkedin' => esc_url_raw($image['linkedin']),
                'marquee_order' => intval($order)
            ));
        }
        
        wp_send_json_success('Marquesina guardada correctamente');
    }
    
    public function get_marquee_contacts() {
        check_ajax_referer('imp_nonce', 'nonce');
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'image_marquees_contacts';
        $marquee_name = sanitize_text_field($_POST['marquee_name']);
        
        $images = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE marquee_name = %s ORDER BY marquee_order ASC",
            $marquee_name
        ));
        
        wp_send_json_success($images);
    }
    
    public function delete_marquee() {
        check_ajax_referer('imp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Permisos insuficientes');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'image_marquees_contacts';
        $marquee_name = sanitize_text_field($_POST['marquee_name']);
        
        $wpdb->delete($table_name, array('marquee_name' => $marquee_name));
        
        wp_send_json_success('Marquesina eliminada correctamente');
    }
    
    public function customize_register($wp_customize) {
        // Sección para marquesinas
        $wp_customize->add_section('imp_marquee_settings', array(
            'title' => 'Configuración de Marquesinas',
            'priority' => 120,
        ));
        
        // Configuración de velocidad
        $wp_customize->add_setting('imp_marquee_speed', array(
            'default' => '50',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_marquee_speed', array(
            'label' => 'Velocidad de la marquesina (píxeles por segundo)',
            'section' => 'imp_marquee_settings',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 10,
                'max' => 200,
                'step' => 5,
            ),
        ));
        
        // Configuración de altura
        $wp_customize->add_setting('imp_marquee_height', array(
            'default' => '200',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_marquee_height', array(
            'label' => 'Altura de la marquesina (píxeles)',
            'section' => 'imp_marquee_settings',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 100,
                'max' => 800,
                'step' => 10,
            ),
        ));
        
        // Configuración de dirección
        $wp_customize->add_setting('imp_marquee_direction', array(
            'default' => 'left',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_marquee_direction', array(
            'label' => 'Dirección de movimiento',
            'section' => 'imp_marquee_settings',
            'type' => 'select',
            'choices' => array(
                'left' => 'Izquierda a Derecha',
                'right' => 'Derecha a Izquierda',
            ),
        ));
        
        // Sección para estilos de texto
        $wp_customize->add_section('imp_text_styles', array(
            'title' => 'Estilos de Texto de Marquesinas',
            'priority' => 121,
        ));
        
        // Tamaño de fuente del título
        $wp_customize->add_setting('imp_title_font_size', array(
            'default' => '16',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_title_font_size', array(
            'label' => 'Tamaño de fuente del título (px)',
            'section' => 'imp_text_styles',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 10,
                'max' => 32,
                'step' => 1,
            ),
        ));
        
        // Color del título
        $wp_customize->add_setting('imp_title_color', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'imp_title_color', array(
            'label' => 'Color del título',
            'section' => 'imp_text_styles',
        )));
        
        // Peso de fuente del título
        $wp_customize->add_setting('imp_title_font_weight', array(
            'default' => '600',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_title_font_weight', array(
            'label' => 'Peso de fuente del título',
            'section' => 'imp_text_styles',
            'type' => 'select',
            'choices' => array(
                '300' => 'Ligera (300)',
                '400' => 'Normal (400)',
                '500' => 'Media (500)',
                '600' => 'Semi-negrita (600)',
                '700' => 'Negrita (700)',
                '800' => 'Extra-negrita (800)',
            ),
        ));
        
        // Tamaño de fuente del subtítulo
        $wp_customize->add_setting('imp_subtitle_font_size', array(
            'default' => '14',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_subtitle_font_size', array(
            'label' => 'Tamaño de fuente del subtítulo (px)',
            'section' => 'imp_text_styles',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 8,
                'max' => 24,
                'step' => 1,
            ),
        ));
        
        // Color del subtítulo
        $wp_customize->add_setting('imp_subtitle_color', array(
            'default' => '#e6e6e6',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'imp_subtitle_color', array(
            'label' => 'Color del subtítulo',
            'section' => 'imp_text_styles',
        )));
        
        // Peso de fuente del subtítulo
        $wp_customize->add_setting('imp_subtitle_font_weight', array(
            'default' => '400',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_subtitle_font_weight', array(
            'label' => 'Peso de fuente del subtítulo',
            'section' => 'imp_text_styles',
            'type' => 'select',
            'choices' => array(
                '300' => 'Ligera (300)',
                '400' => 'Normal (400)',
                '500' => 'Media (500)',
                '600' => 'Semi-negrita (600)',
                '700' => 'Negrita (700)',
            ),
        ));
        
        // Tamaño de iconos de contacto
        $wp_customize->add_setting('imp_contact_icon_size', array(
            'default' => '32',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_contact_icon_size', array(
            'label' => 'Tamaño de iconos de contacto (px)',
            'section' => 'imp_text_styles',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 20,
                'max' => 50,
                'step' => 2,
            ),
        ));
        
        // Mostrar/ocultar información de contacto
        $wp_customize->add_setting('imp_show_contact_info', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('imp_show_contact_info', array(
            'label' => 'Mostrar información de contacto',
            'section' => 'imp_text_styles',
            'type' => 'checkbox',
        ));
    }
    
    public function get_marquee_names() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'image_marquees_contacts';
        
        $names = $wpdb->get_col("SELECT DISTINCT marquee_name FROM $table_name");
        return $names;
    }

    public function imp_send_email() {
        // Validar nonce
        check_ajax_referer('imp_nonce', 'nonce');

        // Tomar y sanear campos
        $sender_name = isset($_POST['senderName']) ? sanitize_text_field($_POST['senderName']) : '';
        $sender_email = isset($_POST['senderEmail']) ? sanitize_email($_POST['senderEmail']) : '';
        $recipient_email = isset($_POST['recipientEmail']) ? sanitize_email($_POST['recipientEmail']) : '';
        $message = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';

        // Instanciar y enviar
        $email_sender = new EmailSender($sender_name, $sender_email, $recipient_email, $message);
        $result = $email_sender->send();

        if (is_array($result) && isset($result['success']) && $result['success']) {
            wp_send_json_success(isset($result['message']) ? $result['message'] : 'Correo enviado correctamente');
        } else {
            $msg = is_array($result) && isset($result['message']) ? $result['message'] : 'Fallo al enviar correo';
            wp_send_json_error($msg);
        }
    }


   public function enqueue_tailwind_scoped() {
            if ( is_singular() && has_shortcode( get_post()->post_content, 'image_marquee_contact' ) ) {

                wp_enqueue_script(
                    'imp-tailwind',
                    'https://cdn.tailwindcss.com',
                    [],
                    null,
                    false
                );

                wp_add_inline_script(
                    'imp-tailwind',
                    "
                    tailwind.config = {
                        important: '.imp-tailwind-scope',
                        corePlugins: {
                            preflight: false
                        }
                    };
                    "
                );
            }
   }
 
   


}

function feather_scripts() {
	wp_enqueue_script( 'feather', plugins_url( 'feather.min.js', __FILE__ ), array(), '4.29.0' );
}

/*function tailwindcss_scripts() {
	wp_enqueue_script( 'tailwindcss', plugins_url( 'tailwindcss.min.js', __FILE__ ), array(), '4.29.0' );
}*/

add_action( 'wp_enqueue_scripts', 'feather_scripts' );
//add_action( 'wp_enqueue_scripts', 'tailwindcss_scripts' );


/*// Call the feather.replace() method.
function run_feather() { ?>
	<script>
		feather.replace();
	</script>
	<?php
}
add_action( 'wp_footer', 'run_feather' );
*/


// Inicializar el plugin
new ContactMarqueePlugin();