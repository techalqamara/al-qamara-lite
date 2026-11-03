<?php
/**
 * Template Name: Blog Listing
 *
 * A dedicated "Blog" page template that runs its own WP_Query for the
 * latest published posts and renders them with the exact same card
 * markup/classes as archive.php's category/tag/date archive loop, so the
 * two stay visually identical.
 *
 * Added 2026-08-19: the "Blog" page (slug /blog/) previously had no
 * connection to the actual post loop at all — Settings > Reading never had
 * "Posts page" assigned to it, so it rendered as an ordinary near-empty
 * static page via page.php. Rather than changing the site-wide "Front page
 * displays" setting (show_on_front) to wire up WordPress's native
 * page_for_posts behavior — which would also affect is_front_page()/
 * is_home() semantics used elsewhere in the theme for conditional CSS/
 * layout — this page template gives the Blog page its own self-contained
 * post loop instead, with zero risk to the homepage's existing behavior.
 *
 * @package alqamara
 */

get_header();

$alqamara_show_banner_a = (bool) get_theme_mod( 'alqamara_banner_show_archive', 1 );
$alqamara_banner_align  = get_theme_mod( 'alqamara_banner_alignment', 'left' );
$alqamara_page_title    = get_the_title();

$alqamara_paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

$alqamara_blog_query = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => get_option( 'posts_per_page' ), // phpcs:ignore WPThemeReview.CoreFunctionality.PostsPerPage.posts_per_page_posts_per_page -- Uses the site's own "Blog pages show at most" setting.
    'paged'          => $alqamara_paged,
] );
?>

<div class="blog-page-wrapper">

    <!-- PAGE BANNER -->
    <?php if ( $alqamara_show_banner_a ) : ?>
    <div class="blog-page-banner">
        <div class="container">
            <div class="blog-banner-inner<?php echo 'center' === $alqamara_banner_align ? ' text-center' : ''; ?>">

                <?php alqamara_breadcrumb(); ?>

                <h1 class="blog-banner-title"><?php echo esc_html( $alqamara_page_title ); ?></h1>

                <?php if ( $alqamara_blog_query->have_posts() ) : ?>
                <p class="blog-banner-subtitle">
                    <?php printf(
                        /* translators: %d: number of posts */
                        esc_html( _n( '%d post found', '%d posts found', (int) $alqamara_blog_query->found_posts, 'al-qamara-lite' ) ),
                        (int) $alqamara_blog_query->found_posts
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
    <!-- PAGE BANNER END -->


    <!-- MAIN CONTENT -->
    <div class="container blog-container<?php echo $alqamara_show_banner_a ? '' : ' blog-container--no-banner'; ?>">

        <?php if ( $alqamara_blog_query->have_posts() ) : ?>

            <div class="row g-4">

                <?php $alqamara_i = 0; while ( $alqamara_blog_query->have_posts() ) : $alqamara_blog_query->the_post(); $alqamara_i++; ?>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( min( ( $alqamara_i - 1 ) * 100, 300 ) ); ?>">
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-post-card h-100 d-flex flex-column' ); ?>>

                        <!-- Thumbnail -->
                        <?php if ( has_post_thumbnail() && get_theme_mod( 'alqamara_show_thumbnail', 1 ) ) : ?>
                        <div class="blog-post-thumb">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php the_post_thumbnail( 'alqamara-card', [ 'class' => 'blog-thumb-img', 'loading' => 'lazy' ] ); ?>
                            </a>
                            <?php $category = get_the_category(); if ( $category ) : ?>
                            <span class="blog-post-cat"><?php echo esc_html( $category[0]->name ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Post Body -->
                        <div class="blog-post-body d-flex flex-column flex-grow-1">

                            <h2 class="blog-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="blog-post-meta">
                                <?php if ( get_theme_mod( 'alqamara_show_meta_date', 1 ) ) : ?>
                                <span class="blog-meta-item">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                    <time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
                                        <?php echo esc_html( get_the_date() ); ?>
                                    </time>
                                </span>
                                <?php endif; ?>
                                <?php if ( get_theme_mod( 'alqamara_show_meta_author', 1 ) ) : ?>
                                <span class="blog-meta-divider" aria-hidden="true">·</span>
                                <span class="blog-meta-item">
                                    <i class="fas fa-user" aria-hidden="true"></i>
                                    <?php echo esc_html( get_the_author() ); ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <?php $alqamara_excerpt = get_the_excerpt(); if ( $alqamara_excerpt ) : ?>
                            <p class="blog-post-excerpt"><?php echo esc_html( $alqamara_excerpt ); ?></p>
                            <?php endif; ?>

                            <?php $rm_text = get_theme_mod( 'alqamara_readmore_text', __( 'Read More', 'al-qamara-lite' ) ); if ( $rm_text ) : ?>
                            <a href="<?php the_permalink(); ?>" class="blog-read-more mt-auto">
                                <?php echo esc_html( $rm_text ); ?>
                                <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                            </a>
                            <?php endif; ?>

                        </div>

                    </article>
                </div>

                <?php endwhile; wp_reset_postdata(); ?>

            </div><!-- /.row -->

            <!-- Pagination — built from $alqamara_blog_query directly, since this
                 isn't the global main query paginate_links() defaults to. -->
            <?php
            $alqamara_links = paginate_links( [
                'total'     => $alqamara_blog_query->max_num_pages,
                'current'   => $alqamara_paged,
                'type'      => 'list',
                'prev_text' => '<i class="fas fa-chevron-left" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Previous', 'al-qamara-lite' ) . '</span>',
                'next_text' => '<i class="fas fa-chevron-right" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Next', 'al-qamara-lite' ) . '</span>',
            ] );
            if ( $alqamara_links ) :
            ?>
            <div class="blog-pagination-wrap mt-5">
                <nav class="alqamara-pagination d-flex justify-content-center mt-5" aria-label="<?php esc_attr_e( 'Page navigation', 'al-qamara-lite' ); ?>">
                    <?php echo wp_kses_post( $alqamara_links ); ?>
                </nav>
            </div>
            <?php endif; ?>

        <?php else : ?>

            <div class="blog-no-posts text-center py-5" data-aos="fade-up">
                <div class="blog-no-posts-icon mb-3">
                    <i class="fas fa-folder-open fa-3x" aria-hidden="true"></i>
                </div>
                <h3><?php esc_html_e( 'No Posts Found', 'al-qamara-lite' ); ?></h3>
                <p><?php esc_html_e( 'Check back soon for new posts.', 'al-qamara-lite' ); ?></p>
            </div>

        <?php endif; ?>

    </div><!-- /.container -->

</div>

<?php get_footer(); ?>
