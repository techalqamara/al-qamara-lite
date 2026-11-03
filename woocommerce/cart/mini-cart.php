<?php
/**
 * Mini-cart (cart drawer and header cart preview) - Al Qamara Lite override.
 *
 * Each product is one row: picture, name with "quantity x price", remove button.
 * The subtotal and the two buttons stay at the bottom of the drawer.
 *
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' );

if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget alqamara-mc-list">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $alqamara_key => $alqamara_item ) :
			$alqamara_product = apply_filters( 'woocommerce_cart_item_product', $alqamara_item['data'], $alqamara_item, $alqamara_key );
			$alqamara_product_id = apply_filters( 'woocommerce_cart_item_product_id', $alqamara_item['product_id'], $alqamara_item, $alqamara_key );

			if ( ! $alqamara_product || ! $alqamara_product->exists() || $alqamara_item['quantity'] <= 0
				|| ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $alqamara_item, $alqamara_key ) ) {
				continue;
			}

			$alqamara_name = apply_filters( 'woocommerce_cart_item_name', $alqamara_product->get_name(), $alqamara_item, $alqamara_key );
			$alqamara_thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $alqamara_product->get_image( 'woocommerce_thumbnail' ), $alqamara_item, $alqamara_key );
			$alqamara_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $alqamara_product ), $alqamara_item, $alqamara_key );
			$alqamara_permalink = apply_filters( 'woocommerce_cart_item_permalink', $alqamara_product->is_visible() ? $alqamara_product->get_permalink( $alqamara_item ) : '', $alqamara_item, $alqamara_key );
			$alqamara_item_class = apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $alqamara_item, $alqamara_key );
			?>
			<li class="woocommerce-mini-cart-item <?php echo esc_attr( $alqamara_item_class ); ?> alqamara-mc-item">

				<div class="alqamara-mc-item__img">
					<?php if ( $alqamara_permalink ) : ?>
						<a href="<?php echo esc_url( $alqamara_permalink ); ?>" tabindex="-1" aria-hidden="true">
							<?php echo $alqamara_thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce product image HTML. ?>
						</a>
					<?php else : ?>
						<?php echo $alqamara_thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce product image HTML. ?>
					<?php endif; ?>
				</div>

				<div class="alqamara-mc-item__details">
					<?php if ( $alqamara_permalink ) : ?>
						<a href="<?php echo esc_url( $alqamara_permalink ); ?>" class="alqamara-mc-item__name"><?php echo wp_kses_post( $alqamara_name ); ?></a>
					<?php else : ?>
						<span class="alqamara-mc-item__name"><?php echo wp_kses_post( $alqamara_name ); ?></span>
					<?php endif; ?>

					<?php echo wc_get_formatted_cart_item_data( $alqamara_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by WooCommerce. ?>

					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- same filter and markup as the WooCommerce template.
						'woocommerce_widget_cart_item_quantity',
						'<span class="quantity alqamara-mc-item__price">' . sprintf( '%s &times; %s', $alqamara_item['quantity'], $alqamara_price ) . '</span>',
						$alqamara_item,
						$alqamara_key
					);
					?>
				</div>

				<?php
				echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- same filter and markup as the WooCommerce template.
					'woocommerce_cart_item_remove_link',
					sprintf(
						'<a href="%s" class="remove remove_from_cart_button alqamara-mc-item__remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">&times;</a>',
						esc_url( wc_get_cart_remove_url( $alqamara_key ) ),
						/* translators: %s: product name */
						esc_attr( sprintf( __( 'Remove %s from cart', 'al-qamara-lite' ), wp_strip_all_tags( $alqamara_name ) ) ),
						esc_attr( $alqamara_product_id ),
						esc_attr( $alqamara_key ),
						esc_attr( $alqamara_product->get_sku() ),
						/* translators: %s: product name */
						esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'al-qamara-lite' ), wp_strip_all_tags( $alqamara_name ) ) )
					),
					$alqamara_key
				);
				?>
			</li>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_mini_cart_contents' ); ?>
	</ul>

	<div class="alqamara-mc-subtotal">
		<span class="alqamara-mc-subtotal__label"><?php esc_html_e( 'Subtotal', 'al-qamara-lite' ); ?></span>
		<span class="alqamara-mc-subtotal__value"><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
	</div>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<div class="alqamara-mc-buttons">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-sm btn-outline-secondary alqamara-mc-btn"><?php esc_html_e( 'View Cart', 'al-qamara-lite' ); ?></a>
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-sm btn-primary alqamara-mc-btn"><?php esc_html_e( 'Checkout', 'al-qamara-lite' ); ?></a>
	</div>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<div class="alqamara-mini-cart-empty">
		<i class="bi bi-bag alqamara-mini-cart-empty__icon" aria-hidden="true"></i>
		<p class="alqamara-mini-cart-empty__msg"><?php esc_html_e( 'Your cart is empty.', 'al-qamara-lite' ); ?></p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="alqamara-mini-cart-empty__btn"><?php esc_html_e( 'Shop Now', 'al-qamara-lite' ); ?></a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
