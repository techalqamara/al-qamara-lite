<?php
/**
 * Single Post Template
 *
 * @package alqamara
 */

get_header();

// ── Set up post data ONCE before the banner ──────────────────────────────────
// This allows get_the_author(), the_title(), get_the_date() etc. to work
// in the banner section — outside the loop below.
the_post();
?>

<!-- ===================== PAGE WRAPPER ===================== -->
<div class="blog-page-wrapper">

    <!-- ===================== PAGE BANNER ===================== -->
    <?php
    $alqamara_show_banner_s = (bool) get_theme_mod( 'alqamara_banner_show_single', 1 );
    $alqamara_banner_align  = get_theme_mod( 'alqamara_banner_alignment', 'left' );
    $single_cats       = get_the_category(); // used in in-content meta (banner OFF)
    ?>
    <?php if ( $alqamara_show_banner_s ) : ?>
    <div class="blog-page-banner">
        <div class="container">
            <div class="blog-banner-inner<?php echo 'center' === $alqamara_banner_align ? ' text-center' : ''; ?>" data-aos="fade-down" data-aos-delay="100">

                <!-- Breadcrumb -->
                <?php alqamara_breadcrumb(); ?>

                <!-- Post Title -->
                <h1 class="blog-banner-title"><?php the_title(); ?></h1>

                <!-- Post Meta -->
                <div class="single-banner-meta">
                    <span class="single-meta-item">
                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                        <time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
                            <?php echo esc_html( get_the_date() ); ?>
                        </time>
                    </span>
                    <span class="single-meta-sep" aria-hidden="true">·</span>
                    <span class="single-meta-item">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <?php echo esc_html( get_the_author() ); ?>
                    </span>
                    <span class="single-meta-sep" aria-hidden="true">·</span>
                    <span class="single-meta-item">
                        <i class="fas fa-comment" aria-hidden="true"></i>
                        <?php
/* translators: %s: number of comments */
printf( esc_html( _n( '%s Comment', '%s Comments', get_comments_number(), 'al-qamara-lite' ) ), esc_html( number_format_i18n( get_comments_number() ) ) );
?>
                    </span>
                    <?php if ( get_the_tags() ) : ?>
                    <span class="single-meta-sep" aria-hidden="true">·</span>
                    <span class="single-meta-item">
                        <i class="fas fa-tag" aria-hidden="true"></i>
                        <?php the_tags( '', ', ', '' ); ?>
                    </span>
                    <?php endif; ?>
                </div><!-- /.single-banner-meta -->

            </div>
        </div>
    </div>

    <?php else : ?>

    <!-- Fallback: thin breadcrumb bar (banner OFF) -->
    <?php alqamara_breadcrumb( 'bar' ); ?>

    <?php endif; ?>
    <!-- ===================== PAGE BANNER END ===================== -->


    <!-- ===================== MAIN CONTENT ===================== -->
    <div class="container page-container<?php echo $alqamara_show_banner_s ? '' : ' page-container--no-banner'; ?>">
        <div class="row g-5">


            <!-- ===================== POST COLUMN ===================== -->
            <div class="col-lg-8">

                <!-- Post Card -->
                <!-- NOTE: No while/the_post() here — already called above -->
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content-card mb-4' ); ?>>

                    <!-- In-content title + meta (only when banner is OFF) -->
                    <?php if ( ! $alqamara_show_banner_s ) : ?>
                    <div class="alqamara-content-header">
                        <h1 class="alqamara-content-title"><?php the_title(); ?></h1>
                        <div class="alqamara-content-meta">
                            <span class="single-meta-item">
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                <time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </time>
                            </span>
                            <span class="single-meta-sep" aria-hidden="true">·</span>
                            <span class="single-meta-item">
                                <i class="fas fa-user" aria-hidden="true"></i>
                                <?php echo esc_html( get_the_author() ); ?>
                            </span>
                            <span class="single-meta-sep" aria-hidden="true">·</span>
                            <span class="single-meta-item">
                                <i class="fas fa-comment" aria-hidden="true"></i>
                                <?php
/* translators: %s: number of comments */
printf( esc_html( _n( '%s Comment', '%s Comments', get_comments_number(), 'al-qamara-lite' ) ), esc_html( number_format_i18n( get_comments_number() ) ) );
?>
                            </span>
                            <?php if ( $single_cats ) : ?>
                            <span class="single-meta-sep" aria-hidden="true">·</span>
                            <span class="single-meta-item">
                                <i class="fas fa-folder" aria-hidden="true"></i>
                                <a href="<?php echo esc_url( get_category_link( $single_cats[0]->term_id ) ); ?>">
                                    <?php echo esc_html( $single_cats[0]->name ); ?>
                                </a>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Featured Image — LCP element; eager + high priority for fast paint -->
                    <?php if ( has_post_thumbnail() && get_theme_mod( 'alqamara_single_show_thumbnail', 1 ) ) : ?>
                    <div class="page-featured-img">
                        <?php the_post_thumbnail( 'alqamara-featured', [
                            'class'            => 'img-fluid w-100',
                            'loading'          => 'eager',
                            'fetchpriority'    => 'high',
                            'decoding'         => 'async',
                            'alt'              => get_the_title(),
                        ] ); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Post Body -->
                    <div class="page-body">

                        <!-- Post Content -->
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

                        <!-- Tags -->
                        <?php if ( get_the_tags() ) : ?>
                        <div class="single-tags-wrap">
                            <i class="fas fa-tags me-2 text-primary" aria-hidden="true"></i>
                            <?php the_tags( '<span class="single-tags-label">' . esc_html__( 'Tags:', 'al-qamara-lite' ) . '</span> ', ', ', '' ); ?>
                        </div>
                        <?php endif; ?>

                    </div><!-- /.page-body -->

                </article>
                <!-- Post Card End -->


                <!-- ===================== AUTHOR BOX ===================== -->
                <?php if ( get_theme_mod( 'alqamara_show_author_box', 1 ) ) :
                    $alqamara_author_id  = (int) get_the_author_meta( 'ID' );
                    $alqamara_author_bio = get_the_author_meta( 'description' );
                    $alqamara_author_url = get_author_posts_url( $alqamara_author_id );
                ?>
                <div class="single-author-box">
                    <a href="<?php echo esc_url( $alqamara_author_url ); ?>" class="author-box-avatar">
                        <?php echo get_avatar( $alqamara_author_id, 90 ); ?>
                    </a>
                    <div class="author-box-body">
                        <h3 class="author-box-name">
                            <a href="<?php echo esc_url( $alqamara_author_url ); ?>">
                                <?php echo esc_html( get_the_author() ); ?>
                            </a>
                        </h3>
                        <?php if ( $alqamara_author_bio ) : ?>
                        <p class="author-box-bio"><?php echo esc_html( $alqamara_author_bio ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <!-- ===================== AUTHOR BOX END ===================== -->


                <!-- ===================== POST NAVIGATION ===================== -->
                <div class="single-post-nav">
                    <div class="row g-3">

                        <!-- Previous Post -->
                        <div class="col-6">
                            <?php $prev_post = get_previous_post(); if ( $prev_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" class="single-nav-card single-nav-prev">
                                <span class="single-nav-label">
                                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                                    <?php esc_html_e( 'Previous Post', 'al-qamara-lite' ); ?>
                                </span>
                                <span class="single-nav-title">
                                    <?php echo esc_html( get_the_title( $prev_post ) ); ?>
                                </span>
                            </a>
                            <?php endif; ?>
                        </div>

                        <!-- Next Post -->
                        <div class="col-6">
                            <?php $next_post = get_next_post(); if ( $next_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="single-nav-card single-nav-next text-end">
                                <span class="single-nav-label">
                                    <?php esc_html_e( 'Next Post', 'al-qamara-lite' ); ?>
                                    <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
                                </span>
                                <span class="single-nav-title">
                                    <?php echo esc_html( get_the_title( $next_post ) ); ?>
                                </span>
                            </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <!-- ===================== POST NAVIGATION END ===================== -->


                <!-- ===================== RELATED POSTS ===================== -->
                <?php
                $related_posts = null;
                $category_ids  = wp_list_pluck( get_the_category(), 'term_id' );
                if ( $category_ids && get_theme_mod( 'alqamara_show_related', 1 ) ) {
                    $related_posts = new WP_Query( [
                        'category__in'        => $category_ids,
                        'posts_per_page'      => 3,
                        'post__not_in'        => [ get_the_ID() ],
                        'orderby'             => 'rand',
                        'ignore_sticky_posts' => true,
                    ] );
                }

                if ( $related_posts && $related_posts->have_posts() ) :
                ?>
                <div class="single-related-wrap">

                    <h2 class="single-related-title">
                        <i class="fas fa-layer-group me-2 text-primary" aria-hidden="true"></i>
                        <?php esc_html_e( 'Related Posts', 'al-qamara-lite' ); ?>
                    </h2>

                    <div class="row g-4">
                        <?php $rel_i = 0; while ( $related_posts->have_posts() ) : $related_posts->the_post(); $rel_i++; ?>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( ( $rel_i - 1 ) * 100 ); ?>">
                            <div class="related-post-card">
                                <?php if ( has_post_thumbnail() ) : ?>
                                <div class="related-post-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'alqamara-thumb', [
                                            'class'   => 'related-thumb-img',
                                            'loading' => 'lazy',
                                        ] ); ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div class="related-post-body">
                                    <span class="related-post-date">
                                        <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i>
                                        <?php echo esc_html( get_the_date() ); ?>
                                    </span>
                                    <h3 class="related-post-title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>

                </div>
                <?php endif; ?>
                <!-- ===================== RELATED POSTS END ===================== -->


                <!-- ===================== COMMENTS ===================== -->
                <?php comments_template(); ?>
                <!-- ===================== COMMENTS END ===================== -->


            </div>
            <!-- ===================== POST COLUMN END ===================== -->


            <!-- ===================== SIDEBAR ===================== -->
            <?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
            <div class="col-lg-4 <?php echo esc_attr( get_theme_mod( 'alqamara_sidebar_position', 'right' ) === 'left' ? 'order-lg-first' : '' ); ?>">
                <aside class="blog-sidebar" role="complementary"
                       aria-label="<?php esc_attr_e( 'Post Sidebar', 'al-qamara-lite' ); ?>">
                    <?php get_sidebar(); ?>
                </aside>
            </div>
            <?php endif; ?>
            <!-- ===================== SIDEBAR END ===================== -->


        </div><!-- /.row -->
    </div><!-- /.container -->
    <!-- ===================== MAIN CONTENT END ===================== -->

</div>
<!-- ===================== PAGE WRAPPER END ===================== -->

<?php
get_footer();