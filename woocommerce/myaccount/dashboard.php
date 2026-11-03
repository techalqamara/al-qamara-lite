<?php
/**
 * My Account dashboard - Al Qamara Lite override.
 *
 * Instead of WooCommerce's plain "From your account dashboard you can view..."
 * text, the customer sees two cards: account details (name, email, addresses)
 * and the most recent orders. The greeting with the "Log out" link stays.
 * The woocommerce_account_dashboard action still runs below the cards.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$alqamara_user = wp_get_current_user();
?>

<div class="alqamara-account-dashboard">

	<p class="alqamara-account-greeting">
		<?php
		printf(
			/* translators: 1: user display name, 2: logout URL */
			wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'al-qamara-lite' ), array( 'a' => array( 'href' => array() ) ) ),
			'<strong>' . esc_html( $alqamara_user->display_name ) . '</strong>',
			esc_url( wc_logout_url() )
		);
		?>
	</p>

	<div class="alqamara-account-cards">

		<div class="alqamara-account-card">
			<div class="alqamara-account-card__header">
				<h2><i class="bi bi-person-circle" aria-hidden="true"></i> <?php esc_html_e( 'Account Details', 'al-qamara-lite' ); ?></h2>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="alqamara-account-card__link">
					<?php esc_html_e( 'Edit', 'al-qamara-lite' ); ?> <i class="bi bi-pencil" aria-hidden="true"></i>
				</a>
			</div>

			<dl class="alqamara-account-summary">
				<div class="alqamara-account-summary__row">
					<dt><?php esc_html_e( 'Name', 'al-qamara-lite' ); ?></dt>
					<dd>
						<?php
						$alqamara_full_name = trim( $alqamara_user->first_name . ' ' . $alqamara_user->last_name );
						echo esc_html( $alqamara_full_name ? $alqamara_full_name : $alqamara_user->display_name );
						?>
					</dd>
				</div>
				<div class="alqamara-account-summary__row">
					<dt><?php esc_html_e( 'Email', 'al-qamara-lite' ); ?></dt>
					<dd><?php echo esc_html( $alqamara_user->user_email ); ?></dd>
				</div>
			</dl>

			<?php
			// Same rule as WooCommerce's myaccount/my-address.php: a separate shipping address only when the store ships.
			$alqamara_addresses = ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() )
				? array(
					'shipping' => __( 'Shipping address', 'al-qamara-lite' ),
					'billing'  => __( 'Billing address', 'al-qamara-lite' ),
				)
				: array(
					'billing' => __( 'Billing address', 'al-qamara-lite' ),
				);
			?>

			<?php foreach ( $alqamara_addresses as $alqamara_addr_type => $alqamara_addr_label ) : ?>
				<?php $alqamara_formatted = wc_get_account_formatted_address( $alqamara_addr_type ); ?>
				<div class="woocommerce-Address alqamara-account-address">
					<header class="woocommerce-Address-title title">
						<h3><?php echo esc_html( $alqamara_addr_label ); ?></h3>
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $alqamara_addr_type ) ); ?>" class="edit">
							<?php echo esc_html( $alqamara_formatted ? __( 'Edit', 'al-qamara-lite' ) : __( 'Add', 'al-qamara-lite' ) ); ?>
						</a>
					</header>
					<address>
						<?php echo $alqamara_formatted ? wp_kses_post( $alqamara_formatted ) : esc_html__( 'You have not set this address yet.', 'al-qamara-lite' ); ?>
					</address>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="alqamara-account-card">
			<div class="alqamara-account-card__header">
				<h2><i class="bi bi-bag-check" aria-hidden="true"></i> <?php esc_html_e( 'Recent Orders', 'al-qamara-lite' ); ?></h2>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="alqamara-account-card__link">
					<?php esc_html_e( 'View all', 'al-qamara-lite' ); ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
				</a>
			</div>

			<?php
			$alqamara_orders = wc_get_orders(
				array(
					'customer' => get_current_user_id(),
					'limit'    => 5,
					'orderby'  => 'date',
					'order'    => 'DESC',
					'type'     => 'shop_order',
				)
			);
			?>

			<?php if ( $alqamara_orders ) : ?>
				<ul class="alqamara-account-orders-list">
					<?php foreach ( $alqamara_orders as $alqamara_order ) : ?>
						<?php $alqamara_status = $alqamara_order->get_status(); ?>
						<li class="alqamara-account-orders-list__item">
							<a href="<?php echo esc_url( $alqamara_order->get_view_order_url() ); ?>" class="alqamara-account-orders-list__link">
								<span class="alqamara-account-orders-list__num">
									<?php
									/* translators: %s: order number */
									printf( esc_html__( 'Order #%s', 'al-qamara-lite' ), esc_html( $alqamara_order->get_order_number() ) );
									?>
								</span>
								<span class="alqamara-account-orders-list__date"><?php echo esc_html( wc_format_datetime( $alqamara_order->get_date_created() ) ); ?></span>
								<mark class="order-status status-<?php echo esc_attr( $alqamara_status ); ?>">
									<span><?php echo esc_html( wc_get_order_status_name( $alqamara_status ) ); ?></span>
								</mark>
								<span class="alqamara-account-orders-list__total"><?php echo wp_kses_post( $alqamara_order->get_formatted_order_total() ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="alqamara-account-empty">
					<?php esc_html_e( 'You have not placed any orders yet.', 'al-qamara-lite' ); ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Start shopping', 'al-qamara-lite' ); ?> &rarr;</a>
				</p>
			<?php endif; ?>
		</div>

	</div>

</div>

<?php
/**
 * My Account dashboard (other plugins can still add their output below the cards).
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );
