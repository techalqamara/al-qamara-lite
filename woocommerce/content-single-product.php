<?php
/**
 * content-single-product.php — Al Qamara theme override
 *
 * Two layouts controlled by Customizer → WooCommerce Shop → Single Product → Product Page Layout:
 *
 *  "default"  — Standard WooCommerce layout: gallery top-left, summary
 *               top-right, then full width below (in this order): tabs
 *               (description/specs/reviews), up-sells, related products.
 *
 *  "sticky"   — Custom sticky gallery layout:
 *               ┌─────────────────┬───────────────────────────┐
 *               │  Sticky Image   │  Title, Price, Add to Cart│
 *               │  (stays fixed   │  Description, Reviews     │
 *               │   while right   │  (scrolls with the page)  │
 *               │   scrolls)      │                           │
 *               └─────────────────┴───────────────────────────┘
 *                            Related / Up-sells (full width below)
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package alqamara
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$layout       = get_theme_mod( 'alqamara_sp_layout', 'default' );
$show_related = (bool) get_theme_mod( 'alqamara_sp_show_related', true );
$show_upsells = (bool) get_theme_mod( 'alqamara_sp_show_upsells', true );
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'alqamara-sp-wrap', $product ); ?>>

    <?php do_action( 'woocommerce_before_single_product' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

    <?php if ( 'sticky' === $layout ) : ?>

        <?php
        /*
         * Sticky layout — move upsells & related out of woocommerce_after_single_product_summary
         * so we can render them below the two-column area ourselves.
         */
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display',          15 );
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
        ?>

        <div class="row g-0 alqamara-sp-sticky-row">

            <!-- Left column: sticky product gallery -->
            <div class="col-lg-6 alqamara-sp-gallery-col">
                <?php do_action( 'woocommerce_before_single_product_summary' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
            </div>

            <!-- Right column: summary + tabs (scrolls naturally) -->
            <div class="col-lg-6 alqamara-sp-content-col">
                <div class="alqamara-sp-content-inner">

                    <!-- Product summary: title, rating, price, add to cart, meta -->
                    <div class="summary entry-summary">
                        <?php do_action( 'woocommerce_single_product_summary' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
                    </div>

                    <!-- Description + Reviews tabs inline in the right column -->
                    <div class="alqamara-sp-tabs-wrap">
                        <?php do_action( 'woocommerce_after_single_product_summary' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
                    </div>

                </div><!-- /.alqamara-sp-content-inner -->
            </div><!-- /.alqamara-sp-content-col -->

        </div><!-- /.alqamara-sp-sticky-row -->

        <!-- Full-width section below: upsells + related products -->
        <?php if ( $show_upsells || $show_related ) : ?>
        <div class="alqamara-sp-below-fold mt-3">
            <?php if ( $show_upsells  ) woocommerce_upsell_display(); ?>
            <?php if ( $show_related  ) woocommerce_output_related_products(); ?>
        </div>
        <?php endif; ?>

    <?php else : ?>

        <!-- ── Default WooCommerce layout ─────────────────────────────── -->
        <?php do_action( 'woocommerce_before_single_product_summary' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

        <div class="summary entry-summary">
            <?php do_action( 'woocommerce_single_product_summary' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
        </div>

        <?php do_action( 'woocommerce_after_single_product_summary' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

    <?php endif; ?>

    <?php do_action( 'woocommerce_after_single_product' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

</div><!-- /#product-{id} -->
