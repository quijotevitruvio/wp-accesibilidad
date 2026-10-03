<?php
/**
 * Plugin Name: WP Accesibilidad
 * Plugin URI: https://github.com/quijotevitruvio/wp-accesibilidad
 * Description: Panel flotante de accesibilidad (letra, modo oscuro, espaciado, dislexia, guía de lectura) e insignia «Formato accesible» para libros de WooCommerce.
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Author: Andrés Valencia Tobón
 * Author URI: https://github.com/quijotevitruvio
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-accesibilidad
 *
 * LinkedIn: https://www.linkedin.com/in/andr%C3%A9s-valencia-tob%C3%B3n/
 * Donate link: https://paypal.me/bibliotecologo
 * Desarrollado para librosmedellin.com.
 *
 * QUÉ HACE   Panel ♿ con modo oscuro y preferencias de lectura, e insignia
 *            «Formato accesible» en fichas y tarjetas de producto.
 * CÓMO SE APAGA  Una pieza:  update_option( 'pa_accesibilidad_panel_off', 1 );
 *                            update_option( 'pa_accesibilidad_libros_off', 1 );
 *                Todo: desactivar este plugin.
 * CÓMO SE DESHACE  Borrar la opción (o poner 0) y la pieza vuelve.
 * AJUSTES    Ajustes → Accesibilidad.
 */

defined( 'ABSPATH' ) || exit;

define( 'PA_ACCESIBILIDAD_VERSION', '1.0.0' );

// Panel ♿ y modo oscuro.
if ( ! get_option( 'pa_accesibilidad_panel_off' ) ) {
	require_once __DIR__ . '/modulos/panel.php';

	// Enlace «Ajustes» en la fila del plugin.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=pa-accesibilidad' ) ) . '">' . esc_html__( 'Ajustes', 'wp-accesibilidad' ) . '</a>';
		return $links;
	} );
}

// Insignia «Formato accesible».
if ( ! get_option( 'pa_accesibilidad_libros_off' ) ) {
	require_once __DIR__ . '/modulos/formato-accesible.php';
}
