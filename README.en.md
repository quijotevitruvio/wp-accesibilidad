# WP Accesibilidad

> Floating accessibility panel (text size, dark mode, dyslexia font, reading guide) plus an «Accessible format» badge for books.

Español: [README.md](README.md)

## What it is
- **♿ panel** on the left side of every page: text size (80–150 %), underline links, extra spacing, dyslexia font (OpenDyslexic), reading guide and «reset».
- **Site-wide dark mode** with its own button (🌙/☀): sets `html[data-theme="dark"]` and applies Dark Reader; the choice is remembered in the browser. You can set separate light and dark logos.
- **Accessible format** (optional module for bookshops): badge on the product page and shop cards, plus `accessibilityFeature` / `accessMode` in the product schema (through Rank Math).
- Everything is stored **on the visitor's device** (localStorage); nothing is sent to your server.

## Who it is for
WordPress sites that want to offer reading options without a heavy plugin, and bookshops that sell large-print, braille, accessible EPUB or audiobook editions.

## Requirements
- WordPress 6.0+, PHP 7.4+.
- «Accessible format» module: WooCommerce, a block-based product template and a global attribute with slug `formato-accesible`. Rank Math is optional (schema only).
- Visitors need to reach the jsDelivr CDN for dark mode and the dyslexia font.

## Installation
1. Copy the repository's `wp-accesibilidad/` folder into `wp-content/plugins/` and activate it.
2. The panel appears on its own; no setup needed.
3. For the books module: under Products → Attributes create «Formato accesible» (slug `formato-accesible`) with the terms `letra-grande`, `braille`, `epub-accesible`, `audiolibro`, and assign them to products.

## Setup
Settings → Accesibilidad (option `lb_options`, everything empty by default):

| Setting | Key in `lb_options` | Default | What it does |
|---|---|---|---|
| Logo selector | `logo_selector` | empty | CSS selector of the logo image; empty = logo untouched |
| Light logo | `light_logo_url` | empty | Light-mode logo URL |
| Dark logo | `dark_logo_url` | empty | Dark-mode logo URL |
| Images without filter | `keep_images` | empty | Keep image brightness in dark mode |
| Panel colour | `color` | `#1d4ed8` | Hex colour of buttons and titles (CSS var `--pa-a11y-color`) |
| Hover colour | `color_hover` | `#1e3a8a` | Hex hover colour (`--pa-a11y-color-hover`) |
| Badge colour | `badge_color` | `#1e5631` | Hex colour of the «Accessible format» badge (`--pa-fa-color`) |
| Guide URL | `guide_url` | empty | If set, the panel shows a link to that page |

## Switches and options
- `update_option( 'pa_accesibilidad_panel_off', 1 )` turns off the panel and dark mode.
- `update_option( 'pa_accesibilidad_libros_off', 1 )` turns off the «Accessible format» badge.
- Each visitor resets their preferences with the panel's «Reset» button.

## How to undo
- Set the option to 0 (or delete it) and the piece comes back.
- Deactivating the plugin removes everything; only the `lb_options` option and localStorage keys in browsers remain.

## FAQ

**Does it work with any theme?**  
Yes for the panel and dark mode. The book badge hooks the `woocommerce/product-price` or `product-meta` blocks; with a classic template it shows on shop cards but not on the product page.

**Will dark mode change my theme colours?**  
It uses Dark Reader (loaded from jsDelivr), which recolours the page on the fly. Results depend on the theme; images can be left unfiltered.

**Does it collect visitor data?**  
No. Preferences live in localStorage. The only requests are to the jsDelivr CDN.

**Is it WCAG compliant?**  
It helps readers, but it does not replace an accessibility audit of your theme and content.

## Limitations
- Dark Reader and OpenDyslexic come from an external CDN (not bundled).
- Panel texts are in Spanish and not yet translation-ready.
- localStorage keys use the `pa_a11y_` prefix (fontsize, theme, ul, sp, dys, guide).
- Dark Reader 4.9.92 and OpenDyslexic load from jsDelivr (MIT and SIL OFL licences); the reader's IP reaches the CDN when they are enabled.

## License, author and support
- License: GPL-2.0-or-later (see `LICENSE`).
- Author: **Andrés Valencia Tobón** · [GitHub](https://github.com/quijotevitruvio) · [LinkedIn](https://www.linkedin.com/in/andr%C3%A9s-valencia-tob%C3%B3n/)
- Support the project: https://paypal.me/bibliotecologo
- Developed for librosmedellin.com.
