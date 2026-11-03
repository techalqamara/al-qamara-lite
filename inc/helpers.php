<?php
/**
 * =============================================================================
 *
 *  Helper Functions
 *
 *  Reusable utility functions used across templates and theme files.
 *  Loaded on every page via functions.php require_once.
 *
 *  @package alqamara
 *
 * =============================================================================
 *
 *  FUNCTIONS:
 *  ─────────────────────────────────────────────────────────────────────────
 *  alqamara_the_logo( $class )       → Custom logo <img> or site name
 *  alqamara_pagination()             → Bootstrap pagination
 *  alqamara_comment_template()       → Comment list callback
 *  alqamara_nav_fallback()           → Desktop nav fallback links
 *  alqamara_mobile_nav_fallback()    → Mobile nav fallback links
 *
 *  Note: alqamara_copyright() and the section helpers live in inc/customizer.php
 *
 * =============================================================================
 */

defined( 'ABSPATH' ) || exit;


/* =============================================================================
   LOGO
============================================================================= */

/**
 * Prints the custom logo <img> (or the site name as text when no logo is set).
 *
 * With $dual = true it prints TWO stacked <img> tags (transparent-header logo +
 * solid-header logo) — only used where a single static logo cannot work: Single
 * Row header, home page, while the header is still transparent over the hero.
 * css/custom.css (".alqamara-logo-transparent" / ".alqamara-logo-solid") shows/hides
 * between them using the `.scrolled` class js/main.js already adds on scroll.
 * It falls back to the single logo when no transparent logo is uploaded.
 *
 * @param string $class CSS class applied to the <img> element(s).
 * @param bool   $dual  Print the transparent + solid logo pair.
 */
if ( ! function_exists( 'alqamara_the_logo' ) ) :
	function alqamara_the_logo( $class = 'custom-logo', $dual = false ) {

		$name = get_bloginfo( 'name' );

		if ( ! has_custom_logo() ) {
			echo '<span class="site-title">' . esc_html( $name ) . '</span>';
			return;
		}

		$solid_url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		if ( ! $solid_url ) {
			echo '<span class="site-title">' . esc_html( $name ) . '</span>';
			return;
		}

		if ( $dual ) {
			$transparent_url = alqamara_get_customize_image_url( 'alqamara_logo_transparent' );
			if ( ! $transparent_url ) {
				$transparent_url = $solid_url;
			}
			echo '<img src="' . esc_url( $transparent_url ) . '" class="' . esc_attr( $class . ' alqamara-logo-transparent' ) . '" alt="' . esc_attr( $name ) . '">';
			echo '<img src="' . esc_url( $solid_url ) . '" class="' . esc_attr( $class . ' alqamara-logo-solid' ) . '" alt="' . esc_attr( $name ) . '">';
			return;
		}

		echo '<img src="' . esc_url( $solid_url ) . '" class="' . esc_attr( $class ) . '" alt="' . esc_attr( $name ) . '">';
	}
endif;


/* =============================================================================
   SEARCH FORM
============================================================================= */

/**
 * Adds a hidden post_type=product field to the theme's search form so the
 * results use WooCommerce's product archive. Hooked only while
 * alqamara_product_search_form() runs.
 *
 * @param string $form Search form HTML from get_search_form().
 * @return string
 */
function alqamara_add_product_post_type_field( $form ) {
	return str_replace( '</form>', '<input type="hidden" name="post_type" value="product"></form>', $form );
}

/**
 * Prints the theme search form. It searches products only when WooCommerce is
 * active and either the Header Style is "Shop" or $force is true.
 * Uses WordPress's own get_search_form() (see searchform.php).
 *
 * @param bool $force Search products regardless of the header style.
 */
if ( ! function_exists( 'alqamara_product_search_form' ) ) :
	function alqamara_product_search_form( $force = false ) {
		if ( class_exists( 'WooCommerce' ) && ( $force || 'shop' === get_theme_mod( 'alqamara_header_style', 'default' ) ) ) {
			add_filter( 'get_search_form', 'alqamara_add_product_post_type_field' );
			get_search_form();
			remove_filter( 'get_search_form', 'alqamara_add_product_post_type_field' );
			return;
		}
		get_search_form();
	}
endif;

/* =============================================================================
   PAGINATION
============================================================================= */

/**
 * Outputs Bootstrap-styled pagination for archive / blog pages.
 *
 * Usage: alqamara_pagination();
 */
if ( ! function_exists( 'alqamara_pagination' ) ) :
function alqamara_pagination() {

    $links = paginate_links( [
        'type'      => 'list',
        'prev_text' => '<i class="fas fa-chevron-left" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Previous', 'al-qamara-lite' ) . '</span>',
        'next_text' => '<i class="fas fa-chevron-right" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Next', 'al-qamara-lite' ) . '</span>',
    ] );

    if ( ! $links ) {
        return;
    }

    echo '<nav class="alqamara-pagination d-flex justify-content-center mt-5"'
       . ' aria-label="' . esc_attr__( 'Page navigation', 'al-qamara-lite' ) . '">';
    echo wp_kses_post( $links );
    echo '</nav>';
}
endif;


/* =============================================================================
   COMMENT TEMPLATE
============================================================================= */

/**
 * Custom comment template callback.
 * Passed to wp_list_comments() in comments.php as 'callback'.
 *
 * @param  WP_Comment  $comment  The comment object.
 * @param  array       $args     Arguments passed to wp_list_comments().
 * @param  int         $depth    Nesting depth of the comment.
 *
 * Usage (in comments.php):
 *   wp_list_comments( [ 'callback' => 'alqamara_comment_template' ] );
 */
if ( ! function_exists( 'alqamara_comment_template' ) ) :
function alqamara_comment_template( $comment, $args, $depth ) {
    // wp_list_comments() has already set the global $comment for this callback.
    ?>

    <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment-item' ); ?>>

        <div class="comment-body">

            <!-- Awaiting moderation notice -->
            <?php if ( '0' === $comment->comment_approved ) : ?>
            <span class="comment-awaiting-moderation">
                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                <?php esc_html_e( 'Awaiting moderation', 'al-qamara-lite' ); ?>
            </span>
            <?php endif; ?>

            <!-- Header: avatar + author + date + reply -->
            <div class="comment-header">

                <div class="comment-avatar">
                    <?php echo get_avatar( $comment, 56 ); ?>
                </div>

                <div class="comment-author-info">
                    <div class="comment-author-name">
                        <?php comment_author_link(); ?>
                    </div>
                    <div class="comment-meta-date">
                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                        <a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
                            <time datetime="<?php echo esc_attr( get_comment_date( 'Y-m-d' ) ); ?>">
                                <?php echo esc_html( get_comment_date() ); ?>
                            </time>
                        </a>
                    </div>
                </div>

                <div class="ms-auto">
                    <?php
                    comment_reply_link( array_merge( $args, [
                        'depth'      => $depth,
                        'max_depth'  => $args['max_depth'],
                        'before'     => '<span class="comment-reply-link-wrap">',
                        'after'      => '</span>',
                        'reply_text' => '<i class="fas fa-reply me-1" aria-hidden="true"></i>'
                                      . esc_html__( 'Reply', 'al-qamara-lite' ),
                    ] ) );
                    ?>
                </div>

            </div><!-- /.comment-header -->

            <!-- Comment text -->
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

        </div><!-- /.comment-body -->

    <?php
    // Note: closing </li> is added automatically by WordPress
}
endif;


/* =============================================================================
   NAV FALLBACKS
   Shown only until menus are assigned in Appearance → Menus
============================================================================= */

/**
 * Links for the menu fallbacks: Home, then up to four published top-level
 * pages (the page set as the static front page is left out, Home already
 * points there). Real pages only, so no link leads to a missing page.
 *
 * @return array[] Each item: 'label', 'url', 'current'.
 */
if ( ! function_exists( 'alqamara_fallback_links' ) ) :
function alqamara_fallback_links() {

    $links = [
        [
            'label'   => __( 'Home', 'al-qamara-lite' ),
            'url'     => home_url( '/' ),
            'current' => is_front_page(),
        ],
    ];

    $pages = get_pages( [
        'parent'      => 0,
        'sort_column' => 'menu_order,post_title',
        'number'      => 5,
        'exclude'     => array_filter( [ (int) get_option( 'page_on_front' ) ] ),
    ] );

    foreach ( array_slice( $pages, 0, 4 ) as $page ) {
        $links[] = [
            'label'   => get_the_title( $page ),
            'url'     => get_permalink( $page ),
            'current' => is_page( $page->ID ) || ( is_home() && 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_for_posts' ) === $page->ID ),
        ];
    }

    return $links;
}
endif;

/**
 * Desktop navbar fallback - shown until a menu is assigned to the Primary
 * Menu location. Passed as 'fallback_cb' in wp_nav_menu() in header.php.
 */
if ( ! function_exists( 'alqamara_nav_fallback' ) ) :
function alqamara_nav_fallback() {
    foreach ( alqamara_fallback_links() as $link ) {
        echo '<a href="' . esc_url( $link['url'] ) . '" class="nav-item nav-link' . ( $link['current'] ? ' active" aria-current="page' : '' ) . '">'
           . esc_html( $link['label'] ) . '</a>';
    }
}
endif;


/**
 * Mobile menu fallback - shown until a menu is assigned to the Primary
 * Menu location. Passed as 'fallback_cb' in the mobile wp_nav_menu() call.
 */
if ( ! function_exists( 'alqamara_mobile_nav_fallback' ) ) :
function alqamara_mobile_nav_fallback() {
    foreach ( alqamara_fallback_links() as $link ) {
        echo '<a href="' . esc_url( $link['url'] ) . '" class="mobile-nav-link' . ( $link['current'] ? ' active" aria-current="page' : '' ) . '">'
           . esc_html( $link['label'] ) . '</a>';
    }
}
endif;

/* =============================================================================
   ICON HELPERS
============================================================================= */

/**
 * Normalize a stored icon value into a full, ready-to-use CSS class.
 *
 * Every icon-class field in the theme now stores (and the icon picker
 * now always inserts) a FULL class, e.g. "fas fa-star" or "bi bi-star" —
 * consistent everywhere, so a value copied from one field always means
 * the same thing in another. Before this, three fields (Service Icon,
 * the Customizer Stats + Features icons) stored only a BARE name (e.g.
 * "fa-star") and their templates hard-coded the family prefix ("fas "/
 * "bi ") themselves. This helper keeps old content saved that way
 * working: if the value already looks like "family name" (has a space),
 * it's used as-is; otherwise $default_family is prepended, exactly
 * matching the old hard-coded-prefix behaviour it replaces.
 *
 * @param  string $value           Stored icon value, bare or full class.
 * @param  string $default_family  Family to assume for a legacy bare
 *                                  value — 'fas' or 'bi'.
 * @return string  Full CSS class (e.g. "fas fa-star"), or '' if empty.
 */
if ( ! function_exists( 'alqamara_icon_class' ) ) :
function alqamara_icon_class( $value, $default_family = 'fas' ) {
    $value = trim( (string) $value );
    if ( '' === $value ) {
        return '';
    }
    // Already a full class — starts with a known family prefix + a space?
    if ( preg_match( '/^(fas|far|fab|fa-solid|fa-regular|fa-brands|bi)\s+\S/', $value ) ) {
        return $value;
    }
    return $default_family . ' ' . $value;
}
endif;


/* =============================================================================
   FRONT-PAGE SECTION UTILITIES
============================================================================= */

/**
 * Allowed HTML for core-generated <img> markup (keeps srcset, sizes, decoding).
 *
 * @return array
 */
function alqamara_allowed_img_html() {
    return array(
        'img' => array(
            'src'           => true,
            'srcset'        => true,
            'sizes'         => true,
            'alt'           => true,
            'class'         => true,
            'width'         => true,
            'height'        => true,
            'loading'       => true,
            'decoding'      => true,
            'fetchpriority' => true,
            'title'         => true,
        ),
    );
}

/**
 * Returns the URL stored in a Customizer image setting.
 *
 * @param  string $setting_id Customizer setting ID, e.g. 'alqamara_hero_bg_image'.
 * @param  string $size       Unused; kept so existing calls keep working.
 * @return string             Image URL, or '' if the field is empty.
 */
if ( ! function_exists( 'alqamara_get_customize_image_url' ) ) :
function alqamara_get_customize_image_url( $setting_id, $size = 'full' ) {
    unset( $size );
    return (string) get_theme_mod( $setting_id, '' );
}
endif;

/**
 * Returns an inline style string for a section background.
 *
 * Priority: bg_image → bg_color → $default_color.
 *
 * @param  string $prefix        Customizer setting prefix, e.g. 'alqamara_services'
 * @param  string $default_color Fallback hex colour, e.g. '#ffffff'
 * @return string                CSS property string (without style="" wrapper),
 *                               e.g. "background-color:#1e2c5f;"
 */
if ( ! function_exists( 'alqamara_section_bg_style' ) ) :
function alqamara_section_bg_style( $prefix, $default_color = '#ffffff' ) {
    $img   = alqamara_get_customize_image_url( $prefix . '_bg_image' );
    $color = get_theme_mod( $prefix . '_bg_color', $default_color );

    if ( $img ) {
        return "background-image:url('" . esc_url( $img ) . "');background-size:cover;background-position:center;";
    }
    if ( $color ) {
        return 'background-color:' . sanitize_hex_color( $color ) . ';';
    }
    return '';
}
endif;

/**
 * Whether a homepage section's title/subtitle should render in light
 * (white) or dark (navy) ink, based on that section's ACTUAL currently
 * configured background — not a value hardcoded per call site.
 *
 * Every section has its own independently-customizable Background Color
 * (`{$prefix}_bg_color`, same setting alqamara_section_bg_style() above
 * reads) — a store owner can set the Services section to black, Projects
 * to white, etc., each completely independently. Front-page.php used to
 * pass a fixed `true`/`false` per section instead (e.g. Stats always
 * "true"/light-text, Services always "false"/dark-text) — correct for the
 * theme's own defaults, but wrong the moment someone customizes a
 * section's background to the opposite end of the light/dark spectrum
 * (found while investigating a user report questioning exactly this,
 * 2026-08-18). This auto-detects from the resolved color using the same
 * YIQ contrast formula already used for the header (alqamara_contrast_ink()),
 * so it stays correct for ANY color the owner picks.
 *
 * Background IMAGE is the one case this can't auto-solve — no reliable way
 * to compute one "correct" ink color from an arbitrary photo — so image
 * sections fall back to $fallback_dark (the original per-section default),
 * same reasoning as the Hero/Banner manual color pickers.
 *
 * @param  string $prefix        Customizer setting prefix, e.g. 'alqamara_services'.
 * @param  string $default_color Same default passed to alqamara_section_bg_style().
 * @param  bool   $fallback_dark What this section used to hardcode; also the
 *                                fallback when a background IMAGE is set.
 * @return bool                  True = light (white) text, false = dark (navy) text.
 */
if ( ! function_exists( 'alqamara_section_dark' ) ) :
function alqamara_section_dark( $prefix, $default_color, $fallback_dark = false ) {
    $img = alqamara_get_customize_image_url( $prefix . '_bg_image' );
    if ( $img ) {
        return $fallback_dark;
    }
    $color = get_theme_mod( $prefix . '_bg_color', $default_color );
    if ( ! $color ) {
        return $fallback_dark;
    }
    return '#ffffff' === strtolower( alqamara_contrast_ink( $color ) );
}
endif;


/**
 * Maps a column count string to Bootstrap responsive column classes.
 * For text-heavy content (blog): 1 column on xs mobile (<576px).
 *
 * @param  string|int $cols  '2', '3', or '4'
 * @return string            e.g. 'col-sm-6 col-lg-4'
 */
if ( ! function_exists( 'alqamara_col_class' ) ) :
function alqamara_col_class( $cols ) {
    $map = [
        '2' => 'col-sm-6',
        '3' => 'col-sm-6 col-lg-4',
        '4' => 'col-sm-6 col-xl-3',
    ];
    return $map[ (string) $cols ] ?? 'col-sm-6 col-lg-4';
}
endif;

/* =============================================================================
   BREADCRUMB
   Two contexts:
     'banner' — nav only, output inside the dark page banner  (default)
     'bar'    — full thin breadcrumb bar wrapper (when banner is OFF)
   Respects Customizer "Show Breadcrumb" toggle globally.
   Automatically detects page context: post, page, archive,
   taxonomy, blog archive, search.
============================================================================= */

if ( ! function_exists( 'alqamara_breadcrumb' ) ) :
function alqamara_breadcrumb( $context = 'banner' ) {

    if ( ! get_theme_mod( 'alqamara_show_breadcrumb', 1 ) ) {
        return;
    }

    $dark  = 'banner' === $context;
    $class = $dark ? 'page-breadcrumb' : 'page-breadcrumb alqamara-breadcrumb-light';
    $items = [];

    /* ── Home ── */
    $items[] = '<li><a href="' . esc_url( home_url( '/' ) ) . '">'
             . '<i class="fas fa-home me-1" aria-hidden="true"></i>'
             . esc_html__( 'Home', 'al-qamara-lite' )
             . '</a></li>';

    /* ── Middle + current ── */
    if ( is_singular() ) {

        $post_type = get_post_type();

        if ( 'post' === $post_type ) {
            /* Blog post: Home → Category → Title */
            $cats = get_the_category();
            if ( $cats ) {
                $items[] = '<li><a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">'
                         . esc_html( $cats[0]->name ) . '</a></li>';
            }

        }

        $items[] = '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';

    } elseif ( is_post_type_archive() ) {
        /* Post type archive: Home → Archive Title */
        $items[] = '<li aria-current="page">'
                 . esc_html( post_type_archive_title( '', false ) ) . '</li>';

    } elseif ( is_tax() ) {
        /* Taxonomy: Home → Archive → Term */
        $term = get_queried_object();
        $tax  = get_taxonomy( $term->taxonomy );
        if ( $tax && ! empty( $tax->object_type ) ) {
            $archive_url = get_post_type_archive_link( $tax->object_type[0] );
            $pt_obj      = get_post_type_object( $tax->object_type[0] );
            if ( $archive_url && $pt_obj ) {
                $items[] = '<li><a href="' . esc_url( $archive_url ) . '">'
                         . esc_html( $pt_obj->labels->name ) . '</a></li>';
            }
        }
        $items[] = '<li aria-current="page">' . esc_html( $term->name ) . '</li>';

    } elseif ( is_search() ) {
        /* Search results */
        $items[] = '<li aria-current="page">'
                 . sprintf(
                       /* translators: %s: search query */
                       esc_html__( 'Search: %s', 'al-qamara-lite' ),
                       '&ldquo;' . esc_html( get_search_query() ) . '&rdquo;'
                   )
                 . '</li>';

    } elseif ( is_archive() ) {
        /* Blog category, tag, date, author, etc. */
        $items[] = '<li aria-current="page">' . get_the_archive_title() . '</li>';
    }

    /* ── Build output ── */
    $nav = '<nav aria-label="' . esc_attr__( 'Breadcrumb', 'al-qamara-lite' ) . '">'
         . '<ol class="' . esc_attr( $class ) . '">'
         . implode( '', $items )
         . '</ol></nav>';

    $allowed = array(
        'nav'  => array( 'aria-label' => true ),
        'ol'   => array( 'class' => true ),
        'li'   => array( 'aria-current' => true ),
        'a'    => array( 'href' => true ),
        'span' => array( 'class' => true ),
        'i'    => array( 'class' => true, 'aria-hidden' => true ),
    );

    if ( 'bar' === $context ) {
        echo '<div class="alqamara-breadcrumb-bar"><div class="container">' . wp_kses( $nav, $allowed ) . '</div></div>';
    } else {
        echo wp_kses( $nav, $allowed );
    }
}
endif;


/**
 * Outputs a centred section header block (eyebrow + title + optional subtitle).
 * Renders nothing when $title is empty.
 *
 * @param string $title    Section heading text.
 * @param string $subtitle Optional subtitle.
 * @param bool   $dark     True to use white text (for dark backgrounds).
 * @param string $eyebrow  Optional small label above the title.
 */
if ( ! function_exists( 'alqamara_section_header' ) ) :
function alqamara_section_header( $title, $subtitle = '', $dark = false, $eyebrow = '' ) {
    if ( ! $title ) {
        return;
    }
    $color_class = $dark ? ' text-white' : '';
    ?>
    <div class="alqamara-section-header text-center mb-5<?php echo esc_attr( $color_class ); ?>" data-aos="fade-down">
        <?php if ( $eyebrow ) : ?>
        <span class="alqamara-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="alqamara-section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $subtitle ) : ?>
        <p class="alqamara-section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
        <?php endif; ?>
    </div>
    <?php
}
endif;


/* =============================================================================
   COLOR / CONTRAST
============================================================================= */

/**
 * Picks a readable ink (text/icon) color for a given background hex color,
 * using the standard YIQ brightness formula (same formula used by most
 * "auto contrast text" features in page builders).
 *
 * Used for Customizer-controlled backgrounds where the admin can pick ANY
 * color (e.g. Header Row 1 / Row 2 background) — without this, a fixed
 * hardcoded text color breaks (becomes unreadable) the moment someone picks
 * a background on the same end of the light/dark spectrum. See
 * --alqamara-header-row1-ink / --alqamara-header-row2-ink in functions.php's dynamic
 * CSS block for where this is consumed.
 *
 * @param  string $hex   Background color, e.g. '#0f172a' or '#fff'.
 * @param  string $dark  Ink color returned for LIGHT backgrounds.
 * @param  string $light Ink color returned for DARK backgrounds.
 * @return string         Either $dark or $light.
 */
if ( ! function_exists( 'alqamara_contrast_ink' ) ) :
function alqamara_contrast_ink( $hex, $dark = '#14182a', $light = '#ffffff' ) {
    $hex = ltrim( trim( (string) $hex ), '#' );

    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
        return $light; // Unparseable input — assume dark bg, safest default.
    }

    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );

    // YIQ brightness (0–255). >= 150 reads as a light background.
    $yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

    return ( $yiq >= 150 ) ? $dark : $light;
}
endif;