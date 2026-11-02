<?php
/**
 * content-product.php — Al Qamara theme override
 *
 * Uses the SAME .alqamara-pw-* CSS classes as the homepage Posts Widget so
 * product cards look identical everywhere — shop grid, category pages,
 * search results, related products, up-sells.
 *
 * CSS lives entirely in css/custom.css (.alqamara-pw-* rules).
 * No duplicate card CSS needed in woocommerce.css.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package alqamara
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$alqamara_post_id    = $product->get_id();
$post_url   = get_permalink( $alqamara_post_id );
$is_on_sale = $product->is_on_sale();

/* ── Sale percentage text ─────────────────────────────────────────────── */
$sale_text = esc_html__( 'Sale', 'al-qamara-lite' );
if ( $is_on_sale ) {
    if ( $product->is_type( 'variable' ) ) {
        $prices = $product->get_variation_prices();
        if ( ! empty( $prices['regular_price'] ) && ! empty( $prices['sale_price'] ) ) {
            $max_reg  = (float) max( $prices['regular_price'] );
            $min_sale = (float) min( $prices['sale_price'] );
            if ( $max_reg > 0 && $min_sale < $max_reg ) {
                $pct       = round( ( ( $max_reg - $min_sale ) / $max_reg ) * 100 );
                $sale_text = '-' . $pct . '%';
            }
        }
    } else {
        $reg  = (float) $product->get_regular_price();
        $sale = (float) $product->get_price();
        if ( $reg > 0 && $sale < $reg ) {
            $pct       = round( ( ( $reg - $sale ) / $reg ) * 100 );
            $sale_text = '-' . $pct . '%';
        }
    }
}

/* ── Rating ───────────────────────────────────────────────────────────── */
$avg    = (float) $product->get_average_rating();
$rcount = (int)   $product->get_rating_count();
?>
<li <?php wc_product_class( 'alqamara-pw-card-wrap', $product ); ?>>

    <?php
    /* Standard WooCommerce loop hooks, called in the same order as WooCommerce's own
     * content-product.php template. */
    do_action( 'woocommerce_before_shop_loop_item' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name.
    ?>

    <div class="alqamara-pw-card card h-100 border-0">

        <!-- ── Thumbnail wrap (1:1 aspect-ratio, same as widget) ─────── -->
        <div class="alqamara-pw-thumb-wrap alqamara-pw-thumb-wrap--product">

            <?php do_action( 'woocommerce_before_shop_loop_item_title' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

            <?php if ( has_post_thumbnail( $alqamara_post_id ) ) : ?>
            <a href="<?php echo esc_url( $post_url ); ?>"
               class="alqamara-pw-thumb-link"
               tabindex="-1" aria-hidden="true">
                <?php echo wp_kses(
                    get_the_post_thumbnail(
                        $alqamara_post_id,
                        'woocommerce_single',
                        [ 'class' => 'alqamara-pw-thumb', 'loading' => 'lazy' ]
                    )
                ,
                    alqamara_allowed_img_html()
                ); ?>
            </a>
            <?php else : ?>
            <a href="<?php echo esc_url( $post_url ); ?>"
               class="alqamara-pw-thumb-link alqamara-pw-no-thumb"
               tabindex="-1" aria-hidden="true"></a>
            <?php endif; ?>

            <!-- Sale badge — top-left -->
            <div class="alqamara-badge-stack">
                <?php if ( $is_on_sale ) : ?>
                <span class="alqamara-pw-sale-badge" aria-label="<?php esc_attr_e( 'On sale', 'al-qamara-lite' ); ?>">
                    <?php echo esc_html( $sale_text ); ?>
                </span>
                <?php endif; ?>
            </div><!-- /.alqamara-badge-stack -->

        </div><!-- /.alqamara-pw-thumb-wrap -->

        <!-- ── Card body ─────────────────────────────────────────────── -->
        <div class="alqamara-pw-body d-flex flex-column flex-grow-1">

            <h3 class="alqamara-pw-title">
                <a href="<?php echo esc_url( $post_url ); ?>">
                    <?php echo esc_html( $product->get_name() ); ?>
                </a>
            </h3>

            <?php if ( $avg > 0 ) :
                $filled = min( 5, (int) round( $avg ) );
                $label  = sprintf(
                    /* translators: %s: average product rating */
                    __( 'Rated %s out of 5', 'al-qamara-lite' ),
                    number_format( $avg, 1 )
                );
            ?>
            <div class="alqamara-pw-rating" role="img" aria-label="<?php echo esc_attr( $label ); ?>">
                <span class="alqamara-pw-stars">
                    <?php for ( $alqamara_star = 1; $alqamara_star <= 5; $alqamara_star++ ) : ?>
                        <i class="bi <?php echo $alqamara_star <= $filled ? 'bi-star-fill' : 'bi-star'; ?>" aria-hidden="true"></i>
                    <?php endfor; ?>
                </span>
                <?php if ( $rcount > 0 ) : ?>
                <span class="alqamara-pw-rcount" aria-hidden="true">(<?php echo (int) $rcount; ?>)</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="alqamara-pw-price mt-auto">
                <?php echo wp_kses_post( $product->get_price_html() ); ?>
            </div>

            <?php do_action( 'woocommerce_after_shop_loop_item_title' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

            <div class="alqamara-pw-cart d-grid">
                <?php woocommerce_template_loop_add_to_cart( [ 'quantity' => 1 ] ); ?>
            </div>

        </div><!-- /.alqamara-pw-body -->

    </div><!-- /.alqamara-pw-card -->

    <?php do_action( 'woocommerce_after_shop_loop_item' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>

</li>
