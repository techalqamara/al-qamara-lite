<?php
/**
 * =============================================================================
 *
 *  Navigation Menu Walkers
 *
 *  Alqamara_Desktop_Menu_Walker — Desktop: Bootstrap dropdowns (up to 4 levels)
 *  Alqamara_Mobile_Menu_Walker  — Mobile: slide-in menu with accordion panels
 *
 *  Both use the single 'primary' menu location (Appearance → Menus). The
 *  walkers only print markup for the standard menu fields (title, URL,
 *  "Open link in a new tab"); they read no custom menu-item data.
 *
 *  @package alqamara
 *
 * =============================================================================
 */

defined( 'ABSPATH' ) || exit;


/* =============================================================================
   Shared helper
============================================================================= */

if ( ! function_exists( 'alqamara_menu_target_attr' ) ) {
	/**
	 * `target="_blank" rel="noopener"` when the menu item's own "Open link in a
	 * new tab" checkbox is ticked, otherwise an empty string.
	 *
	 * @param object $item Nav menu item.
	 * @return string Attribute string (with a leading space), or ''.
	 */
	function alqamara_menu_target_attr( $item ) {
		if ( empty( $item->target ) ) {
			return '';
		}
		return ' target="' . esc_attr( $item->target ) . '" rel="noopener"';
	}
}


/* =============================================================================
   DESKTOP WALKER
============================================================================= */

if ( ! class_exists( 'Alqamara_Desktop_Menu_Walker' ) ) :

	/**
	 * Desktop menu walker: Bootstrap dropdowns.
	 */
	class Alqamara_Desktop_Menu_Walker extends Walker_Nav_Menu {

		/**
		 * Stamps a has-children flag on each element before it is walked.
		 *
		 * @param object $element           Menu item.
		 * @param array  $children_elements Children of all items.
		 * @param int    $max_depth         Max depth.
		 * @param int    $depth             Current depth.
		 * @param array  $args              Menu arguments.
		 * @param string $output            Output buffer.
		 */
		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			$element->alqamara_has_children = ! empty( $children_elements[ $element->ID ] );
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		/**
		 * Opens a sub-level.
		 *
		 * @param string $output Output buffer.
		 * @param int    $depth  Depth of the parent item.
		 * @param object $args   Menu arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			if ( 0 === $depth ) {
				$output .= '<ul class="dropdown-menu">';
			} else {
				$output .= '<ul class="dropdown-menu dropdown-submenu">';
			}
		}

		/**
		 * Closes a sub-level.
		 *
		 * @param string $output Output buffer.
		 * @param int    $depth  Depth of the parent item.
		 * @param object $args   Menu arguments.
		 */
		public function end_lvl( &$output, $depth = 0, $args = null ) {
			$output .= '</ul>';
		}

		/**
		 * Opens a menu item.
		 *
		 * @param string $output Output buffer.
		 * @param object $item   Menu item.
		 * @param int    $depth  Depth.
		 * @param object $args   Menu arguments.
		 * @param int    $id     Item id.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

			$classes      = empty( $item->classes ) ? array() : array_filter( (array) $item->classes );
			$has_children = ! empty( $item->alqamara_has_children );
			$is_current   = in_array( 'current-menu-item', $classes, true )
				|| in_array( 'current-menu-parent', $classes, true )
				|| in_array( 'current-menu-ancestor', $classes, true );
			$active       = $is_current ? ' active' : '';
			$target_attr  = alqamara_menu_target_attr( $item ) . ( in_array( 'current-menu-item', $classes, true ) ? ' aria-current="page"' : '' );

			if ( 0 === $depth ) {

				if ( $has_children ) {
					$output .= '<div class="nav-item dropdown">';
					$output .= '<a href="' . esc_url( $item->url ) . '"' . $target_attr . ' class="nav-link' . esc_attr( $active ) . '" role="button" aria-haspopup="true" aria-expanded="false">';
					$output .= esc_html( $item->title );
					$output .= '<i class="fas fa-chevron-down dropdown-arrow ms-1" aria-hidden="true"></i>';
					$output .= '</a>';
				} else {
					$output .= '<a href="' . esc_url( $item->url ) . '"' . $target_attr . ' class="nav-item nav-link' . esc_attr( $active ) . '">';
					$output .= esc_html( $item->title );
					$output .= '</a>';
				}
			} else {

				$output .= $has_children ? '<li class="dropdown-submenu-item">' : '<li>';
				$output .= '<a href="' . esc_url( $item->url ) . '"' . $target_attr . ' class="dropdown-item' . esc_attr( $active ) . '">';
				$output .= esc_html( $item->title );
				if ( $has_children ) {
					$output .= '<i class="fas fa-chevron-right ms-auto float-end mt-1" aria-hidden="true"></i>';
				}
				$output .= '</a>';
			}
		}

		/**
		 * Closes a menu item.
		 *
		 * @param string $output Output buffer.
		 * @param object $item   Menu item.
		 * @param int    $depth  Depth.
		 * @param object $args   Menu arguments.
		 */
		public function end_el( &$output, $item, $depth = 0, $args = null ) {

			$has_children = ! empty( $item->alqamara_has_children );

			if ( 0 === $depth ) {
				if ( $has_children ) {
					$output .= '</div>'; // .dropdown.
				}
			} else {
				$output .= '</li>';
			}
		}
	}

endif; // Alqamara_Desktop_Menu_Walker.


/* =============================================================================
   MOBILE SLIDE-IN MENU WALKER

   • Uses the same 'primary' menu — no separate mobile menu needed
   • Dropdown panels opened/closed via main.js targeting .mobile-dropdown-toggle
   • aria-controls points to the panel ID — main.js reads this dynamically
============================================================================= */

if ( ! class_exists( 'Alqamara_Mobile_Menu_Walker' ) ) :

	/**
	 * Mobile menu walker: accordion panels.
	 */
	class Alqamara_Mobile_Menu_Walker extends Walker_Nav_Menu {

		/**
		 * ID of the dropdown panel currently being built.
		 *
		 * @var string
		 */
		private $dropdown_id = '';

		/**
		 * Stamps a has-children flag on each element before it is walked.
		 *
		 * @param object $element           Menu item.
		 * @param array  $children_elements Children of all items.
		 * @param int    $max_depth         Max depth.
		 * @param int    $depth             Current depth.
		 * @param array  $args              Menu arguments.
		 * @param string $output            Output buffer.
		 */
		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			$element->alqamara_has_children = ! empty( $children_elements[ $element->ID ] );
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		/**
		 * Opens a sub-level.
		 *
		 * @param string $output Output buffer.
		 * @param int    $depth  Depth of the parent item.
		 * @param object $args   Menu arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			if ( 0 === $depth ) {
				// Slide-down panel — the ID is referenced by aria-controls in the toggle.
				$output .= '<div class="mobile-dropdown-menu" id="' . esc_attr( $this->dropdown_id ) . '">';
			}
			// Depth 1+: content is self-contained.
		}

		/**
		 * Closes a sub-level.
		 *
		 * @param string $output Output buffer.
		 * @param int    $depth  Depth of the parent item.
		 * @param object $args   Menu arguments.
		 */
		public function end_lvl( &$output, $depth = 0, $args = null ) {
			if ( 0 === $depth ) {
				$output .= '</div>'; // .mobile-dropdown-menu.
			}
		}

		/**
		 * Opens a menu item.
		 *
		 * @param string $output Output buffer.
		 * @param object $item   Menu item.
		 * @param int    $depth  Depth.
		 * @param object $args   Menu arguments.
		 * @param int    $id     Item id.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

			$classes      = empty( $item->classes ) ? array() : array_filter( (array) $item->classes );
			$has_children = ! empty( $item->alqamara_has_children );
			$is_current   = in_array( 'current-menu-item', $classes, true )
				|| in_array( 'current-menu-parent', $classes, true )
				|| in_array( 'current-menu-ancestor', $classes, true );
			$active       = $is_current ? ' active' : '';
			$target_attr  = alqamara_menu_target_attr( $item ) . ( in_array( 'current-menu-item', $classes, true ) ? ' aria-current="page"' : '' );

			if ( 0 === $depth ) {

				if ( $has_children ) {

					$this->dropdown_id = 'mobile-dropdown-' . absint( $item->ID );

					$output .= '<div class="mobile-dropdown">';
					$output .= '<a href="#" class="mobile-nav-link mobile-dropdown-toggle' . esc_attr( $active ) . '" aria-expanded="false" aria-controls="' . esc_attr( $this->dropdown_id ) . '">';
					// The label sits in one span so the toggle has exactly two flex children (label, arrow).
					$output .= '<span class="mobile-dropdown-label">' . esc_html( $item->title ) . '</span>';
					// Arrow — rotated by main.js on toggle (dedicated class so only this icon rotates).
					$output .= '<i class="fas fa-chevron-down ms-1 mobile-dropdown-arrow" aria-hidden="true"></i>';
					$output .= '</a>';

				} else {

					$output .= '<a href="' . esc_url( $item->url ) . '"' . $target_attr . ' class="mobile-nav-link' . esc_attr( $active ) . '">';
					$output .= esc_html( $item->title );
					$output .= '</a>';
				}
			} elseif ( $has_children && $depth < 3 ) {

				// A sub-item that has its own children becomes a label.
				$output .= '<div class="mobile-dropdown-sub">' . esc_html( $item->title ) . '</div>';

			} else {

				$output .= '<a href="' . esc_url( $item->url ) . '"' . $target_attr . ' class="mobile-dropdown-item' . esc_attr( $active ) . '">';
				$output .= esc_html( $item->title );
				$output .= '</a>';
			}
		}

		/**
		 * Closes a menu item.
		 *
		 * @param string $output Output buffer.
		 * @param object $item   Menu item.
		 * @param int    $depth  Depth.
		 * @param object $args   Menu arguments.
		 */
		public function end_el( &$output, $item, $depth = 0, $args = null ) {

			$has_children = ! empty( $item->alqamara_has_children );

			if ( 0 === $depth && $has_children ) {
				$output .= '</div>'; // .mobile-dropdown.
			}
			// Depth 1, 2, 3 — all output is self-contained.
		}
	}

endif; // Alqamara_Mobile_Menu_Walker.
