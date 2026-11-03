/**
 * =============================================================================
 *
 *  Al Qamara — main.js
 *
 *  @package alqamara
 *
 * =============================================================================
 */

( function( $ ) {
    'use strict';

    /* =========================================================================
       1. SPINNER — hide as soon as DOM is ready (don't wait for all resources)
    ========================================================================= */
    /* Hide spinner: remove .show (triggers CSS fade-out) and immediately kill
       pointer-events so the fading overlay never blocks clicks on page content. */
    function alqamaraHideSpinner() {
        $( '#spinner' ).removeClass( 'show' ).css( 'pointer-events', 'none' );
    }
    $( document ).ready( alqamaraHideSpinner );
    $( window ).on( 'load', alqamaraHideSpinner );




    /* =========================================================================
       1c. HEADER HEIGHT — measure topbar + navbar, set CSS custom property
       Used by hero section to pull itself up behind the transparent header.
    ========================================================================= */
    function alqamaraSyncHeaderHeight() {
        var topH = 0;
        var $tb  = $( '.alqamara-topbar' );
        if ( $tb.length && $tb[0].offsetHeight > 0 ) {
            topH = $tb.outerHeight( true ) || 0;
        }
        var navH  = $( '.alqamara-navbar-wrap' ).outerHeight( true ) || 0;
        var total = topH + navH;
        document.documentElement.style.setProperty( '--alqamara-header-total-h', total + 'px' );
    }
    $( window ).on( 'load resize', alqamaraSyncHeaderHeight );
    alqamaraSyncHeaderHeight();


    /* =========================================================================
       2. NAVBAR SCROLL — add .scrolled class on scroll + hide-on-scroll-down on mobile
    ========================================================================= */
    var lastScrollTop = 0;

    $( window ).on( 'scroll', function() {
        var st = $( this ).scrollTop();

        // Scrolled background
        if ( st > 50 ) {
            $( '.navbar-transparent' ).addClass( 'scrolled' );
        } else {
            $( '.navbar-transparent' ).removeClass( 'scrolled' );
        }

        // Hide on scroll down / show on scroll up (mobile only)
        if ( $( window ).width() <= 991 ) {
            if ( st > lastScrollTop && st > 100 ) {
                $( '.alqamara-navbar-wrap' ).addClass( 'navbar-hidden' );
            } else {
                $( '.alqamara-navbar-wrap' ).removeClass( 'navbar-hidden' );
            }
        }

        lastScrollTop = st <= 0 ? 0 : st;
    } );


    /* =========================================================================
       2b. ADMIN BAR ≤600px FIX
       At ≤600px the WP admin bar is position:absolute (46px tall, scrolls away).
       CSS keeps our header at top:46px so it's visible below the admin bar.
       Once the admin bar scrolls out of view we add .admin-bar-gone to <body>
       so CSS transitions the header up to top:0.
    ========================================================================= */
    if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
        function alqamaraAdminBarFix() {
            if ( window.innerWidth > 600 ) {
                $( 'body' ).removeClass( 'admin-bar-gone' );
                return;
            }
            if ( $( window ).scrollTop() >= 46 ) {
                $( 'body' ).addClass( 'admin-bar-gone' );
            } else {
                $( 'body' ).removeClass( 'admin-bar-gone' );
            }
        }
        $( window ).on( 'scroll resize', alqamaraAdminBarFix );
        alqamaraAdminBarFix();
    }




    /* =========================================================================
       2c. WC PRODUCT-FILTER OVERFLOW FIX
       WooCommerce product-filter Blocks set body { overflow: auto } on init,
       which makes the body the scroll container and breaks position:sticky on
       the header. Fix: watch for that specific value and remove it.
       We leave overflow:hidden alone — that's used by mobile menu / cart drawer
       to prevent body scroll when overlays are open.
       NOTE: $body is defined later (section 6), so we use document.body directly.
    ========================================================================= */
    if ( document.body.classList.contains( 'woocommerce-active' ) ) {
        function alqamaraFixBodyOverflow() {
            if ( document.body.style.overflow === 'auto' ) {
                document.body.style.overflow = '';
            }
        }
        // Fix already-set state immediately
        alqamaraFixBodyOverflow();
        // Watch future changes from WC filter JS
        new MutationObserver( function() {
            alqamaraFixBodyOverflow();
        } ).observe( document.body, { attributes: true, attributeFilter: [ 'style' ] } );
    }


    /* =========================================================================
       2d. DESKTOP DROPDOWN — KEYBOARD ACCESSIBILITY
       These panels open on CSS :hover, and :focus-within (see custom.css)
       already makes them open when keyboard focus lands anywhere inside —
       that alone covers Tab-in / Tab-through / Tab-out. What CSS can't do:
       keep aria-expanded truthful, and let Escape close an open panel and
       return focus to its trigger link (rather than leaving focus buried
       inside a panel the user no longer wants open).
    ========================================================================= */
    $( '.navbar-nav > .dropdown' ).each( function() {
        var $wrap   = $( this );
        var $toggle = $wrap.children( '.nav-link' );

        function setExpanded( isOpen ) {
            $toggle.attr( 'aria-expanded', isOpen ? 'true' : 'false' );
        }

        $wrap.on( 'mouseenter focusin', function() {
            setExpanded( true );
        } );

        $wrap.on( 'mouseleave focusout', function() {
            // Defer: on focusout, the newly-focused element isn't committed
            // yet, so check on the next tick whether focus is still inside.
            setTimeout( function() {
                if ( ! $wrap.is( ':hover' ) && $( document.activeElement ).closest( $wrap ).length === 0 ) {
                    setExpanded( false );
                }
            }, 0 );
        } );

        $wrap.on( 'keydown', function( e ) {
            if ( e.key === 'Escape' || e.keyCode === 27 ) {
                setExpanded( false );
                // Blur whatever's focused inside the panel — CSS :focus-within
                // then releases and the panel closes. (The toggle itself is
                // also inside the dropdown, so re-focusing it wouldn't
                // actually close the panel — blurring is what closes it.)
                if ( document.activeElement ) {
                    document.activeElement.blur();
                }
            }
        } );
    } );


    /* =========================================================================
       3. AOS — ANIMATE ON SCROLL
    ========================================================================= */
    if ( typeof AOS !== 'undefined' ) {
        /* Sections already on the screen when the scripts start were shown
           from the first paint (see "Scroll animations" in css/custom.css):
           they stay as they are instead of hiding and fading in again. Only
           the sections further down animate. */
        document.querySelectorAll( '[data-aos]' ).forEach( function( el ) {
            if ( el.getBoundingClientRect().top < window.innerHeight ) {
                el.removeAttribute( 'data-aos' );
            }
        } );

        AOS.init( {
            duration : 600,
            easing   : 'ease-in-out',
            once     : true,
            offset   : 80,
        } );

        /* Re-calculate element positions after all images have loaded.
           AOS runs on DOMContentLoaded, but images shift content down
           after that — making stored positions stale for late sections. */
        $( window ).on( 'load', function() {
            AOS.refresh();
        } );
    }


    /* =========================================================================
       6. MOBILE MENU — open / close
    ========================================================================= */
    var $mobileMenu    = $( '#mobileMenu' );
    var $mobileOverlay = $( '#mobileOverlay' );
    var $body          = $( 'body' );

    function openMobileMenu() {
        $mobileMenu.addClass( 'open' );
        $mobileOverlay.addClass( 'active' );
        $body.css( 'overflow', 'hidden' );
        $( '#mobileMenuToggler' ).attr( 'aria-expanded', 'true' );
        // Move keyboard focus into the panel once it is visible.
        setTimeout( function() { $( '#mobileMenuClose' ).trigger( 'focus' ); }, 50 );
    }

    function closeMobileMenu() {
        var wasOpen = $mobileMenu.hasClass( 'open' );
        $mobileMenu.removeClass( 'open' );
        $mobileOverlay.removeClass( 'active' );
        $body.css( 'overflow', '' );
        $( '#mobileMenuToggler' ).attr( 'aria-expanded', 'false' );
        if ( wasOpen ) {
            $( '#mobileMenuToggler' ).trigger( 'focus' );
        }

        // Close all open dropdown panels when menu closes
        $( '.mobile-dropdown-menu.open' ).slideUp( 250 ).removeClass( 'open' );
        $( '.mobile-dropdown-toggle' )
            .attr( 'aria-expanded', 'false' )
            // Scoped to the dedicated arrow class, not a bare 'i' — the
            // toggle link also contains the item's own leading icon (also
            // a plain <i>), which a bare .find('i') would rotate too.
            .find( 'i.mobile-dropdown-arrow' ).css( 'transform', 'rotate(0deg)' );
    }

    $( '#mobileMenuToggler' ).on( 'click', function() {
        if ( $mobileMenu.hasClass( 'open' ) ) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    } );

    $( '#mobileMenuClose' ).on( 'click', function() {
        closeMobileMenu();
    } );

    $mobileOverlay.on( 'click', function() {
        closeMobileMenu();
    } );

    // Close on Escape key
    $( document ).on( 'keydown', function( e ) {
        if ( e.key === 'Escape' && $mobileMenu.hasClass( 'open' ) ) {
            closeMobileMenu();
        }
    } );


    /* =========================================================================
       7. MOBILE DROPDOWN TOGGLES
       Targets ALL .mobile-dropdown-toggle items — works for every dropdown
       and regular Bootstrap dropdowns via dynamic Walker-generated IDs.
    ========================================================================= */
    $( document ).on( 'click', '.mobile-dropdown-toggle', function( e ) {
        e.preventDefault();

        var $toggle  = $( this );
        var targetId = $toggle.attr( 'aria-controls' );
        var $panel   = $( '#' + targetId );
        var $icon    = $toggle.find( 'i.mobile-dropdown-arrow' );
        var isOpen   = $panel.hasClass( 'open' );

        // Close all OTHER open panels first
        $( '.mobile-dropdown-menu.open' ).not( $panel ).slideUp( 250 ).removeClass( 'open' );
        $( '.mobile-dropdown-toggle' ).not( $toggle )
            .attr( 'aria-expanded', 'false' )
            .find( 'i.mobile-dropdown-arrow' ).css( 'transform', 'rotate(0deg)' );

        // Toggle current panel
        if ( isOpen ) {
            $panel.slideUp( 250 ).removeClass( 'open' );
            $toggle.attr( 'aria-expanded', 'false' );
            $icon.css( 'transform', 'rotate(0deg)' );
        } else {
            $panel.slideDown( 250 ).addClass( 'open' );
            $toggle.attr( 'aria-expanded', 'true' );
            $icon.css( 'transform', 'rotate(180deg)' );
        }
    } );


    /* =========================================================================
       8. BACK TO TOP BUTTON + whole-page scroll progress ring
       ─────────────────────────────────────────────────────────────────────────
       The ring is a plain page-scroll percentage (0% at the very top of the
       page, 100% at the very bottom) — deliberately NOT tied to any specific
       "article" content area, so it means the same thing and needs no special
       casing on every page type (homepage, shop, product, blog post, etc.).
       Always on, no Customizer toggle — decided with the user. Reuses this
       SAME scroll listener that already existed for the button's fade in/out;
       no second scroll listener was added just for the ring.
    ========================================================================= */
    var $backToTopWrap  = $( '.alqamara-btt-wrap' );
    var ringProgressEl  = document.getElementById( 'alqamara-btt-ring-progress' );
    var ringCircumference = 0;

    if ( ringProgressEl ) {
        var ringRadius   = ringProgressEl.r.baseVal.value;
        ringCircumference = 2 * Math.PI * ringRadius;
        ringProgressEl.style.strokeDasharray  = ringCircumference;
        ringProgressEl.style.strokeDashoffset = ringCircumference; // start empty (0%)
    }

    function alqamaraUpdateScrollProgress() {
        if ( ! ringProgressEl ) {
            return;
        }
        var scrollTop    = window.scrollY || document.documentElement.scrollTop;
        var docHeight    = document.documentElement.scrollHeight - window.innerHeight;
        var pct          = docHeight > 0 ? Math.min( 1, Math.max( 0, scrollTop / docHeight ) ) : 0;
        ringProgressEl.style.strokeDashoffset = ringCircumference * ( 1 - pct );
    }

    $( window ).on( 'scroll', function() {
        if ( $( this ).scrollTop() > 300 ) {
            $backToTopWrap.fadeIn( 300 );
        } else {
            $backToTopWrap.fadeOut( 300 );
        }
        alqamaraUpdateScrollProgress();
    } );

    // Set the correct initial state on load too (e.g. a mid-page anchor link
    // landing, or a refresh while already scrolled down) — not just on the
    // next scroll event.
    alqamaraUpdateScrollProgress();

    $( '#back-to-top' ).on( 'click', function( e ) {
        e.preventDefault();
        window.scrollTo( { top: 0, behavior: 'smooth' } );
        return false;
    } );


        /* =========================================================================
       9. CART DRAWER
    ========================================================================= */
    var $cartDrawer    = $( '#alqamaraCartDrawer' );
    var $drawerOverlay = $( '#alqamaraDrawerOverlay' );

    var drawerOpener = null;

    function openCartDrawer() {
        drawerOpener = document.activeElement;
        $cartDrawer.addClass( 'is-open' ).attr( 'aria-hidden', 'false' );
        setTimeout( function() { $cartDrawer.find( '.alqamara-drawer-close' ).first().trigger( 'focus' ); }, 50 );
        $drawerOverlay.addClass( 'is-active' );
        $( 'body' ).addClass( 'alqamara-drawer-open' );
    }

    function closeCartDrawer() {
        var wasOpen = $cartDrawer.hasClass( 'is-open' );
        $cartDrawer.removeClass( 'is-open' ).attr( 'aria-hidden', 'true' );
        if ( wasOpen && drawerOpener && drawerOpener.focus ) {
            drawerOpener.focus();
        }
        $drawerOverlay.removeClass( 'is-active' );
        $( 'body' ).removeClass( 'alqamara-drawer-open' );
    }

    $( document ).on( 'click', '.alqamara-open-drawer', function( e ) {
        e.preventDefault();
        openCartDrawer();
    } );

    $( document ).on( 'click', '.alqamara-drawer-close, #alqamaraDrawerOverlay', function() {
        closeCartDrawer();
    } );

    $( document ).on( 'keydown', function( e ) {
        if ( 27 === e.keyCode ) {
            closeCartDrawer();
        }
    } );


    /* =========================================================================
       9b. KEEP KEYBOARD FOCUS INSIDE AN OPEN PANEL (mobile menu, cart drawer)
       Both are modal dialogs: while one is open, Tab and Shift+Tab cycle through
       its own controls only, in this order: the close button first, then the
       rest of the panel in page order. So Tab after the last item goes to the
       close button, Shift+Tab on the first item after it (the logo in the
       mobile menu) goes to the close button, and Shift+Tab on the close button
       goes to the last item. Collapsed sub-menus are skipped because only
       visible controls count.
    ========================================================================= */
    var focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

    function trapFocus( e, panel, closeButton ) {
        var items = $( panel ).find( focusableSelector ).filter( ':visible' ).not( closeButton ).toArray();
        if ( closeButton ) {
            items.unshift( closeButton );
        }
        if ( ! items.length ) {
            return;
        }
        var index = items.indexOf( document.activeElement );
        var next;

        if ( e.shiftKey ) {
            next = index <= 0 ? items.length - 1 : index - 1;
        } else {
            next = ( index === -1 || index === items.length - 1 ) ? 0 : index + 1;
        }
        e.preventDefault();
        items[ next ].focus();
    }

    $( document ).on( 'keydown', function( e ) {
        if ( 'Tab' !== e.key && 9 !== e.keyCode ) {
            return;
        }
        if ( $mobileMenu.hasClass( 'open' ) ) {
            trapFocus( e, $mobileMenu[0], document.getElementById( 'mobileMenuClose' ) );
        } else if ( $cartDrawer.hasClass( 'is-open' ) ) {
            trapFocus( e, $cartDrawer[0], $cartDrawer.find( '.alqamara-drawer-close' )[0] );
        }
    } );


    /* =========================================================================
       10. SEARCH DROPDOWN — auto-focus search field when dropdown opens
    ========================================================================= */
    $( document ).on( 'shown.bs.dropdown', '.dropdown', function() {
        var $field = $( this ).find( '.alqamara-search-dd .search-field' );
        if ( $field.length ) {
            $field.trigger( 'focus' );
        }
    } );


    /* =========================================================================
       11. CART BADGES — apply WC fragments after add-to-cart
       WC's add-to-cart.min.js fires 'added_to_cart' with the full fragments
       object from the server (which includes our woocommerce_add_to_cart_fragments
       filter output). We apply those fragments ourselves so both the desktop
       header badge and the mobile bottom-bar badge update without a page reload.
    ========================================================================= */
    $( document.body ).on( 'added_to_cart', function( e, fragments ) {
        if ( ! fragments ) { return; }
        $.each( fragments, function( selector, html ) {
            $( selector ).replaceWith( html );
        } );
    } );


    /* =========================================================================
       12. STATS COUNTER — count-up animation via Intersection Observer
       Targets: .alqamara-stat-number[data-target]
       Attributes:
         data-target  — numeric end value (digits only)
         data-suffix  — optional suffix appended after the number, e.g. "+" or "%"
    ========================================================================= */
    if ( window.IntersectionObserver ) {
        var statObserver = new IntersectionObserver( function( entries ) {
            entries.forEach( function( entry ) {
                if ( ! entry.isIntersecting ) { return; }

                var el      = entry.target;
                var target  = parseInt( el.getAttribute( 'data-target' ), 10 ) || 0;
                var suffix  = el.getAttribute( 'data-suffix' ) || '';
                var dur     = 1800; // ms
                var start   = null;

                function step( ts ) {
                    if ( ! start ) { start = ts; }
                    var prog  = Math.min( ( ts - start ) / dur, 1 );
                    var eased = 1 - Math.pow( 1 - prog, 3 ); // ease-out cubic
                    el.textContent = Math.floor( eased * target ).toLocaleString() + suffix;
                    if ( prog < 1 ) {
                        requestAnimationFrame( step );
                    } else {
                        el.textContent = target.toLocaleString() + suffix;
                    }
                }

                requestAnimationFrame( step );
                statObserver.unobserve( el );
            } );
        }, { threshold: 0.4 } );

        document.querySelectorAll( '.alqamara-stat-number[data-target]' ).forEach( function( el ) {
            statObserver.observe( el );
        } );
    }



    /* =========================================================================
       12. SWIPER — Posts widget slider + Category Showcase slider
           Posts    : .alqamara-posts-slider      (Posts Widget, slider mode)
           Category : .alqamara-cs-slider         (Category Showcase Widget, slider mode —
                                               shares this same init block, just an
                                               extra selector; nav buttons reuse the
                                               Posts Widget's .alqamara-pw-prev/.alqamara-pw-next
                                               classes directly, so no new bindings needed)
    ========================================================================= */
    $( document ).ready( function() {

        if ( typeof Swiper === 'undefined' ) { return; }

        /* Skip Swiper entirely inside Gutenberg widget-preview iframes.
           When slider mode is enabled, Gutenberg re-renders the preview iframe
           every time the user clicks elsewhere, causing Swiper to re-init and
           auto-slide — which appears as a "shake". Static HTML preview is fine.

           EXCEPTION: the Customizer's own live-preview iframe (customize.php)
           is also `window.self !== window.top`, but it doesn't have Gutenberg's
           repeated-re-render problem — a changed setting either does a full
           iframe reload (transport:refresh) or a targeted postMessage update,
           neither of which re-runs this whole script in a loop. Blocking Swiper
           there made every slider widget look broken in Customizer mobile/tablet
           preview (tiny, unwrapped, overflowing cards) even though the real
           front-end page (top-level, not in an iframe) rendered correctly.
           `wp.customize` only exists as a global on the customize.php parent
           frame, so this reliably tells the Customizer apart from Gutenberg. */
        if ( window.self !== window.top ) {
            var alqamaraInCustomizer = false;
            try {
                alqamaraInCustomizer = !!( window.top.wp && window.top.wp.customize );
            } catch ( e ) {}
            if ( ! alqamaraInCustomizer ) { return; }
        }

        /* Shared nav-visibility helper — used by the Posts/Category
           sliders below. Hides the manual prev/next
           buttons and pauses autoplay whenever the real item count doesn't
           exceed what's already visible at the current breakpoint's
           slidesPerView — there's nothing extra to slide to. Re-run on
           init and on every breakpoint/resize change, since slidesPerView
           differs by viewport width. */
        function alqamaraUpdateSliderNav( swiper, realCount, $navPrev, $navNext ) {
            var visible = swiper.params.slidesPerView;
            if ( typeof visible !== 'number' ) { visible = 1; }
            var hasOverflow = realCount > visible;

            $navPrev.add( $navNext ).toggle( hasOverflow );

            if ( swiper.autoplay && swiper.autoplay.running !== undefined ) {
                if ( hasOverflow ) {
                    if ( ! swiper.autoplay.running ) { swiper.autoplay.start(); }
                } else if ( swiper.autoplay.running ) {
                    swiper.autoplay.stop();
                }
            }
        }

        /* ── Posts widget sliders ── */

        /* Equal-height helper: find tallest slide, apply to all.
           Debounced on resize to avoid visual flicker when Gutenberg
           widget editor resizes the preview iframe. */
        var _equalizeTimers = [];
        function alqamaraEqualizeSlides( swiper, debounce ) {
            var idx = swiper.el ? swiper.el.__alqamaraEqIdx : undefined;
            if ( debounce ) {
                if ( idx !== undefined ) { clearTimeout( _equalizeTimers[ idx ] ); }
                _equalizeTimers.push( setTimeout( function() { alqamaraEqualizeSlides( swiper, false ); }, 150 ) );
                if ( idx === undefined && swiper.el ) {
                    swiper.el.__alqamaraEqIdx = _equalizeTimers.length - 1;
                }
                return;
            }
            swiper.slides.forEach( function( s ) { s.style.height = ''; } );
            var maxH = 0;
            swiper.slides.forEach( function( s ) {
                if ( s.offsetHeight > maxH ) { maxH = s.offsetHeight; }
            } );
            if ( maxH > 0 ) {
                swiper.slides.forEach( function( s ) { s.style.height = maxH + 'px'; } );
            }
        }

        $( '.alqamara-posts-slider, .alqamara-cs-slider' ).each( function() {
            var el    = this;
            var $wrap = $( el ).closest( '.alqamara-pw-wrap, .alqamara-cs-wrap' );
            var items = parseInt( $( el ).data( 'swiper-items' ), 10 ) || 3;
            var speed = parseInt( $( el ).data( 'swiper-speed' ), 10 ) || 3000;
            /* BUG FIX: jQuery .data() parses "false" → boolean false,
               so (false !== 'false') === true. Use .attr() for raw string. */
            var auto  = $( el ).attr( 'data-swiper-auto' ) !== 'false';
            var count = parseInt( $( el ).data( 'swiper-count' ), 10 ) || 0;
            /* Mobile (<576px) slides: 2 for product widgets, 1 for posts/blog.
               Falls back to 1 if the data attribute wasn't printed. */
            var mobileCols = parseInt( $( el ).data( 'swiper-mobile-cols' ), 10 ) || 1;
            var $navPrev   = $wrap.find( '.alqamara-pw-prev' );
            var $navNext   = $wrap.find( '.alqamara-pw-next' );

            /* `rewind: true` instead of `loop: true`: the bundled Swiper build
             * does not generate loop clones at these slide counts and gets
             * stuck after the first click. */
            var swiperI = new Swiper( el, {
                loop          : false,
                rewind        : true,
                grabCursor    : true,
                slidesPerView : mobileCols,
                spaceBetween  : 24,
                autoplay      : auto
                    ? { delay: speed, disableOnInteraction: false, pauseOnMouseEnter: true }
                    : false,
                breakpoints : {
                    576 : { slidesPerView: Math.min( items, 2 ) },
                    992 : { slidesPerView: items },
                },
                on : {
                    /* Equalise card heights after init and on layout changes.
                       resize is debounced (150ms) to prevent flicker in
                       Gutenberg widget editor preview. Nav-visibility check
                       (alqamaraUpdateSliderNav, defined above)
                       hides prev/next + pauses autoplay when the real item
                       count already fits within the current breakpoint's
                       visible columns — nothing left to slide to. */
                    init       : function() { alqamaraEqualizeSlides( this, false ); alqamaraUpdateSliderNav( this, count, $navPrev, $navNext ); },
                    breakpoint : function() { alqamaraEqualizeSlides( this, false ); alqamaraUpdateSliderNav( this, count, $navPrev, $navNext ); },
                    resize     : function() { alqamaraEqualizeSlides( this, true  ); alqamaraUpdateSliderNav( this, count, $navPrev, $navNext ); },
                },
            } );

            /* Manual prev/next — avoids Swiper's swiper-button-lock behaviour */
            $navPrev.on( 'click', function() { swiperI.slidePrev(); } );
            $navNext.on( 'click', function() { swiperI.slideNext(); } );
        } );

    } );


} )( jQuery );


/* =============================================================================
   SHOP — PRICE FILTER: submit only on button click, not on slider stop
   WooCommerce auto-submits the price filter form on every slidestop.
   We intercept programmatic submits (no originalEvent) and block them.
   The form only submits when the user explicitly clicks the Filter button.
============================================================================= */
( function( $ ) {
    'use strict';

    $( 'body' ).on( 'submit', '.widget_price_filter form', function( e ) {
        /* Real button-click submits have an originalEvent; JS $.submit() calls do not */
        if ( ! e.originalEvent ) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
    } );

} )( jQuery );


/* =============================================================================
   SHOP — LIST / GRID VIEW TOGGLE
   Remembers preference in localStorage so the chosen view persists across pages.
============================================================================= */
( function() {
    'use strict';

    var STORAGE_KEY = 'alqamara_shop_view';

    function applyView( view, toggleBtns, productsList ) {
        var isList = ( view === 'list' );
        productsList.classList.toggle( 'alqamara-list-view', isList );

        toggleBtns.forEach( function( btn ) {
            var active = btn.dataset.view === view;
            btn.classList.toggle( 'active', active );
            btn.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
        } );

        try { localStorage.setItem( STORAGE_KEY, view ); } catch ( e ) {}
    }

    function initViewToggle() {
        var toggleBtns   = document.querySelectorAll( '.alqamara-view-btn' );
        var productsList = document.querySelector( 'ul.products' );

        if ( ! toggleBtns.length || ! productsList ) { return; }

        /* Restore saved preference on this (fresh) markup */
        var saved;
        try { saved = localStorage.getItem( STORAGE_KEY ); } catch ( e ) {}
        if ( saved === 'list' || saved === 'grid' ) {
            applyView( saved, toggleBtns, productsList );
        }

        /* Wire up click handlers on the CURRENT buttons */
        toggleBtns.forEach( function( btn ) {
            btn.addEventListener( 'click', function() {
                /* Re-query productsList here too: if this button's own click
                   triggers an AJAX swap later, that's a different pass of
                   initViewToggle() — but within a single click, the list
                   present right now is still the correct target. */
                applyView( this.dataset.view, toggleBtns, productsList );
            } );
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initViewToggle );
    } else {
        initViewToggle();
    }

} )();
