<?php
/**
 * Módulo «Formato accesible».
 * Insignia en fichas y tarjetas, y metadatos de accesibilidad en el schema.
 * Apagar: update_option( 'pa_accesibilidad_libros_off', 1 );
 */

defined( 'ABSPATH' ) || exit;

const LM_FA_TAX = 'pa_formato-accesible';

/** Símbolo universal de accesibilidad (SVG inline). */
function lm_fa_ico( $color = 'currentColor', $size = '1em' ) {
	return '<svg viewBox="0 0 24 24" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" fill="' . esc_attr( $color ) . '" aria-hidden="true" focusable="false" style="vertical-align:-.15em"><path d="M20.5 6c-2.61.7-5.67 1-8.5 1s-5.89-.3-8.5-1L3 8c1.86.5 4 .83 6 1v13h2v-6h2v6h2V9c2-.17 4.14-.5 6-1l-.5-2zM12 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/></svg>';
}

/** Devuelve los términos de formato accesible de un producto, o []. */
function lm_fa_terms( $product_id ) {
	$terms = get_the_terms( $product_id, LM_FA_TAX );
	return ( is_array( $terms ) && $terms ) ? $terms : array();
}

/** CSS de la insignia. */
add_action( 'wp_head', function () {
	if ( ! function_exists( 'is_product' ) ) return;
	$o = get_option( 'lb_options', array() );
	$c = isset( $o['badge_color'] ) ? sanitize_hex_color( $o['badge_color'] ) : '';
	$c = $c ? $c : '#1e5631';
	?>
	<style id="lm-fa-css">
		:root{--pa-fa-color:<?php echo esc_html( $c ); ?>;}
		.lm-fa-badge{display:inline-flex;align-items:center;gap:7px;flex-wrap:wrap;background:color-mix(in srgb,var(--pa-fa-color) 8%,#fff);border:1px solid color-mix(in srgb,var(--pa-fa-color) 25%,#fff);color:var(--pa-fa-color);border-radius:10px;padding:8px 12px;font-size:.9rem;font-weight:600;margin:10px 0;line-height:1.3;}
		.lm-fa-badge a{color:var(--pa-fa-color);text-decoration:underline;}
		.lm-fa-badge .lm-fa-t{background:#fff;border:1px solid color-mix(in srgb,var(--pa-fa-color) 25%,#fff);border-radius:999px;padding:1px 9px;font-size:.82rem;text-decoration:none;}
		.lm-fa-card{display:inline-flex;align-items:center;gap:4px;background:var(--pa-fa-color);color:#fff;border-radius:999px;padding:2px 8px;font-size:.72rem;font-weight:700;margin-top:6px;}
	</style>
	<?php
} );

/** HTML de la insignia para unos términos. */
function lm_fa_badge_html( $terms ) {
	$chips = array();
	foreach ( $terms as $t ) {
		$chips[] = '<a class="lm-fa-t" href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a>';
	}
	return '<div class="lm-fa-badge">' . lm_fa_ico( 'currentColor', '1.15em' )
		. '<span>Formato accesible:</span> ' . implode( ' ', $chips ) . '</div>';
}

/**
 * Inserta la insignia tras el bloque de precio en la página del producto.
 * La tienda usa plantilla de BLOQUES (los hooks clásicos del summary no disparan,
 * y la descripción corta puede estar vacía), así que render_block es lo fiable.
 * Una sola vez por página.
 */
add_filter( 'render_block', function ( $content, $block ) {
	static $done = false;
	if ( $done || is_admin() ) return $content;
	$name = isset( $block['blockName'] ) ? $block['blockName'] : '';
	if ( 'woocommerce/product-price' !== $name && 'woocommerce/product-meta' !== $name ) return $content;
	$pid = (int) get_queried_object_id();
	if ( ! $pid || get_post_type( $pid ) !== 'product' ) return $content;
	$terms = lm_fa_terms( $pid );
	if ( ! $terms ) return $content;
	$done = true;
	return $content . lm_fa_badge_html( $terms );
}, 10, 2 );

/** Mini-insignia en las tarjetas de la tienda. */
add_action( 'woocommerce_after_shop_loop_item_title', function () {
	global $product;
	if ( ! $product ) return;
	if ( ! lm_fa_terms( $product->get_id() ) ) return;
	echo '<span class="lm-fa-card">' . lm_fa_ico( '#fff', '.95em' ) . ' Formato accesible</span>';
}, 12 );

/**
 * Metadatos de accesibilidad en el schema del producto (vía Rank Math).
 * Mapea cada término a su valor de accesibilidad oficial.
 */
add_filter( 'rank_math/json_ld', function ( $data, $jsonld ) {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) return $data;
	global $product;
	if ( ! $product ) return $data;
	$terms = lm_fa_terms( $product->get_id() );
	if ( ! $terms ) return $data;

	$features = array();
	$modes    = array( 'textual', 'visual' );
	foreach ( $terms as $t ) {
		switch ( $t->slug ) {
			case 'letra-grande':  $features[] = 'largePrint'; break;
			case 'braille':       $features[] = 'braille'; break;
			case 'epub-accesible':$features = array_merge( $features, array( 'readingOrder', 'structuralNavigation', 'displayTransformability', 'tableOfContents' ) ); break;
			case 'audiolibro':    $modes[]    = 'auditory'; break;
		}
	}
	$features = array_values( array_unique( $features ) );
	$modes    = array_values( array_unique( $modes ) );

	foreach ( $data as $key => $node ) {
		if ( ! is_array( $node ) || empty( $node['@type'] ) ) continue;
		$type = (array) $node['@type'];
		if ( array_intersect( array( 'Product', 'Book' ), $type ) ) {
			if ( $features ) $data[ $key ]['accessibilityFeature'] = $features;
			$data[ $key ]['accessMode'] = $modes;
			break;
		}
	}
	return $data;
}, 99, 2 );
