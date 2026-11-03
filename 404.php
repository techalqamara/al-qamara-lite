<?php
/**
 * 404 Error Page Template
 *
 * @package alqamara
 */

get_header();
?>

<!-- ===================== PAGE WRAPPER ===================== -->
<div class="blog-page-wrapper">

    <!-- ===================== PAGE BANNER ===================== -->
    <?php
    $alqamara_banner_align = get_theme_mod( 'alqamara_banner_alignment', 'left' );
    if ( get_theme_mod( 'alqamara_banner_show_404', 1 ) ) :
    ?>
    <div class="blog-page-banner">
        <div class="container">
            <div class="blog-banner-inner<?php echo 'center' === $alqamara_banner_align ? ' text-center' : ''; ?>">

                <span class="blog-banner-label">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?php esc_html_e( '404 Error', 'al-qamara-lite' ); ?>
                </span>

                <h1 class="blog-banner-title">
                    <?php esc_html_e( 'Page Not Found', 'al-qamara-lite' ); ?>
                </h1>

                <p class="blog-banner-subtitle">
                    <?php esc_html_e( "Sorry, the page you're looking for can't be found.", 'al-qamara-lite' ); ?>
                </p>

            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- ===================== PAGE BANNER END ===================== -->


    <!-- ===================== 404 CONTENT ===================== -->
    <div class="container-xxl py-6" data-aos="fade-up" data-aos-delay="100">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">

                    <!-- Icon -->
                    <i class="bi bi-exclamation-triangle display-1 text-primary" aria-hidden="true"></i>

                    <!-- 404 Number -->
                    <p class="display-1">404</p>

                    <!-- Heading -->
                    <h2 class="mb-4">
                        <?php esc_html_e( 'Page Not Found', 'al-qamara-lite' ); ?>
                    </h2>

                    <!-- Message -->
                    <p class="mb-4">
                        <?php esc_html_e( "Sorry, the page you're looking for can't be found. Try the homepage or a search instead.", 'al-qamara-lite' ); ?>
                    </p>

                    <!-- Search Form -->
                    <div class="mb-4">
                        <?php get_search_form(); ?>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a class="btn btn-custom"
                           href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <i class="fas fa-home me-2" aria-hidden="true"></i>
                            <?php esc_html_e( 'Back to Home', 'al-qamara-lite' ); ?>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- ===================== 404 CONTENT END ===================== -->

</div>
<!-- ===================== PAGE WRAPPER END ===================== -->

<?php get_footer(); ?>