<?php
/**
 * Template Name: Blank Canvas
 * Template Post Type: post, page
 *
 * No banner, no card, no sidebar. Content inside a standard container
 * with minimal top/bottom padding. Use for: Contact, Landing Pages, etc.
 *
 * @package alqamara
 */

get_header();
?>

<div class="container clearfix">
    <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
