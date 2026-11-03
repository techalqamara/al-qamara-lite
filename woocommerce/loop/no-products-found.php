<?php
/**
 * "No products were found" - Al Qamara Lite override.
 *
 * Tells apart an empty category from a filter that matched nothing, and
 * offers a way back to all products.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

// Read-only check of the shop filter parameters in the address (no data is changed).
$alqamara_filter_keys = array_filter(
	array_keys( $_GET ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	static function ( $key ) {
		return 0 === strpos( $key, 'filter_' )
			|| in_array( $key, array( 'min_price', 'max_price', 'rating_filter' ), true );
	}
);
$alqamara_is_empty_cat = ( is_product_category() || is_tax( 'product_cat' ) ) && empty( $alqamara_filter_keys );
?>

<div class="alqamara-no-products">

	<div class="alqamara-no-products__icon" aria-hidden="true">
		<i class="bi <?php echo esc_attr( $alqamara_is_empty_cat ? 'bi-bag' : 'bi-bag-x' ); ?>"></i>
	</div>

	<h2 class="alqamara-no-products__title">
		<?php
		if ( $alqamara_is_empty_cat ) {
			esc_html_e( 'No products yet', 'al-qamara-lite' );
		} else {
			esc_html_e( 'No products found', 'al-qamara-lite' );
		}
		?>
	</h2>

	<p class="alqamara-no-products__msg">
		<?php
		if ( $alqamara_is_empty_cat ) {
			esc_html_e( 'This category does not have any products yet. Browse the full collection instead.', 'al-qamara-lite' );
		} else {
			esc_html_e( 'No products matched your filters. Try clearing the filters or browse all products.', 'al-qamara-lite' );
		}
		?>
	</p>

	<a class="btn btn-custom" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
		<?php esc_html_e( 'View All Products', 'al-qamara-lite' ); ?>
	</a>

</div>
