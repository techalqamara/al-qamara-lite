<?php
/**
 * =============================================================================
 *
 *  Al Qamara Lite — functions.php
 *
 *  This file is the theme loader. It defines constants, sets up theme support
 *  and registers menus, widget areas and the Customizer colour variables.
 *  Everything else lives in the inc/ directory.
 *
 *  @package    alqamara
 *  @license    GNU General Public License v2 or later
 *
 * =============================================================================
 *
 *  FILE MAP — inc/ directory
 *  ─────────────────────────────────────────────────────────────────────────
 *  inc/class-walkers.php    Navigation menu walkers (desktop + mobile)
 *  inc/enqueue.php          Styles + scripts enqueue
 *  inc/customizer.php       Customizer panels/sections/settings
 *  inc/helpers.php          Reusable template helper functions
 *  inc/widgets.php          Display widgets (posts grid/slider, category showcase)
 *  inc/contact-widget.php   Contact details display widget
 *  inc/woocommerce.php      WooCommerce styling wrappers (WooCommerce active only)
 *
 *  This is a presentation-only theme: it styles content and WooCommerce pages
 *  but adds no post types, shortcodes, SEO output, tracking or store features.
 *
 * =============================================================================
 */

defined( 'ABSPATH' ) || exit;


/* =============================================================================
   #1 — THEME CONSTANTS
   Must be defined first — all inc/ files depend on these.
============================================================================= */

define( 'ALQAMARA_VERSION', '1.0.5'                        );
define( 'ALQAMARA_DIR',     get_template_directory()     );
define( 'ALQAMARA_URI',     get_template_directory_uri() );
define( 'ALQAMARA_CSS',     ALQAMARA_URI . '/css'            );
define( 'ALQAMARA_JS',      ALQAMARA_URI . '/js'             );
define( 'ALQAMARA_FONTS',   ALQAMARA_URI . '/fonts'          );
define( 'ALQAMARA_LIB',     ALQAMARA_URI . '/lib'            );


/* =============================================================================
   #2 — REQUIRED FILES
============================================================================= */

/** Navigation walkers — desktop menu + mobile slide-in menu */
require_once ALQAMARA_DIR . '/inc/class-walkers.php';

/** Styles + scripts enqueue */
require_once ALQAMARA_DIR . '/inc/enqueue.php';

/** Customizer panels, sections, settings, controls */
require_once ALQAMARA_DIR . '/inc/customizer.php';

/** Reusable template helpers */
require_once ALQAMARA_DIR . '/inc/helpers.php';

/** WooCommerce styling wrappers — only loaded when WooCommerce is active */
if ( class_exists( 'WooCommerce' ) ) {
	require_once ALQAMARA_DIR . '/inc/woocommerce.php';
}

/** Display widgets — posts grid/slider and category showcase */
require_once ALQAMARA_DIR . '/inc/widgets.php';

/** Contact details display widget */
require_once ALQAMARA_DIR . '/inc/contact-widget.php';


/* =============================================================================
   #3 — THEME SETUP
============================================================================= */

if ( ! function_exists( 'alqamara_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function alqamara_setup() {

		/* ── Translation ── */
		load_theme_textdomain( 'al-qamara-lite', ALQAMARA_DIR . '/languages' );

		/* ── Core WordPress Features ── */
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'customize-selective-refresh-widgets' );

		/* ── Custom Image Sizes ── */
		add_image_size( 'alqamara-featured', 1200, 630, true );
		add_image_size( 'alqamara-card', 600, 400, false ); // soft crop — full image preserved.
		add_image_size( 'alqamara-thumb', 300, 200, true );
		add_image_size( 'alqamara-square', 400, 400, true );

		/* ── Custom Logo ── */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 65,
				'width'       => 220,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);

		/* ── Custom Background ── */
		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'fbfbfb',
			)
		);

		/* ── HTML5 Markup ── */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		/* ── Block Editor ── */
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'wp-block-styles' );
		add_editor_style( array( 'css/editor-style.css', 'fonts/text-fonts/fonts.css' ) );

		/* ── WooCommerce ── */
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
endif;
add_action( 'after_setup_theme', 'alqamara_setup' );


/**
 * Sets the content width (maximum width of embedded media).
 */
function alqamara_content_width() {
	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- $content_width is the documented WordPress global for this.
	$GLOBALS['content_width'] = apply_filters( 'alqamara_content_width', 1320 );
}
add_action( 'after_setup_theme', 'alqamara_content_width', 0 );


/* =============================================================================
   #4 — REGISTER NAVIGATION MENUS
============================================================================= */

if ( ! function_exists( 'alqamara_register_menus' ) ) :
	/**
	 * Registers the primary navigation menu location.
	 */
	function alqamara_register_menus() {
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary Menu (Desktop + Mobile)', 'al-qamara-lite' ),
			)
		);
	}
endif;
add_action( 'init', 'alqamara_register_menus' );


/* =============================================================================
   #5 — REGISTER SIDEBARS / WIDGET AREAS
============================================================================= */

if ( ! function_exists( 'alqamara_register_sidebars' ) ) :
	/**
	 * Registers the blog/shop sidebars, footer columns and homepage widget areas.
	 */
	function alqamara_register_sidebars() {

		$defaults = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="widget-title">',
			'after_title'   => '</h5>',
		);

		register_sidebar(
			array_merge(
				$defaults,
				array(
					'name'        => esc_html__( 'Default Sidebar', 'al-qamara-lite' ),
					'id'          => 'sidebar-blog',
					'description' => esc_html__( 'Widgets shown in the blog / post sidebar.', 'al-qamara-lite' ),
				)
			)
		);

		/** Shop Sidebar — shown on shop/category/tag/search pages (position set in Customizer) */
		register_sidebar(
			array_merge(
				$defaults,
				array(
					'name'        => esc_html__( 'Shop Sidebar', 'al-qamara-lite' ),
					'id'          => 'sidebar-shop',
					'description' => esc_html__( 'Widgets shown in the shop/category sidebar.', 'al-qamara-lite' ),
				)
			)
		);

		/** Footer Columns — 4 widget areas (active ones auto-adjust column width) */
		$footer_cols = array(
			'footer-col-1' => esc_html__( 'Footer — Column 1', 'al-qamara-lite' ),
			'footer-col-2' => esc_html__( 'Footer — Column 2', 'al-qamara-lite' ),
			'footer-col-3' => esc_html__( 'Footer — Column 3', 'al-qamara-lite' ),
			'footer-col-4' => esc_html__( 'Footer — Column 4', 'al-qamara-lite' ),
		);
		foreach ( $footer_cols as $id => $name ) {
			register_sidebar(
				array_merge(
					$defaults,
					array(
						'name'        => $name,
						'id'          => $id,
						'description' => esc_html__( 'Add any widget — Navigation Menu, Text, Custom HTML etc.', 'al-qamara-lite' ),
					)
				)
			);
		}

		/** Footer Legal row — widget area (Navigation Menu, Text, Custom HTML etc.) */
		register_sidebar(
			array_merge(
				$defaults,
				array(
					'name'        => esc_html__( 'Footer — Legal Row', 'al-qamara-lite' ),
					'id'          => 'footer-legal',
					'description' => esc_html__( 'Shown above copyright. Add a Navigation Menu, Text, or Custom HTML widget.', 'al-qamara-lite' ),
				)
			)
		);

		/** Homepage section widget areas — one per homepage section. */
		$homepage_widgets = array(
			'hero-widget'       => esc_html__( 'Homepage — Hero', 'al-qamara-lite' ),
			'about-widget'      => esc_html__( 'Homepage — About Us', 'al-qamara-lite' ),
			'blog-widget'       => esc_html__( 'Homepage — Blog', 'al-qamara-lite' ),
			'newsletter-widget' => esc_html__( 'Homepage — Newsletter', 'al-qamara-lite' ),
			'generic-row-1'     => esc_html__( 'Homepage — Generic Row 1', 'al-qamara-lite' ),
			'generic-row-2'     => esc_html__( 'Homepage — Generic Row 2', 'al-qamara-lite' ),
			'generic-row-3'     => esc_html__( 'Homepage — Generic Row 3', 'al-qamara-lite' ),
		);
		foreach ( $homepage_widgets as $id => $name ) {
			register_sidebar(
				array_merge(
					$defaults,
					array(
						'name'        => $name,
						'id'          => $id,
						'description' => esc_html__( 'Widgets here override the default section layout. Leave empty to use the built-in section.', 'al-qamara-lite' ),
					)
				)
			);
		}
	}
endif;
add_action( 'widgets_init', 'alqamara_register_sidebars' );


/* =============================================================================
   #6 — EXCERPT CUSTOMIZATION
============================================================================= */

add_filter(
	'excerpt_length',
	function () {
		return 20;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '&hellip;';
	}
);


/* =============================================================================
   #7 — BODY CLASSES
============================================================================= */

add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_singular() ) {
			$classes[] = 'singular';
		}
		if ( has_post_thumbnail() ) {
			$classes[] = 'has-thumbnail';
		}
		if ( is_active_sidebar( 'sidebar-blog' ) ) {
			$classes[] = 'has-sidebar';
		}
		if ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {
			$classes[] = 'woocommerce-active';
		}
		if ( 'shop' === get_theme_mod( 'alqamara_header_style', 'default' ) && function_exists( 'WC' ) ) {
			$classes[] = 'alqamara-shop-header';
		}
		/* Front page + "Solid Background Header on Homepage" toggle ON + Hero still
		 * enabled: lets custom.css cancel the Hero's negative top-margin (which pulls
		 * its background up behind a TRANSPARENT header) so the Hero is not cropped
		 * by the now-opaque header. See `.alqamara-header-solid-home .hero-section`. */
		if ( is_front_page() && get_theme_mod( 'alqamara_header_solid_home', 0 ) && get_theme_mod( 'alqamara_hero_show', 1 ) ) {
			$classes[] = 'alqamara-header-solid-home';
		}
		/* Sticky sidebar — two independent toggles, one per sidebar. */
		if ( ! get_theme_mod( 'alqamara_blog_sidebar_sticky', 0 ) ) {
			$classes[] = 'alqamara-sidebar-not-sticky';
		}
		if ( ! get_theme_mod( 'alqamara_shop_sidebar_sticky', 1 ) ) {
			$classes[] = 'alqamara-shop-sidebar-not-sticky';
		}
		return $classes;
	}
);


/* =============================================================================
   #9 — ARCHIVE TITLE — Remove prefix (Category:, Tag:, Author:)
============================================================================= */

add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );


/* =============================================================================
   #10 — DYNAMIC CSS — Customizer colour variables
   Built from sanitised Customizer values and attached to the theme stylesheet
   with wp_add_inline_style(), so it prints right after custom.css.
============================================================================= */

/**
 * Keeps only the characters a CSS background-position value can contain.
 *
 * @param string $value Raw value, e.g. "center center" or "50% 20%".
 * @return string
 */
function alqamara_sanitize_bg_position( $value ) {
	$value = preg_replace( '/[^a-zA-Z0-9% .\-]/', '', (string) $value );
	return '' === trim( $value ) ? 'center center' : trim( $value );
}

/**
 * Builds the dynamic (Customizer-driven) CSS and attaches it to custom.css.
 */
function alqamara_dynamic_css() {

	// Hero section background image.
	$hero_img        = alqamara_get_customize_image_url( 'alqamara_hero_bg_image' );
	$hero_pos        = get_theme_mod( 'alqamara_hero_bg_position', 'center center' );
	$hero_overlay    = (bool) get_theme_mod( 'alqamara_hero_overlay', 1 );
	$hero_overlay_op = (int) get_theme_mod( 'alqamara_hero_overlay_opacity', 40 );

	// Footer.
	$footer_bg  = get_theme_mod( 'alqamara_footer_bg', '#060b12' );
	$footer_img = alqamara_get_customize_image_url( 'alqamara_footer_bg_image' );
	$footer_pos = get_theme_mod( 'alqamara_footer_bg_position', 'center center' );

	// Global colours.
	$primary          = get_theme_mod( 'alqamara_color_primary', '#0f172a' );
	$accent           = get_theme_mod( 'alqamara_color_accent', '#b8912a' );
	$color_text       = get_theme_mod( 'alqamara_color_text', '#333333' );
	$color_content_bg = get_theme_mod( 'alqamara_color_content_bg', '#fbfbfb' );
	$color_border     = get_theme_mod( 'alqamara_color_border', '#e5e2d6' );
	$color_link       = get_theme_mod( 'alqamara_color_link', '#0b5cd5' );
	$color_link_hover = get_theme_mod( 'alqamara_color_link_hover', '#b8912a' );

	// Optional colours (empty = CSS fallbacks apply).
	$topbar_bg         = get_theme_mod( 'alqamara_topbar_bg', '' );
	$topbar_text_color = get_theme_mod( 'alqamara_topbar_text_color', '' );
	$hero_header_ink   = get_theme_mod( 'alqamara_hero_header_ink', '' );
	$header_bg         = get_theme_mod( 'alqamara_header_bg', '' );
	$header_row1_bg    = get_theme_mod( 'alqamara_header_row1_bg', '' );
	$header_row2_bg    = get_theme_mod( 'alqamara_header_row2_bg', '' );

	// Page banner.
	$banner_bg             = get_theme_mod( 'alqamara_banner_bg', '#1e293b' );
	$banner_img            = alqamara_get_customize_image_url( 'alqamara_banner_bg_image' );
	$banner_pos            = get_theme_mod( 'alqamara_banner_bg_position', 'center center' );
	$banner_title_color    = get_theme_mod( 'alqamara_banner_title_color', '' );
	$banner_subtitle_color = get_theme_mod( 'alqamara_banner_subtitle_color', '' );

	// Hero + homepage section text colours.
	$hero_title_color       = get_theme_mod( 'alqamara_hero_title_color', '' );
	$hero_subtitle_color    = get_theme_mod( 'alqamara_hero_subtitle_color', '' );
	$section_title_color    = get_theme_mod( 'alqamara_section_title_color', '' );
	$section_subtitle_color = get_theme_mod( 'alqamara_section_subtitle_color', '' );

	// CSS variables — output all Customizer-controlled values into :root.
	$css  = ':root {';
	$css .= '--alqamara-primary: ' . sanitize_hex_color( $primary ) . ';';
	$css .= '--alqamara-accent: ' . sanitize_hex_color( $accent ) . ';';
	$css .= '--alqamara-text: ' . sanitize_hex_color( $color_text ) . ';';
	$css .= '--alqamara-content-bg: ' . sanitize_hex_color( $color_content_bg ) . ';';
	$css .= '--alqamara-border: ' . sanitize_hex_color( $color_border ) . ';';
	$css .= '--alqamara-link: ' . ( sanitize_hex_color( $color_link ) ? sanitize_hex_color( $color_link ) : '#0b5cd5' ) . ';';
	$css .= '--alqamara-link-hover: ' . ( sanitize_hex_color( $color_link_hover ) ? sanitize_hex_color( $color_link_hover ) : '#b8912a' ) . ';';
	$css .= '--alqamara-footer-bg: ' . sanitize_hex_color( $footer_bg ) . ';';
	$css .= '--alqamara-banner-bg: ' . sanitize_hex_color( $banner_bg ) . ';';

	// Only defined when explicitly saved — CSS fallbacks handle the default.
	if ( $topbar_bg ) {
		$css .= '--alqamara-topbar-bg: ' . sanitize_hex_color( $topbar_bg ) . ';';
	}
	if ( $topbar_text_color ) {
		$css .= '--alqamara-topbar-ink: ' . sanitize_hex_color( $topbar_text_color ) . ';';
	}
	if ( $hero_header_ink ) {
		$css .= '--alqamara-hero-header-ink: ' . sanitize_hex_color( $hero_header_ink ) . ';';
	}
	if ( $header_bg ) {
		$css .= '--alqamara-header-bg: ' . sanitize_hex_color( $header_bg ) . ';';
	}
	if ( $header_row1_bg ) {
		$css .= '--alqamara-header-row1-bg: ' . sanitize_hex_color( $header_row1_bg ) . ';';
	}
	if ( $header_row2_bg ) {
		$css .= '--alqamara-header-row2-bg: ' . sanitize_hex_color( $header_row2_bg ) . ';';
	}

	// Ink (text + icon) colours auto-computed from the background that actually applies.
	$row1_bg_effective = $header_row1_bg ? $header_row1_bg : $primary;
	$row2_bg_effective = $header_row2_bg ? $header_row2_bg : $primary;
	$css              .= '--alqamara-header-row1-ink: ' . alqamara_contrast_ink( $row1_bg_effective ) . ';';
	$css              .= '--alqamara-header-row2-ink: ' . alqamara_contrast_ink( $row2_bg_effective ) . ';';

	$header_bg_effective   = $header_bg ? $header_bg : $primary;
	$header_ink            = alqamara_contrast_ink( $header_bg_effective );
	$header_toggler_filter = ( '#ffffff' === $header_ink ) ? 'brightness(0) invert(1)' : 'none';
	$css                  .= '--alqamara-header-ink: ' . $header_ink . ';';
	$css                  .= '--alqamara-header-toggler-filter: ' . $header_toggler_filter . ';';
	$css                  .= '}';

	// Hero section background.
	$hero_overlay_layer = '';
	if ( $hero_overlay ) {
		$hero_overlay_layer = 'linear-gradient(rgba(0,0,0,' . ( $hero_overlay_op / 100 ) . '),rgba(0,0,0,' . ( $hero_overlay_op / 100 ) . ')),';
	}

	$css .= '.hero-section {';
	if ( $hero_img ) {
		$css .= 'background-image: ' . $hero_overlay_layer . 'url("' . esc_url_raw( $hero_img ) . '");';
		$css .= 'background-size: cover;';
		$css .= 'background-position: ' . alqamara_sanitize_bg_position( $hero_pos ) . ';';
		$css .= 'background-repeat: no-repeat;';
	} else {
		$css .= 'background-image: linear-gradient(100deg, color-mix(in srgb, var(--alqamara-accent) 10%, white 90%) 0%, color-mix(in srgb, var(--alqamara-primary) 10%, white 90%) 100%);';
	}
	$css .= '}';

	// Hero title/subtitle text colour — only override when explicitly set.
	if ( $hero_title_color ) {
		$css .= '.hero-title { color: ' . sanitize_hex_color( $hero_title_color ) . ' !important; }';
	}
	if ( $hero_subtitle_color ) {
		$css .= '.hero-subtitle { color: ' . sanitize_hex_color( $hero_subtitle_color ) . ' !important; }';
	}

	// Homepage section title/subtitle colours — light-background headers only.
	if ( $section_title_color ) {
		$css .= '.alqamara-section-header:not(.text-white) .alqamara-section-title { color: ' . sanitize_hex_color( $section_title_color ) . ' !important; }';
	}
	if ( $section_subtitle_color ) {
		$css .= '.alqamara-section-header:not(.text-white) .alqamara-section-subtitle { color: ' . sanitize_hex_color( $section_subtitle_color ) . ' !important; }';
	}

	if ( $footer_img ) {
		$css .= '.alqamara-footer {';
		$css .= 'background-image: url("' . esc_url_raw( $footer_img ) . '");';
		$css .= 'background-size: cover;';
		$css .= 'background-position: ' . alqamara_sanitize_bg_position( $footer_pos ) . ';';
		$css .= 'background-repeat: no-repeat;';
		$css .= '}';
	}

	// Page banner global background image.
	if ( $banner_img ) {
		$css .= '.blog-page-banner {';
		$css .= 'background-image: url("' . esc_url_raw( $banner_img ) . '");';
		$css .= 'background-size: cover;';
		$css .= 'background-position: ' . alqamara_sanitize_bg_position( $banner_pos ) . ';';
		$css .= 'background-repeat: no-repeat;';
		$css .= '}';
	}

	// Banner title/secondary text colour as CSS custom properties.
	if ( $banner_title_color ) {
		$css .= ':root { --alqamara-banner-title-color: ' . sanitize_hex_color( $banner_title_color ) . '; }';
	}
	if ( $banner_subtitle_color ) {
		$css .= ':root { --alqamara-banner-text-color: ' . sanitize_hex_color( $banner_subtitle_color ) . '; }';
	}

	// Sticky header — remove fixed-top if disabled.
	if ( ! get_theme_mod( 'alqamara_sticky_header', 1 ) ) {
		$css .= '.navbar-transparent { position: relative !important; }';
		$css .= '.w-100.fixed-top { position: relative !important; top: auto !important; }';
	}

	wp_add_inline_style( 'alqamara-custom', wp_strip_all_tags( $css ) );
}
add_action( 'wp_enqueue_scripts', 'alqamara_dynamic_css', 20 );
