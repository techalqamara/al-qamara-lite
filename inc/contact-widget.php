<?php
/**
 * Contact Info Widget — Al Qamara Lite
 *
 * A small native widget for Email / Phone / Address / Office Hours /
 * Support Hours (+ one extra free field). Each field is a GROUP — one
 * fixed icon + one label, shown ONCE — followed by as many lines of text
 * as needed underneath (e.g. two office-hour rows, a 3-line address, two
 * phone numbers).
 *
 * Label text is editable per field (defaults to a sensible name like
 * "Email") so a group can be relabelled to something like "Contact Us" if
 * the site owner wants to combine concepts visually, without changing
 * what the field actually stores.
 *
 * No "label" field shown unless typed in on purpose — icon + text only by
 * default; the icon itself communicates what each row is. Any field left
 * empty is skipped.
 *
 * @package al-qamara-lite
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alqamara_Contact_Info_Widget' ) ) :

class Alqamara_Contact_Info_Widget extends WP_Widget {

	/** Field key => [ fixed icon class, default label key ]. The label text itself comes from default_labels(). */
	const FIELDS = [
		'email'   => [ 'fas fa-envelope',     'email' ],
		'phone'   => [ 'fas fa-phone',        'phone' ],
		'address' => [ 'fas fa-location-dot', 'address' ],
		'hours'   => [ 'fas fa-clock',        'hours' ],
		'support' => [ 'fas fa-headset',      'support' ],
		'extra'   => [ 'fas fa-circle-info',  'extra' ],
	];

	/**
	 * Translatable default labels, shown as placeholders in the widget form.
	 *
	 * @return string[]
	 */
	private static function default_labels() {
		return array(
			'email'   => __( 'Email', 'al-qamara-lite' ),
			'phone'   => __( 'Phone', 'al-qamara-lite' ),
			'address' => __( 'Address', 'al-qamara-lite' ),
			'hours'   => __( 'Office Hours', 'al-qamara-lite' ),
			'support' => __( 'Support Hours', 'al-qamara-lite' ),
			'extra'   => __( 'More Info', 'al-qamara-lite' ),
		);
	}

	public function __construct() {
		parent::__construct(
			'alqamara_contact_info_widget',
			__( 'Al Qamara: Contact Info', 'al-qamara-lite' ),
			[
				'description'                 => __( 'Email, phone, address, office/support hours, and one extra field. Each shows its icon and label once, with as many lines of text underneath as you add. Any field left empty is skipped.', 'al-qamara-lite' ),
				'customize_selective_refresh' => false,
			]
		);
	}

	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo wp_kses_post( $args['before_widget'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<div class="alqamara-contact-info-widget">';

		foreach ( self::FIELDS as $key => list( $icon, $default_label ) ) {
			$raw = isset( $instance[ $key . '_text' ] ) ? trim( (string) $instance[ $key . '_text' ] ) : '';
			if ( '' === $raw ) {
				continue;
			}

			// Label only shows when the site owner actually typed one - no
			// fallback to the field's default name. Left empty, the whole
			// icon+label header is skipped and the lines start flush at
			// the top (no indent either, since there's no icon column to
			// align under anymore).
			$label = isset( $instance[ $key . '_label' ] ) ? trim( (string) $instance[ $key . '_label' ] ) : '';

			$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ), 'strlen' );
			if ( ! $lines ) {
				continue;
			}

			echo '<div class="alqamara-contact-info-group">';

			if ( '' !== $label ) {
				$bold = ! isset( $instance[ $key . '_bold' ] ) || (bool) $instance[ $key . '_bold' ]; // default: bold
				$label_class = 'alqamara-contact-info-label' . ( $bold ? '' : ' alqamara-contact-info-label--regular' );
				echo '<div class="alqamara-contact-info-header">';
				if ( $icon ) {
					echo '<i class="' . esc_attr( $icon ) . ' alqamara-contact-info-icon" aria-hidden="true"></i>';
				}
				echo '<strong class="' . esc_attr( $label_class ) . '">' . esc_html( $label ) . '</strong>';
				echo '</div>';
			}

			$lines_class = 'alqamara-contact-info-lines' . ( '' === $label ? ' alqamara-contact-info-lines--flush' : '' );
			echo '<div class="' . esc_attr( $lines_class ) . '">';
			foreach ( $lines as $line ) {
				echo '<div class="alqamara-contact-info-line">' . esc_html( $line ) . '</div>';
			}
			echo '</div>';

			echo '</div><!-- /.alqamara-contact-info-group -->';
		}

		echo '</div><!-- /.alqamara-contact-info-widget -->';

		echo wp_kses_post( $args['after_widget'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = [];
		$instance['title'] = sanitize_text_field( $new_instance['title'] ?? '' );

		foreach ( self::FIELDS as $key => list( $icon, $default_label ) ) {
			$instance[ $key . '_label' ] = sanitize_text_field( $new_instance[ $key . '_label' ] ?? '' );
			$instance[ $key . '_text' ]  = sanitize_textarea_field( $new_instance[ $key . '_text' ] ?? '' );
			$instance[ $key . '_bold' ]  = ! empty( $new_instance[ $key . '_bold' ] );
		}

		return $instance;
	}

	public function form( $instance ) {
		$title = $instance['title'] ?? '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'al-qamara-lite' ); ?></label>
			<input class="widefat" type="text"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php foreach ( self::FIELDS as $key => list( $icon, $default_label ) ) :
			$label = $instance[ $key . '_label' ] ?? '';
			$text  = $instance[ $key . '_text' ] ?? '';
			$bold  = ! isset( $instance[ $key . '_bold' ] ) || (bool) $instance[ $key . '_bold' ]; // default: bold
			?>
			<p class="alqamara-cw-field">
				<span class="alqamara-cw-head">
					<label for="<?php echo esc_attr( $this->get_field_id( $key . '_label' ) ); ?>" class="alqamara-cw-label">
						<?php esc_html_e( 'Label', 'al-qamara-lite' ); ?>
					</label>
					<label for="<?php echo esc_attr( $this->get_field_id( $key . '_bold' ) ); ?>"
						class="alqamara-cw-bold-toggle"
						title="<?php esc_attr_e( 'Bold label', 'al-qamara-lite' ); ?>">
						B
						<input type="checkbox" class="alqamara-cw-bold-input"
							id="<?php echo esc_attr( $this->get_field_id( $key . '_bold' ) ); ?>"
							name="<?php echo esc_attr( $this->get_field_name( $key . '_bold' ) ); ?>"
							value="1" <?php checked( $bold ); ?> />
					</label>
				</span>
				<input type="text" class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( $key . '_label' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( $key . '_label' ) ); ?>"
					value="<?php echo esc_attr( $label ); ?>"
					placeholder="<?php echo esc_attr( self::default_labels()[ $default_label ] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( $key . '_text' ) ); ?>"><?php esc_html_e( 'Lines (one per line)', 'al-qamara-lite' ); ?></label>
				<textarea class="widefat" rows="3"
					id="<?php echo esc_attr( $this->get_field_id( $key . '_text' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( $key . '_text' ) ); ?>"
					placeholder="<?php echo esc_attr( 'extra' === $key ? __( 'Leave empty to skip this group', 'al-qamara-lite' ) : '' ); ?>"
				><?php echo esc_textarea( $text ); ?></textarea>
			</p>
		<?php endforeach;
	}
}

endif;

add_action( 'widgets_init', function() {
	register_widget( 'Alqamara_Contact_Info_Widget' );
} );
