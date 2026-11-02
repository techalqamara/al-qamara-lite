<?php
/**
 * Template Name: Narrow
 * Template Post Type: post, page
 *
 * No banner, no breadcrumb. Page title shown as a clean in-content header.
 * Content in col-lg-10 centered — no sidebar.
 * Use for: clean minimal pages where the dark banner is not needed.
 *
 * This template is assignable to BOTH pages and posts (see "Template Post
 * Type" above). For a POST using this template, the same blog-post extras as
 * single.php (tags, related posts, comments) are shown, using the same
 * Customizer toggles. A PAGE using this template shows title, featured image
 * and content only.
 *
 * @package alqamara
 */

get_header();
?>

<div class="blog-page-wrapper">

    <div class="container page-container page-container--no-banner">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <?php while ( have_posts() ) : the_post();
                    /* Blog-specific extras below only apply when a POST
                     * uses this template — see file header comment. */
                    $alqamara_is_post = ( 'post' === get_post_type() );
                ?>

                <article id="page-<?php the_ID(); ?>" <?php post_class( 'page-content-card' ); ?>>

                    <!-- Clean title header — no banner, no breadcrumb -->
                    <div class="alqamara-content-header">
                        <h1 class="alqamara-content-title"><?php the_title(); ?></h1>
                    </div>

                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="page-featured-img">
                        <?php the_post_thumbnail( 'alqamara-featured', [
                            'class'         => 'img-fluid w-100',
                            'loading'       => 'eager',
                            'fetchpriority' => 'high',
                            'decoding'      => 'async',
                            'alt'           => get_the_title(),
                        ] ); ?>
                    </div>
                    <?php endif; ?>

                    <div class="page-body">
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

                        <?php if ( $alqamara_is_post ) : ?>

                        <!-- Tags -->
                        <?php if ( get_the_tags() ) : ?>
                        <div class="single-tags-wrap">
                            <i class="fas fa-tags me-2 text-primary" aria-hidden="true"></i>
                            <?php the_tags( '<span class="single-tags-label">' . esc_html__( 'Tags:', 'al-qamara-lite' ) . '</span> ', ', ', '' ); ?>
                        </div>
                        <?php endif; ?>

                        <?php endif; // $alqamara_is_post ?>

                    </div><!-- /.page-body -->

                </article>

                <?php if ( $alqamara_is_post ) : ?>

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


                <!-- ===================== RELATED POSTS ===================== -->
                <?php
                $alqamara_nw_related      = null;
                $alqamara_nw_category_ids = wp_list_pluck( get_the_category(), 'term_id' );
                if ( $alqamara_nw_category_ids && get_theme_mod( 'alqamara_show_related', 1 ) ) {
                    $alqamara_nw_related = new WP_Query( [
                        'category__in'        => $alqamara_nw_category_ids,
                        'posts_per_page'      => 3,
                        'post__not_in'        => [ get_the_ID() ],
                        'orderby'             => 'rand',
                        'ignore_sticky_posts' => true,
                    ] );
                }

                if ( $alqamara_nw_related && $alqamara_nw_related->have_posts() ) :
                ?>
                <div class="single-related-wrap">

                    <h2 class="single-related-title">
                        <i class="fas fa-layer-group me-2 text-primary" aria-hidden="true"></i>
                        <?php esc_html_e( 'Related Posts', 'al-qamara-lite' ); ?>
                    </h2>

                    <div class="row g-4">
                        <?php $alqamara_nw_rel_i = 0; while ( $alqamara_nw_related->have_posts() ) : $alqamara_nw_related->the_post(); $alqamara_nw_rel_i++; ?>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( ( $alqamara_nw_rel_i - 1 ) * 100 ); ?>">
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

                <?php endif; // $alqamara_is_post ?>

                <?php endwhile; ?>

            </div>
        </div>
    </div>

</div>

<?php get_footer(); ?>
