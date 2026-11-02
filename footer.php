<?php
/**
 * Footer — 3 rows, brand (dark navy) background.
 *
 *   Row 1  Brand column (fixed col-lg-3) + up to 4 dynamic widget columns (col-lg-9).
 *          Active widget areas auto-adjust width:
 *            1 active → full width   |  2 active → col-sm-6
 *            3 active → col-sm-6 col-lg-4  |  4 active → col-sm-6 col-lg-3
 *          Manage via: Appearance → Widgets → Footer Column 1 / 2 / 3 / 4
 *   Row 2  Footer legal row — Appearance → Widgets → Footer Legal Row.
 *   Row 3  Copyright (Customizer → Footer Settings → Copyright Text).
 *
 * @package alqamara
 */
?>

    </main><!-- /#main-content -->

    <!-- ===================== FOOTER ===================== -->
    <footer class="alqamara-footer" role="contentinfo">

        <?php
        /* ── Row 1 visibility vars ─────────────────────────────────────── */
        $show_brand        = (bool) get_theme_mod( 'alqamara_footer_brand_show', true );
        $footer_show_social = (bool) get_theme_mod( 'alqamara_footer_brand_show_social', true );

        // Count active widget areas
        $footer_active = 0;
        foreach ( [ 'footer-col-1', 'footer-col-2', 'footer-col-3', 'footer-col-4' ] as $_fc ) {
            if ( is_active_sidebar( $_fc ) ) $footer_active++;
        }

        // Only render Row 1 when there is something to show
        $show_row1 = $show_brand || $footer_active > 0;
        ?>

        <!-- Row 1 — brand column + up to 4 dynamic widget columns -->
        <?php if ( $show_row1 ) : ?>
        <div class="alqamara-footer-top">
            <div class="container">
                <div class="row g-4 g-lg-5 align-items-start">

                    <?php if ( $show_brand ) : ?>
                    <!-- Brand column — col-lg-3 when brand shown -->
                    <div class="col-12 col-lg-3 alqamara-footer-brand">

                        <?php
                        /* ── Logo: footer-specific → site logo → site name ── */
                        $footer_logo_url = alqamara_get_customize_image_url( 'alqamara_footer_logo' );
                        if ( $footer_logo_url ) :
                        ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="alqamara-footer-logo">
                            <img src="<?php echo esc_url( $footer_logo_url ); ?>"
                                 class="footer-logo"
                                 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                                 loading="lazy">
                        </a>
                        <?php else : ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="alqamara-footer-logo">
                            <?php alqamara_the_logo( 'footer-logo' ); ?>
                        </a>
                        <?php endif; ?>

                        <?php $desc = get_theme_mod( 'alqamara_footer_desc', '' ); ?>
                        <?php if ( $desc ) : ?>
                        <p class="mt-3"><?php echo esc_html( $desc ); ?></p>
                        <?php endif; ?>

                        <?php if ( $footer_show_social ) : ?>
                        <div class="alqamara-footer-social d-flex flex-wrap gap-2 mt-3">
                            <?php
                            $alqamara_socials = [
                                'facebook'  => 'fa-facebook-f',
                                'instagram' => 'fa-instagram',
                                'youtube'   => 'fa-youtube',
                                'linkedin'  => 'fa-linkedin-in',
                                'twitter'   => 'fa-x-twitter',
                            ];
                            foreach ( $alqamara_socials as $platform => $icon ) :
                                $url = alqamara_social( $platform );
                                if ( ! $url ) continue;
                            ?>
                            <a href="<?php echo esc_url( $url ); ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               aria-label="<?php echo esc_attr( ucfirst( $platform ) ); ?>">
                                <i class="fab <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; // $footer_show_social ?>

                    </div><!-- /.col.alqamara-footer-brand -->
                    <?php endif; // $show_brand ?>

                    <!-- Dynamic widget zone -->
                    <?php
                    // Widget inner col class (per active count)
                    // (PHP 7.4 compatible — no match() expression, that is PHP 8.0+.)
                    $footer_col_classes = array(
                        0 => '',
                        1 => 'col-12',
                        2 => 'col-sm-6',
                        3 => 'col-sm-6 col-lg-4',
                    );
                    $footer_col_class = isset( $footer_col_classes[ $footer_active ] )
                        ? $footer_col_classes[ $footer_active ]
                        : 'col-sm-6 col-lg-3';
                    // Widget zone outer class: expand to full width when brand hidden
                    $widget_zone_class = $show_brand ? 'col-12 col-lg-9' : 'col-12';
                    ?>

                    <?php if ( $footer_active > 0 ) : ?>
                    <div class="<?php echo esc_attr( $widget_zone_class ); ?>">
                        <div class="row g-4">

                            <?php foreach ( [ 'footer-col-1', 'footer-col-2', 'footer-col-3', 'footer-col-4' ] as $i => $fc_id ) :
                                if ( ! is_active_sidebar( $fc_id ) ) continue;
                            ?>
                            <div class="<?php echo esc_attr( $footer_col_class ); ?> alqamara-footer-col">
                                <?php dynamic_sidebar( $fc_id ); ?>
                            </div>
                            <?php endforeach; ?>

                        </div><!-- /.row -->
                    </div><!-- /.widget-zone -->
                    <?php endif; ?>

                </div><!-- /.row -->
            </div><!-- /.container -->
        </div><!-- /.alqamara-footer-top -->
        <?php endif; // $show_row1 ?>


        <!-- Row 2 — legal row — Appearance → Widgets → Footer Legal Row -->
        <?php if ( is_active_sidebar( 'footer-legal' ) ) : ?>
        <div class="alqamara-footer-mid">
            <div class="container">
                <?php dynamic_sidebar( 'footer-legal' ); ?>
            </div>
        </div><!-- /.alqamara-footer-mid -->
        <?php endif; ?>


        <!-- Row 3 — copyright + designed by -->
        <div class="alqamara-footer-bottom">
            <div class="container">
                <div class="row align-items-center g-2">
                    <div class="col-md-6 text-center text-md-start">
                        <?php alqamara_copyright(); ?>
                    </div>
                    <div class="col-md-6 text-center text-md-end alqamara-designed">
                        <?php
                        // Default: the standard WordPress.org credit. A custom credit
                        // (text + optional link) can be entered in the Customizer.
                        $credit_text = get_theme_mod( 'alqamara_credit_text', '' );
                        $credit_url  = get_theme_mod( 'alqamara_credit_url', '' );
                        if ( $credit_text ) {
                            $credit_name = $credit_url
                                ? '<a href="' . esc_url( $credit_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $credit_text ) . '</a>'
                                : esc_html( $credit_text );
                            echo wp_kses_post(
                                sprintf(
                                    /* translators: %s: developer name with link */
                                    __( 'Developed by %s', 'al-qamara-lite' ),
                                    $credit_name
                                )
                            );
                        } else {
                            echo wp_kses_post(
                                sprintf(
                                    /* translators: %s: link to WordPress.org */
                                    __( 'Proudly powered by %s', 'al-qamara-lite' ),
                                    '<a href="' . esc_url( __( 'https://wordpress.org/', 'al-qamara-lite' ) ) . '">WordPress</a>'
                                )
                            );
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div><!-- /.alqamara-footer-bottom -->

    </footer>
    <!-- ===================== FOOTER END ===================== -->


    <!-- ===================== BACK TO TOP (+ scroll progress ring) ===================== -->
    <div class="alqamara-btt-wrap">
        <svg class="alqamara-btt-ring" width="56" height="56" viewBox="0 0 56 56" aria-hidden="true">
            <circle class="alqamara-btt-ring-track" cx="28" cy="28" r="25.5"></circle>
            <circle class="alqamara-btt-ring-progress" id="alqamara-btt-ring-progress" cx="28" cy="28" r="25.5"></circle>
        </svg>
        <a href="#"
           id="back-to-top"
           class="btn btn-primary rounded-circle p-0"
           aria-label="<?php esc_attr_e( 'Back to top', 'al-qamara-lite' ); ?>">
            <i class="bi bi-arrow-up fs-5" aria-hidden="true"></i>
        </a>
    </div>

<?php wp_footer(); ?>

</body>
</html>
