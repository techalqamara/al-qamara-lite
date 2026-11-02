<?php
/**
 * content-widget-product.php — Al Qamara theme override
 *
 * Separates thumbnail and product info into two distinct elements
 * so CSS flex layout works cleanly (image left, name + price stacked right).
 * No whitespace text nodes between flex siblings — prevents spurious gap offset.
 *
 * @see     woocommerce/templates/content-widget-product.php
 * @package alqamara
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?><li><a href="<?php echo esc_url( get_permalink() ); ?>"
    class="alqamara-wpr-thumb"
    tabindex="-1"
    aria-hidden="true"><?php echo wp_kses( $product->get_image( 'woocommerce_thumbnail', [ 'class' => 'alqamara-wpr-img', 'loading' => 'lazy' ] ), alqamara_allowed_img_html() ); ?></a><div class="alqamara-wpr-info">
        <a href="<?php echo esc_url( get_permalink() ); ?>" class="alqamara-wpr-name">
            <?php echo esc_html( $product->get_name() ); ?>
        </a>
        <div class="alqamara-wpr-price">
            <?php echo wp_kses_post( $product->get_price_html() ); ?>
        </div>
    </div></li>
