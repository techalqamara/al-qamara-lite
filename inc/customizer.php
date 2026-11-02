<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alqamara_customizer' ) ) :
function alqamara_customizer( $wp_customize ) {

    /* =========================================================================
       SITE IDENTITY — extra logo for the transparent (hero) header state
       Lives in WordPress core's own built-in `title_tagline` section, right
       next to the Site Logo control (which core adds via
       add_theme_support('custom-logo') in functions.php) — not a separate
       section, so both logo fields sit together in the same place admins
       already know. Only consumed when Header Layout = Single Row AND the
       homepage hero is showing a transparent header (see header.php +
       alqamara_the_logo() in inc/helpers.php). Falls back to the
       regular Site Logo everywhere else — inner pages, Two Rows layout, or
       simply when left empty here — fully optional and backward compatible.
    ========================================================================= */
    $wp_customize->add_setting( 'alqamara_logo_transparent', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_logo_transparent', [
        'label'       => esc_html__( 'Logo — Transparent Header (Home Page Hero)', 'al-qamara-lite' ),
        'description' => esc_html__( 'Optional. Shown only on the home page while the header is still transparent over the hero image, and only when Header Layout = Single Row. Switches to the Site Logo above once the visitor scrolls, on every other page, and in Two Rows layout. Leave empty to always use the Site Logo above. Recommended size: 220×65px transparent PNG.', 'al-qamara-lite' ),
        'section'     => 'title_tagline',
        'priority'    => 9,
    ] ) );


    /* =========================================================================
       HOMEPAGE SETTINGS PANEL
    ========================================================================= */

    $wp_customize->add_panel( 'alqamara_homepage_panel', [
        'title'    => esc_html__( 'Front Page Settings', 'al-qamara-lite' ),
        'priority' => 30,
    ] );

    // Hero Section
    $wp_customize->add_section( 'alqamara_hero', [
        'title'    => esc_html__( 'Hero Section', 'al-qamara-lite' ),
        'panel'    => 'alqamara_homepage_panel',
        'priority' => 10,
    ] );

    // Hero visibility
    $wp_customize->add_setting( 'alqamara_hero_show', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_hero_show', [
        'label'   => esc_html__( 'Show Hero Section', 'al-qamara-lite' ),
        'section' => 'alqamara_hero',
        'type'    => 'checkbox',
    ] );

    $hero_fields = [
        'alqamara_hero_title'     => [
            'label'   => esc_html__( 'Hero Title', 'al-qamara-lite' ),
            'type'    => 'text',
            'default' => __( 'Your Growth Is Our Goal, Not Just Your Project', 'al-qamara-lite' ),
        ],
        'alqamara_hero_subtitle'  => [
            'label'   => esc_html__( 'Hero Subtitle', 'al-qamara-lite' ),
            'type'    => 'textarea',
            'default' => __( 'Every business deserves honest digital support. We work with purpose — helping you build, grow, and sustain your online presence with integrity.', 'al-qamara-lite' ),
        ],
        'alqamara_hero_btn1_text' => [
            'label'   => esc_html__( 'Button 1 Text', 'al-qamara-lite' ),
            'type'    => 'text',
            'default' => __( 'Learn More', 'al-qamara-lite' ),
        ],
        'alqamara_hero_btn1_url'  => [
            'label'       => esc_html__( 'Button 1 URL', 'al-qamara-lite' ),
            'type'        => 'url',
            /* home_url(), not a bare relative string — a root-relative path
               resolves against the DOMAIN ROOT in the browser, which is wrong
               for any install that isn't at its domain's root (e.g. a
               subdirectory install). See the matching fix in alqamara_hero()
               below. */
            'default'     => home_url( '/' ),
            'description' => esc_html__( 'Paste the full page URL (e.g. https://yourdomain.com/about/) copied from your browser\'s address bar — not just a page name.', 'al-qamara-lite' ),
        ],
        'alqamara_hero_btn2_text' => [
            'label'   => esc_html__( 'Button 2 Text', 'al-qamara-lite' ),
            'type'    => 'text',
            'default' => __( 'Get in Touch', 'al-qamara-lite' ),
        ],
        'alqamara_hero_btn2_url'  => [
            'label'       => esc_html__( 'Button 2 URL', 'al-qamara-lite' ),
            'type'        => 'url',
            'default'     => home_url( '/' ),
            'description' => esc_html__( 'Paste the full page URL (e.g. https://yourdomain.com/contact/) copied from your browser\'s address bar — not just a page name.', 'al-qamara-lite' ),
        ],
    ];

    foreach ( $hero_fields as $id => $args ) {
        $wp_customize->add_setting( $id, [
            'default'           => $args['default'],
            'sanitize_callback' => ( 'url' === $args['type'] ) ? 'esc_url_raw' : 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'       => $args['label'],
            'description' => isset( $args['description'] ) ? $args['description'] : '',
            'section'     => 'alqamara_hero',
            'type'        => $args['type'],
        ] );
    }

    // Hero background image
    $wp_customize->add_setting( 'alqamara_hero_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_hero_bg_image', [
        'label'       => esc_html__( 'Hero Background Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'Replaces the default color background. This image always fills the section edge-to-edge and crops to fit — there is no single "perfect" pixel size for every screen. Recommended size: 1920×950px, wide/landscape, main subject centered. Use Image Position below if the important part gets cropped off on a particular device.', 'al-qamara-lite' ),
        'section'     => 'alqamara_hero',
    ] ) );

    // Background image position — mirrors alqamara_banner_bg_position exactly.
    // Hero didn't have this control before (2026-08-18: user reported the
    // uploaded photo only shows its plain/empty side on narrow mobile
    // viewports, because background-size:cover + a fixed center-center
    // position crops symmetrically from both edges — if the photo's actual
    // subject isn't dead-center in the source file, a narrow crop cuts the
    // subject out and leaves the empty background visible instead). Letting
    // the user choose the crop anchor per-image fixes this without needing
    // a differently-cropped image for every upload.
    $wp_customize->add_setting( 'alqamara_hero_bg_position', [
        'default'           => 'center center',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_hero_bg_position', [
        'label'       => esc_html__( 'Hero Image Position', 'al-qamara-lite' ),
        'description' => esc_html__( 'If the main subject gets cropped out on some screens, change which part of the image stays in view.', 'al-qamara-lite' ),
        'section'     => 'alqamara_hero',
        'type'        => 'select',
        'choices'     => [
            'center center' => esc_html__( 'Center Center', 'al-qamara-lite' ),
            'center top'    => esc_html__( 'Center Top',    'al-qamara-lite' ),
            'center bottom' => esc_html__( 'Center Bottom', 'al-qamara-lite' ),
            'left center'   => esc_html__( 'Left Center',   'al-qamara-lite' ),
            'right center'  => esc_html__( 'Right Center',  'al-qamara-lite' ),
        ],
    ] );

    // Dark overlay on the Hero image — same control pair, same defaults
    // (on, 40%) and same purpose as the Homepage Slider's own "Dark Overlay
    // on Slide Images" / "Overlay Opacity (%)": keeps the Hero's title/
    // subtitle/button readable regardless of the uploaded photo. Only has
    // an effect when a Hero Background Image is actually set.
    $wp_customize->add_setting( 'alqamara_hero_overlay', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_hero_overlay', [
        'label'       => esc_html__( 'Dark Overlay on Hero Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'Keeps the Hero text readable regardless of the photo. Only applies when a Hero Background Image is set. Optional — turn off for a photo that doesn\'t need it.', 'al-qamara-lite' ),
        'section'     => 'alqamara_hero',
        'type'        => 'checkbox',
    ] );
    $wp_customize->add_setting( 'alqamara_hero_overlay_opacity', [
        'default'           => 40,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_hero_overlay_opacity', [
        'label'       => esc_html__( 'Overlay Opacity (%)', 'al-qamara-lite' ),
        'section'     => 'alqamara_hero',
        'type'        => 'number',
        'input_attrs' => [ 'min' => 0, 'max' => 90, 'step' => 5 ],
    ] );

    // Content alignment
    $wp_customize->add_setting( 'alqamara_hero_alignment', [
        'default'           => 'center',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_hero_alignment', [
        'label'   => esc_html__( 'Content Alignment', 'al-qamara-lite' ),
        'section' => 'alqamara_hero',
        'type'    => 'select',
        'choices' => [
            'left'   => esc_html__( 'Left',   'al-qamara-lite' ),
            'center' => esc_html__( 'Center', 'al-qamara-lite' ),
            'right'  => esc_html__( 'Right',  'al-qamara-lite' ),
        ],
    ] );

    // Title / subtitle text color — dedicated color pickers rather than
    // reusing the header's auto-contrast (background-lightness) logic:
    // that trick only works against a KNOWN solid color, and Hero can have
    // an uploaded background IMAGE instead (Hero Background Image above) —
    // there's no reliable way to compute one "correct" ink color from an
    // arbitrary photo's overall brightness. Two SEPARATE controls (not one
    // shared color) so title and subtitle can be two different shades —
    // e.g. a strong dark title + a softer gray subtitle — instead of both
    // being forced to the exact same color. Left empty (default) → the
    // existing hardcoded navy title / dark-gray subtitle colors are kept
    // exactly as before, zero-risk for existing installs.
    $wp_customize->add_setting( 'alqamara_hero_title_color', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_hero_title_color', [
        'label'       => esc_html__( 'Title Text Color', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty for the default dark navy. Set this if the Hero Background Image makes the default text hard to read.', 'al-qamara-lite' ),
        'section'     => 'alqamara_hero',
    ] ) );
    $wp_customize->add_setting( 'alqamara_hero_subtitle_color', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_hero_subtitle_color', [
        'label'       => esc_html__( 'Subtitle Text Color', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty for the default gray.', 'al-qamara-lite' ),
        'section'     => 'alqamara_hero',
    ] ) );

    // Hero Header Text Color — a THIRD, separate control from the two
    // above: those two color the Hero's own heading/subheading text; this
    // one colors the HEADER (menu items, WooCommerce icons, and the
    // outline "btn-custom" navbar button) while it sits transparently on
    // top of the Hero, before the visitor scrolls. Only applies in that
    // exact state — once scrolled, or on any page without a transparent
    // hero header, the existing "Header Background Color" auto-contrast
    // logic keeps working untouched.
    $wp_customize->add_setting( 'alqamara_hero_header_ink', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_hero_header_ink', [
        'label'       => esc_html__( 'Header Text Color (Transparent State)', 'al-qamara-lite' ),
        'description' => esc_html__( 'Menu items, WooCommerce icons, and the header button — only while the header is transparent over the Hero (before scrolling). Leave empty for the default dark ink. Once scrolled, or on any page without a transparent header, the Header Background Color setting controls this instead.', 'al-qamara-lite' ),
        'section'     => 'alqamara_hero',
    ] ) );





    /* =========================================================================
       GLOBAL SETTINGS PANEL  (priority 100 — appears first)
    ========================================================================= */

    $wp_customize->add_panel( 'alqamara_global_panel', [
        'title'    => esc_html__( 'Global Settings', 'al-qamara-lite' ),
        'priority' => 25,
    ] );


    /* ── Colors ── */
    $wp_customize->add_section( 'alqamara_colors', [
        'title'    => esc_html__( 'Theme Colors', 'al-qamara-lite' ),
        'panel'    => 'alqamara_global_panel',
        'priority' => 10,
    ] );

    $color_controls = [
        // Brand colors — drive the entire site palette
        'alqamara_color_primary'      => [
            'label'       => esc_html__( 'Primary Color (Navy)',    'al-qamara-lite' ),
            'default'     => '#0f172a',
            'description' => esc_html__( 'Used in: navbar background, banner background, buttons, footer, section headings, and links.', 'al-qamara-lite' ),
        ],
        'alqamara_color_accent'       => [
            'label'       => esc_html__( 'Accent Color (Gold)',     'al-qamara-lite' ),
            'default'     => '#b8912a',
            'description' => esc_html__( 'Used in: hover effects, icon highlights, badges, "Read More" links, and focus borders.', 'al-qamara-lite' ),
        ],
        // Typography colors
        'alqamara_color_text'         => [
            'label'       => esc_html__( 'Body Text Color',         'al-qamara-lite' ),
            'default'     => '#333333',
            'description' => esc_html__( 'Used in: paragraphs, meta info, card descriptions, and general readable content.', 'al-qamara-lite' ),
        ],
        // Background & surface
        'alqamara_color_content_bg'   => [
            'label'       => esc_html__( 'Content Body Background', 'al-qamara-lite' ),
            'default'     => '#fbfbfb',
            'description' => esc_html__( 'Used in: the content wrapper on blog, archive, single post, search, and page templates.', 'al-qamara-lite' ),
        ],
        // Borders
        'alqamara_color_border'       => [
            'label'       => esc_html__( 'Border / Divider Color',  'al-qamara-lite' ),
            'default'     => '#e5e2d6',
            'description' => esc_html__( 'Used in: card borders, separator lines, post meta dividers, and horizontal rules.', 'al-qamara-lite' ),
        ],
        // Content links (2026-09-25) — text links inside page/post/service content.
        'alqamara_color_link'         => [
            'label'       => esc_html__( 'Content Link Color',       'al-qamara-lite' ),
            'default'     => '#0b5cd5',
            'description' => esc_html__( 'Color of text links inside pages, posts and other content, so they can be told apart from ordinary text. Buttons, menus and widgets are not affected.', 'al-qamara-lite' ),
        ],
        'alqamara_color_link_hover'   => [
            'label'       => esc_html__( 'Content Link Hover Color', 'al-qamara-lite' ),
            'default'     => '#b8912a',
            'description' => esc_html__( 'Color of those links when the mouse is over them. It does not follow the Accent color, so you can change one without the other.', 'al-qamara-lite' ),
        ],
        // Homepage section title/subtitle colors — a manual override layered
        // ON TOP of automatic per-section contrast detection (2026-08-18,
        // alqamara_section_dark() in inc/helpers.php): each section (About,
        // Services, Projects, Team, etc.) has its OWN independently
        // customizable Background Color, so a title color can no longer be
        // decided once globally — every section call in front-page.php now
        // reads its own actual configured background and auto-picks white
        // or dark ink to match (same contrast math the header already uses).
        // These two controls are only needed as an escape hatch for the one
        // case auto-detection can't solve: a section using a BACKGROUND
        // IMAGE, where there's no reliable way to compute one "correct" ink
        // color from an arbitrary photo. Scoped to light/auto sections only
        // (:not(.text-white) in functions.php) so it can never accidentally
        // override a dark section back to illegible dark-on-dark text.
        'alqamara_section_title_color'    => [
            'label'       => esc_html__( 'Section Title Color (Override)', 'al-qamara-lite' ),
            'default'     => '',
            'description' => esc_html__( 'Usually not needed — section headings already auto-match each section\'s own Background Color. Set this only if a section uses a Background Image and the automatic color is hard to read.', 'al-qamara-lite' ),
        ],
        'alqamara_section_subtitle_color' => [
            'label'       => esc_html__( 'Section Subtitle Color (Override)', 'al-qamara-lite' ),
            'default'     => '',
            'description' => esc_html__( 'Same override, for the subtitle text below each section title.', 'al-qamara-lite' ),
        ],
    ];
    foreach ( $color_controls as $id => $args ) {
        $wp_customize->add_setting( $id, [
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, [
            'label'       => $args['label'],
            'description' => $args['description'],
            'section'     => 'alqamara_colors',
        ] ) );
    }


    /* ── Typography ── */
    $wp_customize->add_section( 'alqamara_typography', [
        'title'    => esc_html__( 'Typography', 'al-qamara-lite' ),
        'panel'    => 'alqamara_global_panel',
        'priority' => 15,
    ] );

    // Available fonts — all loaded from local woff2 files.
    // To add a new font: put the woff2 file in fonts/webfonts/, add @font-face
    // in fonts/text-fonts/fonts.css, then add it to the arrays below.
    $alqamara_heading_fonts = [
        'Playfair Display' => __( 'Playfair Display (Luxury – Recommended)', 'al-qamara-lite' ),
        'Lora'             => __( 'Lora (Editorial)', 'al-qamara-lite' ),
        'Poppins'          => __( 'Poppins (Modern Sans)', 'al-qamara-lite' ),
        'Open Sans'        => __( 'Open Sans (Clean)', 'al-qamara-lite' ),
    ];
    $alqamara_body_fonts = [
        'Poppins'   => __( 'Poppins — Modern', 'al-qamara-lite' ),
        'Open Sans' => __( 'Open Sans — Classic', 'al-qamara-lite' ),
        'Roboto'    => __( 'Roboto — Clean & Neutral', 'al-qamara-lite' ),
    ];

    // Heading Font Family — priority 10 (shows first)
    $wp_customize->add_setting( 'alqamara_font_heading', [
        'default'           => 'Playfair Display',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_font_heading', [
        'label'    => esc_html__( 'Heading Font (H1–H6)', 'al-qamara-lite' ),
        'section'  => 'alqamara_typography',
        'type'     => 'select',
        'choices'  => $alqamara_heading_fonts,
        'priority' => 10,
    ] );

    // Body Font Family — priority 20
    $wp_customize->add_setting( 'alqamara_font_body', [
        'default'           => 'Poppins',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_font_body', [
        'label'    => esc_html__( 'Body Font', 'al-qamara-lite' ),
        'section'  => 'alqamara_typography',
        'type'     => 'select',
        'choices'  => $alqamara_body_fonts,
        'priority' => 20,
    ] );

    // Body Font Size — priority 30
    $wp_customize->add_setting( 'alqamara_font_size_body', [
        'default'           => 16,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_font_size_body', [
        'label'       => esc_html__( 'Body Font Size (px)', 'al-qamara-lite' ),
        'section'     => 'alqamara_typography',
        'type'        => 'number',
        'input_attrs' => [ 'min' => 12, 'max' => 24, 'step' => 1 ],
        'priority'    => 30,
    ] );

    /* ── Header & Navigation ── */
    $wp_customize->add_section( 'alqamara_header_section', [
        'title'    => esc_html__( 'Header & Navigation', 'al-qamara-lite' ),
        'panel'    => 'alqamara_global_panel',
        'priority' => 20,
    ] );

    // ── 1. Show Top Bar on Desktop ──
    $wp_customize->add_setting( 'alqamara_topbar_desktop', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_topbar_desktop', [
        'label'    => esc_html__( 'Show Top Bar on Desktop', 'al-qamara-lite' ),
        'section'  => 'alqamara_header_section',
        'type'     => 'checkbox',
        'priority' => 10,
    ] );

    // ── 2. Show Top Bar on Mobile ──
    $wp_customize->add_setting( 'alqamara_topbar_mobile', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_topbar_mobile', [
        'label'       => esc_html__( 'Show Top Bar on Mobile', 'al-qamara-lite' ),
        'description' => esc_html__( 'On mobile the layout stacks vertically with centered text.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'checkbox',
        'priority'    => 20,
    ] );

    // ── 3. Top Bar Left Announcement Text ──
    $wp_customize->add_setting( 'alqamara_topbar_text', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_topbar_text', [
        'label'       => esc_html__( 'Top Bar Left Announcement Text', 'al-qamara-lite' ),
        'description' => esc_html__( 'Short message shown on the left side of the top bar.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'text',
        'priority'    => 30,
    ] );

    // ── 4. Top Bar Right Phone Number ──
    $wp_customize->add_setting( 'alqamara_topbar_phone', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_topbar_phone', [
        'label'       => esc_html__( 'Top Bar Right Phone Number', 'al-qamara-lite' ),
        'description' => esc_html__( 'Shown on the right side of the top bar (with call icon).', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'text',
        'priority'    => 40,
    ] );

    // ── 4b. Show Social Icons in Top Bar ──
    $wp_customize->add_setting( 'alqamara_topbar_show_social', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_topbar_show_social', [
        'label'       => esc_html__( 'Show Social Icons in Top Bar Right', 'al-qamara-lite' ),
        'description' => esc_html__( 'URLs are set under Footer → Social Media Links.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'checkbox',
        'priority'    => 45,
    ] );

    // ── 4c. Top Bar Background Color ──
    $wp_customize->add_setting( 'alqamara_topbar_bg', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control(
        new WP_Customize_Color_Control( $wp_customize, 'alqamara_topbar_bg', [
            'label'       => esc_html__( 'Top Bar Background Color', 'al-qamara-lite' ),
            'description' => esc_html__( 'Leave empty to use the Primary Color.', 'al-qamara-lite' ),
            'section'     => 'alqamara_header_section',
            'priority'    => 47,
        ] )
    );

    // ── 4d. Top Bar Text Color ──
    $wp_customize->add_setting( 'alqamara_topbar_text_color', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control(
        new WP_Customize_Color_Control( $wp_customize, 'alqamara_topbar_text_color', [
            'label'       => esc_html__( 'Top Bar Text Color', 'al-qamara-lite' ),
            'description' => esc_html__( 'Leave empty for the default (semi-transparent white).', 'al-qamara-lite' ),
            'section'     => 'alqamara_header_section',
            'priority'    => 48,
        ] )
    );

    // ── 5. Sticky Header ──
    $wp_customize->add_setting( 'alqamara_sticky_header', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_sticky_header', [
        'label'    => esc_html__( 'Sticky Header (fixed on scroll)', 'al-qamara-lite' ),
        'section'  => 'alqamara_header_section',
        'type'     => 'checkbox',
        'priority' => 50,
    ] );

    // ── 5b. Solid Background Header on Homepage ──
    $wp_customize->add_setting( 'alqamara_header_solid_home', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_header_solid_home', [
        'label'       => esc_html__( 'Solid Background Header on Homepage', 'al-qamara-lite' ),
        'description' => esc_html__( 'Forces a solid header on the homepage, even at the top.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'checkbox',
        'priority'    => 51,
    ] );

    // ── 6. Header Style ──
    $wp_customize->add_setting( 'alqamara_header_style', [
        'default'           => 'default',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_header_style', [
        'label'       => esc_html__( 'Header Style', 'al-qamara-lite' ),
        'description' => esc_html__( 'Default: navbar only. Agency: adds CTA button (navbar centered). Shop: adds WooCommerce icons (requires WooCommerce).', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'select',
        'priority'    => 60,
        'choices'     => [
            'default' => esc_html__( 'Default (Navbar only)', 'al-qamara-lite' ),
            'agency'  => esc_html__( 'Agency (CTA Button)', 'al-qamara-lite' ),
            'shop'    => esc_html__( 'Shop (WooCommerce Icons)', 'al-qamara-lite' ),
        ],
    ] );

    // ── 6b. Header Layout (Desktop) ──
    // Independent of Header Style (6) above — Header Style decides WHAT shows
    // on the right (nothing / CTA button / WooCommerce icons), Header Layout
    // decides HOW the row is arranged. "Two Rows" moves the menu to its own
    // full-width row below and puts an always-visible search box where the
    // menu used to sit, next to the logo. Desktop (992px+) only — mobile
    // always keeps the existing single-row + hamburger menu behavior,
    // completely unaffected by this setting. When Two Rows is active the
    // header is always solid (see the two color controls below, 6c/6d) —
    // it does not participate in the homepage transparent-over-hero effect,
    // by design (a menu row full of text floating over a photo is hard to
    // read), so alqamara_header_solid_home (5b) has no effect in this mode.
    $wp_customize->add_setting( 'alqamara_header_layout', [
        'default'           => 'single',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_header_layout', [
        'label'       => esc_html__( 'Header Layout (Desktop)', 'al-qamara-lite' ),
        'description' => esc_html__( 'Single Row: current layout, unchanged. Two Rows: menu moves to its own row below, with an always-visible search box next to the logo above it. Desktop only — mobile is unaffected either way.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'select',
        'priority'    => 61,
        'choices'     => [
            'single'  => esc_html__( 'Single Row (Default)', 'al-qamara-lite' ),
            'stacked' => esc_html__( 'Two Rows (Logo + Search / Menu)', 'al-qamara-lite' ),
        ],
    ] );

    // ── 6c. Row 2 Menu Alignment (Two Rows layout only) ──
    // No "Right" choice on purpose: this uses logical flex alignment
    // (flex-start / center), which already mirrors correctly under RTL
    // (Arabic etc.) the same way the rest of the theme's RTL stylesheet
    // does — flex-start resolves to the visual right edge automatically
    // once the page direction is RTL, no separate value needed.
    $wp_customize->add_setting( 'alqamara_header_row2_align', [
        'default'           => 'center',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_header_row2_align', [
        'label'       => esc_html__( 'Row 2 Menu Alignment', 'al-qamara-lite' ),
        'description' => esc_html__( 'Used only when Header Layout = Two Rows. RTL languages mirror automatically, so there is no separate Right option.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'select',
        'priority'    => 62,
        'choices'     => [
            'center' => esc_html__( 'Center (Default)', 'al-qamara-lite' ),
            'left'   => esc_html__( 'Left', 'al-qamara-lite' ),
        ],
    ] );

    // ── 7. CTA Button Text (Agency mode) ──
    $wp_customize->add_setting( 'alqamara_header_cta_text', [
        'default'           => __( 'Get in touch', 'al-qamara-lite' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_header_cta_text', [
        'label'       => esc_html__( 'CTA Button Text', 'al-qamara-lite' ),
        'description' => esc_html__( 'Visible when Header Style = Agency. Leave empty to hide.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'text',
        'priority'    => 70,
    ] );

    // ── 8. CTA Button URL (Agency mode) ──
    $wp_customize->add_setting( 'alqamara_header_cta_url', [
        /* home_url(), not a bare '/contact' — matches the render-time
           fallback already used in header.php (get_theme_mod( ...,
           home_url( '/' ) )). This 'default' only affects what the
           Customizer admin UI shows before the field is ever saved, so it
           was harmless here (the front-end was always correct), but a bare
           path is still wrong to show an admin as "the URL" for their site.
           Fixed for consistency with the real bug found in the Hero
           section's buttons, which used this exact same wrong pattern. */
        'default'           => home_url( '/' ),
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_header_cta_url', [
        'label'       => esc_html__( 'CTA Button URL', 'al-qamara-lite' ),
        'description' => esc_html__( 'Paste the full page URL (e.g. https://yourdomain.com/contact/) copied from your browser\'s address bar — not just a page name.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'type'        => 'url',
        'priority'    => 80,
    ] );

    // ── 9. Show Search Icon (Shop mode) ──
    $wp_customize->add_setting( 'alqamara_show_search', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_show_search', [
        'label'    => esc_html__( 'Show Search Icon (Shop mode)', 'al-qamara-lite' ),
        'section'  => 'alqamara_header_section',
        'type'     => 'checkbox',
        'priority' => 88,
    ] );

    // ── 10. Show Account Icon (Shop mode) ──
    $wp_customize->add_setting( 'alqamara_show_user_icon', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_show_user_icon', [
        'label'    => esc_html__( 'Show Account Icon (Shop mode)', 'al-qamara-lite' ),
        'section'  => 'alqamara_header_section',
        'type'     => 'checkbox',
        'priority' => 100,
    ] );

    // ── 11. Header Background Color (always last) ──
    $wp_customize->add_setting( 'alqamara_header_bg', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_header_bg', [
        'label'       => esc_html__( 'Header Background Color', 'al-qamara-lite' ),
        'description' => esc_html__( 'Applies to the solid header on inner pages and sticky state. Leave empty to use the Global Primary Color. Not used when Header Layout = Two Rows — see the two row colors below instead.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'priority'    => 110,
    ] ) );

    // ── 11b/11c. Header Row 1 / Row 2 Background Color (Two Rows layout only) ──
    $wp_customize->add_setting( 'alqamara_header_row1_bg', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_header_row1_bg', [
        'label'       => esc_html__( 'Header Row 1 Background Color (Logo/Search row)', 'al-qamara-lite' ),
        'description' => esc_html__( 'Used only when Header Layout = Two Rows. Leave empty to use the Global Primary Color.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'priority'    => 111,
    ] ) );

    $wp_customize->add_setting( 'alqamara_header_row2_bg', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_header_row2_bg', [
        'label'       => esc_html__( 'Header Row 2 Background Color (Menu row)', 'al-qamara-lite' ),
        'description' => esc_html__( 'Used only when Header Layout = Two Rows. Leave empty to use the Global Primary Color.', 'al-qamara-lite' ),
        'section'     => 'alqamara_header_section',
        'priority'    => 112,
    ] ) );


    /* ── Page Banner & Breadcrumb ── */
    $wp_customize->add_section( 'alqamara_banner_section', [
        'title'       => esc_html__( 'Page Banner & Breadcrumb', 'al-qamara-lite' ),
        'panel'       => 'alqamara_global_panel',
        'priority'    => 30,
        'description' => esc_html__( 'Toggle banner visibility per page type. Color, image, and alignment apply to all banners.', 'al-qamara-lite' ),
    ] );

    // Show/hide per page type
    $banner_show_controls = [
        'alqamara_banner_show_single'  => esc_html__( 'Show Banner on Single Post',    'al-qamara-lite' ),
        'alqamara_banner_show_archive' => esc_html__( 'Show Banner on Archive / Blog', 'al-qamara-lite' ),
        'alqamara_banner_show_page'    => esc_html__( 'Show Banner on Pages',          'al-qamara-lite' ),
        'alqamara_banner_show_search'  => esc_html__( 'Show Banner on Search Results', 'al-qamara-lite' ),
        'alqamara_banner_show_404'     => esc_html__( 'Show Banner on 404 Page',       'al-qamara-lite' ),
    ];
    foreach ( $banner_show_controls as $id => $label ) {
        $wp_customize->add_setting( $id, [
            'default'           => 1,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'   => $label,
            'section' => 'alqamara_banner_section',
            'type'    => 'checkbox',
        ] );
    }


    // Global breadcrumb toggle (WooCommerce has its own separate control)
    $wp_customize->add_setting( 'alqamara_show_breadcrumb', [
        'default'           => 1,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_show_breadcrumb', [
        'label'       => esc_html__( 'Show Breadcrumb', 'al-qamara-lite' ),
        'description' => esc_html__( 'Show/hide breadcrumb on all pages (single, archive, search etc.). WooCommerce breadcrumb is controlled separately.', 'al-qamara-lite' ),
        'section'     => 'alqamara_banner_section',
        'type'        => 'checkbox',
    ] );

    // Banner content alignment (left / center)
    $wp_customize->add_setting( 'alqamara_banner_alignment', [
        'default'           => 'left',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_banner_alignment', [
        'label'       => esc_html__( 'Banner Content Alignment', 'al-qamara-lite' ),
        'description' => esc_html__( 'Aligns the breadcrumb, title and meta inside the banner.', 'al-qamara-lite' ),
        'section'     => 'alqamara_banner_section',
        'type'        => 'select',
        'choices'     => [
            'left'   => esc_html__( 'Left',   'al-qamara-lite' ),
            'center' => esc_html__( 'Center', 'al-qamara-lite' ),
        ],
    ] );


    $wp_customize->add_setting( 'alqamara_banner_bg', [
        'default'           => '#1e293b',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_banner_bg', [
        'label'       => esc_html__( 'Banner Background Color', 'al-qamara-lite' ),
        'description' => esc_html__( 'Used when no image is set.', 'al-qamara-lite' ),
        'section'     => 'alqamara_banner_section',
    ] ) );

    $wp_customize->add_setting( 'alqamara_banner_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_banner_bg_image', [
        'label'       => esc_html__( 'Banner Background Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty to use color only. Recommended size: 1920×400px (wide banner).', 'al-qamara-lite' ),
        'section'     => 'alqamara_banner_section',
    ] ) );

    // Banner title/secondary text color — was always hardcoded white before
    // this, which only reads correctly against a dark banner (the default
    // navy, or a dark background image). Two shared controls rather than
    // one, same reasoning as Hero above: the title needs to stay the
    // strongest element, everything else (breadcrumb, category badge,
    // subtitle line, post meta) is intentionally a step dimmer.
    // Reworked 2026-08-19 (user feedback): previously "Subtitle Color"
    // only ever recolored the literal .blog-banner-subtitle element, which
    // doesn't exist on every banner — regular Pages have no subtitle line,
    // and single pages show a category badge under a different class
    // that was never wired up, so the control silently did nothing there.
    // It now sets a shared CSS variable that every secondary text element
    // inside the banner reads from — breadcrumb, category badge, subtitle,
    // post meta, whatever a given page type actually renders — so one
    // control covers all of it, consistently, on every page with a banner.
    // Leave empty to keep the existing white/near-white behavior unchanged.
    $wp_customize->add_setting( 'alqamara_banner_title_color', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_banner_title_color', [
        'label'       => esc_html__( 'Banner Title Text Color', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty for the default white. Set this if a lighter Banner Background Color/Image makes it hard to read.', 'al-qamara-lite' ),
        'section'     => 'alqamara_banner_section',
    ] ) );
    $wp_customize->add_setting( 'alqamara_banner_subtitle_color', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_banner_subtitle_color', [
        'label'       => esc_html__( 'Banner Secondary Text Color', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty for the default near-white. Covers everything on the banner besides the title — breadcrumb, category badge, subtitle line, post meta — on every page that has one.', 'al-qamara-lite' ),
        'section'     => 'alqamara_banner_section',
    ] ) );

    $wp_customize->add_setting( 'alqamara_banner_bg_position', [
        'default'           => 'center center',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_banner_bg_position', [
        'label'   => esc_html__( 'Banner Image Position', 'al-qamara-lite' ),
        'section' => 'alqamara_banner_section',
        'type'    => 'select',
        'choices' => [
            'center center' => esc_html__( 'Center Center', 'al-qamara-lite' ),
            'center top'    => esc_html__( 'Center Top',    'al-qamara-lite' ),
            'center bottom' => esc_html__( 'Center Bottom', 'al-qamara-lite' ),
            'left center'   => esc_html__( 'Left Center',   'al-qamara-lite' ),
            'right center'  => esc_html__( 'Right Center',  'al-qamara-lite' ),
        ],
    ] );


    /* ── Archive Page ── */
    $wp_customize->add_section( 'alqamara_archive_section', [
        'title'       => esc_html__( 'Archive Page', 'al-qamara-lite' ),
        'panel'       => 'alqamara_global_panel',
        'priority'    => 35,
        'description' => esc_html__( 'Settings for blog listing, category, tag, and author pages.', 'al-qamara-lite' ),
    ] );

    $archive_checkboxes = [
        'alqamara_show_thumbnail'      => [ 'label' => esc_html__( 'Show Post Thumbnail',  'al-qamara-lite' ), 'default' => 1 ],
        'alqamara_show_meta_date'      => [ 'label' => esc_html__( 'Show Post Date',        'al-qamara-lite' ), 'default' => 1 ],
        'alqamara_show_meta_author'    => [ 'label' => esc_html__( 'Show Post Author',      'al-qamara-lite' ), 'default' => 1 ],
        'alqamara_show_meta_comments'  => [ 'label' => esc_html__( 'Show Comments Count',   'al-qamara-lite' ), 'default' => 1 ],
    ];
    foreach ( $archive_checkboxes as $id => $args ) {
        $wp_customize->add_setting( $id, [
            'default'           => $args['default'],
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'   => $args['label'],
            'section' => 'alqamara_archive_section',
            'type'    => 'checkbox',
        ] );
    }

    $wp_customize->add_setting( 'alqamara_readmore_text', [
        'default'           => __( 'Read More', 'al-qamara-lite' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_readmore_text', [
        'label'       => esc_html__( 'Read More Button Text', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty to hide the button.', 'al-qamara-lite' ),
        'section'     => 'alqamara_archive_section',
        'type'        => 'text',
    ] );


    /* ── Single Post ── */
    $wp_customize->add_section( 'alqamara_single_section', [
        'title'       => esc_html__( 'Single Post', 'al-qamara-lite' ),
        'panel'       => 'alqamara_global_panel',
        'priority'    => 40,
        'description' => esc_html__( 'Settings for individual post pages.', 'al-qamara-lite' ),
    ] );

    /* Order below (via explicit 'priority' on each control) matches the
     * actual top-to-bottom order these render in on the page (banner meta
     * line: date → author → comments, then featured image, then — further
     * down the page, after the post body and tags — related posts) so a client
     * scanning this list top-to-bottom sees the same sequence they'd see
     * on the live page. Keep this order (and the priority numbers, spaced
     * by 10 so new items can be slotted in between) in sync with
     * single.php if that render order ever changes. */
    $single_checkboxes = [
        'alqamara_single_show_meta_date'     => [ 'label' => esc_html__( 'Show Post Date',        'al-qamara-lite' ), 'default' => 1, 'priority' => 10 ],
        'alqamara_single_show_meta_author'   => [ 'label' => esc_html__( 'Show Post Author',      'al-qamara-lite' ), 'default' => 1, 'priority' => 20 ],
        'alqamara_single_show_meta_comments' => [ 'label' => esc_html__( 'Show Comments Count',   'al-qamara-lite' ), 'default' => 1, 'priority' => 30 ],
        'alqamara_single_show_thumbnail'     => [ 'label' => esc_html__( 'Show Featured Image',  'al-qamara-lite' ), 'default' => 1, 'priority' => 40 ],
        'alqamara_show_author_box'           => [ 'label' => esc_html__( 'Show Author Box',        'al-qamara-lite' ), 'default' => 1, 'priority' => 70 ],
        'alqamara_show_related'              => [ 'label' => esc_html__( 'Show Related Posts',     'al-qamara-lite' ), 'default' => 1, 'priority' => 80 ],
    ];
    foreach ( $single_checkboxes as $id => $args ) {
        $wp_customize->add_setting( $id, [
            'default'           => $args['default'],
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'    => $args['label'],
            'section'  => 'alqamara_single_section',
            'type'     => 'checkbox',
            'priority' => $args['priority'],
        ] );
    }

    /* ── Sidebar ── */
    $wp_customize->add_section( 'alqamara_sidebar_section', [
        'title'    => esc_html__( 'Sidebar', 'al-qamara-lite' ),
        'panel'    => 'alqamara_global_panel',
        'priority' => 45,
    ] );

    $wp_customize->add_setting( 'alqamara_sidebar_position', [
        'default'           => 'right',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_sidebar_position', [
        'label'   => esc_html__( 'Sidebar Position', 'al-qamara-lite' ),
        'section' => 'alqamara_sidebar_section',
        'type'    => 'select',
        'choices' => [
            'right' => esc_html__( 'Right', 'al-qamara-lite' ),
            'left'  => esc_html__( 'Left',  'al-qamara-lite' ),
        ],
    ] );

    /* Controls the blog/single-post sidebar ONLY (`.blog-sidebar`). Kept as
     * its own separate setting from the Shop Sidebar's sticky toggle (see
     * `alqamara_shop_sidebar_sticky` in the WooCommerce → Shop / Archive
     * section) — this theme serves two different audiences (content/blog
     * sites and WooCommerce stores), so a buyer using it for one purpose
     * shouldn't have their choice here forced onto the other area too. */
    $wp_customize->add_setting( 'alqamara_blog_sidebar_sticky', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_blog_sidebar_sticky', [
        'label'       => esc_html__( 'Sticky Sidebar', 'al-qamara-lite' ),
        'description' => esc_html__( 'The blog/post sidebar stays in view while the page scrolls, instead of scrolling away and leaving empty space below it. Applies on desktop/tablet only. (This controls the blog sidebar only — the Shop Sidebar has its own separate toggle under WooCommerce → Shop / Archive.)', 'al-qamara-lite' ),
        'section'     => 'alqamara_sidebar_section',
        'type'        => 'checkbox',
    ] );



    /* =========================================================================
       FOOTER SETTINGS PANEL
    ========================================================================= */

    /* =========================================================================
       SHOP SETTINGS (WooCommerce) — layout and display only
    ========================================================================= */
    if ( class_exists( 'WooCommerce' ) ) {

        $wp_customize->add_panel( 'alqamara_wc_panel', [
            'title'       => esc_html__( 'Shop Settings', 'al-qamara-lite' ),
            'description' => esc_html__( 'Layout and display settings for the WooCommerce shop pages.', 'al-qamara-lite' ),
            'priority'    => 32,
        ] );

        /* ── Global ── */
        $wp_customize->add_section( 'alqamara_wc_global', [
            'title'    => esc_html__( 'Global', 'al-qamara-lite' ),
            'panel'    => 'alqamara_wc_panel',
            'priority' => 10,
        ] );
        $wp_customize->add_setting( 'alqamara_wc_breadcrumb', [
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_wc_breadcrumb', [
            'label'       => esc_html__( 'Show Breadcrumb', 'al-qamara-lite' ),
            'description' => esc_html__( 'Show/hide breadcrumb on shop and single product pages.', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_global',
            'type'        => 'checkbox',
        ] );

        /* ── Shop / Archive ── */
        $wp_customize->add_section( 'alqamara_wc_shop', [
            'title'    => esc_html__( 'Shop / Archive', 'al-qamara-lite' ),
            'panel'    => 'alqamara_wc_panel',
            'priority' => 20,
        ] );
        $wp_customize->add_setting( 'alqamara_shop_sidebar', [
            'default'           => 'left',
            'sanitize_callback' => 'sanitize_key',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_shop_sidebar', [
            'label'       => esc_html__( 'Sidebar Position', 'al-qamara-lite' ),
            'description' => esc_html__( 'Add widgets in Appearance → Widgets → Shop Sidebar.', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_shop',
            'type'        => 'select',
            'choices'     => [
                'left'  => esc_html__( 'Left sidebar', 'al-qamara-lite' ),
                'right' => esc_html__( 'Right sidebar', 'al-qamara-lite' ),
                'none'  => esc_html__( 'No sidebar (full width)', 'al-qamara-lite' ),
            ],
        ] );
        $wp_customize->add_setting( 'alqamara_shop_sidebar_sticky', [
            'default'           => 1,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_shop_sidebar_sticky', [
            'label'       => esc_html__( 'Sticky Sidebar', 'al-qamara-lite' ),
            'description' => esc_html__( 'The Shop Sidebar stays in view while the page scrolls. Applies on desktop/tablet only.', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_shop',
            'type'        => 'checkbox',
        ] );
        $wp_customize->add_setting( 'alqamara_shop_per_page', [
            'default'           => 12,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_shop_per_page', [
            'label'       => esc_html__( 'Products Per Page', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_shop',
            'type'        => 'number',
            'input_attrs' => [ 'min' => 4, 'max' => 48, 'step' => 4 ],
        ] );
        $wp_customize->add_setting( 'alqamara_shop_columns', [
            'default'           => 4,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_shop_columns', [
            'label'   => esc_html__( 'Product Columns', 'al-qamara-lite' ),
            'section' => 'alqamara_wc_shop',
            'type'    => 'select',
            'choices' => [
                '2' => esc_html__( '2 columns', 'al-qamara-lite' ),
                '3' => esc_html__( '3 columns', 'al-qamara-lite' ),
                '4' => esc_html__( '4 columns', 'al-qamara-lite' ),
                '5' => esc_html__( '5 columns', 'al-qamara-lite' ),
            ],
        ] );
        $wp_customize->add_setting( 'alqamara_shop_result_count', [
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_shop_result_count', [
            'label'   => esc_html__( 'Show Result Count', 'al-qamara-lite' ),
            'section' => 'alqamara_wc_shop',
            'type'    => 'checkbox',
        ] );
        $wp_customize->add_setting( 'alqamara_shop_ordering', [
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_shop_ordering', [
            'label'   => esc_html__( 'Show Sort/Order Dropdown', 'al-qamara-lite' ),
            'section' => 'alqamara_wc_shop',
            'type'    => 'checkbox',
        ] );

        /* ── Single Product ── */
        $wp_customize->add_section( 'alqamara_wc_single', [
            'title'    => esc_html__( 'Single Product', 'al-qamara-lite' ),
            'panel'    => 'alqamara_wc_panel',
            'priority' => 40,
        ] );
        $wp_customize->add_setting( 'alqamara_sp_layout', [
            'default'           => 'default',
            'sanitize_callback' => 'sanitize_key',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_sp_layout', [
            'label'       => esc_html__( 'Product Page Layout', 'al-qamara-lite' ),
            'description' => esc_html__( 'Default: WooCommerce standard layout. Sticky Gallery: image fixed on left, content & tabs scroll on right.', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_single',
            'type'        => 'select',
            'choices'     => [
                'default' => esc_html__( 'Default (WooCommerce standard)', 'al-qamara-lite' ),
                'sticky'  => esc_html__( 'Sticky Gallery', 'al-qamara-lite' ),
            ],
            'priority'    => 10,
        ] );
        $wp_customize->add_setting( 'alqamara_sp_thumb_position', [
            'default'           => 'bottom',
            'sanitize_callback' => 'sanitize_key',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_sp_thumb_position', [
            'label'       => esc_html__( 'Thumbnail Position', 'al-qamara-lite' ),
            'description' => esc_html__( 'Bottom: thumbnails below main image. Left: vertical strip on left side.', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_single',
            'type'        => 'select',
            'choices'     => [
                'bottom' => esc_html__( 'Bottom (default)', 'al-qamara-lite' ),
                'left'   => esc_html__( 'Left (vertical strip)', 'al-qamara-lite' ),
            ],
            'priority'    => 20,
        ] );

        $sp_toggles = [
            'alqamara_sp_show_rating'     => [ esc_html__( 'Show Star Rating', 'al-qamara-lite' ), 30 ],
            'alqamara_sp_show_sku'        => [ esc_html__( 'Show SKU', 'al-qamara-lite' ), 40 ],
            'alqamara_sp_show_categories' => [ esc_html__( 'Show Product Categories', 'al-qamara-lite' ), 50 ],
            'alqamara_sp_show_tags'       => [ esc_html__( 'Show Product Tags', 'al-qamara-lite' ), 60 ],
            'alqamara_sp_square_image'    => [ esc_html__( 'Square (1:1) Product Image', 'al-qamara-lite' ), 70 ],
            'alqamara_sp_show_upsells'    => [ esc_html__( 'Show Up-sells', 'al-qamara-lite' ), 80 ],
            'alqamara_sp_show_related'    => [ esc_html__( 'Show Related Products', 'al-qamara-lite' ), 90 ],
        ];
        foreach ( $sp_toggles as $sp_id => $sp_args ) {
            $wp_customize->add_setting( $sp_id, [
                'default'           => true,
                'sanitize_callback' => 'wp_validate_boolean',
                'transport'         => 'refresh',
            ] );
            $wp_customize->add_control( $sp_id, [
                'label'    => $sp_args[0],
                'section'  => 'alqamara_wc_single',
                'type'     => 'checkbox',
                'priority' => $sp_args[1],
            ] );
        }

        $wp_customize->add_setting( 'alqamara_sp_related_count', [
            'default'           => 4,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_sp_related_count', [
            'label'       => esc_html__( 'Related Products Count', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_single',
            'type'        => 'number',
            'input_attrs' => [ 'min' => 2, 'max' => 8, 'step' => 1 ],
            'priority'    => 100,
        ] );

        /* ── Cart ── */
        $wp_customize->add_section( 'alqamara_wc_cart', [
            'title'    => esc_html__( 'Cart', 'al-qamara-lite' ),
            'panel'    => 'alqamara_wc_panel',
            'priority' => 50,
        ] );
        $wp_customize->add_setting( 'alqamara_cart_cross_sells', [
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( 'alqamara_cart_cross_sells', [
            'label'       => esc_html__( 'Show Cross-sells', 'al-qamara-lite' ),
            'description' => esc_html__( 'Show the "You may be interested in…" products block on the cart page.', 'al-qamara-lite' ),
            'section'     => 'alqamara_wc_cart',
            'type'        => 'checkbox',
        ] );
    }


    /* =========================================================================
       FOOTER SETTINGS
    ========================================================================= */
    $wp_customize->add_panel( 'alqamara_footer_panel', [
        'title'    => esc_html__( 'Footer Settings', 'al-qamara-lite' ),
        'priority' => 35,
    ] );

    // Brand Column (logo description shown under footer logo)
    $wp_customize->add_section( 'alqamara_footer_brand', [
        'title'    => esc_html__( 'Brand Column', 'al-qamara-lite' ),
        'panel'    => 'alqamara_footer_panel',
        'priority' => 5,
    ] );

    /* ── Show / Hide the brand column ── */
    $wp_customize->add_setting( 'alqamara_footer_brand_show', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_footer_brand_show', [
        'label'       => esc_html__( 'Show Brand Column', 'al-qamara-lite' ),
        'description' => esc_html__( 'Uncheck to hide the logo / description column in the first footer row.', 'al-qamara-lite' ),
        'section'     => 'alqamara_footer_brand',
        'type'        => 'checkbox',
        'priority'    => 1,
    ] );

    /* ── Footer-specific logo (optional — overrides the site logo) ── */
    $wp_customize->add_setting( 'alqamara_footer_logo', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_footer_logo', [
        'label'       => esc_html__( 'Footer Logo (optional)', 'al-qamara-lite' ),
        'description' => esc_html__( 'Upload a separate logo for the footer (e.g. a light/white version). Leave empty to use the site logo. If no logo is set at all, the site name is shown. Recommended size: 220×65px transparent PNG.', 'al-qamara-lite' ),
        'section'     => 'alqamara_footer_brand',
        'priority'    => 2,
    ] ) );

    $wp_customize->add_setting( 'alqamara_footer_desc', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_footer_desc', [
        'label'   => esc_html__( 'Footer Brand Description', 'al-qamara-lite' ),
        'section' => 'alqamara_footer_brand',
        'type'    => 'text',
        'priority' => 5,
    ] );

    /* ── Show Social Icons in Footer (Brand Column) ── */
    $wp_customize->add_setting( 'alqamara_footer_brand_show_social', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_footer_brand_show_social', [
        'label'       => esc_html__( 'Show Social Icons in Footer', 'al-qamara-lite' ),
        'description' => esc_html__( 'Displays social icons below the brand description. URLs are set under Social Media Links.', 'al-qamara-lite' ),
        'section'     => 'alqamara_footer_brand',
        'type'        => 'checkbox',
        'priority'    => 10,
    ] );

    // Social Media Links
    $wp_customize->add_section( 'alqamara_social', [
        'title'    => esc_html__( 'Social Media Links', 'al-qamara-lite' ),
        'panel'    => 'alqamara_footer_panel',
        'priority' => 20,
    ] );

    $social_fields = [
        'alqamara_facebook'  => esc_html__( 'Facebook URL',  'al-qamara-lite' ),
        'alqamara_linkedin'  => esc_html__( 'LinkedIn URL',  'al-qamara-lite' ),
        'alqamara_twitter'   => esc_html__( 'Twitter URL',   'al-qamara-lite' ),
        'alqamara_youtube'   => esc_html__( 'YouTube URL',   'al-qamara-lite' ),
        'alqamara_instagram' => esc_html__( 'Instagram URL', 'al-qamara-lite' ),
    ];

    foreach ( $social_fields as $id => $label ) {
        $wp_customize->add_setting( $id, [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'   => $label,
            'section' => 'alqamara_social',
            'type'    => 'url',
        ] );
    }

    // Copyright Text
    $wp_customize->add_section( 'alqamara_footer', [
        'title'    => esc_html__( 'Copyright Text', 'al-qamara-lite' ),
        'panel'    => 'alqamara_footer_panel',
        'priority' => 30,
    ] );

    $wp_customize->add_setting( 'alqamara_copyright', [
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'alqamara_copyright', [
        'label'   => esc_html__( 'Copyright Text (HTML allowed)', 'al-qamara-lite' ),
        'section' => 'alqamara_footer',
        'type'    => 'textarea',
    ] );

    $wp_customize->add_setting( 'alqamara_credit_text', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_credit_text', [
        'label'       => esc_html__( 'Footer Credit Text', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty to show the standard WordPress.org credit.', 'al-qamara-lite' ),
        'section'     => 'alqamara_footer',
        'type'        => 'text',
    ] );

    $wp_customize->add_setting( 'alqamara_credit_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_credit_url', [
        'label'   => esc_html__( 'Footer Credit URL', 'al-qamara-lite' ),
        'section' => 'alqamara_footer',
        'type'    => 'url',
    ] );


    /* ── Footer Background (color + image) ── */
    $wp_customize->add_section( 'alqamara_footer_bg_section', [
        'title'    => esc_html__( 'Background', 'al-qamara-lite' ),
        'panel'    => 'alqamara_footer_panel',
        'priority' => 40,
    ] );

    // Footer background color
    $wp_customize->add_setting( 'alqamara_footer_bg', [
        'default'           => '#060b12',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_footer_bg', [
        'label'       => esc_html__( 'Background Color', 'al-qamara-lite' ),
        'description' => esc_html__( 'Used when no background image is set.', 'al-qamara-lite' ),
        'section'     => 'alqamara_footer_bg_section',
    ] ) );

    // Footer background image
    $wp_customize->add_setting( 'alqamara_footer_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_footer_bg_image', [
        'label'       => esc_html__( 'Background Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'If set, image overlays the background color. Recommended size: 1920×800px.', 'al-qamara-lite' ),
        'section'     => 'alqamara_footer_bg_section',
    ] ) );

    // Footer background image position
    $wp_customize->add_setting( 'alqamara_footer_bg_position', [
        'default'           => 'center center',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_footer_bg_position', [
        'label'   => esc_html__( 'Image Position', 'al-qamara-lite' ),
        'section' => 'alqamara_footer_bg_section',
        'type'    => 'select',
        'choices' => [
            'center center' => esc_html__( 'Center Center', 'al-qamara-lite' ),
            'center top'    => esc_html__( 'Center Top',    'al-qamara-lite' ),
            'center bottom' => esc_html__( 'Center Bottom', 'al-qamara-lite' ),
            'left center'   => esc_html__( 'Left Center',   'al-qamara-lite' ),
            'right center'  => esc_html__( 'Right Center',  'al-qamara-lite' ),
        ],
    ] );



}
endif;
add_action( 'customize_register', 'alqamara_customizer' );


/* =========================================================================
   HELPER FUNCTIONS
========================================================================= */

if ( ! function_exists( 'alqamara_hero' ) ) :
/**
 * Get a Hero section value from the Customizer.
 *
 * @param string $field  title | subtitle | btn1_text | btn1_url | btn2_text | btn2_url | bg_image | alignment
 * @return string
 */
function alqamara_hero( $field ) {
    $defaults = [
        'title'      => __( 'Your Growth Is Our Goal, Not Just Your Project', 'al-qamara-lite' ),
        'subtitle'   => __( 'Every business deserves honest digital support. We work with purpose — helping you build, grow, and sustain your online presence with integrity.', 'al-qamara-lite' ),
        'btn1_text'  => __( 'Learn More', 'al-qamara-lite' ),
        /* home_url(), not a bare relative string — this is the actual
           fallback used at render time whenever no theme_mod row exists yet
           (i.e. every fresh install before the admin touches these fields
           in the Customizer). A root-relative path resolves against the
           domain ROOT in the browser, so on any install that isn't at its
           domain's root (a subdirectory install, or a test/staging site
           sharing a domain with another install) these buttons silently
           pointed at the WRONG site instead of this one — reported live on
           a fresh test install. */
        'btn1_url'   => home_url( '/' ),
        'btn2_text'  => __( 'Get in Touch', 'al-qamara-lite' ),
        'btn2_url'   => home_url( '/' ),
        'bg_image'   => '',
        'alignment'  => 'center',
    ];
    $default = isset( $defaults[ $field ] ) ? $defaults[ $field ] : '';
    return get_theme_mod( 'alqamara_hero_' . $field, $default );
}
endif;

if ( ! function_exists( 'alqamara_social' ) ) :
/**
 * Get a Social Media URL from the Customizer.
 *
 * @param string $platform  facebook | linkedin | twitter | youtube | instagram
 * @return string
 */
function alqamara_social( $platform ) {
    return get_theme_mod( 'alqamara_' . $platform, '' );
}
endif;

if ( ! function_exists( 'alqamara_copyright' ) ) :
/**
 * Prints the footer copyright text. Default: the site's own copyright
 * (year + site name). A custom text can be entered in the Customizer.
 */
function alqamara_copyright() {
    $default = sprintf(
        /* translators: 1: year, 2: home URL, 3: site name, 4: "All Rights Reserved." */
        '&copy; %1$s <a href="%2$s">%3$s</a>. %4$s',
        gmdate( 'Y' ),
        esc_url( home_url( '/' ) ),
        esc_html( get_bloginfo( 'name' ) ),
        esc_html__( 'All Rights Reserved.', 'al-qamara-lite' )
    );
    echo wp_kses_post( get_theme_mod( 'alqamara_copyright', $default ) );
}
endif;


/* =========================================================================
   PRIVATE HELPER — register standard homepage-section controls
========================================================================= */

if ( ! function_exists( 'alqamara_customize_hp_controls' ) ) :
/**
 * Register controls for a homepage section in UI-logical order:
 * show → eyebrow → title → subtitle → count → columns → view_all_text → bg_color → bg_image
 *
 * @param WP_Customize_Manager $wp_customize
 * @param string               $prefix   Setting prefix, e.g. 'services'
 * @param string               $section  Customizer section ID
 * @param array                $args {
 *   @type int    show_default   Default for _show checkbox (1 or 0). Default 1.
 *   @type string eyebrow        Default eyebrow label. Omit to skip.
 *   @type string title          Default for _title.
 *   @type bool   subtitle       Register _subtitle control? Default false.
 *   @type int    count          Default for _count. Omit to skip.
 *   @type string columns        Default for _columns ('2','3','4'). Omit to skip.
 *   @type string view_all_text  Default "View All" button text. Omit to skip.
 *   @type string bg_color       Default hex for _bg_color. Omit to skip.
 *   @type bool   bg_image       Register _bg_image control? Default false.
 * }
 */
function alqamara_customize_hp_controls( $wp_customize, $prefix, $section, $args = [] ) {

    $show_def = isset( $args['show_default'] ) ? (int) $args['show_default'] : 1;

    // 1 — Show / Hide (skipped for widget-only sections that self-manage visibility)
    if ( empty( $args['no_show'] ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_show", [
            'default'           => $show_def,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$prefix}_show", [
            'label'   => esc_html__( 'Show Section', 'al-qamara-lite' ),
            'section' => $section,
            'type'    => 'checkbox',
        ] );
    }

    // 2 — Eyebrow label (optional)
    if ( array_key_exists( 'eyebrow', $args ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_eyebrow", [
            'default'           => $args['eyebrow'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$prefix}_eyebrow", [
            'label'   => esc_html__( 'Eyebrow Label', 'al-qamara-lite' ),
            'section' => $section,
            'type'    => 'text',
        ] );
    }

    // 3 — Section title (optional — omit key to skip)
    if ( array_key_exists( 'title', $args ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_title", [
            'default'           => $args['title'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$prefix}_title", [
            'label'   => esc_html__( 'Section Title', 'al-qamara-lite' ),
            'section' => $section,
            'type'    => 'text',
        ] );
    }

    // 4 — Subtitle (optional)
    if ( ! empty( $args['subtitle'] ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_subtitle", [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$prefix}_subtitle", [
            'label'   => esc_html__( 'Section Subtitle', 'al-qamara-lite' ),
            'section' => $section,
            'type'    => 'text',
        ] );
    }

    // 5 — Item count
    if ( isset( $args['count'] ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_count", [
            'default'           => (int) $args['count'],
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$prefix}_count", [
            'label'       => esc_html__( 'Number of Items to Show', 'al-qamara-lite' ),
            'section'     => $section,
            'type'        => 'number',
            'input_attrs' => [ 'min' => 1, 'max' => 20 ],
        ] );
    }

    // 6 — Grid columns
    if ( isset( $args['columns'] ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_columns", [
            'default'           => (string) $args['columns'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$prefix}_columns", [
            'label'   => esc_html__( 'Grid Columns', 'al-qamara-lite' ),
            'section' => $section,
            'type'    => 'select',
            'choices' => [
                '2' => esc_html__( '2 Columns', 'al-qamara-lite' ),
                '3' => esc_html__( '3 Columns', 'al-qamara-lite' ),
                '4' => esc_html__( '4 Columns', 'al-qamara-lite' ),
            ],
        ] );
    }

    // 7 — "View All" button text
    if ( isset( $args['view_all_text'] ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_view_all_text", [
            'default'           => $args['view_all_text'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$prefix}_view_all_text", [
            'label'       => esc_html__( '"View All" Button Text', 'al-qamara-lite' ),
            'description' => esc_html__( 'Leave empty to hide the button.', 'al-qamara-lite' ),
            'section'     => $section,
            'type'        => 'text',
        ] );
    }

    // 8 — Background color
    if ( isset( $args['bg_color'] ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_bg_color", [
            'default'           => $args['bg_color'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control(
            new WP_Customize_Color_Control( $wp_customize, "alqamara_{$prefix}_bg_color", [
                'label'   => esc_html__( 'Background Color', 'al-qamara-lite' ),
                'section' => $section,
            ] )
        );
    }

    // 9 — Background image
    if ( ! empty( $args['bg_image'] ) ) {
        $wp_customize->add_setting( "alqamara_{$prefix}_bg_image", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control(
            new WP_Customize_Image_Control( $wp_customize, "alqamara_{$prefix}_bg_image", [
                'label'       => esc_html__( 'Background Image', 'al-qamara-lite' ),
                'description' => esc_html__( 'If set, overrides background color. Recommended size: 1920×800px.', 'al-qamara-lite' ),
                'section'     => $section,
            ] )
        );
    }
}
endif;


/* =========================================================================
   HOMEPAGE SECTION SETTINGS — all sections under alqamara_homepage_panel
========================================================================= */

if ( ! function_exists( 'alqamara_sections_customizer' ) ) :
function alqamara_sections_customizer( $wp_customize ) {


    /* -----------------------------------------------------------------------
       STATS COUNTER  (priority 15)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_stats_section', [
        'title'    => esc_html__( 'Stats Counter', 'al-qamara-lite' ),
        'panel'    => 'alqamara_homepage_panel',
        'priority' => 15,
    ] );

    alqamara_customize_hp_controls( $wp_customize, 'stats', 'alqamara_stats_section', [
        'title'         => __( 'Our Achievements', 'al-qamara-lite' ),
        'eyebrow'       => __( 'Track Record', 'al-qamara-lite' ),
        'show_default'  => 0, // off by default on a fresh install — see front-page.php's matching get_theme_mod() fallback; only Hero/Blog (plus the always-on Header/Footer) ship visible out of the box, everything else is opt-in until the site owner has real content for it.
        // bg_color & bg_image registered below, after stat items
    ] );

    // 4 stat items
    $stat_defaults = [
        1 => [ 'number' => '200+', 'label' => __( 'Projects Done', 'al-qamara-lite' ),      'icon' => 'bi-briefcase' ],
        2 => [ 'number' => '50+',  'label' => __( 'Happy Clients', 'al-qamara-lite' ),       'icon' => 'bi-people'    ],
        3 => [ 'number' => '5+',   'label' => __( 'Years Experience', 'al-qamara-lite' ),    'icon' => 'bi-calendar3' ],
        4 => [ 'number' => '100%', 'label' => __( 'Client Satisfaction', 'al-qamara-lite' ), 'icon' => 'bi-star'      ],
    ];
    foreach ( $stat_defaults as $n => $d ) {
        foreach ( [
            /* translators: %d: stat number */
            'number' => sprintf( __( 'Stat %d — Number', 'al-qamara-lite' ), $n ),
            /* translators: %d: stat number */
            'label'  => sprintf( __( 'Stat %d — Label', 'al-qamara-lite' ), $n ),
            /* translators: %d: stat number */
            'icon'   => sprintf( __( 'Stat %d — Icon class (e.g. bi bi-star)', 'al-qamara-lite' ), $n ),
        ] as $field => $label ) {
            $wp_customize->add_setting( "alqamara_stat_{$n}_{$field}", [
                'default'           => $d[ $field ],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'refresh',
            ] );
            $control_args = [
                'label'   => esc_html( $label ),
                'section' => 'alqamara_stats_section',
                'type'    => 'text',
            ];
            // Icon field only: full class (e.g. "bi bi-star"), same
            // format as every other icon field in the theme — the
            // front-end (front-page.php) uses alqamara_icon_class() so
            // older bare-saved values ("bi-star") still work too. Empty
            // = no icon shown for this stat.
            if ( 'icon' === $field ) {
                $control_args['input_attrs'] = [
                    'class' => 'alqamara-icon-picker-input alqamara-icon-picker-autowire',
                ];
            }
            $wp_customize->add_control( "alqamara_stat_{$n}_{$field}", $control_args );
        }
    }

    // bg_color + bg_image — at the end, after all stat items
    $wp_customize->add_setting( 'alqamara_stats_bg_color', [
        'default'           => '#0a0f2c',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_stats_bg_color', [
        'label'   => esc_html__( 'Background Color', 'al-qamara-lite' ),
        'section' => 'alqamara_stats_section',
    ] ) );
    $wp_customize->add_setting( 'alqamara_stats_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_stats_bg_image', [
        'label'       => esc_html__( 'Background Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'If set, overrides background color. Recommended size: 1920×600px.', 'al-qamara-lite' ),
        'section'     => 'alqamara_stats_section',
    ] ) );


    /* -----------------------------------------------------------------------
       ABOUT US  (priority 30)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_about_section', [
        'title'    => esc_html__( 'About Us', 'al-qamara-lite' ),
        'panel'    => 'alqamara_homepage_panel',
        'priority' => 30,
    ] );

    // Show/Hide only — no title/subtitle (About has its own heading system)
    // bg_color & bg_image registered at the end, after content fields
    alqamara_customize_hp_controls( $wp_customize, 'about', 'alqamara_about_section', [
        'show_default' => 0, // off by default — see stats section above for the reasoning.
    ] );

    // Content fields: eyebrow → heading → bio → button text → image
    $about_fields = [
        'eyebrow'  => [ 'label' => __( 'Eyebrow Label', 'al-qamara-lite' ),  'type' => 'text',     'default' => __( 'Who We Are', 'al-qamara-lite' ) ],
        'heading'  => [ 'label' => __( 'Heading', 'al-qamara-lite' ),         'type' => 'text',     'default' => __( 'Beyond Development: We Build Relationships That Last', 'al-qamara-lite' ) ],
        'bio'      => [ 'label' => __( 'Mission Text', 'al-qamara-lite' ),    'type' => 'textarea', 'default' => __( 'We don\'t measure success by the number of projects we complete. We measure it by the problems we solve. Every website we build starts with a real question: what does this business actually need? Then we work to answer it — honestly, carefully, and completely.', 'al-qamara-lite' ) ],
        'btn_text' => [ 'label' => __( 'Button Text', 'al-qamara-lite' ),     'type' => 'text',     'default' => __( 'About Us', 'al-qamara-lite' ) ],
    ];
    foreach ( $about_fields as $key => $args ) {
        $wp_customize->add_setting( "alqamara_about_{$key}", [
            'default'           => $args['default'],
            'sanitize_callback' => 'textarea' === $args['type'] ? 'wp_kses_post' : 'sanitize_text_field',
        ] );
        $wp_customize->add_control( "alqamara_about_{$key}", [
            'label'   => esc_html( $args['label'] ),
            'section' => 'alqamara_about_section',
            'type'    => $args['type'],
        ] );
    }

    // Button page — page selector (dropdown-pages)
    $wp_customize->add_setting( 'alqamara_about_btn_url', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ] );
    $wp_customize->add_control( 'alqamara_about_btn_url', [
        'label'       => esc_html__( 'Button Page', 'al-qamara-lite' ),
        'description' => esc_html__( 'Select the page this button links to.', 'al-qamara-lite' ),
        'section'     => 'alqamara_about_section',
        'type'        => 'dropdown-pages',
    ] );
    $wp_customize->add_setting( 'alqamara_about_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_about_image', [
        'label'       => esc_html__( 'Right Column Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'Leave empty to hide. Recommended size: 600×700px.', 'al-qamara-lite' ),
        'section'     => 'alqamara_about_section',
    ] ) );

    // bg_color + bg_image — at the end, after all content fields
    $wp_customize->add_setting( 'alqamara_about_bg_color', [
        'default'           => '#fdf9f0',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_about_bg_color', [
        'label'   => esc_html__( 'Background Color', 'al-qamara-lite' ),
        'section' => 'alqamara_about_section',
    ] ) );
    $wp_customize->add_setting( 'alqamara_about_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_about_bg_image', [
        'label'       => esc_html__( 'Background Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'If set, overrides background color. Recommended size: 1920×800px.', 'al-qamara-lite' ),
        'section'     => 'alqamara_about_section',
    ] ) );




    /* -----------------------------------------------------------------------
       FEATURES — Why Work With Us  (priority 45)
       Pure Customizer — no widget area.
       8 items, each: icon (FA class) + title + description.
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_features_section', [
        'title'    => esc_html__( 'Features (Why Us)', 'al-qamara-lite' ),
        'panel'    => 'alqamara_homepage_panel',
        'priority' => 45,
    ] );

    // Show / Hide — off by default on a fresh install (see Stats section comment
    // further up for the reasoning: only Hero/Blog + the always-on Header/Footer
    // ship visible out of the box).
    $wp_customize->add_setting( 'alqamara_features_show', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_features_show', [
        'label'   => esc_html__( 'Show Features Section', 'al-qamara-lite' ),
        'section' => 'alqamara_features_section',
        'type'    => 'checkbox',
    ] );

    // Eyebrow
    $wp_customize->add_setting( 'alqamara_features_eyebrow', [
        'default'           => __( 'Why Choose Us', 'al-qamara-lite' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_features_eyebrow', [
        'label'   => esc_html__( 'Eyebrow Text', 'al-qamara-lite' ),
        'section' => 'alqamara_features_section',
        'type'    => 'text',
    ] );

    // Heading
    $wp_customize->add_setting( 'alqamara_features_title', [
        'default'           => __( 'Why Work With Us', 'al-qamara-lite' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_features_title', [
        'label'   => esc_html__( 'Section Heading', 'al-qamara-lite' ),
        'section' => 'alqamara_features_section',
        'type'    => 'text',
    ] );

    // Subtitle
    $wp_customize->add_setting( 'alqamara_features_subtitle', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'alqamara_features_subtitle', [
        'label'   => esc_html__( 'Section Subtitle', 'al-qamara-lite' ),
        'section' => 'alqamara_features_section',
        'type'    => 'text',
    ] );

    // 8 Feature Items
    $feature_defaults = [
        1 => [ 'icon' => 'fa-code',        'title' => __( 'Clean Code', 'al-qamara-lite' ),           'desc' => __( 'Well-structured, readable code that is easy to maintain and scale.', 'al-qamara-lite' ) ],
        2 => [ 'icon' => 'fa-bolt',        'title' => __( 'Fast Delivery', 'al-qamara-lite' ),         'desc' => __( 'Projects delivered on time, without cutting corners on quality.', 'al-qamara-lite' ) ],
        3 => [ 'icon' => 'fa-comments',    'title' => __( 'Clear Communication', 'al-qamara-lite' ),   'desc' => __( 'Regular updates and honest feedback throughout every project.', 'al-qamara-lite' ) ],
        4 => [ 'icon' => 'fa-headset',     'title' => __( 'Ongoing Support', 'al-qamara-lite' ),       'desc' => __( 'Available after launch to fix issues, add features, and keep things running smoothly.', 'al-qamara-lite' ) ],
        5 => [ 'icon' => 'fa-shield-alt',  'title' => __( 'Secure & Reliable', 'al-qamara-lite' ),     'desc' => __( 'Built with security best practices and thoroughly tested for stability.', 'al-qamara-lite' ) ],
        6 => [ 'icon' => 'fa-chart-line',  'title' => __( 'Results-Driven', 'al-qamara-lite' ),        'desc' => __( 'We measure success by the growth and real impact we bring to your business.', 'al-qamara-lite' ) ],
        7 => [ 'icon' => 'fa-paint-brush', 'title' => __( 'Creative Design', 'al-qamara-lite' ),       'desc' => __( 'Thoughtful, modern designs that represent your brand with confidence.', 'al-qamara-lite' ) ],
        8 => [ 'icon' => 'fa-handshake',   'title' => __( 'Long-Term Partnership', 'al-qamara-lite' ), 'desc' => __( 'We build lasting relationships — your success is our success.', 'al-qamara-lite' ) ],
    ];

    for ( $n = 1; $n <= 8; $n++ ) :
        $d = $feature_defaults[ $n ];

        // Icon
        $wp_customize->add_setting( "alqamara_feature_{$n}_icon", [
            'default'           => $d['icon'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        // Full class (e.g. "fas fa-code"), same format as every other
        // icon field in the theme — the front-end (front-page.php) uses
        // alqamara_icon_class() so older bare-saved values ("fa-code")
        // still work too. Empty = no icon shown for this item.
        $wp_customize->add_control( "alqamara_feature_{$n}_icon", [
            /* translators: %d: item number */
            'label'       => sprintf( esc_html__( 'Item %d — Icon class', 'al-qamara-lite' ), $n ),
            'description' => esc_html__( 'e.g. fas fa-code, fab fa-github, bi bi-code', 'al-qamara-lite' ),
            'section'     => 'alqamara_features_section',
            'type'        => 'text',
            'input_attrs' => [
                'class' => 'alqamara-icon-picker-input alqamara-icon-picker-autowire',
            ],
        ] );

        // Title
        $wp_customize->add_setting( "alqamara_feature_{$n}_title", [
            'default'           => $d['title'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_feature_{$n}_title", [
            /* translators: %d: item number */
            'label'   => sprintf( esc_html__( 'Item %d — Title', 'al-qamara-lite' ), $n ),
            'section' => 'alqamara_features_section',
            'type'    => 'text',
        ] );

        // Description
        $wp_customize->add_setting( "alqamara_feature_{$n}_desc", [
            'default'           => $d['desc'],
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_feature_{$n}_desc", [
            /* translators: %d: item number */
            'label'   => sprintf( esc_html__( 'Item %d — Description', 'al-qamara-lite' ), $n ),
            'section' => 'alqamara_features_section',
            'type'    => 'text',
        ] );
    endfor;

    // Background Color — at end for consistency with all other sections
    $wp_customize->add_setting( 'alqamara_features_bg_color', [
        'default'           => '#fdf9f0',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'alqamara_features_bg_color', [
        'label'   => esc_html__( 'Background Color', 'al-qamara-lite' ),
        'section' => 'alqamara_features_section',
    ] ) );

    // Background Image
    $wp_customize->add_setting( 'alqamara_features_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'alqamara_features_bg_image', [
        'label'       => esc_html__( 'Background Image', 'al-qamara-lite' ),
        'description' => esc_html__( 'If set, overrides background color. Recommended size: 1920×800px.', 'al-qamara-lite' ),
        'section'     => 'alqamara_features_section',
    ] ) );








    /* -----------------------------------------------------------------------
       BLOG  (priority 80)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_blog_section', [
        'title'    => esc_html__( 'Blog / Latest Posts', 'al-qamara-lite' ),
        'panel'    => 'alqamara_homepage_panel',
        'priority' => 80,
    ] );
    alqamara_customize_hp_controls( $wp_customize, 'blog', 'alqamara_blog_section', [
        'title'         => __( 'Latest Articles', 'al-qamara-lite' ),
        'eyebrow'       => __( 'From the Blog', 'al-qamara-lite' ),
        'subtitle'      => true,
        'count'         => 3,
        'columns'       => '3',
        'view_all_text' => __( 'View All Articles', 'al-qamara-lite' ),
        'bg_color'      => '#fdf9f0',
        'bg_image'      => true,
    ] );




    /* -----------------------------------------------------------------------
       NEWSLETTER  (priority 95)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_newsletter_section', [
        'title'       => esc_html__( 'Newsletter', 'al-qamara-lite' ),
        'panel'       => 'alqamara_homepage_panel',
        'priority'    => 95,
        'description' => esc_html__( 'This section appears automatically when you add a widget to Appearance → Widgets → Homepage — Newsletter. It stays hidden when the widget area is empty.', 'al-qamara-lite' ),
    ] );
    alqamara_customize_hp_controls( $wp_customize, 'newsletter', 'alqamara_newsletter_section', [
        'title'        => __( 'Stay in the Loop', 'al-qamara-lite' ),
        'eyebrow'      => __( 'Stay Connected', 'al-qamara-lite' ),
        'subtitle'     => true,
        'bg_color'     => '#1e2c5f',
        'bg_image'     => true,
        'show_default' => 1, // ON by default (2026-09-24): hidden until a widget is added, because front-page.php also checks is_active_sidebar().
    ] );


    /* -----------------------------------------------------------------------
       CTA BANNER  (priority 100)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_cta_section', [
        'title'    => esc_html__( 'CTA Banner', 'al-qamara-lite' ),
        'panel'    => 'alqamara_homepage_panel',
        'priority' => 100,
    ] );

    alqamara_customize_hp_controls( $wp_customize, 'cta', 'alqamara_cta_section', [
        'title'        => __( 'Ready to Work Together?', 'al-qamara-lite' ),
        'subtitle'     => true,
        'bg_color'     => '#0a0f2c',
        'bg_image'     => true,
        'show_default' => 0, // off by default — see Stats section comment above.
    ] );

    // CTA buttons — two action buttons shown by default
    $cta_btns = [
        'cta_btn1_text' => [ 'label' => __( 'Button 1 Text (solid)', 'al-qamara-lite' ),   'type' => 'text', 'default' => __( 'Get In Touch', 'al-qamara-lite' )  ],
        // home_url(), not bare paths — same cosmetic-only fix as the header
        // CTA URL above (front-page.php's render fallback already uses
        // home_url() via alqamara_section_mod(), so this only affects what
        // the Customizer admin UI shows before the field is ever saved).
        'cta_btn1_url'  => [ 'label' => __( 'Button 1 URL', 'al-qamara-lite' ),            'type' => 'url',  'default' => home_url( '/' ), 'description' => __( 'Paste the full page URL (e.g. https://yourdomain.com/contact/) copied from your browser\'s address bar — not just a page name.', 'al-qamara-lite' ) ],
        'cta_btn2_text' => [ 'label' => __( 'Button 2 Text (outline)', 'al-qamara-lite' ), 'type' => 'text', 'default' => __( 'Learn More', 'al-qamara-lite' ) ],
        'cta_btn2_url'  => [ 'label' => __( 'Button 2 URL', 'al-qamara-lite' ),            'type' => 'url',  'default' => home_url( '/' ), 'description' => __( 'Paste the full page URL (e.g. https://yourdomain.com/about/) copied from your browser\'s address bar — not just a page name.', 'al-qamara-lite' ) ],
    ];
    foreach ( $cta_btns as $id => $args ) {
        $wp_customize->add_setting( "alqamara_{$id}", [
            'default'           => $args['default'],
            'sanitize_callback' => 'url' === $args['type'] ? 'esc_url_raw' : 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( "alqamara_{$id}", [
            'label'       => esc_html( $args['label'] ),
            'description' => isset( $args['description'] ) ? esc_html( $args['description'] ) : '',
            'section'     => 'alqamara_cta_section',
            'type'        => $args['type'],
        ] );
    }


    /* -----------------------------------------------------------------------
       GENERIC ROW 1  (priority 110)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_row1_section', [
        'title'       => esc_html__( 'Generic Row 1', 'al-qamara-lite' ),
        'panel'       => 'alqamara_homepage_panel',
        'priority'    => 110,
        'description' => esc_html__( 'This section appears automatically when you add a widget to Appearance → Widgets → Homepage — Generic Row 1. It stays hidden when the widget area is empty.', 'al-qamara-lite' ),
    ] );
    alqamara_customize_hp_controls( $wp_customize, 'row1', 'alqamara_row1_section', [
        'title'        => '',
        'bg_color'     => '#fdf9f0',
        'bg_image'     => true,
        'show_default' => 1, // ON by default (2026-09-24): hidden until a widget is added, because front-page.php also checks is_active_sidebar().
    ] );


    /* -----------------------------------------------------------------------
       GENERIC ROW 2  (priority 115)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_row2_section', [
        'title'       => esc_html__( 'Generic Row 2', 'al-qamara-lite' ),
        'panel'       => 'alqamara_homepage_panel',
        'priority'    => 115,
        'description' => esc_html__( 'This section appears automatically when you add a widget to Appearance → Widgets → Homepage — Generic Row 2. It stays hidden when the widget area is empty.', 'al-qamara-lite' ),
    ] );
    alqamara_customize_hp_controls( $wp_customize, 'row2', 'alqamara_row2_section', [
        'title'        => '',
        'bg_color'     => '#ffffff',
        'bg_image'     => true,
        'show_default' => 1, // ON by default (2026-09-24): hidden until a widget is added, because front-page.php also checks is_active_sidebar().
    ] );


    /* -----------------------------------------------------------------------
       GENERIC ROW 3  (priority 125)
    ----------------------------------------------------------------------- */
    $wp_customize->add_section( 'alqamara_row3_section', [
        'title'       => esc_html__( 'Generic Row 3', 'al-qamara-lite' ),
        'panel'       => 'alqamara_homepage_panel',
        'priority'    => 125,
        'description' => esc_html__( 'This section appears automatically when you add a widget to Appearance → Widgets → Homepage — Generic Row 3. It stays hidden when the widget area is empty.', 'al-qamara-lite' ),
    ] );
    alqamara_customize_hp_controls( $wp_customize, 'row3', 'alqamara_row3_section', [
        'title'        => '',
        'bg_color'     => '#fdf9f0',
        'bg_image'     => true,
        'show_default' => 1, // ON by default (2026-09-24): hidden until a widget is added, because front-page.php also checks is_active_sidebar().
    ] );


}
endif;
add_action( 'customize_register', 'alqamara_sections_customizer' );

/*
 * Force the Shop Sidebar widget area to always be visible in
 * Customizer → Widgets, regardless of which page is being previewed.
 *
 * WordPress normally hides widget-area sections when the sidebar is not
 * rendered on the current preview page. We override the active_callback
 * so "Shop Sidebar" always appears in the list, just like Default Sidebar.
 *
 * Priority 999 ensures this runs after WordPress registers the section.
 */
add_action( 'customize_register', function( $wp_customize ) {
    $section = $wp_customize->get_section( 'sidebar-widgets-sidebar-shop' );
    if ( $section ) {
        $section->active_callback = '__return_true';
    }
}, 999 );


/* =========================================================================
   HELPER FUNCTIONS
   ========================================================================= */

if ( ! function_exists( 'alqamara_about' ) ) :
/**
 * Get an About Us field from Customizer.
 *
 * @param string $field  show|title|subtitle|bg_color|bg_image|eyebrow|heading|bio|btn_text|btn_url|image
 * @return mixed
 */
function alqamara_about( $field ) {
    // btn_url stores a page ID (dropdown-pages) — return its permalink
    if ( 'btn_url' === $field ) {
        $page_id = (int) get_theme_mod( 'alqamara_about_btn_url', 0 );
        return $page_id ? get_permalink( $page_id ) : home_url( '/' );
    }

    $defaults = [
        'show'     => 1,
        'title'    => __( 'About Us', 'al-qamara-lite' ),
        'subtitle' => '',
        'bg_color' => '#fdf9f0',
        'bg_image' => '',
        'eyebrow'  => __( 'Who We Are', 'al-qamara-lite' ),
        'heading'  => __( 'Beyond Development: We Build Relationships That Last', 'al-qamara-lite' ),
        'bio'      => __( 'We don\'t measure success by the number of projects we complete. We measure it by the problems we solve. Every website we build starts with a real question: what does this business actually need? Then we work to answer it — honestly, carefully, and completely.', 'al-qamara-lite' ),
        'btn_text' => __( 'About Us', 'al-qamara-lite' ),
        'image'    => '',
    ];
    // 'image' stores a full URL via WP_Customize_Image_Control — resolve it
    // through the helper instead of trusting the raw stored
    // value (see alqamara_get_customize_image_url()'s docblock).
    if ( 'image' === $field ) {
        return alqamara_get_customize_image_url( 'alqamara_about_image' );
    }

    $default = isset( $defaults[ $field ] ) ? $defaults[ $field ] : '';
    return get_theme_mod( 'alqamara_about_' . $field, $default );
}
endif;

if ( ! function_exists( 'alqamara_section_mod' ) ) :
/**
 * Get any homepage section Customizer value.
 *
 * @param string $section  Section prefix: stats|features|blog|row1|row2|cta
 * @param string $field    Field: show|title|subtitle|bg_color|bg_image|count|columns
 * @param mixed  $default  Fallback value
 * @return mixed
 */
function alqamara_section_mod( $section, $field, $default = '' ) {
    return get_theme_mod( 'alqamara_' . $section . '_' . $field, $default );
}
endif;

if ( ! function_exists( 'alqamara_stat' ) ) :
/**
 * Get a Stats Counter item field.
 *
 * @param int    $n      Stat number (1–4)
 * @param string $field  number|label|icon
 * @return string
 */
function alqamara_stat( $n, $field ) {
    $defaults = [
        1 => [ 'number' => '200+', 'label' => __( 'Projects Done', 'al-qamara-lite' ),      'icon' => 'bi-briefcase' ],
        2 => [ 'number' => '50+',  'label' => __( 'Happy Clients', 'al-qamara-lite' ),       'icon' => 'bi-people'    ],
        3 => [ 'number' => '5+',   'label' => __( 'Years Experience', 'al-qamara-lite' ),    'icon' => 'bi-calendar3' ],
        4 => [ 'number' => '100%', 'label' => __( 'Client Satisfaction', 'al-qamara-lite' ), 'icon' => 'bi-star'      ],
    ];
    $default = isset( $defaults[ $n ][ $field ] ) ? $defaults[ $n ][ $field ] : '';
    return get_theme_mod( "alqamara_stat_{$n}_{$field}", $default );
}
endif;


/* =============================================================================
   SELECT / RADIO CONTROLS — accept only one of the control's own choices
============================================================================= */

if ( ! function_exists( 'alqamara_sanitize_choice' ) ) :
/**
 * Returns $value only if it is one of the control's choices, otherwise the
 * setting's default.
 *
 * @param mixed                $value   Value being saved.
 * @param WP_Customize_Setting $setting The setting.
 * @return mixed
 */
function alqamara_sanitize_choice( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	if ( $control && ! empty( $control->choices ) && array_key_exists( (string) $value, $control->choices ) ) {
		return $value;
	}
	return $setting->default;
}
endif;

add_action(
	'customize_register',
	function ( $wp_customize ) {
		foreach ( $wp_customize->controls() as $control ) {
			if ( 0 !== strpos( $control->id, 'alqamara_' ) || empty( $control->choices ) || ! in_array( $control->type, array( 'select', 'radio' ), true ) ) {
				continue;
			}
			foreach ( $control->settings as $setting ) {
				add_filter( 'customize_sanitize_' . $setting->id, 'alqamara_sanitize_choice', 20, 2 );
			}
		}
	},
	999
);


/* =============================================================================
   NUMBER CONTROLS — clamp to the min/max declared on the control
============================================================================= */

if ( ! function_exists( 'alqamara_sanitize_number_range' ) ) :
/**
 * Clamps an integer to the min/max set in the control's input_attrs.
 *
 * @param mixed                $value   Value being saved.
 * @param WP_Customize_Setting $setting The setting.
 * @return int
 */
function alqamara_sanitize_number_range( $value, $setting ) {
	$value   = (int) $value;
	$control = $setting->manager->get_control( $setting->id );
	if ( $control && isset( $control->input_attrs['min'] ) ) {
		$value = max( (int) $control->input_attrs['min'], $value );
	}
	if ( $control && isset( $control->input_attrs['max'] ) ) {
		$value = min( (int) $control->input_attrs['max'], $value );
	}
	return $value;
}
endif;

add_action(
	'customize_register',
	function ( $wp_customize ) {
		foreach ( $wp_customize->controls() as $control ) {
			if ( 0 !== strpos( $control->id, 'alqamara_' ) || 'number' !== $control->type ) {
				continue;
			}
			foreach ( $control->settings as $setting ) {
				add_filter( 'customize_sanitize_' . $setting->id, 'alqamara_sanitize_number_range', 20, 2 );
			}
		}
	},
	999
);
