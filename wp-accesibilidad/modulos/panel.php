<?php
/**
 * Módulo «Panel de accesibilidad».
 * Botón ♿ y botón de modo oscuro a la izquierda: tamaño de letra, modo oscuro
 * (html[data-theme], localStorage pa_a11y_theme), subrayar enlaces, espaciado,
 * dislexia y guía de lectura.
 * Ajustes: Ajustes → Accesibilidad (opción lb_options).
 * Apagar: update_option( 'pa_accesibilidad_panel_off', 1 );
 */

if (!defined('ABSPATH')) {
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// 1. CABEZA ── Aplica preferencias guardadas ANTES de pintar (sin parpadeo)
//    + arranque del motor DarkReader si el lector dejó el modo oscuro activo.
// ══════════════════════════════════════════════════════════════════════════════
/** Color de ajustes (hex válido) o el de serie. */
function pa_a11y_color($key, $default)
{
    $o = get_option('lb_options', []);
    $c = isset($o[$key]) ? sanitize_hex_color($o[$key]) : '';
    return $c ? $c : $default;
}

add_action('wp_head', 'lb_a11y_head', 1);
function lb_a11y_head()
{
    ?>
    <script>
    (function(){
        try{
            var h = document.documentElement;
            var fs = parseInt(localStorage.getItem('pa_a11y_fontsize') || '100', 10);
            if (fs && fs !== 100) h.style.fontSize = fs + '%';
            if (localStorage.getItem('pa_a11y_ul') === '1') h.classList.add('lb-a11y-ul');
            if (localStorage.getItem('pa_a11y_sp') === '1') h.classList.add('lb-a11y-sp');
            if (localStorage.getItem('pa_a11y_dys') === '1') h.classList.add('lb-a11y-dys');
        }catch(e){}
    })();
    </script>
    <?php
    // CWV: solo quien YA dejó activo el modo oscuro descarga DarkReader (async, anti-parpadeo).
    ?>
    <script>
    (function(){
        try {
            if (localStorage.getItem('pa_a11y_theme') !== 'dark') return;
            document.documentElement.setAttribute('data-theme', 'dark');

            var boot = document.createElement('style');
            boot.id = 'lm-dark-bootstrap';
            boot.textContent = 'html{background:#181818!important}';
            (document.head || document.documentElement).appendChild(boot);

            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/darkreader@4.9.92/darkreader.min.js';
            s.async = true;
            s.onload = function(){
                try {
                    DarkReader.setFetchMethod(window.fetch);
                    DarkReader.enable({ brightness: 100, contrast: 95, sepia: 0 });
                } catch(e) {}
                var b = document.getElementById('lm-dark-bootstrap');
                if (b) b.remove();
            };
            (document.head || document.documentElement).appendChild(s);
        } catch(e) {}
    })();
    </script>
    <style id="lb-a11y-css">
        :root{ --pa-a11y-color: <?php echo esc_html( pa_a11y_color( 'color', '#1d4ed8' ) ); ?>; --pa-a11y-color-hover: <?php echo esc_html( pa_a11y_color( 'color_hover', '#1e3a8a' ) ); ?>; }
        /* ── Preferencias de lectura ── */
        html.lb-a11y-ul a{ text-decoration: underline !important; text-underline-offset: 2px; }
        html.lb-a11y-sp p, html.lb-a11y-sp li, html.lb-a11y-sp{
            line-height: 1.95 !important; letter-spacing: .03em !important; word-spacing: .08em !important;
        }

        /* ── Fuente para dislexia (OpenDyslexic, se descarga solo si se activa) ── */
        @font-face{ font-family:'OpenDyslexic'; font-display:swap; src:url('https://cdn.jsdelivr.net/gh/antijingoist/opendyslexic/compiled/OpenDyslexic-Regular.woff2') format('woff2'); }
        html.lb-a11y-dys p, html.lb-a11y-dys li, html.lb-a11y-dys h1, html.lb-a11y-dys h2,
        html.lb-a11y-dys h3, html.lb-a11y-dys h4, html.lb-a11y-dys h5, html.lb-a11y-dys h6,
        html.lb-a11y-dys blockquote, html.lb-a11y-dys .entry-content,
        html.lb-a11y-dys .woocommerce-product-details__short-description, html.lb-a11y-dys{
            font-family:'OpenDyslexic','Comic Sans MS','Verdana',sans-serif !important;
        }

        /* ── Guía de lectura (banda que sigue el cursor) ── */
        .lb-a11y-guide-bar{ position:fixed; left:0; width:100%; height:40px; pointer-events:none; z-index:99997; display:none;
            background:color-mix(in srgb,var(--pa-a11y-color) 10%,transparent); border-top:2px solid color-mix(in srgb,var(--pa-a11y-color) 55%,transparent); border-bottom:2px solid color-mix(in srgb,var(--pa-a11y-color) 55%,transparent); }
        html.lb-a11y-guide .lb-a11y-guide-bar{ display:block; }

        /* ── Botones flotantes (borde izquierdo, centrados, apilados) ── */
        .lb-a11y-fabs{ position:fixed; left:0; top:50%; transform:translateY(-50%); z-index:99998; display:flex; flex-direction:column; gap:10px; }
        .lb-a11y-fabs button{
            width:46px; height:46px; border:0; cursor:pointer;
            background:var(--pa-a11y-color); color:#fff; border-radius:0 12px 12px 0;
            box-shadow:0 3px 12px rgba(0,0,0,.25); font-size:24px; line-height:1;
            display:flex; align-items:center; justify-content:center;
        }
        .lb-a11y-fabs button:hover{ background:var(--pa-a11y-color-hover); }
        .lb-a11y-fabs button:focus-visible{ outline:3px solid color-mix(in srgb,var(--pa-a11y-color) 45%,transparent); outline-offset:2px; }

        /* ── Panel ── */
        .lb-a11y-panel{
            position:fixed; left:14px; top:50%; transform:translateY(-50%) scale(.96);
            z-index:99999; width:290px; max-width:calc(100vw - 28px);
            background:#fff; color:#1a1a1a; border-radius:16px; padding:16px 16px 14px;
            box-shadow:0 12px 40px rgba(0,0,0,.28); border:1px solid #eee;
            font-family:inherit; opacity:0; visibility:hidden; transition:opacity .15s, transform .15s;
        }
        .lb-a11y-panel[data-open="1"]{ opacity:1; visibility:visible; transform:translateY(-50%) scale(1); }
        .lb-a11y-panel h3{ margin:0 0 2px; font-size:16px; color:var(--pa-a11y-color); display:flex; align-items:center; gap:7px; }
        .lb-a11y-panel .lb-a11y-sub{ margin:0 0 12px; font-size:12px; color:#666; }
        .lb-a11y-row{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:9px 0; border-top:1px solid #f1f1f1; }
        .lb-a11y-row > span{ font-size:14px; font-weight:600; }
        .lb-a11y-size{ display:flex; gap:6px; align-items:center; }
        .lb-a11y-size button{ width:34px; height:32px; border:1px solid #ddd; background:#fafafa; border-radius:8px; cursor:pointer; font-weight:700; color:#333; line-height:1; }
        .lb-a11y-size button:hover{ background:#f0f0f0; }
        .lb-a11y-size .lb-a11y-lvl{ min-width:38px; text-align:center; font-size:12px; color:#666; }
        .lb-a11y-toggle{ position:relative; width:46px; height:26px; border:0; border-radius:999px; background:#ccc; cursor:pointer; flex:0 0 auto; transition:background .15s; }
        .lb-a11y-toggle::after{ content:""; position:absolute; top:3px; left:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:left .15s; }
        .lb-a11y-toggle[aria-pressed="true"]{ background:var(--pa-a11y-color); }
        .lb-a11y-toggle[aria-pressed="true"]::after{ left:23px; }
        .lb-a11y-reset{ margin-top:12px; width:100%; border:1px solid var(--pa-a11y-color); background:#fff; color:var(--pa-a11y-color); border-radius:10px; padding:9px; font-weight:700; cursor:pointer; }
        .lb-a11y-reset:hover{ background:var(--pa-a11y-color); color:#fff; }
        .lb-a11y-close{ position:absolute; top:10px; right:12px; border:0; background:transparent; font-size:20px; line-height:1; cursor:pointer; color:#999; }
        .lb-a11y-help{ display:block; margin-top:10px; font-size:12px; color:var(--pa-a11y-color); text-decoration:underline; text-align:center; }
        @media (max-width:782px){
            .lb-a11y-fabs button{ width:42px; height:42px; font-size:22px; }
            .lb-a11y-panel{ left:10px; right:10px; width:auto; }
        }
        @media print{ .lb-a11y-launch, .lb-a11y-panel{ display:none !important; } }
    </style>
    <?php
}

// ══════════════════════════════════════════════════════════════════════════════
// 2. PANEL DE AJUSTES (admin) ── Logo en modo oscuro / imágenes
//    wp-admin → Ajustes → Accesibilidad LM
// ══════════════════════════════════════════════════════════════════════════════
add_action('admin_menu', 'lb_add_admin_menu');
function lb_add_admin_menu()
{
    add_options_page(
        'Accesibilidad',
        'Accesibilidad',
        'manage_options',
        'pa-accesibilidad',
        'lb_options_page'
    );
}

// El enlace viejo (page=lb-accesibilidad) sigue llevando a la pantalla.
add_action('admin_init', function () {
    if (isset($_GET['page']) && 'lb-accesibilidad' === $_GET['page'] && false !== strpos((string) ($_SERVER['PHP_SELF'] ?? ''), 'options-general.php')) {
        wp_safe_redirect(admin_url('options-general.php?page=pa-accesibilidad'));
        exit;
    }
});

add_action('admin_init', 'lb_register_settings');
function lb_register_settings()
{
    register_setting('lb_options_group', 'lb_options', 'lb_sanitize_options');
}

function lb_sanitize_options($input)
{
    return [
        'dark_logo_url'  => isset($input['dark_logo_url'])  ? esc_url_raw($input['dark_logo_url'])         : '',
        'light_logo_url' => isset($input['light_logo_url']) ? esc_url_raw($input['light_logo_url'])        : '',
        'logo_selector'  => isset($input['logo_selector'])  ? sanitize_text_field($input['logo_selector']) : '',
        'keep_images'    => !empty($input['keep_images'])   ? '1'                                          : '',
        'color'          => isset($input['color'])          ? (string) sanitize_hex_color($input['color'])       : '',
        'color_hover'    => isset($input['color_hover'])    ? (string) sanitize_hex_color($input['color_hover']) : '',
        'badge_color'    => isset($input['badge_color'])    ? (string) sanitize_hex_color($input['badge_color']) : '',
        'guide_url'      => isset($input['guide_url'])      ? esc_url_raw($input['guide_url'])                   : '',
    ];
}

function lb_options_page()
{
    $options = get_option('lb_options', []);
    ?>
    <div class="wrap">
        <h1>Accesibilidad</h1>
        <p>El botón ♿ del sitio ya funciona solo. Aquí solo eliges el logo para el modo oscuro.</p>
        <form method="post" action="options.php">
            <?php settings_fields('lb_options_group'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="lb_logo_selector">Dónde está el logo</label></th>
                    <td>
                        <input type="text" id="lb_logo_selector" name="lb_options[logo_selector]"
                            value="<?php echo esc_attr($options['logo_selector'] ?? ''); ?>"
                            class="regular-text" placeholder=".site-logo img, .custom-logo" />
                        <p class="description">Selector CSS de la imagen del logo. Vacío = no se cambia el logo.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="lb_light_logo_url">Logo en modo claro</label></th>
                    <td>
                        <input type="url" id="lb_light_logo_url" name="lb_options[light_logo_url]"
                            value="<?php echo esc_attr($options['light_logo_url'] ?? ''); ?>"
                            class="regular-text" placeholder="https://example.com/logo-claro.png" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="lb_dark_logo_url">Logo en modo oscuro</label></th>
                    <td>
                        <input type="url" id="lb_dark_logo_url" name="lb_options[dark_logo_url]"
                            value="<?php echo esc_attr($options['dark_logo_url'] ?? ''); ?>"
                            class="regular-text" placeholder="https://example.com/logo-oscuro.png" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="lb_color">Color del panel</label></th>
                    <td><input type="text" id="lb_color" name="lb_options[color]" value="<?php echo esc_attr($options['color'] ?? ''); ?>" class="regular-text" placeholder="#1d4ed8" />
                    <p class="description">Color hexadecimal de botones y títulos. Vacío = azul de serie.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="lb_color_hover">Color al pasar el cursor</label></th>
                    <td><input type="text" id="lb_color_hover" name="lb_options[color_hover]" value="<?php echo esc_attr($options['color_hover'] ?? ''); ?>" class="regular-text" placeholder="#1e3a8a" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="lb_badge_color">Color de la insignia «Formato accesible»</label></th>
                    <td><input type="text" id="lb_badge_color" name="lb_options[badge_color]" value="<?php echo esc_attr($options['badge_color'] ?? ''); ?>" class="regular-text" placeholder="#1e5631" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="lb_guide_url">URL de una página de guía</label></th>
                    <td><input type="url" id="lb_guide_url" name="lb_options[guide_url]" value="<?php echo esc_attr($options['guide_url'] ?? ''); ?>" class="regular-text" placeholder="https://example.com/accesibilidad/" />
                    <p class="description">Opcional. Si la pones, el panel muestra un enlace «Ver guía de accesibilidad».</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="lb_keep_images">Imágenes sin filtro</label></th>
                    <td>
                        <input type="checkbox" id="lb_keep_images" name="lb_options[keep_images]" value="1"
                            <?php checked(!empty($options['keep_images'])); ?> />
                        <span>Las imágenes conservan su brillo en modo oscuro</span>
                        <p class="description">Sin marcar, se les baja un poco el brillo.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Guardar'); ?>
        </form>
    </div>
    <?php
}

// ══════════════════════════════════════════════════════════════════════════════
// 3. PANEL FLOTANTE (front) ── Lanzador ♿ + panel agrupado + motor
// ══════════════════════════════════════════════════════════════════════════════
add_action('wp_footer', 'lb_add_a11y_panel');
function lb_add_a11y_panel()
{
    $options       = get_option('lb_options', []);
    $dark_logo     = esc_js($options['dark_logo_url']  ?? '');
    $light_logo    = esc_js($options['light_logo_url'] ?? '');
    $logo_selector = esc_js($options['logo_selector']  ?? '');
    $guide_url     = $options['guide_url'] ?? '';
    ?>
    <div class="lb-a11y-fabs">
        <button type="button" class="lb-a11y-dark-fab" aria-pressed="false" aria-label="Modo oscuro" title="Modo oscuro">&#127769;</button>
        <button type="button" class="lb-a11y-launch" aria-haspopup="dialog" aria-expanded="false" aria-label="Opciones de accesibilidad" title="Accesibilidad"><svg viewBox="0 0 24 24" width="27" height="27" fill="currentColor" aria-hidden="true" focusable="false"><path d="M20.5 6c-2.61.7-5.67 1-8.5 1s-5.89-.3-8.5-1L3 8c1.86.5 4 .83 6 1v13h2v-6h2v6h2V9c2-.17 4.14-.5 6-1l-.5-2zM12 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/></svg></button>
    </div>
    <div class="lb-a11y-panel" role="dialog" aria-label="Opciones de accesibilidad" aria-modal="false">
        <button type="button" class="lb-a11y-close" aria-label="Cerrar">&times;</button>
        <h3><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false" style="flex:0 0 auto"><path d="M20.5 6c-2.61.7-5.67 1-8.5 1s-5.89-.3-8.5-1L3 8c1.86.5 4 .83 6 1v13h2v-6h2v6h2V9c2-.17 4.14-.5 6-1l-.5-2zM12 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/></svg> Accesibilidad</h3>
        <p class="lb-a11y-sub">Ajusta la lectura a tu gusto. Se guarda en tu dispositivo.</p>

        <div class="lb-a11y-row">
            <span>Tamaño de texto</span>
            <div class="lb-a11y-size">
                <button type="button" class="lb-a11y-dec" aria-label="Reducir texto">A&minus;</button>
                <span class="lb-a11y-lvl" aria-live="polite">100%</span>
                <button type="button" class="lb-a11y-inc" aria-label="Agrandar texto">A+</button>
            </div>
        </div>
        <div class="lb-a11y-row">
            <span>Subrayar enlaces</span>
            <button type="button" class="lb-a11y-toggle lb-a11y-t-ul" aria-pressed="false" aria-label="Subrayar enlaces"></button>
        </div>
        <div class="lb-a11y-row">
            <span>Mayor espaciado</span>
            <button type="button" class="lb-a11y-toggle lb-a11y-t-sp" aria-pressed="false" aria-label="Aumentar espaciado del texto"></button>
        </div>
        <div class="lb-a11y-row">
            <span>Fuente para dislexia</span>
            <button type="button" class="lb-a11y-toggle lb-a11y-t-dys" aria-pressed="false" aria-label="Fuente para dislexia"></button>
        </div>
        <div class="lb-a11y-row">
            <span>Guía de lectura</span>
            <button type="button" class="lb-a11y-toggle lb-a11y-t-guide" aria-pressed="false" aria-label="Guía de lectura"></button>
        </div>

        <button type="button" class="lb-a11y-reset">Restablecer todo</button>
        <?php if ( $guide_url ) : ?>
        <a class="lb-a11y-help" href="<?php echo esc_url($guide_url); ?>">¿Cómo funciona? Ver guía de accesibilidad</a>
        <?php endif; ?>
    </div>

    <script>
    (function () {
        'use strict';
        var html   = document.documentElement;
        var launch = document.querySelector('.lb-a11y-launch');
        var panel  = document.querySelector('.lb-a11y-panel');
        if (!launch || !panel) return;

        // ── Config logos (desde Ajustes) ──
        var darkLogoUrl  = '<?php echo $dark_logo; ?>';
        var lightLogoUrl = '<?php echo $light_logo; ?>';
        var logoSelector = '<?php echo $logo_selector; ?>';

        var lvlEl = panel.querySelector('.lb-a11y-lvl');
        var tDark = document.querySelector('.lb-a11y-dark-fab'); // botón flotante aparte
        var tUl   = panel.querySelector('.lb-a11y-t-ul');
        var tSp   = panel.querySelector('.lb-a11y-t-sp');
        var tDys  = panel.querySelector('.lb-a11y-t-dys');
        var tGuide= panel.querySelector('.lb-a11y-t-guide');

        // ── Tamaño de fuente (80–150%, clave 'pa_a11y_fontsize') ──
        var currentSize = parseInt(localStorage.getItem('pa_a11y_fontsize') || '100', 10) || 100;
        function applyFont(){ html.style.fontSize = currentSize + '%'; if (lvlEl) lvlEl.textContent = currentSize + '%'; localStorage.setItem('pa_a11y_fontsize', currentSize); }

        // ── Modo oscuro (DarkReader, clave 'pa_a11y_theme') ──
        function isDark(){ return html.getAttribute('data-theme') === 'dark'; }
        function ensureDarkReader(cb){
            if (typeof DarkReader !== 'undefined') { cb(); return; }
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/darkreader@4.9.92/darkreader.min.js';
            s.async = true;
            s.onload = function(){ try { DarkReader.setFetchMethod(window.fetch); } catch(e){} cb(); };
            document.head.appendChild(s);
        }
        function swapLogo(dark){
            if (!logoSelector) return;
            document.querySelectorAll(logoSelector).forEach(function (img) {
                if (dark && darkLogoUrl) { if (!img.dataset.lbLight) img.dataset.lbLight = img.src; img.src = darkLogoUrl; }
                else if (!dark) { img.src = img.dataset.lbLight || lightLogoUrl || img.src; }
            });
        }
        function applyTheme(dark){
            if (dark) {
                ensureDarkReader(function(){ try { DarkReader.enable({ brightness:100, contrast:95, sepia:0 }); } catch(e){} });
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('pa_a11y_theme', 'dark');
            } else {
                if (typeof DarkReader !== 'undefined') { DarkReader.disable(); }
                html.removeAttribute('data-theme');
                localStorage.removeItem('pa_a11y_theme');
            }
            swapLogo(dark);
            tDark.setAttribute('aria-pressed', dark ? 'true' : 'false');
            tDark.innerHTML = dark ? '&#9728;' : '&#127769;'; // ☀ en oscuro / 🌙 en claro
            tDark.title = dark ? 'Modo claro' : 'Modo oscuro';
        }

        // ── Subrayar enlaces / Mayor espaciado ──
        function applyUl(on){ html.classList.toggle('lb-a11y-ul', on); tUl.setAttribute('aria-pressed', on?'true':'false'); if(on) localStorage.setItem('pa_a11y_ul','1'); else localStorage.removeItem('pa_a11y_ul'); }
        function applySp(on){ html.classList.toggle('lb-a11y-sp', on); tSp.setAttribute('aria-pressed', on?'true':'false'); if(on) localStorage.setItem('pa_a11y_sp','1'); else localStorage.removeItem('pa_a11y_sp'); }
        function applyDys(on){ html.classList.toggle('lb-a11y-dys', on); tDys.setAttribute('aria-pressed', on?'true':'false'); if(on) localStorage.setItem('pa_a11y_dys','1'); else localStorage.removeItem('pa_a11y_dys'); }

        // ── Guía de lectura (banda que sigue el cursor) ──
        var guideBar = null, guideMove = null;
        function applyGuide(on){
            tGuide.setAttribute('aria-pressed', on?'true':'false');
            if(on){
                if(!guideBar){ guideBar = document.createElement('div'); guideBar.className = 'lb-a11y-guide-bar'; document.body.appendChild(guideBar); }
                html.classList.add('lb-a11y-guide');
                if(!guideMove){ guideMove = function(e){ guideBar.style.top = (e.clientY - 20) + 'px'; }; document.addEventListener('mousemove', guideMove); }
                localStorage.setItem('pa_a11y_guide','1');
            } else {
                html.classList.remove('lb-a11y-guide');
                if(guideMove){ document.removeEventListener('mousemove', guideMove); guideMove = null; }
                localStorage.removeItem('pa_a11y_guide');
            }
        }

        // ── Sincronizar UI con el estado ya aplicado en <head> ──
        if (lvlEl) lvlEl.textContent = currentSize + '%';
        tDark.setAttribute('aria-pressed', isDark() ? 'true' : 'false');
        tDark.innerHTML = isDark() ? '&#9728;' : '&#127769;';
        tDark.title = isDark() ? 'Modo claro' : 'Modo oscuro';
        tUl.setAttribute('aria-pressed', html.classList.contains('lb-a11y-ul') ? 'true' : 'false');
        tSp.setAttribute('aria-pressed', html.classList.contains('lb-a11y-sp') ? 'true' : 'false');
        tDys.setAttribute('aria-pressed', html.classList.contains('lb-a11y-dys') ? 'true' : 'false');
        if (localStorage.getItem('pa_a11y_guide') === '1') { applyGuide(true); } else { tGuide.setAttribute('aria-pressed', 'false'); }
        swapLogo(isDark());

        // ── Abrir / cerrar panel ──
        function openPanel(){ panel.setAttribute('data-open','1'); launch.setAttribute('aria-expanded','true'); }
        function closePanel(){ panel.removeAttribute('data-open'); launch.setAttribute('aria-expanded','false'); }
        launch.addEventListener('click', function(){ panel.getAttribute('data-open') ? closePanel() : openPanel(); });
        panel.querySelector('.lb-a11y-close').addEventListener('click', closePanel);
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closePanel(); });
        document.addEventListener('click', function(e){ if (panel.getAttribute('data-open') && !panel.contains(e.target) && !launch.contains(e.target)) closePanel(); });

        // ── Acciones ──
        panel.querySelector('.lb-a11y-inc').addEventListener('click', function(){ if (currentSize < 150){ currentSize += 10; applyFont(); } });
        panel.querySelector('.lb-a11y-dec').addEventListener('click', function(){ if (currentSize > 80){ currentSize -= 10; applyFont(); } });
        tDark.addEventListener('click', function(){ applyTheme(!isDark()); });
        tUl.addEventListener('click',   function(){ applyUl(!html.classList.contains('lb-a11y-ul')); });
        tSp.addEventListener('click',   function(){ applySp(!html.classList.contains('lb-a11y-sp')); });
        tDys.addEventListener('click',  function(){ applyDys(!html.classList.contains('lb-a11y-dys')); });
        tGuide.addEventListener('click',function(){ applyGuide(!html.classList.contains('lb-a11y-guide')); });
        panel.querySelector('.lb-a11y-reset').addEventListener('click', function(){
            currentSize = 100; applyFont();
            applyTheme(false); applyUl(false); applySp(false); applyDys(false); applyGuide(false);
        });
    })();
    </script>
    <?php
}
