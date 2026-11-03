<?php
/**
 * Search Results Template
 *
 * Product search  → WooCommerce product grid (full-width, no sidebar)
 * Blog search     → 3-column Bootstrap card grid (no sidebar)
 *
 * @package alqamara
 */

$is_product_search = class_exists( 'WooCommerce' ) && 'product' === get_query_var( 'post_type' );

get_header();
?>

<div class="blog-page-wrapper">

    <!-- ── Banner ──────────────────────────────────────────────── -->
    <?php
    $alqamara_show_banner_sr = (bool) get_theme_mod( 'alqamara_banner_show_search', 1 );
    $alqamara_banner_align   = get_theme_mod( 'alqamara_banner_alignment', 'left' );
    ?>
    <?php if ( $alqamara_show_banner_sr ) : ?>
    <div class="blog-page-banner">
        <div class="container">
            <div class="blog-banner-inner<?php echo 'center' === $alqamara_banner_align ? ' text-center' : ''; ?>">

                <span class="blog-banner-label">
                    <i class="fas fa-search me-2" aria-hidden="true"></i>
                    <?php echo $is_product_search
                        ? esc_html__( 'Product Search', 'al-qamara-lite' )
                        : esc_html__( 'Search Results', 'al-qamara-lite' ); ?>
                </span>

                <h1 class="blog-banner-title">
                    <?php
                    if ( get_search_query() ) {
                        printf(
                            /* translators: %s: search query */
                            esc_html__( 'Results for: "%s"', 'al-qamara-lite' ),
                            '<span>' . esc_html( get_search_query() ) . '</span>'
                        );
                    } else {
                        esc_html_e( 'Search Results', 'al-qamara-lite' );
                    }
                    ?>
                </h1>

                <?php if ( have_posts() ) : ?>
                <p class="blog-banner-subtitle">
                    <?php printf(
                        /* translators: %d: number of results */
                        esc_html( _n( '%d result found', '%d results found', (int) $wp_query->found_posts, 'al-qamara-lite' ) ),
                        (int) $wp_query->found_posts
                    ); ?>
                </p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php else : ?>

    <!-- Fallback: thin breadcrumb bar (banner OFF) -->
    <?php alqamara_breadcrumb( 'bar' ); ?>

    <?php endif; ?>

    <!-- ── Full-width search bar strip ─────────────────────────── -->
    <div class="py-4 bg-white border-bottom shadow-sm">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <?php $is_product_search ? alqamara_product_search_form( true ) : get_search_form(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Results ──────────────────────────────────────────────── -->
    <div class="container blog-container<?php echo $alqamara_show_banner_sr ? '' : ' blog-container--no-banner'; ?>">

        <!-- In-content search title + count (only when banner is OFF) -->
        <?php if ( ! $alqamara_show_banner_sr ) : ?>
        <div class="alqamara-content-header alqamara-archive-header">
            <h1 class="alqamara-content-title">
                <?php
                if ( get_search_query() ) {
                    printf(
                        /* translators: %s: search query */
                        esc_html__( 'Results for: "%s"', 'al-qamara-lite' ),
                        esc_html( get_search_query() )
                    );
                } else {
                    esc_html_e( 'Search Results', 'al-qamara-lite' );
                }
                ?>
            </h1>
            <?php if ( have_posts() ) : ?>
            <div class="alqamara-content-meta">
                <span class="single-meta-item">
                    <?php printf(
                        /* translators: %d: number of results */
                        esc_html( _n( '%d result found', '%d results found', (int) $wp_query->found_posts, 'al-qamara-lite' ) ),
                        (int) $wp_query->found_posts
                    ); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $is_product_search ) : ?>

            <?php /* WooCommerce product grid ──────────────────── */ ?>
            <?php if ( have_posts() ) : ?>
                <?php wc_setup_loop( [ 'name' => 'search-results' ] ); ?>
                <?php do_action( 'woocommerce_before_shop_loop' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
                <?php woocommerce_product_loop_start(); ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php wc_get_template_part( 'content', 'product' ); ?>
                    <?php endwhile; ?>
                <?php woocommerce_product_loop_end(); ?>
                <?php do_action( 'woocommerce_after_shop_loop' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
                <?php wc_reset_loop(); ?>
            <?php else : ?>
                <?php do_action( 'woocommerce_no_products_found' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, must keep its name. ?>
            <?php endif; ?>

        <?php elseif ( have_posts() ) : ?>

            <?php /* 3-column blog card grid ─────────────────────── */ ?>
            <div class="row g-4">

                <?php $alqamara_i = 0; while ( have_posts() ) : the_post(); $alqamara_i++; ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( min( ( $alqamara_i - 1 ) * 100, 300 ) ); ?>">
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-post-card h-100 d-flex flex-column' ); ?>>

                        <?php if ( has_post_thumbnail() ) : ?>
                        <div class="blog-post-thumb">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php the_post_thumbnail( 'alqamara-card', [ 'class' => 'blog-thumb-img', 'loading' => 'lazy' ] ); ?>
                            </a>
                            <?php $alqamara_cat = get_the_category(); if ( $alqamara_cat ) : ?>
                            <span class="blog-post-cat"><?php echo esc_html( $alqamara_cat[0]->name ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="blog-post-body d-flex flex-column flex-grow-1">

                            <div class="blog-post-meta">
                                <span class="blog-meta-item">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                    <time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
                                        <?php echo esc_html( get_the_date() ); ?>
                                    </time>
                                </span>
                                <span class="blog-meta-divider" aria-hidden="true">·</span>
                                <span class="blog-meta-item">
                                    <i class="fas fa-user" aria-hidden="true"></i>
                                    <?php echo esc_html( get_the_author() ); ?>
                                </span>
                            </div>

                            <h2 class="blog-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <?php $alqamara_excerpt = get_the_excerpt(); if ( $alqamara_excerpt ) : ?>
                            <p class="blog-post-excerpt"><?php echo esc_html( $alqamara_excerpt ); ?></p>
                            <?php endif; ?>

                            <a href="<?php the_permalink(); ?>" class="blog-read-more mt-auto">
                                <?php esc_html_e( 'Read More', 'al-qamara-lite' ); ?>
                                <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                            </a>

                        </div><!-- /.blog-post-body -->

                    </article>
                </div><!-- /.col -->
                <?php endwhile; ?>

            </div><!-- /.row -->

            <div class="blog-pagination-wrap mt-5">
                <?php alqamara_pagination(); ?>
            </div>

        <?php else : ?>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 text-center">
                    <div class="blog-no-posts">
                        <div class="blog-no-posts-icon">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </div>
                        <h3><?php esc_html_e( 'No Results Found', 'al-qamara-lite' ); ?></h3>
                        <p>
                            <?php printf(
                                /* translators: %s: search query */
                                esc_html__( 'No results found for "%s". Please try a different keyword.', 'al-qamara-lite' ),
                                '<strong>' . esc_html( get_search_query() ) . '</strong>'
                            ); ?>
                        </p>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-custom mt-3">
                            <i class="fas fa-home me-2" aria-hidden="true"></i>
                            <?php esc_html_e( 'Back to Home', 'al-qamara-lite' ); ?>
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div><!-- /.container.blog-container -->

</div><!-- /.blog-page-wrapper -->

<?php get_footer(); ?>
