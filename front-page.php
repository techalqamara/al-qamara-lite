<?php
/**
 * Front Page Template — Al Qamara Lite
 *
 * Sections (in order):
 *   01. Hero
 *   02. Stats Counter
 *   03. About Us
 *   04. Features (Why Work With Us)
 *   05. Blog
 *   06. Newsletter
 *   07. CTA Banner
 *   08. Generic Row 1
 *   09. Generic Row 2
 *   10. Generic Row 3
 *
 * Pattern per section:
 *   a) Skip if show = 0  (Customizer toggle).
 *   b) If matching widget area is active → render it.
 *   c) Else → render Customizer built-in fallback.
 *
 * Helpers (inc/helpers.php):
 *   alqamara_section_bg_style( $prefix, $default_color )
 *   alqamara_col_class( $cols )
 *   alqamara_section_header( $title, $subtitle, $dark )
 *
 * Helpers (inc/customizer.php):
 *   alqamara_hero( $field )
 *   alqamara_about( $field )
 *   alqamara_section_mod( $section, $field, $default )
 *   alqamara_stat( $n, $field )
 *
 * @package alqamara
 */

defined( 'ABSPATH' ) || exit;

/*
 * Settings > Reading: when the front page shows "Your latest posts", show the
 * normal posts index. The section layout below is only used for a static front page.
 */
if ( 'posts' === get_option( 'show_on_front' ) ) {
	get_template_part( 'index' );
	return;
}

get_header();
?>

<!-- =========================================================================
     01 — HERO
     Light cream background, decorative circles, dark navy text + buttons.
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_hero_show', 1 ) ) : ?>

    <?php
        $hero_align = alqamara_hero( 'alignment' ) ?: 'center';
        $txt_class  = 'center' === $hero_align ? 'text-center' : ( 'right' === $hero_align ? 'text-end' : 'text-start' );
        $btn_flex   = 'center' === $hero_align ? 'justify-content-center' : ( 'right' === $hero_align ? 'justify-content-end' : '' );
        $btn1_text  = alqamara_hero( 'btn1_text' );
        $btn2_text  = alqamara_hero( 'btn2_text' );
        // Decorative primary/accent-tinted circles are designed for the
        // plain gradient background — on top of an uploaded photo they
        // show up as a hazy, out-of-place color patch bleeding into the
        // image (reported 2026-08-18: the tint and the photo looked
        // blended together). Only render them when
        // there's no background image, i.e. exactly when the gradient
        // they were designed for is the thing actually showing.
        $hero_has_bg_image = (bool) alqamara_get_customize_image_url( 'alqamara_hero_bg_image' );
    ?>
    <section class="hero-section">
        <?php if ( ! $hero_has_bg_image ) : ?>
        <div class="hero-circle hero-circle-tr"></div>
        <div class="hero-circle hero-circle-bl"></div>
        <?php endif; ?>

        <div class="container">
            <div class="hero-content <?php echo esc_attr( $txt_class ); ?>">

                <?php if ( is_active_sidebar( 'hero-widget' ) ) : ?>
                    <?php dynamic_sidebar( 'hero-widget' ); ?>
                <?php else : ?>

                    <?php $hero_title = alqamara_hero( 'title' ); ?>
                    <?php if ( $hero_title ) : ?>
                    <h1 class="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
                    <?php endif; ?>

                    <?php $hero_subtitle = alqamara_hero( 'subtitle' ); ?>
                    <?php if ( $hero_subtitle ) : ?>
                    <p class="hero-subtitle"><?php echo wp_kses_post( $hero_subtitle ); ?></p>
                    <?php endif; ?>

                    <?php if ( $btn1_text || $btn2_text ) : ?>
                    <div class="hero-actions d-flex flex-wrap gap-3 <?php echo esc_attr( $btn_flex ); ?>">
                        <?php if ( $btn1_text ) : ?>
                        <a href="<?php echo esc_url( alqamara_hero( 'btn1_url' ) ); ?>" class="btn btn-hero-primary">
                            <?php echo esc_html( $btn1_text ); ?>
                            <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ( $btn2_text ) : ?>
                        <a href="<?php echo esc_url( alqamara_hero( 'btn2_url' ) ); ?>" class="btn btn-hero-secondary">
                            <?php echo esc_html( $btn2_text ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                <?php endif; // hero-widget ?>

            </div>
        </div>
    </section>

<?php endif; // hero show ?>

<?php
/* Content of the static front page itself (Pages > Edit), shown below the hero when it has any. */
while ( have_posts() ) :
	the_post();
	if ( '' !== trim( get_the_content() ) ) :
		?>
<section class="alqamara-front-content py-5">
	<div class="container page-entry-content">
		<?php the_content(); ?>
	</div>
</section>
		<?php
	endif;
endwhile;
?>


<!-- =========================================================================
     02 — STATS COUNTER
     No widget area — pure Customizer (4 stat items). Dark background.
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_stats_show', 0 ) ) :
    $stats_title  = alqamara_section_mod( 'stats', 'title',   __( 'Our Achievements', 'al-qamara-lite' ) );
    $stats_eyebrow = alqamara_section_mod( 'stats', 'eyebrow', __( 'Track Record',     'al-qamara-lite' ) );
    $stats_bg    = alqamara_section_bg_style( 'alqamara_stats', '#0a0f2c' );
    $stats_dark  = alqamara_section_dark( 'alqamara_stats', '#0a0f2c', true );
?>
<section class="alqamara-section alqamara-stats-section" style="<?php echo esc_attr( $stats_bg ); ?>">
    <?php if ( get_theme_mod( 'alqamara_stats_bg_image', '' ) ) : ?>
    <div class="alqamara-bg-overlay"></div>
    <?php endif; ?>
    <div class="container position-relative" style="z-index:1;">
        <?php alqamara_section_header( $stats_title, '', $stats_dark, $stats_eyebrow ); ?>
        <div class="row g-4 justify-content-center">
            <?php for ( $i = 1; $i <= 4; $i++ ) :
                $raw_num = alqamara_stat( $i, 'number' );
                $target  = preg_replace( '/[^0-9]/', '', (string) $raw_num );
                $suffix  = preg_replace( '/^[0-9,]+/', '', (string) $raw_num );
                $label   = alqamara_stat( $i, 'label' );
                $icon    = alqamara_stat( $i, 'icon' );
                if ( ! $raw_num && ! $label ) { continue; }
            ?>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( ( $i - 1 ) * 100 ); ?>">
                <div class="alqamara-stat-item text-center">
                    <?php if ( $icon ) : ?>
                    <div class="alqamara-stat-icon mb-3">
                        <i class="<?php echo esc_attr( alqamara_icon_class( $icon, 'bi' ) ); ?>" aria-hidden="true"></i>
                    </div>
                    <?php endif; ?>
                    <div class="alqamara-stat-number"
                         data-target="<?php echo esc_attr( $target ); ?>"
                         data-suffix="<?php echo esc_attr( $suffix ); ?>">
                        <?php echo esc_html( $raw_num ); ?>
                    </div>
                    <?php if ( $label ) : ?>
                    <div class="alqamara-stat-label"><?php echo esc_html( $label ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
<?php endif; // stats show ?>


<!-- =========================================================================
     03 — ABOUT US
     Widget: about-widget | Fallback: Customizer about fields
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_about_show', 0 ) ) :
    $about_bg = alqamara_section_bg_style( 'alqamara_about', '#fdf9f0' );
?>

    <?php if ( is_active_sidebar( 'about-widget' ) ) : ?>
    <section class="alqamara-section alqamara-about-section" style="<?php echo esc_attr( $about_bg ); ?>">
        <div class="container">
            <?php dynamic_sidebar( 'about-widget' ); ?>
        </div>
    </section>

    <?php else :
        $about_image    = alqamara_about( 'image' );
        $about_eyebrow  = alqamara_about( 'eyebrow' );
        $about_heading  = alqamara_about( 'heading' );
        $about_bio      = alqamara_about( 'bio' );
        $about_btn_text = alqamara_about( 'btn_text' );
        $about_btn_url  = alqamara_about( 'btn_url' );
    ?>
    <section class="alqamara-section alqamara-about-section" style="<?php echo esc_attr( $about_bg ); ?>">
        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">

                <div class="<?php echo $about_image ? 'col-lg-6 order-2 order-lg-1' : 'col-lg-8 mx-auto text-center'; ?>" data-aos="fade-right">

                    <?php if ( $about_eyebrow ) : ?>
                    <span class="alqamara-eyebrow"><?php echo esc_html( $about_eyebrow ); ?></span>
                    <?php endif; ?>

                    <?php if ( $about_heading ) : ?>
                    <h2 class="fw-bold alqamara-section-heading"><?php echo esc_html( $about_heading ); ?></h2>
                    <?php endif; ?>

                    <?php if ( $about_bio ) : ?>
                    <p class="text-muted mb-4"><?php echo wp_kses_post( $about_bio ); ?></p>
                    <?php endif; ?>

                    <?php if ( $about_btn_text ) : ?>
                    <a href="<?php echo esc_url( $about_btn_url ); ?>" class="btn btn-custom px-4 py-2">
                        <?php echo esc_html( $about_btn_text ); ?>
                        <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>

                </div>

                <?php if ( $about_image ) : ?>
                <div class="col-lg-6 text-center order-1 order-lg-2" data-aos="fade-left">
                    <img src="<?php echo esc_url( $about_image ); ?>"
                         alt="<?php esc_attr_e( 'About Us', 'al-qamara-lite' ); ?>"
                         class="alqamara-about-img img-fluid rounded-3 shadow">
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
    <?php endif; // about widget check ?>

<?php endif; // about show ?>


<!-- =========================================================================
     05 — FEATURES  (Why Work With Us)
     No widget area — pure Customizer (8 items: icon + title + description).
     Items with an empty title are automatically skipped.
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_features_show', 0 ) ) :
    $feat_bg      = alqamara_section_bg_style( 'alqamara_features', '#fdf9f0' );
    $feat_dark    = alqamara_section_dark( 'alqamara_features', '#fdf9f0', false );
    $feat_ttl     = alqamara_section_mod( 'features', 'title',   __( 'Why Work With Us', 'al-qamara-lite' ) );
    $feat_sub     = alqamara_section_mod( 'features', 'subtitle', '' );
    $feat_eyebrow = alqamara_section_mod( 'features', 'eyebrow', __( 'Why Choose Us',    'al-qamara-lite' ) );

    // Build items list — each field reads its OWN real default (same
    // defaults registered in inc/customizer.php's $feature_defaults), not
    // an empty string. This matters: get_theme_mod()'s 2nd argument is only
    // used when the field was NEVER saved at all — passing '' there meant
    // an item whose title was never explicitly retyped (even though its
    // Icon WAS just changed via the Browse Icons picker) looked "empty" and
    // the ENTIRE section silently fell back to a separate hardcoded array,
    // discarding the real icon change along with everything else. Using the
    // real per-item default here (matching how alqamara_stat()/alqamara_
    // about() already do it correctly) fixes that — an item now only counts
    // as "empty" when its title was explicitly cleared and saved, which is
    // what the "auto-skip empty title" behavior below was actually meant to
    // detect.
    $feat_defaults = [
        1 => [ 'icon' => 'fa-code',        'title' => __( 'Clean Code',            'al-qamara-lite' ), 'desc' => __( 'Well-structured, readable code that is easy to maintain and scale.',                       'al-qamara-lite' ) ],
        2 => [ 'icon' => 'fa-bolt',        'title' => __( 'Fast Delivery',          'al-qamara-lite' ), 'desc' => __( 'Projects delivered on time, without cutting corners on quality.',                         'al-qamara-lite' ) ],
        3 => [ 'icon' => 'fa-comments',    'title' => __( 'Clear Communication',    'al-qamara-lite' ), 'desc' => __( 'Regular updates and honest feedback throughout every project.',                           'al-qamara-lite' ) ],
        4 => [ 'icon' => 'fa-headset',     'title' => __( 'Ongoing Support',        'al-qamara-lite' ), 'desc' => __( 'Available after launch to fix issues, add features, and keep things running smoothly.', 'al-qamara-lite' ) ],
        5 => [ 'icon' => 'fa-shield-alt',  'title' => __( 'Secure & Reliable',      'al-qamara-lite' ), 'desc' => __( 'Built with security best practices and thoroughly tested for stability.',                'al-qamara-lite' ) ],
        6 => [ 'icon' => 'fa-chart-line',  'title' => __( 'Results-Driven',         'al-qamara-lite' ), 'desc' => __( 'We measure success by the growth and real impact we bring to your business.',           'al-qamara-lite' ) ],
        7 => [ 'icon' => 'fa-paint-brush', 'title' => __( 'Creative Design',        'al-qamara-lite' ), 'desc' => __( 'Thoughtful, modern designs that represent your brand with confidence.',                 'al-qamara-lite' ) ],
        8 => [ 'icon' => 'fa-handshake',   'title' => __( 'Long-Term Partnership',  'al-qamara-lite' ), 'desc' => __( 'We build lasting relationships — your success is our success.',                         'al-qamara-lite' ) ],
    ];

    $feat_items = [];
    for ( $i = 1; $i <= 8; $i++ ) :
        $d       = $feat_defaults[ $i ];
        $f_icon  = get_theme_mod( "alqamara_feature_{$i}_icon",  $d['icon']  );
        $f_title = get_theme_mod( "alqamara_feature_{$i}_title", $d['title'] );
        $f_desc  = get_theme_mod( "alqamara_feature_{$i}_desc",  $d['desc']  );
        if ( $f_title ) :
            $feat_items[] = [ 'icon' => $f_icon, 'title' => $f_title, 'desc' => $f_desc ];
        endif;
    endfor;
?>
<section class="alqamara-section alqamara-features-section" style="<?php echo esc_attr( $feat_bg ); ?>">
    <div class="container">
        <?php alqamara_section_header( $feat_ttl, $feat_sub, $feat_dark, $feat_eyebrow ); ?>
        <div class="row g-4">
            <?php foreach ( $feat_items as $f_idx => $feat ) : ?>
            <div class="col-lg-3 col-sm-6" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( min( $f_idx * 100, 400 ) ); ?>">
                <div class="alqamara-feature-card text-center p-4 h-100">
                    <?php if ( $feat['icon'] ) : ?>
                    <div class="alqamara-feature-icon-wrap mb-3">
                        <i class="<?php echo esc_attr( alqamara_icon_class( $feat['icon'], 'fas' ) ); ?>" aria-hidden="true"></i>
                    </div>
                    <?php endif; ?>
                    <h3 class="alqamara-feature-title"><?php echo esc_html( $feat['title'] ); ?></h3>
                    <?php if ( $feat['desc'] ) : ?>
                    <p class="alqamara-feature-desc mb-0"><?php echo esc_html( $feat['desc'] ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; // features show ?>


<!-- =========================================================================
     09 — BLOG
     Widget: blog-widget | Fallback: latest posts + Customizer settings
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_blog_show', 1 ) ) :
    $blog_bg  = alqamara_section_bg_style( 'alqamara_blog', '#fdf9f0' );
    $blog_dark = alqamara_section_dark( 'alqamara_blog', '#fdf9f0', false );
    $blog_ttl     = alqamara_section_mod( 'blog', 'title',   __( 'Latest Articles', 'al-qamara-lite' ) );
    $blog_sub     = alqamara_section_mod( 'blog', 'subtitle', '' );
    $blog_eyebrow = alqamara_section_mod( 'blog', 'eyebrow', __( 'From the Blog',  'al-qamara-lite' ) );
?>

    <?php if ( is_active_sidebar( 'blog-widget' ) ) : ?>
    <section class="alqamara-section alqamara-blog-section" style="<?php echo esc_attr( $blog_bg ); ?>">
        <div class="container">
            <?php dynamic_sidebar( 'blog-widget' ); ?>
        </div>
    </section>

    <?php else :
        $blog_count = (int) alqamara_section_mod( 'blog', 'count', 3 );
        $blog_cols  = alqamara_col_class( alqamara_section_mod( 'blog', 'columns', '3' ) );
        $blog_query = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => $blog_count,
            'post_status'    => 'publish',
        ] );
        if ( $blog_query->have_posts() ) :
    ?>
    <section class="alqamara-section alqamara-blog-section" style="<?php echo esc_attr( $blog_bg ); ?>">
        <div class="container">
            <?php alqamara_section_header( $blog_ttl, $blog_sub, $blog_dark, $blog_eyebrow ); ?>
            <div class="row g-4">
                <?php $alqamara_i = 0; while ( $blog_query->have_posts() ) : $blog_query->the_post();
                    $cats = get_the_category();
                    $alqamara_i++;
                ?>
                <div class="<?php echo esc_attr( $blog_cols ); ?>" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( ( $alqamara_i - 1 ) * 100 ); ?>">
                    <div class="alqamara-blog-card h-100 rounded-3 overflow-hidden d-flex flex-column">
                        <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="alqamara-blog-img-wrap d-block">
                            <?php the_post_thumbnail( 'alqamara-card', [ 'class' => 'alqamara-blog-img w-100' ] ); ?>
                        </a>
                        <?php endif; ?>
                        <div class="alqamara-blog-body p-4 d-flex flex-column flex-grow-1">
                            <div class="alqamara-blog-meta d-flex flex-wrap gap-2 mb-2">
                                <span class="alqamara-blog-date">
                                    <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i>
                                    <?php echo esc_html( get_the_date() ); ?>
                                </span>
                                <?php if ( $cats ) : ?>
                                <span class="alqamara-blog-cat">
                                    <i class="fas fa-folder me-1" aria-hidden="true"></i>
                                    <?php echo esc_html( $cats[0]->name ); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="alqamara-blog-title h5 mb-2">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <?php if ( get_the_excerpt() ) : ?>
                            <div class="alqamara-blog-excerpt small mb-3 flex-grow-1"><?php the_excerpt(); ?></div>
                            <?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="alqamara-blog-link mt-auto">
                                <?php esc_html_e( 'Read More', 'al-qamara-lite' ); ?>
                                <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <?php $blog_view_all = alqamara_section_mod( 'blog', 'view_all_text', __( 'View All Articles', 'al-qamara-lite' ) ); ?>
            <?php if ( $blog_view_all && wp_count_posts( 'post' )->publish > $blog_count ) : ?>
            <?php
            // Blog URL — 4-level fallback (never falls to uncategorized)
            // 1. Settings → Reading → Posts page
            $blog_url = '';
            $page_for_posts = (int) get_option( 'page_for_posts' );
            if ( $page_for_posts ) {
                $blog_url = get_permalink( $page_for_posts );
            }
            // 2. A published page with slug 'blog'
            if ( ! $blog_url ) {
                $blog_page = get_page_by_path( 'blog' );
                if ( $blog_page && 'publish' === $blog_page->post_status ) {
                    $blog_url = get_permalink( $blog_page->ID );
                }
            }
            // 3. Homepage shows latest posts (no static front page) → home URL IS the blog
            if ( ! $blog_url && ! get_option( 'page_on_front' ) ) {
                $blog_url = home_url( '/' );
            }
            // 4. Last resort: home URL (always a valid link)
            if ( ! $blog_url ) {
                $blog_url = home_url( '/' );
            }
            ?>
            <div class="text-center mt-5" data-aos="fade-up">
                <a href="<?php echo esc_url( $blog_url ); ?>" class="btn btn-custom px-4">
                    <?php echo esc_html( $blog_view_all ); ?>
                    <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                </a>
            </div>
            <?php endif; ?>

        </div>
    </section>
        <?php endif; // blog_query have_posts ?>
    <?php endif; // blog widget check ?>

<?php endif; // blog show ?>


<!-- =========================================================================
     10 — NEWSLETTER
     Widget-only. Hidden if widget area is empty.
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_newsletter_show', 1 ) && is_active_sidebar( 'newsletter-widget' ) ) :
    $nl_bg      = alqamara_section_bg_style( 'alqamara_newsletter', '#1e2c5f' );
    $nl_dark    = alqamara_section_dark( 'alqamara_newsletter', '#1e2c5f', true );
    $nl_ttl     = alqamara_section_mod( 'newsletter', 'title',   __( 'Stay in the Loop', 'al-qamara-lite' ) );
    $nl_sub     = alqamara_section_mod( 'newsletter', 'subtitle', '' );
    $nl_eyebrow = alqamara_section_mod( 'newsletter', 'eyebrow', __( 'Stay Connected', 'al-qamara-lite' ) );
    $nl_has_img = (bool) get_theme_mod( 'alqamara_newsletter_bg_image', '' );
?>
<section class="alqamara-section alqamara-newsletter-section" style="<?php echo esc_attr( $nl_bg ); ?>">
    <?php if ( $nl_has_img ) : ?><div class="alqamara-bg-overlay"></div><?php endif; ?>
    <div class="container position-relative" data-aos="fade-up">
        <?php alqamara_section_header( $nl_ttl, $nl_sub, $nl_dark, $nl_eyebrow ); ?>
        <?php dynamic_sidebar( 'newsletter-widget' ); ?>
    </div>
</section>
<?php endif; // newsletter ?>


<!-- =========================================================================
     11 — CTA BANNER
     No widget area — pure Customizer.
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_cta_show', 0 ) ) :
    $cta_ttl     = alqamara_section_mod( 'cta', 'title', __( 'Ready to Work Together?', 'al-qamara-lite' ) );
    $cta_sub     = alqamara_section_mod( 'cta', 'subtitle', '' );
    $cta_btn1    = alqamara_section_mod( 'cta', 'btn1_text', __( 'Get In Touch', 'al-qamara-lite' ) );
    $cta_url1    = alqamara_section_mod( 'cta', 'btn1_url', home_url( '/' ) );
    $cta_btn2    = alqamara_section_mod( 'cta', 'btn2_text', __( 'Learn More', 'al-qamara-lite' ) );
    $cta_url2    = alqamara_section_mod( 'cta', 'btn2_url', home_url( '/' ) );
    $cta_bg      = alqamara_section_bg_style( 'alqamara_cta', '#0a0f2c' );
    $cta_has_img = (bool) get_theme_mod( 'alqamara_cta_bg_image', '' );
?>
<section class="alqamara-section alqamara-cta-banner" style="<?php echo esc_attr( $cta_bg ); ?>">
    <?php if ( $cta_has_img ) : ?><div class="alqamara-bg-overlay"></div><?php endif; ?>
    <div class="container position-relative text-center">
        <?php if ( $cta_ttl ) : ?>
        <h2 class="alqamara-cta-title text-white fw-bold mb-3" data-aos="fade-up" data-aos-offset="-150"><?php echo esc_html( $cta_ttl ); ?></h2>
        <?php endif; ?>
        <?php if ( $cta_sub ) : ?>
        <p class="alqamara-cta-subtitle mb-4" data-aos="fade-up" data-aos-offset="-150" data-aos-delay="100"><?php echo esc_html( $cta_sub ); ?></p>
        <?php endif; ?>
        <?php if ( $cta_btn1 || $cta_btn2 ) : ?>
        <div class="alqamara-cta-buttons d-flex flex-wrap gap-3 justify-content-center" data-aos="fade-up" data-aos-offset="-150" data-aos-delay="200">
            <?php if ( $cta_btn1 ) : ?>
            <a href="<?php echo esc_url( $cta_url1 ); ?>" class="btn btn-custom px-5 py-2 fw-semibold">
                <?php echo esc_html( $cta_btn1 ); ?>
            </a>
            <?php endif; ?>
            <?php if ( $cta_btn2 ) : ?>
            <a href="<?php echo esc_url( $cta_url2 ); ?>" class="btn btn-outline-custom px-5 py-2 fw-semibold">
                <?php echo esc_html( $cta_btn2 ); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; // cta show ?>


<!-- =========================================================================
     12 — GENERIC ROW 1
     Widget: generic-row-1
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_row1_show', 1 ) && is_active_sidebar( 'generic-row-1' ) ) :
    $r1_bg  = alqamara_section_bg_style( 'alqamara_row1', '#fdf9f0' );
    $r1_dark = alqamara_section_dark( 'alqamara_row1', '#fdf9f0', false );
    $r1_ttl = alqamara_section_mod( 'row1', 'title', '' );
?>
<section class="alqamara-section alqamara-row-section alqamara-row-1" style="<?php echo esc_attr( $r1_bg ); ?>">
    <div class="container">
        <?php alqamara_section_header( $r1_ttl, '', $r1_dark ); ?>
        <?php dynamic_sidebar( 'generic-row-1' ); ?>
    </div>
</section>
<?php endif; // row 1 ?>


<!-- =========================================================================
     13 — GENERIC ROW 2
     Widget: generic-row-2
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_row2_show', 1 ) && is_active_sidebar( 'generic-row-2' ) ) :
    $r2_bg  = alqamara_section_bg_style( 'alqamara_row2', '#ffffff' );
    $r2_dark = alqamara_section_dark( 'alqamara_row2', '#ffffff', false );
    $r2_ttl = alqamara_section_mod( 'row2', 'title', '' );
?>
<section class="alqamara-section alqamara-row-section alqamara-row-2" style="<?php echo esc_attr( $r2_bg ); ?>">
    <div class="container">
        <?php alqamara_section_header( $r2_ttl, '', $r2_dark ); ?>
        <?php dynamic_sidebar( 'generic-row-2' ); ?>
    </div>
</section>
<?php endif; // row 2 ?>


<!-- =========================================================================
     14 — GENERIC ROW 3  (absolute last section before footer)
     Widget: generic-row-3
========================================================================== -->
<?php if ( get_theme_mod( 'alqamara_row3_show', 1 ) && is_active_sidebar( 'generic-row-3' ) ) :
    $r3_bg  = alqamara_section_bg_style( 'alqamara_row3', '#fdf9f0' );
    $r3_dark = alqamara_section_dark( 'alqamara_row3', '#fdf9f0', false );
    $r3_ttl = alqamara_section_mod( 'row3', 'title', '' );
?>
<section class="alqamara-section alqamara-row-section alqamara-row-3" style="<?php echo esc_attr( $r3_bg ); ?>">
    <div class="container">
        <?php alqamara_section_header( $r3_ttl, '', $r3_dark ); ?>
        <?php dynamic_sidebar( 'generic-row-3' ); ?>
    </div>
</section>
<?php endif; // row 3 ?>

<?php get_footer(); ?>
