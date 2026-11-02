<?php
/**
 * =============================================================================
 *
 *  Asset Enqueue — Styles & Scripts
 *
 *  Registers and enqueues all front-end CSS and JavaScript files.
 *  Loaded on every page via functions.php require_once.
 *
 *  @package alqamara
 *
 * =============================================================================
 *
 *  LOAD ORDER — Styles:
 *  ─────────────────────────────────────────────────────────────────────────
 *  1. Bootstrap 5
 *  2. Local web fonts (depends on Bootstrap — loads after, overrides :root vars)
 *  3. Font Awesome, Bootstrap Icons, AOS
 *  4. Swiper (registered only — enqueued on demand by the Posts widget)
 *  5. WooCommerce styles (shop pages only)
 *  6. style.css (theme header) and custom.css (all theme rules)
 *  7. Typography + Customizer colour variables as inline CSS on custom.css
 *
 *  LOAD ORDER — Scripts:
 *  ─────────────────────────────────────────────────────────────────────────
 *  1. Bootstrap 5 bundle (includes Popper.js)
 *  2. AOS (Animate On Scroll)
 *  3. Swiper (registered only)
 *  4. main.js
 *
 *  Every file is bundled in the theme — nothing is loaded from a remote host.
 *
 * =============================================================================
 */

defined( 'ABSPATH' ) || exit;


/* =============================================================================
   STYLES
============================================================================= */

if ( ! function_exists( 'alqamara_enqueue_styles' ) ) :
	/**
	 * Enqueues all front-end stylesheets in the correct dependency order.
	 */
	function alqamara_enqueue_styles() {

		/* Bootstrap 5 */
		wp_enqueue_style(
			'bootstrap',
			ALQAMARA_CSS . '/bootstrap.min.css',
			array(),
			'5.0.2'
		);

		/* Local web fonts — depends on bootstrap so our :root variables load AFTER
		   bootstrap.min.css and correctly override --bs-font-sans-serif */
		wp_enqueue_style(
			'alqamara-local-fonts',
			ALQAMARA_FONTS . '/text-fonts/fonts.css',
			array( 'bootstrap' ),
			ALQAMARA_VERSION
		);

		/* Font Awesome */
		wp_enqueue_style(
			'font-awesome',
			ALQAMARA_FONTS . '/font-awesome/all.min.css',
			array(),
			'7.1.0'
		);

		/* Bootstrap Icons */
		wp_enqueue_style(
			'bootstrap-icons',
			ALQAMARA_FONTS . '/bootstrap-icons/bootstrap-icons.css',
			array(),
			'1.13.1'
		);

		/* AOS — Animate On Scroll */
		wp_enqueue_style(
			'aos',
			ALQAMARA_LIB . '/aos/aos.min.css',
			array(),
			'2.3.4'
		);

		/* Swiper 11 — registered only; enqueued on demand by the Posts widget */
		wp_register_style(
			'swiper',
			ALQAMARA_LIB . '/swiper/swiper-bundle.min.css',
			array(),
			'11.2.10'
		);

		/* WooCommerce styles — only on WooCommerce pages, and wherever a footer
		 * column holds a WooCommerce widget (those render on every page). */
		if ( class_exists( 'WooCommerce' ) && (
			is_shop() || is_product() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page()
			|| ( function_exists( 'alqamara_footer_has_wc_widget' ) && alqamara_footer_has_wc_widget() )
		) ) {
			wp_enqueue_style(
				'alqamara-woocommerce',
				ALQAMARA_CSS . '/woocommerce.css',
				array( 'bootstrap' ),
				ALQAMARA_VERSION
			);
			// RTL: css/woocommerce-rtl.css is swapped in when the site language is RTL.
			wp_style_add_data( 'alqamara-woocommerce', 'rtl', 'replace' );
		}

		/* Main theme stylesheet — style.css (theme header + nothing else) */
		wp_enqueue_style(
			'alqamara-style',
			get_stylesheet_uri(),
			array( 'bootstrap' ),
			ALQAMARA_VERSION
		);

		/* Theme rules */
		wp_enqueue_style(
			'alqamara-custom',
			ALQAMARA_CSS . '/custom.css',
			array( 'alqamara-style' ),
			ALQAMARA_VERSION
		);
		// RTL: css/custom-rtl.css is swapped in when the site language is RTL.
		wp_style_add_data( 'alqamara-custom', 'rtl', 'replace' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'alqamara_enqueue_styles' );


/* =============================================================================
   SCRIPTS
============================================================================= */

if ( ! function_exists( 'alqamara_enqueue_scripts' ) ) :
	/**
	 * Enqueues all front-end JavaScript files in the correct dependency order.
	 */
	function alqamara_enqueue_scripts() {

		/* Bootstrap 5 bundle (includes Popper.js); jQuery is WordPress's own copy. */
		wp_enqueue_script(
			'bootstrap',
			ALQAMARA_JS . '/bootstrap.bundle.min.js',
			array( 'jquery' ),
			'5.0.2',
			true
		);

		/* AOS — Animate On Scroll */
		wp_enqueue_script(
			'aos',
			ALQAMARA_LIB . '/aos/aos.min.js',
			array(),
			'2.3.4',
			true
		);

		/* Swiper 11 JS — registered only; enqueued on demand by the Posts widget. */
		wp_register_script(
			'swiper',
			ALQAMARA_LIB . '/swiper/swiper-bundle.min.js',
			array(),
			'11.2.10',
			true
		);
		wp_script_add_data( 'swiper', 'strategy', 'defer' );

		/* Main theme JS */
		wp_enqueue_script(
			'alqamara-main',
			ALQAMARA_JS . '/main.js',
			array( 'jquery', 'bootstrap', 'aos' ),
			ALQAMARA_VERSION,
			true
		);

		/* Comment reply — only on singular posts with threaded comments enabled */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'alqamara_enqueue_scripts' );


/* =============================================================================
   TYPOGRAPHY — inline CSS variables
   All fonts are local woff2 files (fonts/text-fonts/fonts.css defines the
   @font-face rules). This outputs only the :root variable overrides chosen in
   the Customizer.
============================================================================= */

/**
 * Attaches the Customizer typography variables to custom.css as inline CSS.
 */
function alqamara_typography_inline_css() {

	$body_font    = get_theme_mod( 'alqamara_font_body', 'Poppins' );
	$heading_font = get_theme_mod( 'alqamara_font_heading', 'Playfair Display' );
	$body_size    = (int) get_theme_mod( 'alqamara_font_size_body', 16 );

	// Sanitise & clamp.
	$body_font    = preg_replace( '/[^a-zA-Z0-9 \-]/', '', $body_font );
	$heading_font = preg_replace( '/[^a-zA-Z0-9 \-]/', '', $heading_font );
	$body_size    = max( 12, min( 24, $body_size ) );

	$css  = ':root {';
	$css .= "--bs-font-sans-serif: '" . $body_font . "', sans-serif;";
	$css .= "--bs-font-heading: '" . $heading_font . "', serif;";
	$css .= '--bs-body-font-size: ' . $body_size . 'px;';
	$css .= '}';
	$css .= 'body { font-size: ' . $body_size . 'px; }';

	wp_add_inline_style( 'alqamara-custom', wp_strip_all_tags( $css ) );
}
add_action( 'wp_enqueue_scripts', 'alqamara_typography_inline_css', 20 );
