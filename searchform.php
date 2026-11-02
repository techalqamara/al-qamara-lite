<?php
/**
 * Search Form Template
 *
 * @package alqamara
 */

// Unique id per rendered form, so the header can print the form more than once.
$alqamara_search_id = wp_unique_id( 'alqamara-search-input-' );
?>

<form role="search"
      method="get"
      class="search-form"
      action="<?php echo esc_url( home_url( '/' ) ); ?>"
      aria-label="<?php esc_attr_e( 'Search', 'al-qamara-lite' ); ?>">

    <label class="screen-reader-text" for="<?php echo esc_attr( $alqamara_search_id ); ?>">
        <?php esc_html_e( 'Search for:', 'al-qamara-lite' ); ?>
    </label>

    <div class="search-form-inner">

        <input type="search"
               id="<?php echo esc_attr( $alqamara_search_id ); ?>"
               class="search-field form-control"
               name="s"
               value="<?php echo esc_attr( get_search_query() ); ?>"
               placeholder="<?php esc_attr_e( 'Search...', 'al-qamara-lite' ); ?>"
               autocomplete="off"
               required>

        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Submit Search', 'al-qamara-lite' ); ?>">
            <i class="fas fa-search" aria-hidden="true"></i>
        </button>

    </div>

</form>
