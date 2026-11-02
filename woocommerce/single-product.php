<?php
/**
 * single-product.php — Al Qamara theme override
 *
 * Single product page — loads header/footer, outputs WC hooks.
 * The container wrapper is applied via woocommerce_before/after_main_content
 * hooks in inc/woocommerce.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package alqamara
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header();

do_action( 'woocommerce_before_main_content' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name.
?>

    <?php if ( (bool) get_theme_mod( 'alqamara_wc_breadcrumb', true ) ) : ?>
    <!-- Single product breadcrumb — hidden via Customizer → Shop Settings → Global → Show Breadcrumb -->
    <div class="alqamara-shop-header-bar border-bottom mb-3 py-2">
        <?php woocommerce_breadcrumb(); ?>
    </div>
    <?php endif; ?>

    <?php while ( have_posts() ) : ?>
        <?php the_post(); ?>
        <?php wc_get_template_part( 'content', 'single-product' ); ?>
    <?php endwhile; ?>

<?php
do_action( 'woocommerce_after_main_content' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name.
get_footer();
