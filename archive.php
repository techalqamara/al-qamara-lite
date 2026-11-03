<?php
/**
 * Archive Template
 * Handles: Category, Tag, Author, Date archives
 *
 * @package alqamara
 */

get_header();
?>

<div class="blog-page-wrapper">

    <!-- PAGE BANNER -->
    <?php
    $alqamara_show_banner_a = (bool) get_theme_mod( 'alqamara_banner_show_archive', 1 );
    $alqamara_banner_align  = get_theme_mod( 'alqamara_banner_alignment', 'left' );
    ?>
    <?php if ( $alqamara_show_banner_a ) : ?>
    <div class="blog-page-banner">
        <div class="container">
            <div class="blog-banner-inner<?php echo 'center' === $alqamara_banner_align ? ' text-center' : ''; ?>">

                <?php alqamara_breadcrumb(); ?>

                <h1 class="blog-banner-title"><?php the_archive_title(); ?></h1>

                <?php
                /*
                 * WordPress core wraps term_description() in wpautop by
                 * default (see wp-includes/default-filters.php), so
                 * get_the_archive_description() already comes back as
                 * "<p>...</p>" — wrapping that again in our own
                 * <p class="blog-banner-subtitle"> produced invalid nested
                 * <p><p>...</p></p> markup. Browsers auto-close the outer
                 * <p> when they hit the inner one, which left the real text
                 * sitting in an unstyled sibling <p> (wrong/dark color,
                 * invisible on the dark banner) instead of inside our
                 * styled element. Strip tags before re-wrapping so the
                 * description is always plain text inside one <p> — a
                 * category/tag description is a short one-line blurb, never
                 * intentionally formatted, so nothing is lost.
                 */
                $alqamara_archive_desc = get_the_archive_description();
                ?>
                <?php if ( $alqamara_archive_desc ) : ?>
                <p class="blog-banner-subtitle"><?php echo esc_html( wp_strip_all_tags( $alqamara_archive_desc ) ); ?></p>
                <?php endif; ?>

                <?php if ( have_posts() ) : ?>
                <p class="blog-banner-subtitle">
                    <?php printf(
                        /* translators: %d: number of posts */
                        esc_html( _n( '%d post found', '%d posts found', (int) $wp_query->found_posts, 'al-qamara-lite' ) ),
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
    <!-- PAGE BANNER END -->


    <!-- MAIN CONTENT -->
    <div class="container blog-container<?php echo $alqamara_show_banner_a ? '' : ' blog-container--no-banner'; ?>">

        <!-- In-content archive header (only when banner is OFF) -->
        <?php if ( ! $alqamara_show_banner_a ) : ?>
        <?php
        // Archive type label + icon
        if ( is_category() ) {
            $alqamara_arc_icon  = 'fa-folder';
            $alqamara_arc_label = __( 'Category', 'al-qamara-lite' );
            $alqamara_arc_name  = single_term_title( '', false );
        } elseif ( is_tag() ) {
            $alqamara_arc_icon  = 'fa-tag';
            $alqamara_arc_label = __( 'Tag', 'al-qamara-lite' );
            $alqamara_arc_name  = single_term_title( '', false );
        } elseif ( is_author() ) {
            $alqamara_arc_icon  = 'fa-user';
            $alqamara_arc_label = __( 'Author', 'al-qamara-lite' );
            $alqamara_arc_name  = get_the_author();
        } elseif ( is_year() ) {
            $alqamara_arc_icon  = 'fa-calendar';
            $alqamara_arc_label = __( 'Year Archive', 'al-qamara-lite' );
            $alqamara_arc_name  = get_the_date( 'Y' );
        } elseif ( is_month() ) {
            $alqamara_arc_icon  = 'fa-calendar-alt';
            $alqamara_arc_label = __( 'Monthly Archive', 'al-qamara-lite' );
            $alqamara_arc_name  = get_the_date( 'F Y' );
        } else {
            $alqamara_arc_icon  = 'fa-archive';
            $alqamara_arc_label = __( 'Archive', 'al-qamara-lite' );
            $alqamara_arc_name  = get_the_archive_title();
        }
        ?>
        <div class="alqamara-content-header alqamara-archive-header">

            <!-- Archive type badge -->
            <span class="alqamara-archive-badge">
                <i class="fas <?php echo esc_attr( $alqamara_arc_icon ); ?>" aria-hidden="true"></i>
                <?php echo esc_html( $alqamara_arc_label ); ?>
            </span>

            <!-- Archive name -->
            <h1 class="alqamara-content-title"><?php echo esc_html( $alqamara_arc_name ); ?></h1>

            <!-- Description (optional) -->
            <?php
            // Same wpautop double-<p> issue as the banner subtitle above —
            // see the comment there. Strip tags before re-wrapping.
            $alqamara_archive_desc_nb = get_the_archive_description();
            ?>
            <?php if ( $alqamara_archive_desc_nb ) : ?>
            <p class="alqamara-archive-desc"><?php echo esc_html( wp_strip_all_tags( $alqamara_archive_desc_nb ) ); ?></p>
            <?php endif; ?>

            <!-- Post count -->
            <?php if ( have_posts() ) : ?>
            <p class="alqamara-archive-count">
                <?php printf(
                    /* translators: %d: number of posts */
                    esc_html( _n( '%d post found', '%d posts found', (int) $wp_query->found_posts, 'al-qamara-lite' ) ),
                    (int) $wp_query->found_posts
                ); ?>
            </p>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <?php if ( have_posts() ) : ?>

            <div class="row g-4">

                <?php $alqamara_i = 0; while ( have_posts() ) : the_post(); $alqamara_i++; ?>

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

                <?php endwhile; ?>

            </div><!-- /.row -->

            <!-- Pagination -->
            <div class="blog-pagination-wrap mt-5">
                <?php alqamara_pagination(); ?>
            </div>

        <?php else : ?>

            <div class="blog-no-posts text-center py-5" data-aos="fade-up">
                <div class="blog-no-posts-icon mb-3">
                    <i class="fas fa-folder-open fa-3x" aria-hidden="true"></i>
                </div>
                <h3><?php esc_html_e( 'No Posts Found', 'al-qamara-lite' ); ?></h3>
                <p><?php esc_html_e( 'Sorry, no posts were found in this archive.', 'al-qamara-lite' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-custom mt-3">
                    <i class="fas fa-home me-2" aria-hidden="true"></i>
                    <?php esc_html_e( 'Back to Home', 'al-qamara-lite' ); ?>
                </a>
            </div>

        <?php endif; ?>

    </div><!-- /.container -->

</div>

<?php get_footer(); ?>
