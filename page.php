<?php
/**
 * Default Page Template
 *
 * WooCommerce pages (cart, checkout, my-account) bypass the page banner
 * and sidebar entirely — they get a clean full-width container instead,
 * consistent with other WC pages (shop, single product).
 *
 * @package alqamara
 */

get_header();

/* Detect WooCommerce utility pages — no banner, no sidebar needed */
$is_wc_utility = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url() );
?>

<!-- ===================== PAGE WRAPPER ===================== -->
<div class="blog-page-wrapper">

<?php if ( $is_wc_utility ) : ?>

    <!-- ── WooCommerce utility page (cart / checkout / my-account) ───────── -->
    <!-- No page banner, no sidebar. Thin breadcrumb + full-width content.   -->
    <div class="container py-4">
        <div id="alqamara-wc-main" class="alqamara-wc-main">

            <?php
            /* No breadcrumb on My Account pages (is_account_page() covers every endpoint):
             * they share one sidebar-nav layout, so a trail adds little. Cart and
             * Checkout keep it. */
            if ( (bool) get_theme_mod( 'alqamara_wc_breadcrumb', true ) && ! is_account_page() ) : ?>
            <div class="alqamara-shop-header-bar border-bottom mb-4 py-2">
                <?php woocommerce_breadcrumb(); ?>
            </div>
            <?php endif; ?>

            <?php while ( have_posts() ) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; ?>

        </div>
    </div>

<?php else : ?>

    <!-- ===================== PAGE BANNER ===================== -->
    <?php
    $alqamara_show_banner_p = (bool) get_theme_mod( 'alqamara_banner_show_page', 1 );
    $alqamara_banner_align  = get_theme_mod( 'alqamara_banner_alignment', 'left' );
    ?>
    <?php if ( $alqamara_show_banner_p ) : ?>
    <div class="blog-page-banner">
        <div class="container">
            <div class="blog-banner-inner<?php echo 'center' === $alqamara_banner_align ? ' text-center' : ''; ?>" data-aos="fade-down" data-aos-delay="100">

                <!-- Breadcrumb -->
                <?php alqamara_breadcrumb(); ?>

                <!-- Page Title -->
                <h1 class="blog-banner-title">
                    <?php the_title(); ?>
                </h1>

            </div>
        </div>
    </div>

    <?php else : ?>

    <!-- Fallback: thin breadcrumb bar (banner OFF) -->
    <?php alqamara_breadcrumb( 'bar' ); ?>

    <?php endif; ?>
    <!-- ===================== PAGE BANNER END ===================== -->


    <!-- ===================== PAGE CONTENT ===================== -->
    <div class="container page-container<?php echo $alqamara_show_banner_p ? '' : ' page-container--no-banner'; ?>">
        <div class="row g-5">


            <!-- ===================== CONTENT COLUMN ===================== -->
            <?php $show_page_sidebar = is_active_sidebar( 'sidebar-blog' ); ?>
            <div class="<?php echo $show_page_sidebar ? 'col-lg-8' : 'col-lg-10 mx-auto'; ?>">

                <?php while ( have_posts() ) : the_post(); ?>

                <article id="page-<?php the_ID(); ?>" <?php post_class( 'page-content-card' ); ?>>

                    <!-- In-content title (only when banner is OFF) -->
                    <?php if ( ! $alqamara_show_banner_p ) : ?>
                    <div class="alqamara-content-header">
                        <h1 class="alqamara-content-title"><?php the_title(); ?></h1>
                    </div>
                    <?php endif; ?>

                    <!-- Featured Image — LCP element; eager + high priority for fast paint -->
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="page-featured-img">
                        <?php the_post_thumbnail( 'alqamara-featured', [
                            'class'         => 'img-fluid w-100',
                            'loading'       => 'eager',
                            'fetchpriority' => 'high',
                            'decoding'      => 'async',
                            'alt'     => get_the_title(),
                        ] ); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Page Body -->
                    <div class="page-body">

                        <!-- Content -->
                        <div class="page-entry-content">
                            <?php
                            the_content();

                            wp_link_pages( [
                                'before'      => '<div class="page-links"><span>' . esc_html__( 'Pages:', 'al-qamara-lite' ) . '</span>',
                                'after'       => '</div>',
                                'link_before' => '<span>',
                                'link_after'  => '</span>',
                            ] );
                            ?>
                        </div><!-- /.page-entry-content -->

                    </div><!-- /.page-body -->

                </article>

                <?php endwhile; ?>

            </div>
            <!-- ===================== CONTENT COLUMN END ===================== -->


            <!-- ===================== SIDEBAR ===================== -->
            <?php if ( $show_page_sidebar ) : ?>
            <div class="col-lg-4 <?php echo esc_attr( get_theme_mod( 'alqamara_sidebar_position', 'right' ) === 'left' ? 'order-lg-first' : '' ); ?>">
                <aside class="blog-sidebar" role="complementary"
                       aria-label="<?php esc_attr_e( 'Page Sidebar', 'al-qamara-lite' ); ?>">
                    <?php get_sidebar(); ?>
                </aside>
            </div>
            <?php endif; ?>
            <!-- ===================== SIDEBAR END ===================== -->


        </div><!-- /.row -->
    </div><!-- /.container -->
    <!-- ===================== PAGE CONTENT END ===================== -->

<?php endif; ?>

</div>
<!-- ===================== PAGE WRAPPER END ===================== -->

<?php get_footer(); ?>