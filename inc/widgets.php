<?php
/**
 * =============================================================================
 *
 *  Custom Widgets — Al Qamara
 *
 *  Alqamara_Posts_Widget
 *    Displays posts of any registered type (post, or WooCommerce product)
 *    in a Bootstrap grid or a Swiper slider.
 *
 *  Alqamara_Category_Showcase_Widget
 *    Displays WooCommerce product categories (thumbnail + name + product
 *    count, each linking to the category archive) in a CSS-grid layout.
 *    The admin only checks which categories to feature — thumbnail, name,
 *    and link are pulled automatically from WooCommerce's own category
 *    data. Reusable in any widget area (homepage widget sections, blog/
 *    shop sidebars, footer columns) — same "one widget class, restyled
 *    per placement" pattern as Alqamara_Posts_Widget.
 * *
 * @package alqamara
 * =============================================================================
 */

defined( 'ABSPATH' ) || exit;


/* =============================================================================
   ADMIN ASSETS — widget form interactivity
   Loaded on the Widgets screen and in the Customizer's controls panel.
   The term lists are handed to js/widgets-admin.js as a JSON object, so
   no AJAX round-trip is needed when a select changes.
============================================================================= */

/**
 * Enqueues the widget-form script/style and prints its data.
 */
function alqamara_widgets_admin_assets() {

	/* Taxonomy map for the Posts widget: post type => taxonomy. */
	$type_map = array( 'post' => 'category' );
	if ( class_exists( 'WooCommerce' ) ) {
		$type_map['product'] = 'product_cat';
	}

	/* Every term of every taxonomy, pre-loaded. */
	$all_terms = array();
	foreach ( $type_map as $post_type => $taxonomy ) {
		$fetched                  = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
		$all_terms[ $post_type ] = is_wp_error( $fetched )
			? array()
			: array_map(
				function ( $term ) {
					return array(
						'id'   => $term->term_id,
						'name' => $term->name,
					);
				},
				$fetched
			);
	}

	/* Same, for the Category Showcase widget's "Category Type" select. */
	$cs_taxonomies = array( 'category' );
	if ( class_exists( 'WooCommerce' ) ) {
		$cs_taxonomies[] = 'product_cat';
	}
	$cs_terms = array();
	foreach ( $cs_taxonomies as $taxonomy ) {
		$fetched               = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
		$cs_terms[ $taxonomy ] = is_wp_error( $fetched )
			? array()
			: array_map(
				function ( $term ) {
					return array(
						'id'    => $term->term_id,
						'name'  => $term->name,
						'count' => $term->count,
					);
				},
				$fetched
			);
	}

	wp_enqueue_style( 'alqamara-widgets-admin', ALQAMARA_URI . '/css/widgets-admin.css', array(), ALQAMARA_VERSION );
	wp_enqueue_script( 'alqamara-widgets-admin', ALQAMARA_URI . '/js/widgets-admin.js', array( 'jquery' ), ALQAMARA_VERSION, true );
	wp_add_inline_script(
		'alqamara-widgets-admin',
		'var alqamaraWidgetData = ' . wp_json_encode(
			array(
				'typeMap'    => $type_map,
				'allTerms'   => $all_terms,
				'csTerms'    => $cs_terms,
				'noTerms'    => __( 'No categories found.', 'al-qamara-lite' ),
				'allOptions' => __( '&#8212; All &#8212;', 'al-qamara-lite' ),
			)
		) . ';',
		'before'
	);
}

// Widgets screen.
add_action(
	'admin_enqueue_scripts',
	function ( $hook_suffix ) {
		if ( 'widgets.php' === $hook_suffix ) {
			alqamara_widgets_admin_assets();
		}
	}
);

// Customizer controls panel.
add_action( 'customize_controls_enqueue_scripts', 'alqamara_widgets_admin_assets' );


/* =============================================================================
   WIDGET CLASS
============================================================================= */

if ( ! class_exists( 'Alqamara_Posts_Widget' ) ) :

class Alqamara_Posts_Widget extends WP_Widget {

    /* -------------------------------------------------------------------------
       Post-type map — taxonomy is empty ('') when type has none.
    ------------------------------------------------------------------------- */
    private function type_map(): array {
        $map = [
            'post' => [ 'label' => __( 'Posts', 'al-qamara-lite' ), 'taxonomy' => 'category' ],
        ];
        if ( class_exists( 'WooCommerce' ) ) {
            $map['product'] = [ 'label' => __( 'Products', 'al-qamara-lite' ), 'taxonomy' => 'product_cat' ];
        }
        return $map;
    }


    /* -------------------------------------------------------------------------
       Constructor
    ------------------------------------------------------------------------- */
    public function __construct() {
        parent::__construct(
            'alqamara_posts_widget',
            __( 'Al Qamara: Posts & Products', 'al-qamara-lite' ),
            [
                'description' => __( 'Show posts or WooCommerce products in a Bootstrap grid or Swiper slider.', 'al-qamara-lite' ),
                'classname'   => 'alqamara-posts-widget',
            ]
        );
    }


    /* -------------------------------------------------------------------------
       get_instance_schema() — REST API raw instance support
       Allows wp/v2/widgets PUT requests to update instance settings.
    ------------------------------------------------------------------------- */
    public function get_instance_schema(): array {
        return [
            'title'         => [ 'type' => 'string',  'default' => '' ],
            'post_type'     => [ 'type' => 'string',  'default' => 'post' ],
            'taxonomy'      => [ 'type' => 'string',  'default' => '' ],
            'term_id'       => [ 'type' => 'integer', 'default' => 0 ],
            'count'         => [ 'type' => 'integer', 'default' => 3 ],
            'columns'       => [ 'type' => 'integer', 'default' => 3 ],
            'orderby'       => [ 'type' => 'string',  'default' => 'date' ],
            'order'         => [ 'type' => 'string',  'default' => 'DESC' ],
            'show_image'    => [ 'type' => 'boolean', 'default' => true ],
            'show_date'     => [ 'type' => 'boolean', 'default' => true ],
            'show_category' => [ 'type' => 'boolean', 'default' => true ],
            'show_excerpt'  => [ 'type' => 'boolean', 'default' => true ],
            'show_readmore' => [ 'type' => 'boolean', 'default' => false ],
            'readmore_text' => [ 'type' => 'string',  'default' => __( 'Read More', 'al-qamara-lite' ) ],
            'show_price'    => [ 'type' => 'boolean', 'default' => true ],
            'show_rating'   => [ 'type' => 'boolean', 'default' => true ],
            'show_cart'     => [ 'type' => 'boolean', 'default' => true ],
            'wc_show_date'  => [ 'type' => 'boolean', 'default' => false ],
            'wc_show_cat'   => [ 'type' => 'boolean', 'default' => false ],
            'enable_slider' => [ 'type' => 'boolean', 'default' => false ],
            'slider_speed'  => [ 'type' => 'integer', 'default' => 3000 ],
            'slider_auto'   => [ 'type' => 'boolean', 'default' => true ],
            'show_view_all' => [ 'type' => 'boolean', 'default' => false ],
            'view_all_text' => [ 'type' => 'string',  'default' => __( 'View All', 'al-qamara-lite' ) ],
        ];
    }


    /* =========================================================================
       widget() — FRONT-END RENDER
    ========================================================================= */
    public function widget( $args, $instance ): void {

        /* ── Read settings ── */
        $title         = apply_filters( 'widget_title', $instance['title'] ?? '' );
        $post_type     = $instance['post_type']    ?? 'post';
        $taxonomy      = $instance['taxonomy']     ?? '';
        $term_id       = (int) ( $instance['term_id']     ?? 0 );
        $count         = (int) ( $instance['count']       ?? 3 );
        $columns       = (int) ( $instance['columns']     ?? 3 );
        $orderby       = $instance['orderby']      ?? 'date';
        $order         = $instance['order']        ?? 'DESC';

        $show_image    = ! empty( $instance['show_image']    );
        $show_excerpt  = ! empty( $instance['show_excerpt']  );
        $show_date     = ! empty( $instance['show_date']     );
        $show_readmore = ! empty( $instance['show_readmore'] );
        $readmore_text = $instance['readmore_text'] ?? __( 'Read More', 'al-qamara-lite' );

        $show_category = ! empty( $instance['show_category'] );
        $show_price    = ! empty( $instance['show_price']    );
        $show_rating   = ! empty( $instance['show_rating']   );
        $show_cart     = ! empty( $instance['show_cart']     );
        $wc_show_date  = ! empty( $instance['wc_show_date']  );
        $wc_show_cat   = ! empty( $instance['wc_show_cat']   );
        $enable_slider = ! empty( $instance['enable_slider'] );

        $slider_speed  = (int) ( $instance['slider_speed']  ?? 3000 );
        $slider_auto   = ! empty( $instance['slider_auto']  );
        $show_view_all = ! empty( $instance['show_view_all'] );
        $view_all_text = $instance['view_all_text'] ?? __( 'View All', 'al-qamara-lite' );

        $is_product = ( 'product' === $post_type && class_exists( 'WooCommerce' ) );

        /*
         * Narrow sidebars (blog sidebar, shop sidebar, footer columns):
         * - Slider makes no sense in a single narrow column.
         * - Force single-column stacked layout and disable slider + nav.
         */
        $narrow_sidebars = [
            'sidebar-blog', 'sidebar-shop',
            'footer-col-1', 'footer-col-2', 'footer-col-3', 'footer-col-4',
            'footer-legal',
        ];
        if ( in_array( $args['id'] ?? '', $narrow_sidebars, true ) ) {
            $enable_slider = false;
            $columns       = 1;
            $slider_auto   = false;
        }

        /*
         * For WooCommerce products:
         * - date/category use WC-specific toggles (not the global ones)
         * - excerpt is never shown (product short description ≠ blog excerpt;
         *   hiding it keeps cards clean and consistent with the shop page)
         */
        if ( $is_product ) {
            $show_date     = $wc_show_date;
            $show_category = $wc_show_cat;
            $show_excerpt  = false;
        }

        /* ── WP_Query (found_posts gives total without extra query) ── */
        $q_args = [
            'post_type'           => $post_type,
            'posts_per_page'      => $count,
            'orderby'             => $orderby,
            'order'               => $order,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
        ];

        // Top Selling — WooCommerce total_sales meta (products only)
        if ( 'popularity' === $orderby && $is_product ) {
            $q_args['orderby']   = 'meta_value_num';
            $q_args['meta_key']  = 'total_sales';
            $q_args['order']     = 'DESC';
        }

        // Top Rated — WooCommerce average rating meta (products only)
        if ( 'rating' === $orderby && $is_product ) {
            $q_args['orderby']   = 'meta_value_num';
            $q_args['meta_key']  = '_wc_average_rating';
            $q_args['order']     = 'DESC';
        }

        // Most Commented — works for both posts and products
        if ( 'comment_count' === $orderby ) {
            $q_args['orderby'] = 'comment_count';
            $q_args['order']   = 'DESC';
        }
        if ( $taxonomy && $term_id ) {
            $q_args['tax_query'] = [ [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $term_id,
            ] ];
        }

        $query = new WP_Query( $q_args );
        if ( ! $query->have_posts() ) {
            return;
        }

        /* ── Decide what to show in section head (no extra DB query) ── */
        $total_posts = (int) $query->found_posts;

        // View All: only when checkbox is on AND there are more posts than shown
        $view_all_url = '';
        if ( $show_view_all && $total_posts > $count ) {
            if ( $taxonomy && $term_id ) {
                $term_link = get_term_link( $term_id, $taxonomy );
                if ( ! is_wp_error( $term_link ) ) {
                    $view_all_url = $term_link;
                }
            }
            if ( ! $view_all_url ) {
                if ( 'post' === $post_type ) {
                    $blog_page    = (int) get_option( 'page_for_posts' );
                    $view_all_url = $blog_page ? get_permalink( $blog_page ) : home_url( '/' );
                } elseif ( $is_product ) {
                    $shop_id      = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
                    $view_all_url = $shop_id ? get_permalink( $shop_id ) : '';
                } else {
                    $archive = get_post_type_archive_link( $post_type );
                    $view_all_url = $archive ?: '';
                }
            }
        }

        // Slider arrows: show whenever slider is enabled (loop always works via DOM duplication)
        $show_slider_nav = $enable_slider;

        /* ── Widget shell ── */
        echo wp_kses_post( $args['before_widget'] );
        echo '<div class="alqamara-pw-wrap">';

        /* ── Section head: title LEFT + actions RIGHT ── */
        $has_actions = ( $view_all_url || $show_slider_nav );
        $has_head    = ( $title || $has_actions );

        if ( $has_head ) {
            echo '<div class="alqamara-pw-section-head">';

            // Left — title
            if ( $title ) {
                echo '<h3 class="alqamara-pw-widget-title">' . esc_html( $title ) . '</h3>';
            } else {
                echo '<div></div>';
            }

            // Right — actions (View All + slider arrows)
            if ( $has_actions ) {
                echo '<div class="alqamara-pw-head-actions">';

                if ( $view_all_url ) {
                    printf(
                        '<a href="%s" class="btn btn-outline-custom btn-sm">%s <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>',
                        esc_url( $view_all_url ),
                        esc_html( $view_all_text )
                    );
                }

                if ( $show_slider_nav ) {
                    echo '<div class="alqamara-pw-slider-nav-btns">';
                    printf(
                        '<button class="alqamara-pw-nav-btn alqamara-pw-prev" aria-label="%s"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>',
                        esc_attr__( 'Previous', 'al-qamara-lite' )
                    );
                    printf(
                        '<button class="alqamara-pw-nav-btn alqamara-pw-next" aria-label="%s"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>',
                        esc_attr__( 'Next', 'al-qamara-lite' )
                    );
                    echo '</div>';
                }

                echo '</div>'; // .alqamara-pw-head-actions
            }

            echo '</div>'; // .alqamara-pw-section-head

        } elseif ( $title ) {
            echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
        }

        /* ── Column class map ── */
        $col_map = [
            1 => 'col-12',
            2 => 'col-6',
            3 => 'col-6 col-lg-4',
            4 => 'col-6 col-lg-3',
        ];
        $col_class = $col_map[ max( 1, min( 4, $columns ) ) ] ?? 'col-6 col-lg-4';

        /* ── Outer wrapper ── */
        if ( $enable_slider ) {
            /*
             * Skip Swiper assets in REST context (Gutenberg widget-preview iframes).
             * The iframe is generated via REST API → REST_REQUEST is true there.
             * Our CSS fallback in custom.css handles the preview layout without Swiper.
             * On the real frontend REST_REQUEST is false → assets load normally.
             */
            if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
                wp_enqueue_style( 'swiper' );
                wp_enqueue_script( 'swiper' );
            }

            /* Mobile (<576px) slides-per-view: products show 2-up (visual cards),
               posts/blog stay 1-up (text-heavy, needs full width). */
            $mobile_cols = $is_product ? 2 : 1;
            /* BUG FIX (2026-08-24): this used to pass $total_posts here, which is
             * WP_Query::$found_posts — the TOTAL number of posts matching the
             * query in the database, calculated with SQL_CALC_FOUND_ROWS and
             * completely ignoring posts_per_page/the "Number of Items" setting.
             * $total_posts is the *correct* value for the "View All" link check
             * above (total available vs. shown), but wrong here: data-swiper-count
             * feeds main.js's loop-safety duplication AND its overflow-based
             * arrow-hide logic, both of which need the count of slides ACTUALLY
             * rendered in the DOM below (respecting the widget's "Number of
             * Items" field), not the full matching total. Using found_posts made
             * the "Number of Items" setting look like it did nothing to the
             * slider's nav arrows — e.g. Number of Items=2 with 6 posts in the
             * DB still passed count=6 to the JS, so hasOverflow stayed true and
             * arrows kept showing/cycling through duplicate clones of the same 2
             * real cards. $query->post_count is the number of posts actually in
             * this result set, i.e. exactly how many <div class="swiper-slide">
             * get printed in the loop below. */
            printf(
                '<div class="swiper alqamara-posts-slider" data-swiper-items="%d" data-swiper-speed="%d" data-swiper-auto="%s" data-swiper-count="%d" data-swiper-mobile-cols="%d" style="--alqamara-cols:%d">',
                (int) $columns,
                (int) $slider_speed,
                esc_attr( $slider_auto ? 'true' : 'false' ),
                (int) $query->post_count,
                (int) $mobile_cols,
                (int) $columns
            );
            echo '<div class="swiper-wrapper">';
        } else {
            /* CSS Grid — --alqamara-cols drives grid-template-columns in CSS.
               alqamara-product-grid enables 2-col layout on xs mobile (products are
               more visual; posts are text-heavy → stay at 1-col on xs). */
            $grid_cls = 'alqamara-posts-grid' . ( $is_product ? ' alqamara-product-grid' : '' );
            echo '<div class="' . esc_attr( $grid_cls ) . '" style="--alqamara-cols:' . (int) $columns . '">';
        }

        /* ── Loop ── */
        while ( $query->have_posts() ) :
            $query->the_post();
            $post_url = get_permalink();

            /* WC product object (needed for sale badge + cart overlay) */
            $product = null;
            if ( $is_product ) {
                $product = wc_get_product( get_the_ID() );
            }

            /* Primary taxonomy term for category badge */
            $primary_term = null;
            if ( $show_category && $taxonomy ) {
                $post_terms = get_the_terms( get_the_ID(), $taxonomy );
                if ( $post_terms && ! is_wp_error( $post_terms ) ) {
                    $primary_term = reset( $post_terms );
                }
            }

            echo $enable_slider
                ? '<div class="swiper-slide alqamara-pw-slide">'
                : '<div class="' . esc_attr( $col_class ) . ' alqamara-pw-col">';

            echo '<div class="alqamara-pw-card">';  /* flex layout handled by CSS — no Bootstrap flex classes */

            /* ── Thumbnail wrap (equal height via aspect-ratio in CSS) ── */
            if ( $show_image ) {
                $thumb_class = 'alqamara-pw-thumb-wrap' . ( $is_product ? ' alqamara-pw-thumb-wrap--product' : '' );
                echo '<div class="' . esc_attr( $thumb_class ) . '">';

                if ( has_post_thumbnail() ) {
                    echo '<a href="' . esc_url( $post_url ) . '" class="alqamara-pw-thumb-link" tabindex="-1" aria-hidden="true">';
                    the_post_thumbnail( 'medium', [ 'class' => 'alqamara-pw-thumb', 'loading' => 'lazy' ] );
                    echo '</a>';
                } else {
                    /* Placeholder keeps card height consistent */
                    echo '<a href="' . esc_url( $post_url ) . '" class="alqamara-pw-thumb-link alqamara-pw-no-thumb" tabindex="-1" aria-hidden="true"></a>';
                }

                /* Sale badge (percentage discount) — same top-left stack used on
                   the shop loop and single product page. */
                if ( $is_product && $product instanceof WC_Product ) {
                    $show_sale = $product->is_on_sale();

                    if ( $show_sale ) {
                        echo '<div class="alqamara-badge-stack">';

                        if ( $show_sale ) {
                            $badge_text = esc_html__( 'Sale', 'al-qamara-lite' );
                            if ( $product->is_type( 'variable' ) ) {
                                $prices = $product->get_variation_prices();
                                if ( ! empty( $prices['regular_price'] ) && ! empty( $prices['sale_price'] ) ) {
                                    $max_reg  = (float) max( $prices['regular_price'] );
                                    $min_sale = (float) min( $prices['sale_price'] );
                                    if ( $max_reg > 0 && $min_sale < $max_reg ) {
                                        $pct        = round( ( ( $max_reg - $min_sale ) / $max_reg ) * 100 );
                                        $badge_text = '-' . $pct . '%';
                                    }
                                }
                            } else {
                                $reg  = (float) $product->get_regular_price();
                                $sale = (float) $product->get_price();
                                if ( $reg > 0 && $sale < $reg ) {
                                    $pct        = round( ( ( $reg - $sale ) / $reg ) * 100 );
                                    $badge_text = '-' . $pct . '%';
                                }
                            }
                            echo '<span class="alqamara-pw-sale-badge">' . esc_html( $badge_text ) . '</span>';
                        }

                        echo '</div>'; // .alqamara-badge-stack
                    }
                }

                /* WC: hover overlay — View Details link */
                if ( $is_product && $product instanceof WC_Product ) {
                    echo '<div class="alqamara-pw-cart-overlay">';
                    printf(
                        '<a href="%s" class="alqamara-pw-view-details">%s</a>',
                        esc_url( $post_url ),
                        esc_html__( 'View Details', 'al-qamara-lite' )
                    );
                    echo '</div>'; // .alqamara-pw-cart-overlay
                }

                echo '</div>'; // .alqamara-pw-thumb-wrap
            }

            /* ── Body ── */
            echo '<div class="alqamara-pw-body d-flex flex-column flex-grow-1">';

            /* Meta row: date + category badge */
            if ( $show_date || $primary_term ) {
                echo '<div class="alqamara-pw-meta">';
                if ( $show_date ) {
                    printf(
                        '<span class="alqamara-pw-date"><i class="bi bi-calendar3 me-1" aria-hidden="true"></i>%s</span>',
                        esc_html( get_the_date() )
                    );
                }
                if ( $primary_term ) {
                    $term_url = get_term_link( $primary_term );
                    printf(
                        '<a href="%s" class="alqamara-pw-cat-badge"><i class="bi bi-folder2 me-1" aria-hidden="true"></i>%s</a>',
                        esc_url( is_wp_error( $term_url ) ? '#' : $term_url ),
                        esc_html( $primary_term->name )
                    );
                }
                echo '</div>'; // .alqamara-pw-meta
            }

            /* Title */
            printf(
                '<h3 class="alqamara-pw-title"><a href="%s">%s</a></h3>',
                esc_url( $post_url ),
                esc_html( get_the_title() )
            );

            /* Excerpt */
            if ( $show_excerpt ) {
                printf(
                    '<p class="alqamara-pw-excerpt">%s</p>',
                    esc_html( wp_trim_words( get_the_excerpt(), 15, '…' ) )
                );
            }

            /* WooCommerce: price + rating */
            if ( $is_product && $product instanceof WC_Product ) {

                if ( $show_price ) {
                    echo '<div class="alqamara-pw-price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
                }

                if ( $show_rating ) {
                    $avg    = (float) $product->get_average_rating();
                    $rcount = (int)   $product->get_rating_count();
                    if ( $avg > 0 ) {
                        /*
                         * Use Bootstrap Icons instead of wc_get_rating_html() which
                         * relies on WooCommerce's custom 'star' font — that font may
                         * not render on non-WC pages, showing raw text instead of stars.
                         */
                        $filled  = min( 5, (int) round( $avg ) );
                        $s_html  = '';
                        for ( $i = 1; $i <= 5; $i++ ) {
                            $s_html .= $i <= $filled
                                ? '<i class="bi bi-star-fill" aria-hidden="true"></i>'
                                : '<i class="bi bi-star" aria-hidden="true"></i>';
                        }
                        $label = sprintf(
                            /* translators: %s = average rating number */
                            esc_attr__( 'Rated %s out of 5', 'al-qamara-lite' ),
                            number_format( $avg, 1 )
                        );
                        echo '<div class="alqamara-pw-rating" role="img" aria-label="' . esc_attr( $label ) . '">';
                        echo '<span class="alqamara-pw-stars">' . wp_kses_post( $s_html ) . '</span>';
                        if ( $rcount > 0 ) {
                            echo '<span class="alqamara-pw-rcount" aria-hidden="true">(' . (int) $rcount . ')</span>';
                        }
                        echo '</div>';
                    }
                }

                /* Cart button in body — d-grid makes WC button full-width */
                if ( $show_cart ) {
                    echo '<div class="alqamara-pw-cart d-grid">';
                    if ( $product instanceof WC_Product ) {
                        $alqamara_ajax = $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock();
                        printf(
                            '<a href="%1$s" data-quantity="1" class="%2$s" data-product_id="%3$d" data-product_sku="%4$s" aria-label="%5$s" rel="nofollow">%6$s</a>',
                            esc_url( $product->add_to_cart_url() ),
                            esc_attr( 'button product_type_' . $product->get_type() . ( $alqamara_ajax ? ' add_to_cart_button ajax_add_to_cart' : '' ) ),
                            (int) $product->get_id(),
                            esc_attr( $product->get_sku() ),
                            esc_attr( $product->add_to_cart_description() ),
                            esc_html( $product->add_to_cart_text() )
                        );
                    }
                    echo '</div>';
                }
            }

            /* Read More (non-product) — w-100 full-width to distinguish from section cards */
            if ( $show_readmore && ! $is_product ) {
                printf(
                    '<a href="%s" class="btn btn-sm btn-outline-custom w-100 text-center mt-auto alqamara-pw-readmore">%s</a>',
                    esc_url( $post_url ),
                    esc_html( $readmore_text )
                );
            }

            echo '</div>'; // .alqamara-pw-body
            echo '</div>'; // .alqamara-pw-card
            echo '</div>'; // col / slide

        endwhile;
        wp_reset_postdata();

        if ( $enable_slider ) {
            echo '</div>'; // .swiper-wrapper
        }
        echo '</div>'; // .swiper or .alqamara-posts-grid
        echo '</div>'; // .alqamara-pw-wrap
        echo wp_kses_post( $args['after_widget'] );
    }


    /* =========================================================================
       form() — ADMIN FORM
    ========================================================================= */
    public function form( $instance ): string {

        $type_map  = $this->type_map();
        $wc_active = class_exists( 'WooCommerce' );

        /* ── Defaults ── */
        $title         = $instance['title']        ?? '';
        $post_type     = $instance['post_type']    ?? 'post';
        $saved_tax     = $instance['taxonomy']     ?? ( $type_map[ $post_type ]['taxonomy'] ?? '' );
        $term_id       = (int) ( $instance['term_id'] ?? 0 );
        $count         = (int) ( $instance['count']   ?? 3 );
        $columns       = (int) ( $instance['columns'] ?? 3 );
        $orderby       = $instance['orderby']      ?? 'date';
        $order         = $instance['order']        ?? 'DESC';

        $show_image    = isset( $instance['show_image']    ) ? (bool) $instance['show_image']    : true;
        $show_excerpt  = isset( $instance['show_excerpt']  ) ? (bool) $instance['show_excerpt']  : true;
        $show_date     = isset( $instance['show_date']     ) ? (bool) $instance['show_date']     : false;
        $show_readmore = isset( $instance['show_readmore'] ) ? (bool) $instance['show_readmore'] : true;
        $readmore_text = $instance['readmore_text'] ?? __( 'Read More', 'al-qamara-lite' );

        $show_category = isset( $instance['show_category'] ) ? (bool) $instance['show_category'] : false;
        $show_price    = isset( $instance['show_price']    ) ? (bool) $instance['show_price']    : true;
        $show_rating   = isset( $instance['show_rating']   ) ? (bool) $instance['show_rating']   : false;
        $show_cart     = isset( $instance['show_cart']     ) ? (bool) $instance['show_cart']     : true;
        $wc_show_date  = isset( $instance['wc_show_date']  ) ? (bool) $instance['wc_show_date']  : false;
        $wc_show_cat   = isset( $instance['wc_show_cat']   ) ? (bool) $instance['wc_show_cat']   : false;
        $enable_slider = isset( $instance['enable_slider'] ) ? (bool) $instance['enable_slider'] : false;
        $slider_speed  = (int) ( $instance['slider_speed'] ?? 3000 );
        $slider_auto   = isset( $instance['slider_auto']   ) ? (bool) $instance['slider_auto']   : true;
        $show_view_all = isset( $instance['show_view_all'] ) ? (bool) $instance['show_view_all'] : false;
        $view_all_text = $instance['view_all_text'] ?? __( 'View All', 'al-qamara-lite' );

        /* ── Load saved terms for the current taxonomy ── */
        $terms = [];
        if ( $saved_tax && taxonomy_exists( $saved_tax ) ) {
            $fetched = get_terms( [ 'taxonomy' => $saved_tax, 'hide_empty' => false, 'orderby' => 'name' ] );
            $terms   = is_wp_error( $fetched ) ? [] : $fetched;
        }

        /* ── Short-hands ── */
        $fid = fn( string $k ): string => $this->get_field_id( $k );
        $fn  = fn( string $k ): string => $this->get_field_name( $k );

        ?>

        <?php /* ─── Title ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('title') ); ?>">
                <?php esc_html_e( 'Title', 'al-qamara-lite' ); ?>
            </label>
            <input class="widefat" type="text"
                   id="<?php echo esc_attr( $fid('title') ); ?>"
                   name="<?php echo esc_attr( $fn('title') ); ?>"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>

        <?php /* ─── Post Type ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('post_type') ); ?>">
                <?php esc_html_e( 'Post Type', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat alqamara-pw-type-select"
                    id="<?php echo esc_attr( $fid('post_type') ); ?>"
                    name="<?php echo esc_attr( $fn('post_type') ); ?>">
                <?php foreach ( $type_map as $pt => $info ) : ?>
                <option value="<?php echo esc_attr( $pt ); ?>"<?php selected( $post_type, $pt ); ?>>
                    <?php echo esc_html( $info['label'] ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php /* ─── Category (hidden when type has no taxonomy) ─── */ ?>
        <p class="alqamara-pw-cat-wrap"<?php echo ( ! $saved_tax ) ? ' style="display:none"' : ''; ?>>
            <label for="<?php echo esc_attr( $fid('term_id') ); ?>">
                <?php esc_html_e( 'Filter by Category', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat alqamara-pw-term-select"
                    id="<?php echo esc_attr( $fid('term_id') ); ?>"
                    name="<?php echo esc_attr( $fn('term_id') ); ?>">
                <option value="0"><?php esc_html_e( '— All —', 'al-qamara-lite' ); ?></option>
                <?php foreach ( $terms as $term ) : ?>
                <option value="<?php echo esc_attr( $term->term_id ); ?>"
                        <?php selected( $term_id, $term->term_id ); ?>>
                    <?php echo esc_html( $term->name ); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php /* Hidden: stores current taxonomy so update() can save it */ ?>
            <input type="hidden" class="alqamara-pw-tax-input"
                   id="<?php echo esc_attr( $fid('taxonomy') ); ?>"
                   name="<?php echo esc_attr( $fn('taxonomy') ); ?>"
                   value="<?php echo esc_attr( $saved_tax ); ?>">
        </p>

        <?php /* ─── Item Count ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('count') ); ?>">
                <?php esc_html_e( 'Number of Items', 'al-qamara-lite' ); ?>
            </label>
            <input class="tiny-text" type="number" min="1" max="24"
                   id="<?php echo esc_attr( $fid('count') ); ?>"
                   name="<?php echo esc_attr( $fn('count') ); ?>"
                   value="<?php echo esc_attr( $count ); ?>">
        </p>

        <?php /* ─── Columns ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('columns') ); ?>">
                <?php esc_html_e( 'Columns', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $fid('columns') ); ?>"
                    name="<?php echo esc_attr( $fn('columns') ); ?>">
                <?php foreach ( [ 1, 2, 3, 4 ] as $n ) : ?>
                <option value="<?php echo (int) $n; ?>"<?php selected( $columns, $n ); ?>>
                    <?php echo (int) $n; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php /* ─── Order By ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('orderby') ); ?>">
                <?php esc_html_e( 'Order By', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $fid('orderby') ); ?>"
                    name="<?php echo esc_attr( $fn('orderby') ); ?>">
                <?php foreach ( [
                    'date'          => __( 'Date',             'al-qamara-lite' ),
                    'title'         => __( 'Title',            'al-qamara-lite' ),
                    'menu_order'    => __( 'Menu Order',       'al-qamara-lite' ),
                    'rand'          => __( 'Random',           'al-qamara-lite' ),
                    'comment_count' => __( 'Most Commented',   'al-qamara-lite' ),
                    'popularity'    => __( 'Top Selling (WC)', 'al-qamara-lite' ),
                    'rating'        => __( 'Top Rated (WC)',   'al-qamara-lite' ),
                ] as $v => $l ) : ?>
                <option value="<?php echo esc_attr( $v ); ?>"<?php selected( $orderby, $v ); ?>>
                    <?php echo esc_html( $l ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php /* ─── Order (ASC / DESC) ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('order') ); ?>">
                <?php esc_html_e( 'Order', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $fid('order') ); ?>"
                    name="<?php echo esc_attr( $fn('order') ); ?>">
                <option value="DESC"<?php selected( $order, 'DESC' ); ?>>
                    <?php esc_html_e( 'Newest First (DESC)', 'al-qamara-lite' ); ?>
                </option>
                <option value="ASC"<?php selected( $order, 'ASC' ); ?>>
                    <?php esc_html_e( 'Oldest First (ASC)', 'al-qamara-lite' ); ?>
                </option>
            </select>
        </p>

        <hr class="alqamara-w-sep">
        <p class="alqamara-w-head"><?php esc_html_e( 'Display', 'al-qamara-lite' ); ?></p>

        <?php
        $this->cb( $fid('show_image'),    $fn('show_image'),    $show_image,    __( 'Show Thumbnail',        'al-qamara-lite' ) );
        $this->cb( $fid('show_date'),     $fn('show_date'),     $show_date,     __( 'Show Date',             'al-qamara-lite' ) );
        $this->cb( $fid('show_category'), $fn('show_category'), $show_category, __( 'Show Category Badge',   'al-qamara-lite' ) );
        $this->cb( $fid('show_excerpt'),  $fn('show_excerpt'),  $show_excerpt,  __( 'Show Excerpt',          'al-qamara-lite' ) );
        $this->cb( $fid('show_readmore'), $fn('show_readmore'), $show_readmore, __( 'Show Read More Button', 'al-qamara-lite' ), 'alqamara-pw-readmore-chk' );
        ?>

        <p class="alqamara-pw-readmore-row"<?php echo ( ! $show_readmore ) ? ' style="display:none"' : ''; ?>>
            <label for="<?php echo esc_attr( $fid('readmore_text') ); ?>">
                <?php esc_html_e( 'Read More Text', 'al-qamara-lite' ); ?>
            </label>
            <input class="widefat" type="text"
                   id="<?php echo esc_attr( $fid('readmore_text') ); ?>"
                   name="<?php echo esc_attr( $fn('readmore_text') ); ?>"
                   value="<?php echo esc_attr( $readmore_text ); ?>">
        </p>

        <hr class="alqamara-w-sep">
        <p class="alqamara-w-head"><?php esc_html_e( 'Section Header', 'al-qamara-lite' ); ?></p>

        <?php $this->cb( $fid('show_view_all'), $fn('show_view_all'), $show_view_all, __( 'Show "View All" Button (auto-links to archive/category)', 'al-qamara-lite' ) ); ?>

        <p class="alqamara-w-hint">
            <?php esc_html_e( 'Only visible when total posts exceed the displayed count.', 'al-qamara-lite' ); ?>
        </p>

        <p>
            <label for="<?php echo esc_attr( $fid('view_all_text') ); ?>">
                <?php esc_html_e( 'Button Label', 'al-qamara-lite' ); ?>
            </label>
            <input class="widefat" type="text"
                   id="<?php echo esc_attr( $fid('view_all_text') ); ?>"
                   name="<?php echo esc_attr( $fn('view_all_text') ); ?>"
                   value="<?php echo esc_attr( $view_all_text ); ?>">
        </p>

        <hr class="alqamara-w-sep">
        <p class="alqamara-w-head"><?php esc_html_e( 'Slider', 'al-qamara-lite' ); ?></p>

        <?php $this->cb( $fid('enable_slider'), $fn('enable_slider'), $enable_slider, __( 'Enable Slider (Swiper)', 'al-qamara-lite' ), 'alqamara-pw-slider-chk' ); ?>

        <div class="alqamara-pw-slider-opts"<?php echo ( ! $enable_slider ) ? ' style="display:none"' : ''; ?>>
            <?php $this->cb( $fid('slider_auto'), $fn('slider_auto'), $slider_auto, __( 'Autoplay', 'al-qamara-lite' ) ); ?>
            <p>
                <label for="<?php echo esc_attr( $fid('slider_speed') ); ?>">
                    <?php esc_html_e( 'Autoplay Speed (ms)', 'al-qamara-lite' ); ?>
                </label>
                <input class="tiny-text" type="number" min="500" step="100"
                       id="<?php echo esc_attr( $fid('slider_speed') ); ?>"
                       name="<?php echo esc_attr( $fn('slider_speed') ); ?>"
                       value="<?php echo esc_attr( $slider_speed ); ?>">
            </p>
        </div>

        <?php if ( $wc_active ) : ?>

        <hr class="alqamara-w-sep">
        <p class="alqamara-w-head"><?php esc_html_e( 'WooCommerce', 'al-qamara-lite' ); ?></p>

        <?php
        $this->cb( $fid('wc_show_date'), $fn('wc_show_date'), $wc_show_date, __( 'Show Date',             'al-qamara-lite' ) );
        $this->cb( $fid('wc_show_cat'),  $fn('wc_show_cat'),  $wc_show_cat,  __( 'Show Category',        'al-qamara-lite' ) );
        $this->cb( $fid('show_price'),   $fn('show_price'),   $show_price,   __( 'Show Price',            'al-qamara-lite' ) );
        $this->cb( $fid('show_rating'),  $fn('show_rating'),  $show_rating,  __( 'Show Rating',           'al-qamara-lite' ) );
        $this->cb( $fid('show_cart'),    $fn('show_cart'),    $show_cart,    __( 'Show Add to Cart Button','al-qamara-lite' ) );
        ?>

        <?php endif; // wc_active ?>

        <?php
        return '';
    }

    /** DRY helper — output one checkbox row */
    private function cb(
        string $id,
        string $name,
        bool   $checked,
        string $label,
        string $extra_class = ''
    ): void { ?>
        <p class="alqamara-w-tight">
            <label for="<?php echo esc_attr( $id ); ?>">
                <input type="checkbox"
                       id="<?php echo esc_attr( $id ); ?>"
                       name="<?php echo esc_attr( $name ); ?>"
                       value="1"
                       <?php echo $extra_class ? 'class="' . esc_attr( $extra_class ) . '"' : ''; ?>
                       <?php checked( $checked ); ?>>
                <?php echo esc_html( $label ); ?>
            </label>
        </p>
    <?php }


    /* =========================================================================
       update() — SANITIZE & SAVE
    ========================================================================= */
    public function update( $new, $old ): array {
        return [
            'title'         => sanitize_text_field( $new['title']        ?? '' ),
            'post_type'     => sanitize_key(         $new['post_type']    ?? 'post' ),
            'taxonomy'      => sanitize_key(         $new['taxonomy']     ?? '' ),
            'term_id'       => absint(               $new['term_id']      ?? 0 ),
            'count'         => min( 24, absint(      $new['count']        ?? 3 ) ),
            'columns'       => min( 4,  absint(      $new['columns']      ?? 3 ) ),
            'orderby'       => sanitize_key(         $new['orderby']      ?? 'date' ),
            'order'         => in_array( $new['order'] ?? '', [ 'ASC', 'DESC' ], true )
                                   ? $new['order'] : 'DESC',
            'show_image'    => ! empty( $new['show_image']    ),
            'show_date'     => ! empty( $new['show_date']     ),
            'show_category' => ! empty( $new['show_category'] ),
            'show_excerpt'  => ! empty( $new['show_excerpt']  ),
            'show_readmore' => ! empty( $new['show_readmore'] ),
            'readmore_text' => sanitize_text_field( $new['readmore_text'] ?? __( 'Read More', 'al-qamara-lite' ) ),
            'show_price'    => ! empty( $new['show_price']    ),
            'show_rating'   => ! empty( $new['show_rating']   ),
            'show_cart'     => ! empty( $new['show_cart']     ),
            'wc_show_date'  => ! empty( $new['wc_show_date']  ),
            'wc_show_cat'   => ! empty( $new['wc_show_cat']   ),
            'enable_slider' => ! empty( $new['enable_slider'] ),
            'slider_speed'  => max( 500, absint( $new['slider_speed'] ?? 3000 ) ),
            'slider_auto'   => ! empty( $new['slider_auto']   ),
            'show_view_all' => ! empty( $new['show_view_all'] ),
            'view_all_text' => sanitize_text_field( $new['view_all_text'] ?? __( 'View All', 'al-qamara-lite' ) ),
        ];
    }

} // end class Alqamara_Posts_Widget

endif; // class_exists


/* =============================================================================
   WIDGET CLASS — Category Showcase
============================================================================= */

if ( ! class_exists( 'Alqamara_Category_Showcase_Widget' ) ) :

class Alqamara_Category_Showcase_Widget extends WP_Widget {

    /* -------------------------------------------------------------------------
       Constructor
    ------------------------------------------------------------------------- */
    public function __construct() {
        parent::__construct(
            'alqamara_category_showcase_widget',
            __( 'Al Qamara: Category Showcase', 'al-qamara-lite' ),
            [
                'description' => __( 'Show WooCommerce product category thumbnails (or a list of blog categories) in a grid or slider. Pick which categories to feature.', 'al-qamara-lite' ),
                'classname'   => 'alqamara-cat-showcase-widget',
            ]
        );
    }


    /* -------------------------------------------------------------------------
       taxonomy_map() — which taxonomies this widget can showcase.
       'category' (blog) is always available; 'product_cat' only if
       WooCommerce is active — same conditional pattern as
       Alqamara_Posts_Widget::type_map().
    ------------------------------------------------------------------------- */
    private function taxonomy_map(): array {
        $map = [
            'category' => __( 'Blog Categories', 'al-qamara-lite' ),
        ];
        if ( class_exists( 'WooCommerce' ) ) {
            $map['product_cat'] = __( 'Product Categories', 'al-qamara-lite' );
        }
        return $map;
    }

    private function default_taxonomy(): string {
        return class_exists( 'WooCommerce' ) ? 'product_cat' : 'category';
    }

    /* Count-badge wording differs by taxonomy ("Products" vs "Posts"). */
    private function count_label( string $taxonomy, int $count ): string {
        if ( 'product_cat' === $taxonomy ) {
            /* translators: %d = number of products in this category */
            return sprintf( _n( '%d Product', '%d Products', $count, 'al-qamara-lite' ), $count );
        }
        /* translators: %d = number of posts in this category */
        return sprintf( _n( '%d Post', '%d Posts', $count, 'al-qamara-lite' ), $count );
    }

    /*
     * Thumbnail: only WooCommerce product categories have an image (the
     * native 'thumbnail_id' term meta, set on Products > Categories). Blog
     * categories return '' and are drawn with the generic no-image icon.
     */
    private function term_thumbnail_url( WP_Term $term, string $taxonomy ): string {
        if ( 'product_cat' !== $taxonomy ) {
            return '';
        }
        $meta_key = 'thumbnail_id';
        $thumb_id = (int) get_term_meta( $term->term_id, $meta_key, true );

        if ( $thumb_id ) {
            $url = wp_get_attachment_image_url( $thumb_id, 'medium' );
            if ( $url ) {
                return $url;
            }
        }

        if ( 'product_cat' === $taxonomy && function_exists( 'wc_placeholder_img_src' ) ) {
            return wc_placeholder_img_src( 'medium' );
        }

        return ''; // → .alqamara-cs-no-thumb icon placeholder in the markup below
    }


    /* -------------------------------------------------------------------------
       get_instance_schema() — REST API raw instance support
    ------------------------------------------------------------------------- */
    public function get_instance_schema(): array {
        return [
            'title'         => [ 'type' => 'string',  'default' => '' ],
            'taxonomy'      => [ 'type' => 'string',  'default' => $this->default_taxonomy() ],
            'categories'    => [
                'type'  => 'array',
                'items' => [ 'type' => 'integer' ],
                'default' => [],
            ],
            'columns'       => [ 'type' => 'integer', 'default' => 4 ],
            'limit'         => [ 'type' => 'integer', 'default' => 8 ],
            'orderby'       => [ 'type' => 'string',  'default' => 'name' ],
            'show_count'    => [ 'type' => 'boolean', 'default' => true ],
            'style'         => [ 'type' => 'string',  'default' => 'box' ],
            'enable_slider' => [ 'type' => 'boolean', 'default' => false ],
            'slider_auto'   => [ 'type' => 'boolean', 'default' => true ],
            'slider_speed'  => [ 'type' => 'integer', 'default' => 3000 ],
        ];
    }


    /* =========================================================================
       widget() — FRONT-END RENDER
    ========================================================================= */
    public function widget( $args, $instance ): void {

        $tax_map  = $this->taxonomy_map();
        $taxonomy = $instance['taxonomy'] ?? $this->default_taxonomy();
        if ( ! array_key_exists( $taxonomy, $tax_map ) || ! taxonomy_exists( $taxonomy ) ) {
            return; // e.g. saved as product_cat, WooCommerce later deactivated
        }

        /* ── Read settings ── */
        $title         = apply_filters( 'widget_title', $instance['title'] ?? '' );
        $selected_ids  = array_filter( array_map( 'absint', (array) ( $instance['categories'] ?? [] ) ) );
        $columns       = (int) ( $instance['columns'] ?? 4 );
        $limit         = (int) ( $instance['limit']   ?? 8 );
        $orderby       = $instance['orderby'] ?? 'name';
        $show_count    = ! empty( $instance['show_count'] );
        $style_raw     = $instance['style'] ?? 'box';
        $style         = in_array( $style_raw, [ 'box', 'circle' ], true ) ? $style_raw : 'box';
        $enable_slider = ! empty( $instance['enable_slider'] );
        $slider_auto   = ! empty( $instance['slider_auto'] );
        $slider_speed  = (int) ( $instance['slider_speed'] ?? 3000 );

        /*
         * Narrow sidebars (blog sidebar, shop sidebar, footer columns):
         * force single-column stacked layout + no slider — same rule as
         * Alqamara_Posts_Widget (a slider makes no sense in one narrow column).
         */
        $narrow_sidebars = [
            'sidebar-blog', 'sidebar-shop',
            'footer-col-1', 'footer-col-2', 'footer-col-3', 'footer-col-4',
            'footer-legal',
        ];
        if ( in_array( $args['id'] ?? '', $narrow_sidebars, true ) ) {
            $columns       = 1;
            $enable_slider = false;
        }
        $columns = max( 1, min( 6, $columns ) );

        /* ── Fetch categories ── */
        $term_args = [
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ];

        if ( $selected_ids ) {
            // Specific categories chosen — preserve checkbox-list order unless "By Count" is picked.
            $term_args['include'] = $selected_ids;
            $term_args['orderby'] = ( 'count' === $orderby ) ? 'count' : 'include';
            $term_args['order']   = ( 'count' === $orderby ) ? 'DESC' : 'ASC';
        } else {
            // Nothing checked yet — fall back to top categories so the widget isn't blank.
            $term_args['number']  = max( 1, min( 24, $limit ) );
            $term_args['orderby'] = ( 'count' === $orderby ) ? 'count' : 'name';
            $term_args['order']   = ( 'count' === $orderby ) ? 'DESC' : 'ASC';

            $default_term_id = 0;
            if ( 'category' === $taxonomy ) {
                $default_term_id = (int) get_option( 'default_category' );     // "Uncategorized"
            } elseif ( 'product_cat' === $taxonomy ) {
                $default_term_id = (int) get_option( 'default_product_cat' );
            }
            if ( $default_term_id ) {
                $term_args['exclude'] = [ $default_term_id ];
            }
        }

        $terms = get_terms( $term_args );
        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return;
        }

        // A slider needs more than one real slide to make sense.
        if ( $enable_slider && count( $terms ) < 2 ) {
            $enable_slider = false;
        }

        /* ── Widget shell ── */
        echo wp_kses_post( $args['before_widget'] );
        echo '<div class="alqamara-cs-wrap alqamara-cs-style-' . esc_attr( $style ) . '">';

        /* ── Section head: title LEFT + slider nav RIGHT (reuses the Posts
           Widget's nav-button markup/classes/JS — purely presentational,
           not post-specific, so nothing new needed for it to work here). */
        $has_head = ( $title || $enable_slider );
        if ( $has_head ) {
            echo '<div class="alqamara-cs-section-head">';

            if ( $title ) {
                echo '<h3 class="alqamara-cs-widget-title">' . esc_html( $title ) . '</h3>';
            } else {
                echo '<div></div>';
            }

            if ( $enable_slider ) {
                echo '<div class="alqamara-pw-slider-nav-btns">';
                printf(
                    '<button class="alqamara-pw-nav-btn alqamara-pw-prev" aria-label="%s"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>',
                    esc_attr__( 'Previous', 'al-qamara-lite' )
                );
                printf(
                    '<button class="alqamara-pw-nav-btn alqamara-pw-next" aria-label="%s"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>',
                    esc_attr__( 'Next', 'al-qamara-lite' )
                );
                echo '</div>';
            }

            echo '</div>'; // .alqamara-cs-section-head
        } elseif ( $title ) {
            echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
        }

        /* ── Outer wrapper: Swiper slider or CSS Grid ── */
        if ( $enable_slider ) {
            if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
                wp_enqueue_style( 'swiper' );
                wp_enqueue_script( 'swiper' );
            }
            printf(
                '<div class="swiper alqamara-cs-slider" data-swiper-items="%d" data-swiper-speed="%d" data-swiper-auto="%s" data-swiper-count="%d" data-swiper-mobile-cols="2" style="--alqamara-cols:%d">',
                (int) $columns,
                (int) $slider_speed,
                esc_attr( $slider_auto ? 'true' : 'false' ),
                count( $terms ),
                (int) $columns
            );
            echo '<div class="swiper-wrapper">';
        } else {
            echo '<div class="alqamara-cs-grid" style="--alqamara-cols:' . (int) $columns . '">';
        }

        /* ── Loop ── */
        foreach ( $terms as $term ) {
            $link = get_term_link( $term );
            if ( is_wp_error( $link ) ) {
                continue;
            }

            $img_url = $this->term_thumbnail_url( $term, $taxonomy );

            echo $enable_slider ? '<div class="swiper-slide alqamara-cs-slide">' : '';

            echo '<a href="' . esc_url( $link ) . '" class="alqamara-cs-card">';

            echo '<div class="alqamara-cs-thumb-wrap">';
            if ( $img_url ) {
                printf(
                    '<img src="%s" alt="%s" class="alqamara-cs-thumb" loading="lazy">',
                    esc_url( $img_url ),
                    esc_attr( $term->name )
                );
            } else {
                echo '<span class="alqamara-cs-thumb alqamara-cs-no-thumb" aria-hidden="true"></span>';
            }
            echo '<div class="alqamara-cs-overlay">';
            echo '<span class="alqamara-cs-name">' . esc_html( $term->name ) . '</span>';
            if ( $show_count ) {
                echo '<span class="alqamara-cs-count">' . esc_html( $this->count_label( $taxonomy, (int) $term->count ) ) . '</span>';
            }
            echo '</div>'; // .alqamara-cs-overlay
            echo '</div>'; // .alqamara-cs-thumb-wrap

            echo '</a>'; // .alqamara-cs-card

            echo $enable_slider ? '</div>' : ''; // .swiper-slide / .alqamara-cs-slide
        }

        if ( $enable_slider ) {
            echo '</div>'; // .swiper-wrapper
        }
        echo '</div>'; // .alqamara-cs-grid or .swiper
        echo '</div>'; // .alqamara-cs-wrap
        echo wp_kses_post( $args['after_widget'] );
    }


    /* =========================================================================
       form() — ADMIN FORM
    ========================================================================= */
    public function form( $instance ): string {

        $tax_map     = $this->taxonomy_map();
        $taxonomy    = $instance['taxonomy'] ?? $this->default_taxonomy();
        if ( ! array_key_exists( $taxonomy, $tax_map ) ) {
            $taxonomy = $this->default_taxonomy();
        }

        $title         = $instance['title']      ?? '';
        $selected      = array_map( 'absint', (array) ( $instance['categories'] ?? [] ) );
        $columns       = (int) ( $instance['columns'] ?? 4 );
        $limit         = (int) ( $instance['limit']   ?? 8 );
        $orderby       = $instance['orderby'] ?? 'name';
        $show_count    = isset( $instance['show_count'] ) ? (bool) $instance['show_count'] : true;
        $style_raw     = $instance['style'] ?? 'box';
        $style         = in_array( $style_raw, [ 'box', 'circle' ], true ) ? $style_raw : 'box';
        $enable_slider = ! empty( $instance['enable_slider'] );
        $slider_auto   = isset( $instance['slider_auto'] ) ? (bool) $instance['slider_auto'] : true;
        $slider_speed  = (int) ( $instance['slider_speed'] ?? 3000 );

        $fid = fn( string $k ): string => $this->get_field_id( $k );
        $fn  = fn( string $k ): string => $this->get_field_name( $k );

        $all_terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ] );
        $all_terms = is_wp_error( $all_terms ) ? [] : $all_terms;

        ?>

        <?php /* ─── Title ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('title') ); ?>">
                <?php esc_html_e( 'Title', 'al-qamara-lite' ); ?>
            </label>
            <input class="widefat" type="text"
                   id="<?php echo esc_attr( $fid('title') ); ?>"
                   name="<?php echo esc_attr( $fn('title') ); ?>"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>

        <?php /* ─── Category Type (taxonomy) ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('taxonomy') ); ?>">
                <?php esc_html_e( 'Category Type', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat alqamara-cs-tax-select"
                    id="<?php echo esc_attr( $fid('taxonomy') ); ?>"
                    name="<?php echo esc_attr( $fn('taxonomy') ); ?>">
                <?php foreach ( $tax_map as $tax_key => $tax_label ) : ?>
                <option value="<?php echo esc_attr( $tax_key ); ?>"<?php selected( $taxonomy, $tax_key ); ?>>
                    <?php echo esc_html( $tax_label ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php /* ─── Category picker (rebuilt by JS when Category Type changes) ─── */ ?>
        <p>
            <label><?php esc_html_e( 'Categories to Feature', 'al-qamara-lite' ); ?></label>
            <span class="alqamara-w-note">
                <?php esc_html_e( 'Leave all unchecked to auto-show top categories.', 'al-qamara-lite' ); ?>
            </span>
            <span class="alqamara-cs-cat-list"
                  data-name="<?php echo esc_attr( $fn('categories') ); ?>">
                <?php if ( empty( $all_terms ) ) : ?>
                    <em><?php esc_html_e( 'No categories found.', 'al-qamara-lite' ); ?></em>
                <?php endif; ?>
                <?php foreach ( $all_terms as $term ) : ?>
                    <label>
                        <input type="checkbox"
                               name="<?php echo esc_attr( $fn('categories') ); ?>[]"
                               value="<?php echo esc_attr( $term->term_id ); ?>"
                               <?php checked( in_array( $term->term_id, $selected, true ) ); ?>>
                        <?php echo esc_html( $term->name ); ?>
                        <span class="alqamara-cs-count">(<?php echo (int) $term->count; ?>)</span>
                    </label>
                <?php endforeach; ?>
            </span>
        </p>

        <?php /* ─── Limit (fallback when none selected) ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('limit') ); ?>">
                <?php esc_html_e( 'Number to Show (if none checked above)', 'al-qamara-lite' ); ?>
            </label>
            <input class="tiny-text" type="number" min="1" max="24" step="1"
                   id="<?php echo esc_attr( $fid('limit') ); ?>"
                   name="<?php echo esc_attr( $fn('limit') ); ?>"
                   value="<?php echo esc_attr( $limit ); ?>">
        </p>

        <?php /* ─── Columns ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('columns') ); ?>">
                <?php esc_html_e( 'Columns', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $fid('columns') ); ?>" name="<?php echo esc_attr( $fn('columns') ); ?>">
                <?php foreach ( [ 2, 3, 4, 5, 6 ] as $c ) : ?>
                <option value="<?php echo esc_attr( $c ); ?>"<?php selected( $columns, $c ); ?>><?php echo esc_html( $c ); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="alqamara-w-note alqamara-w-note--top">
                <?php esc_html_e( 'Automatically forced to 1 in sidebars and footer columns. If a slider is enabled below and more categories exist than fit in these columns, it will slide through the rest.', 'al-qamara-lite' ); ?>
            </span>
        </p>

        <?php /* ─── Order By ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('orderby') ); ?>">
                <?php esc_html_e( 'Order By', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $fid('orderby') ); ?>" name="<?php echo esc_attr( $fn('orderby') ); ?>">
                <option value="name"<?php selected( $orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'al-qamara-lite' ); ?></option>
                <option value="count"<?php selected( $orderby, 'count' ); ?>><?php esc_html_e( 'Item Count', 'al-qamara-lite' ); ?></option>
            </select>
        </p>

        <?php /* ─── Design Style ─── */ ?>
        <p>
            <label for="<?php echo esc_attr( $fid('style') ); ?>">
                <?php esc_html_e( 'Design Style', 'al-qamara-lite' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $fid('style') ); ?>" name="<?php echo esc_attr( $fn('style') ); ?>">
                <option value="box"<?php selected( $style, 'box' ); ?>><?php esc_html_e( 'Box / Overlay (image with name on top)', 'al-qamara-lite' ); ?></option>
                <option value="circle"<?php selected( $style, 'circle' ); ?>><?php esc_html_e( 'Circle / Outline (round image, name below)', 'al-qamara-lite' ); ?></option>
            </select>
        </p>

        <?php /* ─── Show Count ─── */ ?>
        <p class="alqamara-w-tight">
            <label for="<?php echo esc_attr( $fid('show_count') ); ?>">
                <input type="checkbox"
                       id="<?php echo esc_attr( $fid('show_count') ); ?>"
                       name="<?php echo esc_attr( $fn('show_count') ); ?>"
                       value="1"
                       <?php checked( $show_count ); ?>>
                <?php esc_html_e( 'Show Item Count', 'al-qamara-lite' ); ?>
            </label>
        </p>

        <hr class="alqamara-w-sep">
        <p class="alqamara-w-head"><?php esc_html_e( 'Slider', 'al-qamara-lite' ); ?></p>

        <?php /* ─── Slider (reuses Posts Widget's .alqamara-pw-slider-chk / .alqamara-pw-slider-opts
                 JS toggle — purely generic show/hide behaviour, nothing post-specific) ─── */ ?>
        <p class="alqamara-w-tight">
            <label for="<?php echo esc_attr( $fid('enable_slider') ); ?>">
                <input type="checkbox"
                       id="<?php echo esc_attr( $fid('enable_slider') ); ?>"
                       name="<?php echo esc_attr( $fn('enable_slider') ); ?>"
                       value="1"
                       class="alqamara-pw-slider-chk"
                       <?php checked( $enable_slider ); ?>>
                <?php esc_html_e( 'Enable Slider (Swiper)', 'al-qamara-lite' ); ?>
            </label>
        </p>
        <p class="alqamara-w-hint">
            <?php esc_html_e( 'Only slides when more categories are shown than fit in the selected columns; otherwise it just displays statically. Disabled automatically in sidebars/footer columns.', 'al-qamara-lite' ); ?>
        </p>

        <div class="alqamara-pw-slider-opts"<?php echo ( ! $enable_slider ) ? ' style="display:none"' : ''; ?>>
            <p class="alqamara-w-tight">
                <label for="<?php echo esc_attr( $fid('slider_auto') ); ?>">
                    <input type="checkbox"
                           id="<?php echo esc_attr( $fid('slider_auto') ); ?>"
                           name="<?php echo esc_attr( $fn('slider_auto') ); ?>"
                           value="1"
                           <?php checked( $slider_auto ); ?>>
                    <?php esc_html_e( 'Autoplay', 'al-qamara-lite' ); ?>
                </label>
            </p>
            <p>
                <label for="<?php echo esc_attr( $fid('slider_speed') ); ?>">
                    <?php esc_html_e( 'Autoplay Speed (ms)', 'al-qamara-lite' ); ?>
                </label>
                <input class="tiny-text" type="number" min="500" step="100"
                       id="<?php echo esc_attr( $fid('slider_speed') ); ?>"
                       name="<?php echo esc_attr( $fn('slider_speed') ); ?>"
                       value="<?php echo esc_attr( $slider_speed ); ?>">
            </p>
        </div>

        <?php
        return '';
    }


    /* =========================================================================
       update() — SANITIZE & SAVE
    ========================================================================= */
    public function update( $new, $old ): array {
        $tax_map  = $this->taxonomy_map();
        $taxonomy = ( isset( $new['taxonomy'] ) && array_key_exists( $new['taxonomy'], $tax_map ) )
            ? $new['taxonomy']
            : $this->default_taxonomy();

        return [
            'title'         => sanitize_text_field( $new['title'] ?? '' ),
            'taxonomy'      => $taxonomy,
            'categories'    => array_filter( array_map( 'absint', (array) ( $new['categories'] ?? [] ) ) ),
            'columns'       => max( 1, min( 6, absint( $new['columns'] ?? 4 ) ) ),
            'limit'         => max( 1, min( 24, absint( $new['limit'] ?? 8 ) ) ),
            'orderby'       => in_array( $new['orderby'] ?? '', [ 'name', 'count' ], true ) ? $new['orderby'] : 'name',
            'show_count'    => ! empty( $new['show_count'] ),
            'style'         => in_array( $new['style'] ?? '', [ 'box', 'circle' ], true ) ? $new['style'] : 'box',
            'enable_slider' => ! empty( $new['enable_slider'] ),
            'slider_auto'   => ! empty( $new['slider_auto'] ),
            'slider_speed'  => max( 500, absint( $new['slider_speed'] ?? 3000 ) ),
        ];
    }

} // end class Alqamara_Category_Showcase_Widget

endif; // class_exists


/* =============================================================================
   REGISTER
============================================================================= */

add_action( 'widgets_init', function() {
    register_widget( 'Alqamara_Posts_Widget' );
    register_widget( 'Alqamara_Category_Showcase_Widget' );
} );
