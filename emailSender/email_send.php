<?php

class EmailSender {
    private $senderName;
    private $senderEmail;
    private $recipientEmail;
    private $message;

    public function __construct($senderName = '', $senderEmail = '', $recipientEmail = '', $message = '') {
        $this->senderName = trim(strip_tags($senderName));
        $this->senderEmail = filter_var(trim($senderEmail), FILTER_SANITIZE_EMAIL);
        $this->recipientEmail = filter_var(trim($recipientEmail), FILTER_SANITIZE_EMAIL);
        $this->message = trim($message);
    }

    private function get_template_message() {
         // Cargar valores actuales desde la tabla Email_setting
        global $wpdb;
        $table = $wpdb->prefix . 'Email_setting';

        $row = $wpdb->get_row("SELECT * FROM $table LIMIT 1", ARRAY_A);
        if ($row) {
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
                    'honeypot' => isset($row['honeypot']) ? $row['honeypot'] : '0'
                );
                    return $values;
        }

        return array();

    }
    private function send_with_template($template_values, $sender_name, $sender_email, $recipient_email, $message) {
        /**
         * Intenta enviar el correo. Devuelve array con keys: success (bool) y message (string).
         */
        $subject = $template_values['subject'];
        $html_template = $template_values['html_template'];
        $strategy = $template_values['strategy'];
        $spam_check = $template_values['spam_check'];
        $email_validation = $template_values['email_validation'];
        $success_message = $template_values['success_message'];
        $error_message = $template_values['error_message'];
        
        // Reemplazar placeholders en la plantilla HTML
        $placeholders = array(
            '{SENDER_NAME}' => esc_html($sender_name),
            '{SENDER_EMAIL}' => esc_html($sender_email),
            '{MESSAGE}' => nl2br(esc_html($message)),
            '{RECIPIENT_EMAIL}' => esc_html($recipient_email),
            '{RECIPIENT_NAME}' => esc_html($recipient_name)
        );
        $body = strtr($html_template, $placeholders);
        // Preparar cabeceras: usar el correo del sitio como From y poner el remitente real en Reply-To

        $site_email = (isset($template_values['from'])) ? $template_values['from'] : '';
       
        // Validaciones
        if (empty($recipient_email) || !function_exists('is_email') || !is_email($recipient_email)) {
            return array('success' => false, 'message' => $email_validation);
        }
        if (empty($sender_email) || !function_exists('is_email') || !is_email($sender_email)) {
            return array('success' => false, 'message' => $email_validation);
        }
        if (empty(trim($message))) {
            return array('success' => false, 'message' => 'El mensaje está vacío');
        }


        if (!empty($sender_name)) {
            $subject .= ' - ' . $sender_name;
        }

        $headers = array();
        $headers[] = 'From: ' . (!empty($site_name) ? $site_name : 'No Reply') . ' <' . $from_email . '>';
        // Poner el remitente real en Reply-To para que las respuestas vayan al usuario
        if (!empty($sender_email)) {
            $headers[] = 'Reply-To: ' . (!empty($sender_name) ? $sender_name . ' <' . $sender_email . '>' : $sender_email);
        }
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        // Texto plano alternativo
        if (function_exists('wp_strip_all_tags')) {
            $text_body = wp_strip_all_tags($body);
        } else {
            $text_body = strip_tags($body);
        }

        // Configurar phpmailer para establecer AltBody y Sender (Return-Path)
        if (function_exists('add_action')) {
            add_action('phpmailer_init', function($phpmailer) use ($text_body, $from_email) {
                if (!empty($text_body)) {
                    $phpmailer->AltBody = $text_body;
                }
                if (!empty($from_email)) {
                    $phpmailer->Sender = $from_email;
                }
                $phpmailer->addCustomHeader('X-Mailer: WordPress');
            });
        }

        // Intentar enviar con wp_mail si está disponible
        if (function_exists('wp_mail')) {
            $sent = wp_mail($recipient_email, $subject, $body, $headers);
            if ($sent) {
                return array('success' => true, 'message' => $success_message);
            } else {
                return array('success' => false, 'message' => $error_message);
            }
        }

        // Fallback: intentar mail()
        $headers_str = implode("\r\n", $headers);
        $sent = @mail($recipient_email, $subject, $body, $headers_str);
        if ($sent) {
            return array('success' => true, 'message' => $success_message);
        }

        return array('success' => false, 'message' => $error_message);
    

    }

    public function send() {
        // Usar las propiedades ya sanitizadas en el constructor
        $sender_name = $this->senderName;
        $sender_email = $this->senderEmail;
        $recipient_email = $this->recipientEmail;
        $message = $this->message;

        $template_values = $this->get_template_message();
        if(!empty($template_values)){
            return $response = $this->send_with_template($template_values, $sender_name, $sender_email, $recipient_email, $message);
        }

        // Validaciones
        if (empty($recipient_email) || !function_exists('is_email') || !is_email($recipient_email)) {
            return array('success' => false, 'message' => 'Correo destinatario inválido');
        }
        if (empty($sender_email) || !function_exists('is_email') || !is_email($sender_email)) {
            return array('success' => false, 'message' => 'Correo remitente inválido');
        }
        if (empty(trim($message))) {
            return array('success' => false, 'message' => 'El mensaje está vacío');
        }

        $subject = 'Mensaje desde el sitio';
        if (!empty($sender_name)) {
            $subject .= ' - ' . $sender_name;
        }

        $body = "<p>Has recibido un nuevo mensaje desde el formulario de contacto:</p>";
        $body .= "<p><strong>Remitente:</strong> " . esc_html($sender_name) . " &lt;" . esc_html($sender_email) . "&gt;</p>";
        $body .= "<p><strong>Mensaje:</strong></p>";
        $body .= "<div>" . nl2br(esc_html($message)) . "</div>";

        // Preparar cabeceras: usar el correo del sitio como From y poner el remitente real en Reply-To
        $site_email = (function_exists('get_option')) ? get_option('admin_email') : '';
        $site_name = (function_exists('get_bloginfo')) ? get_bloginfo('name') : '';
        $from_email = (!empty($site_email) && is_email($site_email)) ? $site_email : $this->senderEmail;

        $headers = array();
        $headers[] = 'From: ' . (!empty($site_name) ? $site_name : 'No Reply') . ' <' . $from_email . '>';
        // Poner el remitente real en Reply-To para que las respuestas vayan al usuario
        if (!empty($sender_email)) {
            $headers[] = 'Reply-To: ' . (!empty($sender_name) ? $sender_name . ' <' . $sender_email . '>' : $sender_email);
        }
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        // Texto plano alternativo
        if (function_exists('wp_strip_all_tags')) {
            $text_body = wp_strip_all_tags($body);
        } else {
            $text_body = strip_tags($body);
        }

        // Configurar phpmailer para establecer AltBody y Sender (Return-Path)
        if (function_exists('add_action')) {
            add_action('phpmailer_init', function($phpmailer) use ($text_body, $from_email) {
                if (!empty($text_body)) {
                    $phpmailer->AltBody = $text_body;
                }
                if (!empty($from_email)) {
                    $phpmailer->Sender = $from_email;
                }
                $phpmailer->addCustomHeader('X-Mailer: WordPress');
            });
        }

        // Intentar enviar con wp_mail si está disponible
        if (function_exists('wp_mail')) {
            $sent = wp_mail($recipient_email, $subject, $body, $headers);
            if ($sent) {
                return array('success' => true, 'message' => 'Correo enviado correctamente');
            } else {
                return array('success' => false, 'message' => 'Fallo al enviar correo (wp_mail)');
            }
        }

        // Fallback: intentar mail()
        $headers_str = implode("\r\n", $headers);
        $sent = @mail($recipient_email, $subject, $body, $headers_str);
        if ($sent) {
            return array('success' => true, 'message' => 'Correo enviado correctamente (mail)');
        }

        return array('success' => false, 'message' => 'Fallo al enviar correo (mail)');
    
    }
}


?>