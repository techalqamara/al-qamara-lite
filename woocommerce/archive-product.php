<?php
/**
 * archive-product.php — Al Qamara theme override
 *
 * Layout:
 *   Left sidebar  → col-lg-3 sidebar | col-lg-9 products
 *   Right sidebar → col-lg-9 products | col-lg-3 sidebar
 *   No sidebar    → col-12 products
 *
 * Sidebar position is set in Customizer → WooCommerce Shop → Sidebar Position.
 * Widgets for the sidebar are added in Appearance → Widgets → Shop Sidebar.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package alqamara
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

do_action( 'woocommerce_before_main_content' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name.

/* ── Sidebar config ─────────────────────────────────────────────────────── */
$sidebar_pos = get_theme_mod( 'alqamara_shop_sidebar', 'left' );
$has_sidebar = ( 'none' !== $sidebar_pos ) && is_active_sidebar( 'sidebar-shop' );
$content_col = $has_sidebar ? 'col-lg-9' : 'col-12';
?>

    <!-- Shop Page Header: breadcrumb on top, then title + controls on the SAME line -->
    <div class="alqamara-shop-header-bar">

        <!-- Breadcrumb row — py-2 matches single product header bar spacing -->
        <?php if ( (bool) get_theme_mod( 'alqamara_wc_breadcrumb', true ) ) : ?>
        <div class="alqamara-shop-bc-row">
            <?php woocommerce_breadcrumb(); ?>
        </div>
        <?php endif; ?>

        <!-- Title (left) + controls (right) on one row.
             Controls get their OWN router-region so they refresh via AJAX on
             filter changes — independent from the products-grid region below.
             The title itself never changes on filter, so it stays outside. -->
        <div class="alqamara-shop-title-row d-flex flex-wrap align-items-center justify-content-between gap-3">

            <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
            <h1 class="alqamara-shop-title mb-0">
                <?php woocommerce_page_title(); ?>
            </h1>
            <?php endif; ?>

            <?php if ( woocommerce_product_loop() ) : ?>
            <!-- Controls: result count (left) | ordering + view toggle (right).
                 justify-content-between here is what actually splits count/icons
                 left-right on mobile, where woocommerce.css forces this div to
                 width:100% (see @media max-width:575.98px .alqamara-shop-controls). -->
            <div class="alqamara-shop-controls d-flex flex-wrap align-items-center justify-content-between gap-3">

                <?php if ( (bool) get_theme_mod( 'alqamara_shop_result_count', true ) ) woocommerce_result_count(); ?>

                <div class="alqamara-shop-controls-right d-flex align-items-center gap-3">
                    <?php if ( (bool) get_theme_mod( 'alqamara_shop_ordering', true ) ) woocommerce_catalog_ordering(); ?>

                    <!-- List / Grid view toggle -->
                    <div class="alqamara-view-toggle d-flex gap-1" role="group" aria-label="<?php esc_attr_e( 'View mode', 'al-qamara-lite' ); ?>">
                        <button class="alqamara-view-btn alqamara-view-btn--grid active" data-view="grid"
                                title="<?php esc_attr_e( 'Grid view', 'al-qamara-lite' ); ?>"
                                aria-pressed="true">
                            <i class="bi bi-grid-3x3-gap-fill" aria-hidden="true"></i>
                        </button>
                        <button class="alqamara-view-btn alqamara-view-btn--list" data-view="list"
                                title="<?php esc_attr_e( 'List view', 'al-qamara-lite' ); ?>"
                                aria-pressed="false">
                            <i class="bi bi-list-ul" aria-hidden="true"></i>
                        </button>
                    </div>
                </div><!-- /.alqamara-shop-controls-right -->

            </div><!-- /.alqamara-shop-controls -->
            <?php endif; ?>

        </div><!-- /.alqamara-shop-title-row -->

    </div><!-- /.alqamara-shop-header-bar -->

    <?php do_action( 'woocommerce_archive_description' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

    <!-- Bootstrap grid: sidebar + products -->
    <div class="row g-4 alqamara-shop-row">

        <!-- Left sidebar -->
        <?php if ( $has_sidebar && 'left' === $sidebar_pos ) : ?>
        <aside class="col-12 col-lg-3 alqamara-shop-sidebar" aria-label="<?php esc_attr_e( 'Shop filters', 'al-qamara-lite' ); ?>">
            <?php dynamic_sidebar( 'sidebar-shop' ); ?>
        </aside>
        <?php endif; ?>

        <!-- Products column -->
        <div class="<?php echo esc_attr( $content_col ); ?> col-12 alqamara-products-col">

            <?php if ( woocommerce_product_loop() ) : ?>

                <?php
                // Suppress WC's default result count & ordering (both rendered above, in the title row)
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count',      20 );
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering',  30 );
                do_action( 'woocommerce_before_shop_loop' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name.
                ?>

                <?php woocommerce_product_loop_start(); ?>

                    <?php if ( wc_get_loop_prop( 'total' ) ) : ?>
                        <?php while ( have_posts() ) : ?>
                            <?php the_post(); ?>
                            <?php wc_get_template_part( 'content', 'product' ); ?>
                        <?php endwhile; ?>
                    <?php endif; ?>

                <?php woocommerce_product_loop_end(); ?>

                <?php do_action( 'woocommerce_after_shop_loop' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

            <?php else : ?>

                <?php do_action( 'woocommerce_no_products_found' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

            <?php endif; ?>

        </div><!-- /.alqamara-products-col -->

        <!-- Right sidebar -->
        <?php if ( $has_sidebar && 'right' === $sidebar_pos ) : ?>
        <aside class="col-12 col-lg-3 alqamara-shop-sidebar" aria-label="<?php esc_attr_e( 'Shop filters', 'al-qamara-lite' ); ?>">
            <?php dynamic_sidebar( 'sidebar-shop' ); ?>
        </aside>
        <?php endif; ?>

    </div><!-- /.row.alqamara-shop-row -->

<?php
do_action( 'woocommerce_after_main_content' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name.
get_footer();
