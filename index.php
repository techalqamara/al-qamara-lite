<?php get_header(); ?>

<!-- ===================== PAGE WRAPPER ===================== -->
<div class="blog-page-wrapper">

    <!-- ===================== MAIN CONTENT AREA ===================== -->
    <div class="container blog-container">
        <?php $show_index_sidebar = is_active_sidebar( 'sidebar-blog' ); ?>
        <div class="row g-5">


            <!-- ===================== POSTS COLUMN ===================== -->
            <div class="<?php echo $show_index_sidebar ? 'col-lg-8' : 'col-lg-10 mx-auto'; ?>">

                <h1 class="screen-reader-text"><?php echo esc_html( is_home() && ! is_front_page() ? single_post_title( '', false ) : get_bloginfo( 'name' ) ); ?></h1>

                <?php if ( have_posts() ) : ?>

                    <div class="blog-posts-grid">

                        <?php while ( have_posts() ) : the_post(); ?>

                        <!-- Post Card -->
                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-post-card' ); ?>>

                            <!-- Thumbnail -->
                            <?php if ( has_post_thumbnail() ) : ?>
                            <div class="blog-post-thumb">
                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                    <?php the_post_thumbnail( 'alqamara-card', [ 'class' => 'blog-thumb-img', 'loading' => 'lazy' ] ); ?>
                                </a>
                                <?php $category = get_the_category(); if ( $category ) : ?>
                                <span class="blog-post-cat">
                                    <?php echo esc_html( $category[0]->name ); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Post Body -->
                            <div class="blog-post-body">

                                <!-- Title -->
                                <h2 class="blog-post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>

                                <!-- Meta -->
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
                                    <span class="blog-meta-divider" aria-hidden="true">·</span>
                                    <span class="blog-meta-item">
                                        <i class="fas fa-comment" aria-hidden="true"></i>
                                        <?php
                                        /* translators: %s: number of comments */
                                        printf( esc_html( _n( '%s Comment', '%s Comments', get_comments_number(), 'al-qamara-lite' ) ), esc_html( number_format_i18n( get_comments_number() ) ) );
                                        ?>
                                    </span>
                                </div>

                                <!-- Excerpt -->
                                <div class="blog-post-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>

                                <!-- Read More -->
                                <a href="<?php the_permalink(); ?>" class="blog-read-more">
                                    <?php esc_html_e( 'Read More', 'al-qamara-lite' ); ?>
                                    <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                                </a>

                            </div><!-- /.blog-post-body -->

                        </article>
                        <!-- Post Card End -->

                        <?php endwhile; ?>

                    </div><!-- /.blog-posts-grid -->

                    <!-- Pagination -->
                    <div class="blog-pagination-wrap">
                        <?php alqamara_pagination(); ?>
                    </div>

                <?php else : ?>

                    <!-- No Posts Found -->
                    <div class="blog-no-posts">
                        <div class="blog-no-posts-icon">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </div>
                        <h3><?php esc_html_e( 'No Posts Found', 'al-qamara-lite' ); ?></h3>
                        <p><?php esc_html_e( 'Sorry, no posts matched your criteria. Try searching for something else.', 'al-qamara-lite' ); ?></p>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-custom mt-3">
                            <?php esc_html_e( 'Back to Home', 'al-qamara-lite' ); ?>
                        </a>
                    </div>

                <?php endif; ?>

            </div>
            <!-- ===================== POSTS COLUMN END ===================== -->


            <!-- ===================== SIDEBAR ===================== -->
            <?php if ( $show_index_sidebar ) : ?>
            <div class="col-lg-4 <?php echo esc_attr( 'left' === get_theme_mod( 'alqamara_sidebar_position', 'right' ) ? 'order-lg-first' : '' ); ?>">
                <aside class="blog-sidebar" role="complementary"
                       aria-label="<?php esc_attr_e( 'Blog Sidebar', 'al-qamara-lite' ); ?>">
                    <?php get_sidebar(); ?>
                </aside>
            </div>
            <?php endif; ?>
            <!-- ===================== SIDEBAR END ===================== -->


        </div><!-- /.row -->
    </div><!-- /.container -->
    <!-- ===================== MAIN CONTENT AREA END ===================== -->

</div>
<!-- ===================== PAGE WRAPPER END ===================== -->

<?php get_footer(); ?>