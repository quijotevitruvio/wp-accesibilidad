# WP Accesibilidad

> Panel flotante de accesibilidad (letra, modo oscuro, dislexia, guía de lectura) e insignia «Formato accesible» para libros.

English: [README.en.md](README.en.md)

## Qué es
- **Panel ♿** a la izquierda de todo el sitio: tamaño de texto (80–150 %), subrayar enlaces, mayor espaciado, fuente para dislexia (OpenDyslexic), guía de lectura y «restablecer».
- **Modo oscuro global** con un botón propio (🌙/☀): pone `html[data-theme="dark"]` y aplica Dark Reader; recuerda la elección en el navegador. Puedes indicar un logo distinto para claro y oscuro.
- **Formato accesible** (módulo opcional para tiendas de libros): insignia en la ficha y en las tarjetas de WooCommerce, y metadatos `accessibilityFeature` / `accessMode` en el schema del producto (vía Rank Math).
- Todo se guarda **en el dispositivo del visitante** (localStorage); no se envía nada a tu servidor.

## Para quién
Sitios WordPress que quieren dar opciones de lectura sin instalar un plugin pesado, y librerías que venden ediciones en letra grande, braille, EPUB accesible o audiolibro.

## Requisitos
- WordPress 6.0 o superior, PHP 7.4 o superior.
- Módulo «Formato accesible»: WooCommerce, plantilla de producto por bloques y un atributo global con slug `formato-accesible`. Rank Math es opcional (solo para el schema).
- El visitante necesita conexión a jsDelivr (CDN) para el modo oscuro y la fuente de dislexia.

## Instalación
1. Copia la carpeta `wp-accesibilidad/` del repositorio en `wp-content/plugins/` y actívalo.
2. El panel aparece solo, sin configurar nada.
3. Para el módulo de libros: en Productos → Atributos crea «Formato accesible» (slug `formato-accesible`) con los términos `letra-grande`, `braille`, `epub-accesible`, `audiolibro` y asígnalos a tus productos.

## Configuración
Ajustes → Accesibilidad (opción `lb_options`, todo vacío por defecto):

| Ajuste | Clave en `lb_options` | Por defecto | Qué hace |
|---|---|---|---|
| Dónde está el logo | `logo_selector` | vacío | Selector CSS de la imagen del logo; vacío = no se cambia |
| Logo en modo claro | `light_logo_url` | vacío | URL del logo claro |
| Logo en modo oscuro | `dark_logo_url` | vacío | URL del logo oscuro |
| Imágenes sin filtro | `keep_images` | vacío | Las imágenes conservan su brillo en modo oscuro |
| Color del panel | `color` | `#1d4ed8` | Color hex de botones y títulos (variable CSS `--pa-a11y-color`) |
| Color al pasar el cursor | `color_hover` | `#1e3a8a` | Color hex del hover (`--pa-a11y-color-hover`) |
| Color de la insignia | `badge_color` | `#1e5631` | Color hex de la insignia «Formato accesible» (`--pa-fa-color`) |
| URL de guía | `guide_url` | vacío | Si se rellena, el panel muestra un enlace a esa página |

## Interruptores y opciones
- `update_option( 'pa_accesibilidad_panel_off', 1 )` apaga el panel y el modo oscuro.
- `update_option( 'pa_accesibilidad_libros_off', 1 )` apaga la insignia «Formato accesible».
- Cada visitante restablece sus preferencias con el botón «Restablecer» del panel.

## Cómo deshacer
- Pon la opción en 0 (o bórrala) y la pieza vuelve.
- Desactivar el plugin lo quita todo; solo quedan la opción `lb_options` y claves de localStorage en los navegadores.

## Preguntas frecuentes

**¿Funciona con cualquier tema?**  
Sí para el panel y el modo oscuro. La insignia de libros busca los bloques `woocommerce/product-price` o `product-meta`; con una plantilla clásica sale en las tarjetas de la tienda, pero no en la ficha.

**¿El modo oscuro cambia los colores de mi tema?**  
Usa Dark Reader (cargado desde jsDelivr), que recolorea la página al vuelo. El resultado depende de cada tema; puedes dejar las imágenes sin filtro.

**¿Recoge datos de los visitantes?**  
No. Las preferencias viven en localStorage. Solo se hacen peticiones al CDN de jsDelivr.

**¿Cumple WCAG?**  
Ayuda a los lectores, pero no sustituye una auditoría de accesibilidad de tu tema y contenido.

## Limitaciones
- La carga de Dark Reader y OpenDyslexic depende de un CDN externo (no se empaquetan).
- Los textos del panel están en español y no están preparados para traducción.
- Las claves de localStorage usan el prefijo `pa_a11y_` (fontsize, theme, ul, sp, dys, guide).
- Dark Reader 4.9.92 y OpenDyslexic se cargan desde jsDelivr (licencias MIT y SIL OFL); la IP del lector llega al CDN al activarlos.

## Licencia, autor y apoyo
- Licencia: GPL-2.0-or-later (ver `LICENSE`).
- Autor: **Andrés Valencia Tobón** · [GitHub](https://github.com/quijotevitruvio) · [LinkedIn](https://www.linkedin.com/in/andr%C3%A9s-valencia-tob%C3%B3n/)
- Apoya el proyecto: https://paypal.me/bibliotecologo
- Desarrollado para librosmedellin.com.
