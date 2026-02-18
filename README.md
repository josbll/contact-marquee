# Plugin de Marquesinas de Imágenes para WordPress

**Versión 2.6 - Con Campo reseña historica y diversos parrafos**

## Descripción
Plugin completo para crear y gestionar marquesinas de imágenes con títulos, subtítulos y información de contacto. Incluye campos para teléfono y redes sociales (Instagram, Facebook, TikTok, WhatsApp, Telegram, LinkedIn). Permite crear múltiples marquesinas, configurar su apariencia desde el Customizer de WordPress y mostrarlas en cualquier lugar usando shortcodes.

## Características
- ✅ Interfaz de administración intuitiva para gestionar imágenes
- ✅ Soporte para títulos y subtítulos en cada imagen
- ✅ Campos de contacto: teléfono y WhatsApp
- ✅ Enlaces a redes sociales: Instagram, Facebook, TikTok, Telegram, LinkedIn
- ✅ Iconos de contacto con efectos hover y colores específicos
- ✅ Estilos de texto completamente personalizables desde el Customizer
- ✅ Control de tamaño, color y peso de fuente para títulos y subtítulos
- ✅ Tamaño de iconos de contacto ajustable
- ✅ Opción para mostrar/ocultar información de contacto
- ✅ Sistema de múltiples marquesinas
- ✅ Shortcode flexible para insertar marquesinas
- ✅ Configuración desde el Customizer de WordPress
- ✅ Diseño responsivo
- ✅ Efectos hover y animaciones suaves
- ✅ Función de arrastrar y soltar para reordenar imágenes
- ✅ Vista previa en tiempo real desde el Customizer
- ✅ Animación continua que considera anchos individuales de imágenes
- ✅ Sin reinicios abruptos de animación

## Instalación

1. Crea una carpeta llamada `image-marquee-plugin` en el directorio `/wp-content/plugins/` de tu WordPress
2. Copia todos los archivos del plugin en esa carpeta
3. Ve al panel de administración de WordPress → Plugins
4. Busca "Image Marquee Plugin" y actívalo
5. ¡Listo! Ya puedes usar el plugin

## Estructura de archivos
```
image-marquee-plugin/
├── image-marquee-plugin.php    # Archivo principal del plugin
├── templates/
│   └── admin-page.php          # Página de administración
├── assets/
│   ├── style.css              # Estilos del frontend
│   ├── script.js              # JavaScript del frontend
│   ├── admin-style.css        # Estilos del admin
│   └── admin-script.js        # JavaScript del admin
└── README.md                  # Este archivo
```

## Uso

### 1. Crear una marquesina
1. Ve a **Marquesinas** en el menú del administrador
2. Introduce el nombre de una nueva marquesina o selecciona una existente
3. Haz clic en **Cargar**
4. Usa **Agregar Imagen** para subir imágenes
5. Añade títulos y subtítulos a cada imagen
6. Completa la información de contacto y redes sociales (opcional)
7. Arrastra las imágenes para reordenarlas
8. Haz clic en **Guardar Marquesina**

### Campos disponibles para cada imagen:
- **Título**: Texto principal que aparece sobre la imagen
- **Subtítulo**: Texto secundario debajo del título
- **Teléfono**: Número de teléfono (crea enlace tel:)
- **WhatsApp**: Número para WhatsApp (crea enlace wa.me)
- **Instagram**: URL del perfil de Instagram
- **Facebook**: URL del perfil de Facebook
- **TikTok**: URL del perfil de TikTok
- **Telegram**: URL del canal/usuario de Telegram
- **LinkedIn**: URL del perfil de LinkedIn
6. Arrastra las imágenes para reordenarlas
7. Haz clic en **Guardar Marquesina**

### 2. Mostrar marquesinas
Usa el shortcode `[image_marquee]` con los siguientes parámetros:

#### Parámetros básicos:
- `name`: Nombre de la marquesina (requerido)

```
[image_marquee name="mi_marquesina"]
```

#### Parámetros opcionales:
- `speed`: Velocidad en píxeles por segundo
- `height`: Altura en píxeles  
- `direction`: Dirección ("left" o "right")

```
[image_marquee name="mi_marquesina" speed="30" height="150" direction="right"]
```

### 3. Configuración global
Ve a **Apariencia → Personalizar** para configurar en tiempo real:

#### Configuración de Marquesinas:
- Velocidad predeterminada
- Altura predeterminada  
- Dirección predeterminada

#### Estilos de Texto de Marquesinas:
- **Título**: Tamaño de fuente (10-32px), color, peso de fuente
- **Subtítulo**: Tamaño de fuente (8-24px), color, peso de fuente  
- **Iconos de contacto**: Tamaño ajustable (20-50px)
- **Visibilidad**: Mostrar/ocultar información de contacto

Los cambios se reflejan inmediatamente en la vista previa sin necesidad de recargar la página.
## Funcionalidades detalladas

### Versión 1.1 - Características principales

#### Información de contacto y redes sociales
- **Enlaces de contacto**: Cada imagen puede incluir teléfono y WhatsApp con enlaces directos
- **Redes sociales**: Soporte completo para Instagram, Facebook, TikTok, Telegram y LinkedIn
- **Iconos intuitivos**: Cada red social tiene su emoji característico
- **Efectos visuales**: Hover con colores específicos de cada plataforma
- **Responsive**: Los iconos se adaptan a diferentes tamaños de pantalla
#### Animación avanzada
- **Flujo continuo**: La animación considera el ancho individual de cada imagen para un movimiento fluido
- **Sin reinicios**: La animación no se reinicia hasta que todas las imágenes hayan transitado completamente
- **Cálculo dinámico**: Ajusta automáticamente la duración basada en el contenido real
- **Responsive**: Se adapta automáticamente a cambios de tamaño de ventana

#### Customizer integrado
- **Vista previa en tiempo real**: Los cambios se reflejan inmediatamente sin recargar
- **Controles intuitivos**: Deslizadores para velocidad y altura, selector para dirección
- **Configuración global**: Establece valores predeterminados para todas las marquesinas

### Gestión de imágenes
- **Subir imágenes**: Integración completa con la biblioteca de medios de WordPress
- **Títulos y subtítulos**: Campos de texto para cada imagen
- **Reordenamiento**: Arrastra y suelta para cambiar el orden
- **Vista previa**: Miniatura de cada imagen en la administración

### Marquesinas múltiples
- Crear marquesinas ilimitadas
- Cada marquesina tiene un nombre único
- Gestión independiente de cada marquesina
- Eliminar marquesinas completas

### Personalización
- **Velocidad ajustable**: Control preciso de la velocidad de desplazamiento
- **Direcciones**: Izquierda a derecha o derecha a izquierda
- **Altura flexible**: Ajusta la altura según tus necesidades
- **Estilos de texto personalizables**: Control completo sobre fuentes, colores y tamaños
- **Iconos ajustables**: Tamaño de iconos de contacto configurable
- **Visibilidad de contactos**: Opción para mostrar/ocultar información de contacto
- **Efectos hover**: Pausa automática al pasar el mouse
- **Vista previa en tiempo real**: Los cambios en el Customizer se ven inmediatamente
- **Animación continua**: La animación considera el ancho individual de cada imagen para un flujo continuo
- **Sin reinicios**: La animación no se reinicia hasta que todas las imágenes hayan transitado completamente

### Diseño responsivo
- Adaptación automática a diferentes tamaños de pantalla
- Optimización para móviles y tablets
- Mantenimiento de proporciones de imagen

## CSS personalizado
Puedes añadir CSS personalizado para modificar la apariencia:

```css
/* Personalizar el contenedor */
.imp-marquee-container {
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Personalizar el texto */
.imp-marquee-text {
    background: linear-gradient(transparent, rgba(0,123,255,0.8));
}

/* Personalizar las imágenes */
.imp-marquee-item img {
    filter: brightness(1.1);
}
```

## Solución de problemas

### Las imágenes no se muestran
- Verifica que el nombre de la marquesina en el shortcode sea correcto
- Asegúrate de que la marquesina tenga imágenes guardadas
- Revisa que el plugin esté activado

### La animación es muy rápida/lenta
- Ajusta el parámetro `speed` en el shortcode
- Modifica la configuración global en el Customizer
- Los valores más bajos = más lento, valores más altos = más rápido

### Problemas de responsive
- El plugin está diseñado para ser responsivo automáticamente
- Si tienes problemas, revisa que tu tema no interfiera con los estilos

## Desarrollador
Este plugin utiliza:
- WordPress Hooks y Actions
- jQuery para interactividad
- CSS3 para animaciones
- WordPress Media Library API
- WordPress Customizer API

## Soporte
Si encuentras algún problema o tienes sugerencias de mejora, por favor documenta el issue detalladamente.

## Licencia