<?php
/**
 * =============================================================================
 *
 *  WooCommerce styling wrappers
 *
 *  Loaded from functions.php ONLY when the WooCommerce plugin is active:
 *      if ( class_exists( 'WooCommerce' ) ) require_once ... /inc/woocommerce.php;
 *
 *  Presentation only: wraps WooCommerce output in the theme's Bootstrap
 *  container/grid and sets layout defaults (columns, sidebar, show/hide of
 *  display elements). It adds no store features, no checkout fields, no
 *  product data and does not change any WooCommerce or WordPress option.
 *  The matching styles live in css/woocommerce.css, enqueued conditionally
 *  in inc/enqueue.php.
 *
 *  @package alqamara
 *
 * =============================================================================
 */

defined( 'ABSPATH' ) || exit;


/* =============================================================================
   FOOTER WC WIDGET DETECTION
   Used by inc/enqueue.php to decide whether css/woocommerce.css must load on
   pages that are otherwise plain: a WooCommerce product-list widget placed in a
   footer column renders on every page and needs this stylesheet.
============================================================================= */

/**
 * Whether any footer widget area contains a native WooCommerce widget.
 *
 * @return bool
 */
function alqamara_footer_has_wc_widget() {
	static $has_wc_widget = null;
	if ( null !== $has_wc_widget ) {
		return $has_wc_widget;
	}

	$has_wc_widget    = false;
	$sidebars_widgets = wp_get_sidebars_widgets();
	$footer_sidebars  = array( 'footer-col-1', 'footer-col-2', 'footer-col-3', 'footer-col-4', 'footer-legal' );

	foreach ( $footer_sidebars as $sidebar_id ) {
		if ( empty( $sidebars_widgets[ $sidebar_id ] ) ) {
			continue;
		}
		foreach ( $sidebars_widgets[ $sidebar_id ] as $widget_id ) {
			// Every native WooCommerce widget's ID is prefixed "woocommerce_".
			if ( 0 === strpos( $widget_id, 'woocommerce_' ) ) {
				$has_wc_widget = true;
				break 2;
			}
		}
	}

	return $has_wc_widget;
}


/* =============================================================================
   1 — THEME WRAPPERS
   Replace WooCommerce's default page wrappers with the theme's container/grid.
============================================================================= */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action(
	'woocommerce_before_main_content',
	function () {
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			$py = 'py-4';
		} elseif ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			$py = 'pt-4 pb-5';
		} else {
			$py = 'py-5';
		}
		echo '<div class="container ' . esc_attr( $py ) . '"><div id="alqamara-wc-main" class="alqamara-wc-main">';
	},
	10
);

add_action(
	'woocommerce_after_main_content',
	function () {
		echo '</div></div>';
	},
	10
);


/* =============================================================================
   1B — LOOP CARD
   `woocommerce/content-product.php` renders its own card, so WooCommerce's own
   default callbacks on the loop hooks are removed (not the hooks themselves) to
   avoid duplicated markup.
============================================================================= */

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );


/* =============================================================================
   2 — SHOP LAYOUT DEFAULTS
============================================================================= */

/**
 * Products per row — Customizer columns, one fewer when a sidebar is shown.
 * Minimum 2 columns regardless of setting.
 */
add_filter(
	'loop_shop_columns',
	function () {
		$cols        = (int) get_theme_mod( 'alqamara_shop_columns', 4 );
		$sidebar_pos = get_theme_mod( 'alqamara_shop_sidebar', 'left' );
		$has_sidebar = ( 'none' !== $sidebar_pos ) && is_active_sidebar( 'sidebar-shop' );
		if ( $has_sidebar ) {
			return max( 2, $cols - 1 );
		}
		return $cols;
	}
);

/** Products per page — Customizer controlled */
add_filter(
	'loop_shop_per_page',
	function () {
		return (int) get_theme_mod( 'alqamara_shop_per_page', 12 );
	},
	20
);

/** Related products — count + columns (Customizer-driven) */
add_filter(
	'woocommerce_output_related_products_args',
	function ( $args ) {
		$count                  = max( 1, min( 12, (int) get_theme_mod( 'alqamara_sp_related_count', 4 ) ) );
		$args['posts_per_page'] = $count;
		$args['columns']        = min( $count, 4 );
		return $args;
	}
);

/** Hide related products / up-sells / star rating on the single page when disabled in the Customizer */
add_action(
	'wp',
	function () {
		if ( ! (bool) get_theme_mod( 'alqamara_sp_show_related', true ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
		if ( ! (bool) get_theme_mod( 'alqamara_sp_show_upsells', true ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		}
		if ( ! (bool) get_theme_mod( 'alqamara_sp_show_rating', true ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 8 );
		}
	}
);

/** Square (1:1) product image — body class on single product pages, styled in woocommerce.css */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_singular( 'product' ) && (bool) get_theme_mod( 'alqamara_sp_square_image', true ) ) {
			$classes[] = 'alqamara-sp-square-img';
		}
		return $classes;
	}
);

/** Up-sells columns */
add_filter(
	'woocommerce_upsells_columns',
	function () {
		return 3;
	}
);
add_filter(
	'woocommerce_upsells_total',
	function () {
		return 3;
	}
);


/* =============================================================================
   3 — TWEAKS
============================================================================= */

/** Remove the WooCommerce default sidebar — the theme controls the layout */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/** Number of thumbnails per row on the single product gallery */
add_filter(
	'woocommerce_product_thumbnails_columns',
	function () {
		return 4;
	}
);

/** Breadcrumb — remove the WC default; archive-product.php calls it manually when enabled */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/** Gallery thumbnail image size */
add_filter(
	'woocommerce_get_image_size_gallery_thumbnail',
	function () {
		return array(
			'width'  => 100,
			'height' => 100,
			'crop'   => 1,
		);
	}
);

/** Review submit button — add the WC button classes so it matches Add to Cart */
add_filter(
	'woocommerce_product_review_comment_form_args',
	function ( $args ) {
		$args['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s button alt" value="%4$s" />';
		return $args;
	}
);


/* =============================================================================
   4 — BREADCRUMB
   Restyle the WooCommerce breadcrumb with Bootstrap breadcrumb markup.
============================================================================= */

add_filter(
	'woocommerce_breadcrumb_defaults',
	function ( $defaults ) {
		$defaults['delimiter']   = '<span class="alqamara-wc-bc-sep mx-1"><i class="bi bi-chevron-right" aria-hidden="true"></i></span>';
		$defaults['wrap_before'] = '<nav aria-label="' . esc_attr__( 'Breadcrumb', 'al-qamara-lite' ) . '" class="alqamara-wc-breadcrumb"><ol class="breadcrumb mb-0">';
		$defaults['wrap_after']  = '</ol></nav>';
		$defaults['before']      = '<li class="breadcrumb-item">';
		$defaults['after']       = '</li>';
		return $defaults;
	}
);


/* =============================================================================
   5 — CART COUNT FRAGMENTS
   WooCommerce's fragment system refreshes the header cart badge, the mobile
   bottom-bar cart icon and the small cart preview after an add-to-cart.
   The markup must stay in sync with the static markup in header.php.
============================================================================= */

add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		$qty = WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;

		// Desktop header badge.
		$desktop_cls                      = 'alqamara-cart-badge alqamara-header-badge badge rounded-pill text-bg-warning' . ( $qty > 0 ? '' : ' d-none' );
		$fragments['span.alqamara-cart-badge'] = '<span class="' . esc_attr( $desktop_cls ) . '">' . absint( $qty ) . '</span>';

		// Mobile bottom-bar icon + badge.
		$mob_badge                            = $qty > 0
			? '<span class="alqamara-mob-cart-badge badge rounded-pill text-bg-warning alqamara-badge">' . absint( $qty ) . '</span>'
			: '';
		$fragments['span.alqamara-mob-cart-wrap'] = '<span class="alqamara-mob-cart-wrap position-relative d-inline-block">'
			. '<i class="bi bi-cart3" aria-hidden="true"></i>'
			. $mob_badge
			. '</span>';

		// Desktop cart preview dropdown.
		if ( $qty > 0 ) {
			$preview_html  = '<p class="small fw-semibold mb-1">';
			$preview_html .= esc_html(
				sprintf(
					/* translators: %d: number of items in the cart */
					_n( '%d item', '%d items', $qty, 'al-qamara-lite' ),
					$qty
				)
			);
			$preview_html .= '<span class="float-end text-muted">' . wp_kses_post( WC()->cart->get_cart_total() ) . '</span>';
			$preview_html .= '</p>';
			$preview_html .= '<div class="d-flex gap-2 mt-3">';
			$preview_html .= '<a href="' . esc_url( wc_get_cart_url() ) . '" class="btn btn-sm btn-outline-custom flex-fill">' . esc_html__( 'View Cart', 'al-qamara-lite' ) . '</a>';
			$preview_html .= '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="btn btn-sm btn-custom flex-fill">' . esc_html__( 'Checkout', 'al-qamara-lite' ) . '</a>';
			$preview_html .= '</div>';
		} else {
			$preview_html = '<p class="small text-muted mb-0 text-center py-2">' . esc_html__( 'Your cart is empty.', 'al-qamara-lite' ) . '</p>';
		}
		$fragments['.alqamara-cart-preview-inner'] = '<div class="alqamara-cart-preview-inner">' . $preview_html . '</div>';

		return $fragments;
	}
);


/* =============================================================================
   6 — SINGLE PRODUCT PAGE
============================================================================= */

/** Standard summary order: title, rating, price */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 8 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

/** Gallery thumbnail position — add a class when set to 'left'; CSS does the layout switch */
add_filter(
	'woocommerce_single_product_image_gallery_classes',
	function ( $classes ) {
		if ( 'left' === get_theme_mod( 'alqamara_sp_thumb_position', 'bottom' ) ) {
			$classes[] = 'alqamara-thumb-left';
		}
		return $classes;
	}
);

/**
 * Sale badge — move it inside the product gallery (position:relative) so it
 * overlaps the image, wrapped in the same stack element the shop cards use.
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_sale_flash', 0 );
add_action(
	'woocommerce_product_thumbnails',
	function () {
		echo '<div class="alqamara-badge-stack">';
	},
	-1
);
add_action(
	'woocommerce_product_thumbnails',
	function () {
		echo '</div>';
	},
	2
);

/**
 * Sale badge text — "Sale -10%" using the highest discount % across the
 * on-sale variations, falling back to "Sale".
 */
add_filter(
	'woocommerce_sale_flash',
	function ( $html, $post, $product ) {

		$percent = 0;

		if ( $product->is_type( 'variable' ) ) {
			$prices = $product->get_variation_prices();
			foreach ( $prices['regular_price'] as $id => $regular ) {
				$sale = $prices['sale_price'][ $id ] ?? '';
				if ( $regular > 0 && '' !== $sale && (float) $sale < (float) $regular ) {
					$p = round( ( ( $regular - $sale ) / $regular ) * 100 );
					if ( $p > $percent ) {
						$percent = $p;
					}
				}
			}
		} else {
			$regular = (float) $product->get_regular_price();
			$sale    = (float) $product->get_sale_price();
			if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
				$percent = round( ( ( $regular - $sale ) / $regular ) * 100 );
			}
		}

		$text = esc_html__( 'Sale', 'al-qamara-lite' );
		if ( $percent ) {
			$text .= ' -' . $percent . '%';
		}

		return '<span class="onsale alqamara-pw-sale-badge">' . esc_html( $text ) . '</span>';
	},
	10,
	3
);


/* =============================================================================
   7 — SHOW / HIDE RULES (Customizer) — attached to the WooCommerce stylesheet
   SKU / Categories / Tags on the single product page and the cart cross-sells
   block. Printed as inline CSS right after css/woocommerce.css.
============================================================================= */

/**
 * Adds the Customizer-driven show/hide rules to the WooCommerce stylesheet.
 */
function alqamara_wc_inline_css() {
	if ( ! wp_style_is( 'alqamara-woocommerce', 'enqueued' ) ) {
		return;
	}

	$rules = array();

	if ( is_product() ) {
		if ( ! (bool) get_theme_mod( 'alqamara_sp_show_sku', true ) ) {
			$rules[] = '.product_meta .sku_wrapper { display: none !important; }';
		}
		if ( ! (bool) get_theme_mod( 'alqamara_sp_show_categories', true ) ) {
			$rules[] = '.product_meta .posted_in { display: none !important; }';
		}
		if ( ! (bool) get_theme_mod( 'alqamara_sp_show_tags', true ) ) {
			$rules[] = '.product_meta .tagged_as { display: none !important; }';
		}
	}

	if ( is_cart() && ! (bool) get_theme_mod( 'alqamara_cart_cross_sells', false ) ) {
		$rules[] = '.cross-sells, .wp-block-woocommerce-cart-cross-sells-block { display: none !important; }';
	}

	if ( $rules ) {
		wp_add_inline_style( 'alqamara-woocommerce', wp_strip_all_tags( implode( ' ', $rules ) ) );
	}
}
add_action( 'wp_enqueue_scripts', 'alqamara_wc_inline_css', 30 );

/* =============================================================================
   QUANTITY STEPPER (- / +)
   Adds a minus and a plus button around WooCommerce's classic quantity field
   (single product page, classic cart page). Only an editable number field gets
   them: a product that can be bought once has a hidden field instead.
   The buttons only change the number in the field; js/main.js wires them.
============================================================================= */
if ( ! function_exists( 'alqamara_qty_stepper_type' ) ) :
function alqamara_qty_stepper_type( $type ) {
	$GLOBALS['alqamara_qty_stepper'] = ( 'number' === $type );
	return $type;
}
endif;
add_filter( 'woocommerce_quantity_input_type', 'alqamara_qty_stepper_type', 999 );

if ( ! function_exists( 'alqamara_qty_stepper_minus' ) ) :
function alqamara_qty_stepper_minus() {
	if ( ! empty( $GLOBALS['alqamara_qty_stepper'] ) ) {
		echo '<button type="button" class="alqamara-qty-btn alqamara-qty-minus" aria-label="' . esc_attr__( 'Decrease quantity', 'al-qamara-lite' ) . '">&minus;</button>';
	}
}
endif;
add_action( 'woocommerce_before_quantity_input_field', 'alqamara_qty_stepper_minus' );

if ( ! function_exists( 'alqamara_qty_stepper_plus' ) ) :
function alqamara_qty_stepper_plus() {
	if ( ! empty( $GLOBALS['alqamara_qty_stepper'] ) ) {
		echo '<button type="button" class="alqamara-qty-btn alqamara-qty-plus" aria-label="' . esc_attr__( 'Increase quantity', 'al-qamara-lite' ) . '">+</button>';
	}
}
endif;
add_action( 'woocommerce_after_quantity_input_field', 'alqamara_qty_stepper_plus' );
