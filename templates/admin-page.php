<?php
// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Obtener nombres de marquesinas existentes
global $wpdb;
$table_name = $wpdb->prefix . 'image_marquees_contacts';
$existing_marquees = $wpdb->get_col("SELECT DISTINCT marquee_name FROM $table_name");
?>

<div class="wrap">
    <h1>Marquesinas de Directorios</h1>
    
    <div id="imp-admin-container">
        <div class="imp-admin-header">
            <div class="imp-marquee-selector">
                <label for="marquee-name-select">Seleccionar Marquesina:</label>
                <select id="marquee-name-select">
                    <option value="">-- Nueva Marquesina --</option>
                    <?php if (!empty($existing_marquees)): ?>
                        <?php foreach ($existing_marquees as $marquee_name): ?>
                            <option value="<?php echo esc_attr($marquee_name); ?>">
                                <?php echo esc_html($marquee_name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                
                <input type="text" id="new-marquee-name" placeholder="Nombre de la nueva marquesina" style="margin-left: 10px;">
                
                <button type="button" id="load-marquee" class="button">Cargar</button>
                <button type="button" id="delete-marquee" class="button button-secondary">Eliminar</button>
            </div>
        </div>
        
        <div class="imp-admin-content">
            <div class="imp-current-marquee">
                <h2>Marquesina: <span id="current-marquee-name">Nueva</span></h2>
            </div>
            
            <div class="imp-actions">
                <button type="button" id="add-image" class="button button-primary">Agregar Imagen</button>
                <button type="button" id="save-marquee" class="button button-secondary">Guardar Marquesina</button>
            </div>
            
            <div id="images-container">
                <div class="imp-no-images">
                    <p>No hay imágenes en esta marquesina. Haz clic en "Agregar Imagen" para comenzar.</p>
                </div>
            </div>
        </div>
        
        <div class="imp-shortcode-info">
            <h3>Cómo usar</h3>
            <p>Para mostrar una marquesina en tu sitio web, usa el shortcode:</p>
            <code>[image_marquee_contact name="nombre_de_tu_marquesina"]</code>
            
            <h4>Parámetros opcionales:</h4>
            <ul>
                <li><strong>speed</strong>: Velocidad en píxeles por segundo (por defecto: configuración del Customizer)</li>
                <li><strong>height</strong>: Altura en píxeles (por defecto: configuración del Customizer)</li>
                <li><strong>direction</strong>: Dirección "left" o "right" (por defecto: configuración del Customizer)</li>
            </ul>
            
            <p>Ejemplo con parámetros personalizados:</p>
            <code>[image_marquee_contact name="mi_marquesina" speed="30" height="150" direction="right"]</code>
        </div>
    </div>
</div>

<!-- Template para elementos de imagen -->
<script type="text/html" id="image-item-template">
    <div class="imp-image-item" data-id="{{id}}">
        <div class="imp-image-preview">
            <img src="{{url}}" alt="Preview">
            <input type="hidden" class="image-url" value="{{url}}">
            <button type="button" class="button imp-change-image">
                Cambiar imagen
            </button>
        </div>
        <div class="imp-image-controls">
            <div class="imp-input-group">
                <label>Título:</label>
                <input type="text" class="image-title" value="{{title}}" placeholder="Título de la imagen">
            </div>
            <div class="imp-input-group">
                <label>Subtítulo:</label>
                <input type="text" class="image-subtitle" value="{{subtitle}}" placeholder="Subtítulo de la imagen">
            </div>
            <div class="imp-input-group">
                <label>Descripci&oacute;n o reseña:</label>
                <textarea class="image-story" placeholder="Descripción o reseña de la imagen" >{{story}}</textarea>
            </div>
            <div class="imp-contact-section">
                <h4>Información de Contacto</h4>
                <div class="imp-contact-grid">
                    <div class="imp-input-group">
                        <label>Teléfono:</label>
                        <input type="text" class="image-phone" value="{{phone}}" placeholder="+1234567890">
                    </div>
                    <div class="imp-input-group">
                        <label>Correo electrónico:</label>
                        <input 
                            type="email" 
                            class="image-email" 
                            value="" 
                            placeholder="correo@ejemplo.com"
                        >
                    </div>

                    <div class="imp-input-group">
                        <label>WhatsApp:</label>
                        <input type="text" class="image-whatsapp" value="{{whatsapp}}" placeholder="1234567890">
                    </div>
                </div>
                
                <h4>Redes Sociales</h4>
                <div class="imp-social-grid">
                    <div class="imp-input-group">
                        <label>Instagram:</label>
                        <input type="url" class="image-instagram" value="{{instagram}}" placeholder="https://instagram.com/usuario">
                    </div>
                    <div class="imp-input-group">
                        <label>Facebook:</label>
                        <input type="url" class="image-facebook" value="{{facebook}}" placeholder="https://facebook.com/usuario">
                    </div>
                    <div class="imp-input-group">
                        <label>TikTok:</label>
                        <input type="url" class="image-tiktok" value="{{tiktok}}" placeholder="https://tiktok.com/@usuario">
                    </div>
                    <div class="imp-input-group">
                        <label>Telegram:</label>
                        <input type="url" class="image-telegram" value="{{telegram}}" placeholder="https://t.me/usuario">
                    </div>
                    <div class="imp-input-group">
                        <label>LinkedIn:</label>
                        <input type="url" class="image-linkedin" value="{{linkedin}}" placeholder="https://linkedin.com/in/usuario">
                    </div>
                </div>
            </div>
            
            <div class="imp-image-actions">
                <button type="button" class="button remove-image">Eliminar</button>
                <span class="imp-drag-handle">≡</span>
            </div>
        </div>
    </div>
</script>