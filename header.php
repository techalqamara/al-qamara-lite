<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Browser UI colour (mobile address bar), same as the primary colour. -->
    <meta name="theme-color" content="#0f172a">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

    <!-- Skip link: first focusable element, jumps to the main content area. -->
    <a class="alqamara-skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'al-qamara-lite' ); ?></a>

    <!-- SPINNER -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden"><?php esc_html_e( 'Loading...', 'al-qamara-lite' ); ?></span>
        </div>
    </div>

<?php
/**
 * Header: Top Bar + Navbar
 * Topbar: always in normal flow (scrolls away, never sticky).
 * Navbar: sticky or static based on Customizer setting.
 *
 * @package alqamara
 */
$header_style   = get_theme_mod( 'alqamara_header_style', 'default' );
$cta_text       = get_theme_mod( 'alqamara_header_cta_text', __( 'Get in touch', 'al-qamara-lite' ) );
$cta_url        = get_theme_mod( 'alqamara_header_cta_url', home_url( '/' ) );
$show_search    = get_theme_mod( 'alqamara_show_search',   1 );
$show_user_icon = get_theme_mod( 'alqamara_show_user_icon', 1 );
$is_sticky      = (bool) get_theme_mod( 'alqamara_sticky_header', 1 );
$wc_active      = function_exists( 'WC' );
$is_shop_mode   = ( 'shop' === $header_style && $wc_active );
$is_front       = is_front_page();

/* Header Layout (Desktop) — independent of Header Style above. 'stacked'
 * moves the menu to its own row below and puts an always-visible search
 * box where the menu used to sit. Desktop (≥992px) only; mobile markup
 * further down is completely unaffected by this flag either way. */
$header_layout  = get_theme_mod( 'alqamara_header_layout', 'single' );
$is_stacked     = ( 'stacked' === $header_layout );

// Top bar visibility
$tb_desktop  = get_theme_mod( 'alqamara_topbar_desktop', 1 );
$tb_mobile   = get_theme_mod( 'alqamara_topbar_mobile', 0 );
$show_topbar = $tb_desktop || $tb_mobile;

if ( $tb_desktop && $tb_mobile ) {
    $tb_vis = '';
} elseif ( $tb_desktop ) {
    $tb_vis = 'd-none d-sm-flex';
} else {
    $tb_vis = 'd-flex d-sm-none';
}

// Navbar wrapper class
$nav_class = 'alqamara-navbar-wrap';
if ( $is_sticky )                  { $nav_class .= ' alqamara-is-sticky'; }
if ( $is_stacked )                 { $nav_class .= ' alqamara-header-stacked'; }

/* Solid vs. transparent background:
 * - Every non-front page: always solid (unchanged, as before).
 * - Front page: transparent UNLESS —
 *     (a) the "Solid Background Header on Homepage" toggle is on (admin choice), OR
 *     (b) the Hero section itself is disabled.
 *   (b) is a safety net, not just a style choice: the transparent-header effect
 *   depends on Hero's own CSS (negative top-margin pulling it up behind the
 *   header) to look right. If Hero is off, the next visible homepage section
 *   (e.g. Stats Counter, dark navy by default) would sit directly behind a
 *   transparent header with dark nav text (--alqamara-ink) — effectively invisible
 *   navigation. Forcing solid here removes that failure mode automatically,
 *   with no admin action required. */
$solid_home_toggle      = (bool) get_theme_mod( 'alqamara_header_solid_home', 0 );
$hero_enabled           = (bool) get_theme_mod( 'alqamara_hero_show', 1 );
$use_transparent_header = $is_front && ! $solid_home_toggle && $hero_enabled;

if ( ! $use_transparent_header ) { $nav_class .= ' alqamara-header-solid'; }
?>

    <!-- ═══════════════════════════════════════════════
         TOP BAR — always in flow, scrolls away naturally
         Never sticky; no sticky/non-sticky option.
    ════════════════════════════════════════════════ -->
    <?php if ( $show_topbar ) :
        $topbar_text        = get_theme_mod( 'alqamara_topbar_text', '' );
        $topbar_phone       = get_theme_mod( 'alqamara_topbar_phone', '' );
        $topbar_show_social = (bool) get_theme_mod( 'alqamara_topbar_show_social', 1 );
        $alqamara_tb_socials = [
            'alqamara_facebook'  => [ 'icon' => 'fa-facebook-f',  'label' => 'Facebook'  ],
            'alqamara_instagram' => [ 'icon' => 'fa-instagram',   'label' => 'Instagram' ],
            'alqamara_twitter'   => [ 'icon' => 'fa-x-twitter',   'label' => 'X (Twitter)' ],
            'alqamara_linkedin'  => [ 'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn'  ],
            'alqamara_youtube'   => [ 'icon' => 'fa-youtube',     'label' => 'YouTube'   ],
        ];
    ?>
    <div class="alqamara-topbar py-1 small <?php echo esc_attr( $tb_vis ); ?>">
        <div class="container">
            <div class="d-flex flex-column flex-sm-row align-items-center gap-1 gap-sm-3">
                <?php if ( $topbar_text ) : ?>
                <span class="flex-sm-grow-1 text-truncate opacity-75 text-center text-sm-start">
                    <?php echo esc_html( $topbar_text ); ?>
                </span>
                <?php endif; ?>
                <div class="d-flex align-items-center justify-content-center gap-3 ms-sm-auto">
                    <?php if ( $topbar_phone ) : ?>
                    <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $topbar_phone ) ); ?>"
                       class="alqamara-topbar-link text-decoration-none">
                        <i class="bi bi-telephone-fill me-1" aria-hidden="true"></i><?php echo esc_html( $topbar_phone ); ?>
                    </a>
                    <?php endif; ?>
                    <?php if ( $topbar_show_social ) : ?>
                    <?php foreach ( $alqamara_tb_socials as $mod => $alqamara_social ) :
                        $s_url = get_theme_mod( $mod, '' );
                        if ( ! $s_url ) continue;
                    ?>
                    <a href="<?php echo esc_url( $s_url ); ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="alqamara-topbar-link text-decoration-none"
                       aria-label="<?php echo esc_attr( $alqamara_social['label'] ); ?>">
                        <i class="fab <?php echo esc_attr( $alqamara_social['icon'] ); ?>" aria-hidden="true"></i>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- /TOP BAR -->


    <!-- ═══════════════════════════════════════════════
         NAVBAR — sticky or static (Customizer toggle)
    ════════════════════════════════════════════════ -->
    <div class="<?php echo esc_attr( $nav_class ); ?>"<?php if ( $is_front ) : ?> data-aos="fade" data-aos-delay="100"<?php endif; ?>>
        <nav class="navbar navbar-expand-lg navbar-transparent py-lg-0"
             aria-label="<?php esc_attr_e( 'Main Navigation', 'al-qamara-lite' ); ?>">
            <div class="container">

                <!-- Logo -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
                   class="navbar-brand ms-3 ms-lg-0"
                   aria-label="<?php bloginfo( 'name' ); ?>">
                    <?php
                    /* Dual (transparent + solid) logo only where a transparent
                       state actually exists: Single Row layout, home page,
                       hero on. Everywhere else (inner pages, Two Rows layout,
                       hero off) the header is always solid, so a single logo
                       is correct and cheaper to render. */
                    alqamara_the_logo( 'custom-logo', $use_transparent_header && ! $is_stacked );
                    ?>
                </a>

                <!-- Mobile-only icons: Search (shop mode) + Menu toggler, grouped together on the right -->
                <div class="d-flex align-items-center gap-1 ms-auto d-lg-none">

                    <?php if ( $is_shop_mode && $show_search ) : ?>
                    <!-- Mobile Search — same click-to-open dropdown pattern as the desktop icon,
                         with a bordered/boxed look (alqamara-mobile-search-btn) to match the
                         mobile-toggler button next to it. -->
                    <div class="dropdown">
                        <button class="btn btn-link p-2 alqamara-nav-icon alqamara-no-caret dropdown-toggle alqamara-mobile-search-btn"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-label="<?php esc_attr_e( 'Search', 'al-qamara-lite' ); ?>">
                            <i class="bi bi-search fs-5" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end alqamara-search-dd p-2">
                            <?php alqamara_product_search_form(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Mobile toggler -->
                    <button type="button"
                            class="mobile-toggler me-2"
                            id="mobileMenuToggler"
                            aria-expanded="false"
                            aria-label="<?php esc_attr_e( 'Toggle navigation', 'al-qamara-lite' ); ?>">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- DESKTOP CONTENT (Row 1 when Two Rows layout is active; the only desktop row otherwise) -->
                <div class="d-none d-lg-flex flex-grow-1 align-items-center">

                    <?php if ( $is_stacked ) : ?>
                    <!-- Two Rows layout: an always-visible search box takes the space
                         the menu normally fills here — the menu itself moves to its
                         own full-width row below (rendered after </nav>, see
                         .alqamara-navbar-row2 further down). Same mx-auto/ms-auto rule
                         the menu itself used to follow (see the single-row branch
                         below): centered between the logo and the CTA button/icons
                         in Agency/Shop, pushed to the right in Default where there's
                         nothing else on that side — a bare centered box with empty
                         space past it on the right looked odd in Default (user
                         feedback, 2026-08-15). -->
                    <div class="alqamara-header-search-inline <?php echo ( 'agency' === $header_style || $is_shop_mode ) ? 'mx-auto' : 'ms-auto'; ?>">
                        <?php alqamara_product_search_form(); ?>
                    </div>
                    <?php else : ?>
                    <div class="navbar-nav <?php echo ( 'agency' === $header_style || $is_shop_mode ) ? 'mx-auto' : 'ms-auto'; ?>">
                        <?php
                        wp_nav_menu( [
                            'theme_location' => 'primary',
                            'container'      => false,
                            'items_wrap'     => '%3$s',
                            'walker'         => new Alqamara_Desktop_Menu_Walker(),
                            'fallback_cb'    => 'alqamara_nav_fallback',
                            'depth'          => 4,
                        ] );
                        ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( 'agency' === $header_style && $cta_text ) : ?>
                    <!-- Agency: CTA button -->
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn-custom px-4 py-2">
                        <?php echo esc_html( $cta_text ); ?>
                    </a>

                    <?php elseif ( $is_shop_mode ) :
                        $cart_qty = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
                    ?>
                    <!-- Shop: icon row -->
                    <div class="d-flex align-items-center gap-1 alqamara-header-icons">

                        <?php if ( ! $is_stacked && $show_search ) : ?>
                        <!-- Search (click to open, auto-close outside) — Two Rows
                             layout skips this, its search box is already inline
                             above (in Row 1, next to the logo). -->
                        <div class="dropdown">
                            <button class="btn btn-link text-white p-2 alqamara-nav-icon alqamara-no-caret dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside"
                                    aria-label="<?php esc_attr_e( 'Search', 'al-qamara-lite' ); ?>">
                                <i class="bi bi-search fs-5" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end alqamara-search-dd p-2">
                                <?php alqamara_product_search_form(); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ( $show_user_icon ) : ?>
                        <!-- Account: click → navigate; hover → dropdown -->
                        <div class="dropdown alqamara-account-dd">
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
                               class="btn btn-link text-white p-2 alqamara-nav-icon text-decoration-none"
                               aria-label="<?php esc_attr_e( 'My Account', 'al-qamara-lite' ); ?>">
                                <i class="bi bi-person fs-5" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end alqamara-account-dropdown shadow-sm border-0 py-2">
                                <?php if ( is_user_logged_in() ) : ?>
                                <li><a class="dropdown-item py-2" href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>">
                                    <i class="bi bi-person-circle me-2 text-muted"></i><?php esc_html_e( 'My Account', 'al-qamara-lite' ); ?>
                                </a></li>
                                <li><a class="dropdown-item py-2" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
                                    <i class="bi bi-bag-check me-2 text-muted"></i><?php esc_html_e( 'Orders', 'al-qamara-lite' ); ?>
                                </a></li>
                                <?php endif; ?>

                                <li><hr class="dropdown-divider my-1"></li>
                                <?php if ( is_user_logged_in() ) : ?>
                                <li><a class="dropdown-item py-2 text-danger" href="<?php echo esc_url( wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i><?php esc_html_e( 'Logout', 'al-qamara-lite' ); ?>
                                </a></li>
                                <?php else : ?>
                                <li><a class="dropdown-item py-2" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
                                    <i class="bi bi-box-arrow-in-right me-2 text-muted"></i><?php esc_html_e( 'Login / Register', 'al-qamara-lite' ); ?>
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Cart: click → drawer; hover → mini-preview -->
                        <div class="dropdown alqamara-cart-dd">
                            <!-- `text-decoration-none`: Bootstrap's .btn-link underlines by default. -->
                            <button class="btn btn-link text-white p-2 alqamara-nav-icon position-relative text-decoration-none alqamara-open-drawer"
                                    aria-label="<?php esc_attr_e( 'Cart', 'al-qamara-lite' ); ?>">
                                <i class="bi bi-cart3 fs-5" aria-hidden="true"></i>
                                <span class="alqamara-cart-badge alqamara-header-badge badge rounded-pill text-bg-warning<?php echo $cart_qty > 0 ? '' : ' d-none'; ?>">
                                    <?php echo absint( $cart_qty ); ?>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end alqamara-cart-preview shadow-sm border-0 p-3">
                                <div class="alqamara-cart-preview-inner">
                                <?php if ( $cart_qty > 0 ) : ?>
                                <p class="small fw-semibold mb-1">
                                    <?php /* translators: %d: number of items in the cart */ printf( esc_html__( '%d item(s)', 'al-qamara-lite' ), (int) $cart_qty ); ?>
                                    <span class="float-end text-muted"><?php echo wp_kses_post( WC()->cart->get_cart_total() ); ?></span>
                                </p>
                                <div class="d-flex gap-2 mt-3">
                                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-sm btn-outline-secondary flex-fill">
                                        <?php esc_html_e( 'View Cart', 'al-qamara-lite' ); ?>
                                    </a>
                                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-sm btn-primary flex-fill">
                                        <?php esc_html_e( 'Checkout', 'al-qamara-lite' ); ?>
                                    </a>
                                </div>
                                <?php else : ?>
                                <p class="small text-muted mb-0 text-center py-2"><i class="bi bi-cart me-1" aria-hidden="true"></i><?php esc_html_e( 'Your cart is empty.', 'al-qamara-lite' ); ?></p>
                                <?php endif; ?>
                                </div><!-- /.alqamara-cart-preview-inner -->
                            </div>
                        </div>

                    </div><!-- /.d-flex shop icons -->
                    <?php endif; ?>

                </div>
                <!-- /DESKTOP CONTENT -->

            </div><!-- /.container -->
        </nav>

        <?php if ( $is_stacked ) : ?>
        <!-- ═══════════════════════════════════════════════
             ROW 2 — Menu row (Two Rows layout, desktop only).
             Deliberately a SIBLING of <nav> above, not nested inside it —
             nesting it there would pull it into Bootstrap's .navbar flex
             row and lay it out side-by-side with Row 1 instead of stacked
             below it. Both rows are still children of .alqamara-navbar-wrap,
             which is exactly what --alqamara-header-total-h (js/main.js)
             measures on load/resize — so every place that positions
             something relative to header height (page banner top padding,
             sticky product gallery offset, checkout scroll-to-error,
             anchor scroll-margin, etc.) picks up this extra row's height
             automatically. Verified none of those hardcode an independent
             pixel value — they all read the same CSS var. -->
        <?php
        $row2_align = get_theme_mod( 'alqamara_header_row2_align', 'center' );
        if ( ! in_array( $row2_align, [ 'left', 'center' ], true ) ) { $row2_align = 'center'; }
        ?>
        <div class="alqamara-navbar-row2 alqamara-navbar-row2-align-<?php echo esc_attr( $row2_align ); ?> d-none d-lg-block">
            <div class="container">
                <div class="navbar-nav">
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'primary',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'walker'         => new Alqamara_Desktop_Menu_Walker(),
                        'fallback_cb'    => 'alqamara_nav_fallback',
                        'depth'          => 4,
                    ] );
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <!-- /NAVBAR -->


    <!-- CART DRAWER (shop mode) -->
    <?php if ( $is_shop_mode ) :
        $cart_qty = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ?>
    <div class="alqamara-cart-drawer" id="alqamaraCartDrawer" role="dialog"
         aria-label="<?php esc_attr_e( 'Cart', 'al-qamara-lite' ); ?>" aria-hidden="true">
        <div class="mobile-menu-header">
            <span class="fw-semibold"><?php esc_html_e( 'Your Cart', 'al-qamara-lite' ); ?></span>
            <button class="mobile-close-btn alqamara-drawer-close"
                    aria-label="<?php esc_attr_e( 'Close cart', 'al-qamara-lite' ); ?>">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <div class="alqamara-cart-drawer-body p-3">
            <?php if ( WC()->cart ) : ?>
            <div class="woocommerce">
                <div class="widget_shopping_cart_content">
                    <?php woocommerce_mini_cart(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="alqamara-drawer-overlay" id="alqamaraDrawerOverlay"></div>
    <?php endif; ?>


    <!-- MOBILE BOTTOM BAR (shop mode only) -->
    <?php if ( $is_shop_mode ) :
        $mob_cart_qty = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

        /* "Active tab" detection — previously the `.active` class existed in
         * CSS but was never actually applied anywhere in this markup (dead
         * code), so the bar had no real current-page indicator. Wired up
         * here for Shop / Cart / Account. */
        $mob_is_shop     = is_shop() || is_product() || is_product_category() || is_product_tag();
        $mob_is_cart     = function_exists( 'is_cart' ) && is_cart();
        $mob_is_account  = function_exists( 'is_account_page' ) && is_account_page();
    ?>
    <nav class="alqamara-mobile-bar d-lg-none" aria-label="<?php esc_attr_e( 'Mobile navigation', 'al-qamara-lite' ); ?>">
        <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="alqamara-mobile-bar-item<?php echo $mob_is_shop ? ' active' : ''; ?>">
            <!-- `bi-shop` (a storefront/awning glyph) reads as a house icon
                 at this small size, easy to confuse with "Home" — swapped
                 for `bi-bag` (a shopping bag), now free for this since Cart
                 moved to `bi-cart3` above. A bag is a clearer, unambiguous
                 "Shop" symbol at a glance. -->
            <i class="bi bi-bag" aria-hidden="true"></i>
            <span><?php esc_html_e( 'Shop', 'al-qamara-lite' ); ?></span>
        </a>
        <button class="alqamara-mobile-bar-item alqamara-open-drawer alqamara-mobile-bar-cart border-0 bg-transparent<?php echo $mob_is_cart ? ' active' : ''; ?>">
            <span class="alqamara-mob-cart-wrap position-relative d-inline-block">
                <i class="bi bi-cart3" aria-hidden="true"></i>
                <?php if ( $mob_cart_qty > 0 ) : ?>
                <span class="alqamara-mob-cart-badge badge rounded-pill text-bg-warning alqamara-badge">
                    <?php echo absint( $mob_cart_qty ); ?>
                </span>
                <?php endif; ?>
            </span>
            <span><?php esc_html_e( 'Cart', 'al-qamara-lite' ); ?></span>
        </button>
        <?php if ( $show_user_icon ) : ?>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="alqamara-mobile-bar-item text-decoration-none<?php echo $mob_is_account ? ' active' : ''; ?>">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span><?php esc_html_e( 'Account', 'al-qamara-lite' ); ?></span>
        </a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>



    <!-- MOBILE OVERLAY -->
    <div class="mobile-overlay" id="mobileOverlay" aria-hidden="true"></div>

    <!-- MOBILE SLIDE-IN MENU -->
    <div class="mobile-menu" id="mobileMenu"
         role="dialog"
         aria-label="<?php esc_attr_e( 'Mobile Navigation', 'al-qamara-lite' ); ?>"
         aria-modal="true">
        <div class="mobile-menu-header">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
               aria-label="<?php bloginfo( 'name' ); ?>">
                <?php alqamara_the_logo( 'mobile-logo' ); ?>
            </a>
            <button class="mobile-close-btn"
                    id="mobileMenuClose"
                    aria-label="<?php esc_attr_e( 'Close menu', 'al-qamara-lite' ); ?>">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <?php if ( $is_shop_mode && ! $show_search ) : ?>
        <!-- Search inside the mobile menu — only in Shop header style, and
             only when the dedicated mobile search icon above is hidden
             (search icon toggle off), so there's always exactly one way to
             search on mobile in shop mode, never zero, never a duplicate.
             In Default/Agency header style this is intentionally omitted
             entirely — those styles show no shop UI on desktop either
             (no cart/account/search icons), so a WooCommerce product
             search box in the mobile menu would be inconsistent with that
             choice. -->
        <div class="mobile-menu-search px-3 pt-3 pb-2">
            <?php alqamara_product_search_form(); ?>
        </div>
        <?php endif; ?>

        <div class="mobile-nav-links">
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'walker'         => new Alqamara_Mobile_Menu_Walker(),
                'fallback_cb'    => 'alqamara_mobile_nav_fallback',
                'depth'          => 4,
            ] );
            ?>
        </div>
        <?php if ( 'agency' === $header_style && $cta_text ) : ?>
        <div class="mobile-menu-footer">
            <a href="<?php echo esc_url( $cta_url ); ?>"
               class="btn btn-custom d-flex justify-content-center px-4 py-2 w-100">
                <?php echo esc_html( $cta_text ); ?>
            </a>
        </div>
        <?php endif; ?>
    </div><!-- /#mobileMenu -->

<!-- WCAG — Skip Link target. See the anchor near the top of this file. -->
<main id="main-content" class="alqamara-main" tabindex="-1">
