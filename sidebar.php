<?php
/**
 * Sidebar Template
 *
 * @package alqamara
 */
?>

<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>

    <?php dynamic_sidebar( 'sidebar-blog' ); ?>

<?php else : ?>

    <!-- Default widgets when sidebar is empty -->
    <div class="widget">
        <h5 class="widget-title">
            <?php esc_html_e( 'Search', 'al-qamara-lite' ); ?>
        </h5>
        <?php get_search_form(); ?>
    </div>

    <div class="widget">
        <h5 class="widget-title">
            <?php esc_html_e( 'Recent Posts', 'al-qamara-lite' ); ?>
        </h5>
        <ul>
            <?php
            $alqamara_recent = get_posts( array( 'numberposts' => 5 ) ); // phpcs:ignore WPThemeReview.CoreFunctionality.PostsPerPage.posts_per_page_numberposts -- Five recent posts only.
            foreach ( $alqamara_recent as $alqamara_recent_post ) : ?>
                <li>
                    <a href="<?php echo esc_url( get_permalink( $alqamara_recent_post->ID ) ); ?>">
                        <?php echo esc_html( get_the_title( $alqamara_recent_post ) ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="widget">
        <h5 class="widget-title">
            <?php esc_html_e( 'Categories', 'al-qamara-lite' ); ?>
        </h5>
        <ul>
            <?php wp_list_categories( [
                'orderby'    => 'name',
                'show_count' => true,
                'title_li'   => '',
            ] ); ?>
        </ul>
    </div>

<?php endif; ?>