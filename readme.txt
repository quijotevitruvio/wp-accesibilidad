=== WP Accesibilidad ===
Contributors: quijotevitruvio
Donate link: https://paypal.me/bibliotecologo
Tags: accessibility, dark mode, dyslexia, font size, books
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Floating accessibility panel (text size, dark mode, dyslexia font, reading guide) plus an «Accessible format» badge for books.

== Description ==

* **♿ panel** on the left side of every page: text size (80–150 %), underline links, extra spacing, dyslexia font (OpenDyslexic), reading guide and «reset».
* **Site-wide dark mode** with its own button (🌙/☀): sets `html[data-theme="dark"]` and applies Dark Reader; the choice is remembered in the browser. You can set separate light and dark logos.
* **Accessible format** (optional module for bookshops): badge on the product page and shop cards, plus `accessibilityFeature` / `accessMode` in the product schema (through Rank Math).
* Everything is stored **on the visitor's device** (localStorage); nothing is sent to your server.

Who it is for: WordPress sites that want to offer reading options without a heavy plugin, and bookshops that sell large-print, braille, accessible EPUB or audiobook editions.

Author: Andrés Valencia Tobón - https://github.com/quijotevitruvio - https://www.linkedin.com/in/andr%C3%A9s-valencia-tob%C3%B3n/
Developed for librosmedellin.com. Support: https://paypal.me/bibliotecologo

== Installation ==

1. Copy the `wp-accesibilidad/` folder into `wp-content/plugins/` and activate it.
2. The panel appears on its own; no setup needed.
3. For the books module: under Products → Attributes create «Formato accesible» (slug `formato-accesible`) with the terms `letra-grande`, `braille`, `epub-accesible`, `audiolibro`, and assign them to products.

== Setup ==

* Settings → Accesibilidad: logo selector, light/dark logo URLs, «Images without filter», panel colour (`color`, default #1d4ed8), hover colour (`color_hover`), badge colour (`badge_color`, default #1e5631) and optional guide URL (`guide_url`). All stored in option `lb_options`.

== Frequently Asked Questions ==

= Does it work with any theme? =
Yes for the panel and dark mode. The book badge hooks the `woocommerce/product-price` or `product-meta` blocks; with a classic template it shows on shop cards but not on the product page.

= Will dark mode change my theme colours? =
It uses Dark Reader (loaded from jsDelivr), which recolours the page on the fly. Results depend on the theme; images can be left unfiltered.

= Does it collect visitor data? =
No. Preferences live in localStorage. The only requests are to the jsDelivr CDN.

= Is it WCAG compliant? =
It helps readers, but it does not replace an accessibility audit of your theme and content.

== Limitations ==

* Dark Reader and OpenDyslexic come from an external CDN (not bundled).
* Panel texts are in Spanish and not yet translation-ready.
* localStorage keys use the `pa_a11y_` prefix.

== Changelog ==

= 1.0.0 =
* First public release.

== Upgrade Notice ==

= 1.0.0 =
First public release.
