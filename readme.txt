=== Al Qamara Lite ===
Contributors: alqamara
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: e-commerce, custom-menu, custom-logo, custom-colors, custom-background, featured-images, footer-widgets, threaded-comments, translation-ready, full-width-template, left-sidebar, right-sidebar

A clean, fast theme for blogs, business sites and WooCommerce stores. Built on Bootstrap 5 with a widget-driven homepage.

== Description ==

Al Qamara Lite is a free, general-purpose WordPress theme built on Bootstrap 5. It works as a blog or business theme on its own and has full styling for WooCommerce when that plugin is active.

What you get:

* A header with an optional top bar, a primary menu with dropdowns on desktop and a slide-in menu on mobile, and an optional transparent header over the homepage hero.
* A homepage made of sections (Hero, Stats, About, Features, Blog, Newsletter, Call to Action and three generic rows). Hero, About, Blog, Newsletter and the three rows can be filled from widget areas; Stats, Features and Call to Action are set in the Customizer. The sections are shown when a static page is chosen as the front page (Settings > Reading); with "Your latest posts" the normal blog index is shown.
* Customizer options for the logo, colours, fonts (from the fonts included in the theme), header, footer, blog, sidebar position, page banners and, with WooCommerce, the shop and single product pages.
* Two custom widgets: a Posts widget (grid or slider) and a Category Showcase widget, plus a Contact Info widget.
* Page templates: Full Width, Narrow, Blog and Blank Canvas.
* Block editor support: responsive embeds, block styles and editor styles.
* Translation ready, with RTL versions of the theme's own stylesheets.

= Accessibility =

The theme has a "Skip to content" link, visible focus outlines on links, buttons and form fields, and keyboard-operable menus. It is not tagged "accessibility-ready".

= Standard CSS classes =

The stylesheet includes styles for the WordPress core classes: .alignleft, .alignright, .aligncenter, .wp-caption, .wp-caption-text, .gallery-caption, .sticky, .bypostauthor and .screen-reader-text.

== Installation ==

1. In your WordPress admin go to Appearance > Themes > Add New, search for "Al Qamara Lite", then click Install and Activate. (Or choose Upload Theme and select the theme zip.)
2. Go to Appearance > Customize to set your logo, colours and homepage content.
3. Go to Appearance > Menus and assign a menu to the Primary Menu location.
4. Go to Appearance > Widgets to add widgets to the homepage section areas, the footer columns or the sidebars. Every homepage section also works with no widgets added: it falls back to its own Customizer settings.
5. To sell products, install and activate WooCommerce (free, from the WordPress.org plugin directory).

== Frequently Asked Questions ==

= Does this theme require WooCommerce? =

No. It works as a normal blog or business theme. The WooCommerce styles and Customizer options only load when WooCommerce is active.

= Does it add custom post types, shortcodes or SEO features? =

No. The theme only handles design and presentation. Use plugins for content types, SEO, forms, analytics and similar features.

= Does the theme load anything from other websites? =

No. All CSS, JavaScript, icons and fonts are included in the theme. The only outside request comes from WordPress itself: if avatars are enabled under Settings > Discussion, WordPress loads commenter avatars from Gravatar. See "Privacy" below.

= Where do I get support? =

Please use the WordPress.org support forum for this theme. A step-by-step guide with screenshots is on the theme's homepage: https://tech.alqamara.com/al-qamara-lite/

== Privacy ==

The theme does not collect, store or send any personal data, and it adds no requests to third-party servers. All fonts, icons and scripts are loaded from the theme folder. If avatars are enabled in Settings > Discussion, WordPress core shows commenter and author avatars from Gravatar; that is a core feature, not something the theme adds. The shop page stores one preference (grid or list view) in the visitor's own browser using localStorage; it never leaves the browser.

== Copyright ==

Al Qamara Lite, Copyright 2026 Tech Al Qamara.
Al Qamara Lite is distributed under the terms of the GNU GPL v2 or later (see the LICENSE file).

This theme bundles the third-party resources listed under "Resources" below. Each one is distributed under the license shown next to it, all of which are compatible with the GPL. No images are bundled apart from the theme screenshot (screenshot.png). The screenshot shows the theme's demo site; the pictures in it were created and edited by the theme author with AI image tools, and the screenshot and all pictures in it are released under CC0 1.0 (public domain), https://creativecommons.org/publicdomain/zero/1.0/

== Resources ==

* Bootstrap 5.0.2, MIT License, https://getbootstrap.com/ (css/bootstrap.min.css and js/bootstrap.bundle.min.js, the latter includes Popper.js). Copyright 2011-2021 The Bootstrap Authors and Twitter, Inc. The unminified files sit next to the minified ones (css/bootstrap.css, js/bootstrap.bundle.js); upstream: https://github.com/twbs/bootstrap/tree/v5.0.2
* Font Awesome Free 7.1.0, Icons CC BY 4.0 / Fonts SIL OFL 1.1 / Code MIT License, https://fontawesome.com/ (fonts/font-awesome/ and fonts/webfonts/fa-*.woff2). Copyright 2025 Fonticons, Inc. The unminified file sits next to the minified one (fonts/font-awesome/all.css)
* Bootstrap Icons 1.13.1, MIT License, https://icons.getbootstrap.com/ (fonts/bootstrap-icons/ and fonts/webfonts/bootstrap-icons.*). Copyright 2019-2024 The Bootstrap Authors; the CSS is the official, unminified file
* AOS (Animate On Scroll) 2.3.4, MIT License, https://michalsnik.github.io/aos/ (lib/aos/). Copyright Michal Sajnog. lib/aos/aos.min.js and aos.min.css are the official dist build; the original source (JavaScript and SCSS) is included in lib/aos/src/ with the license in lib/aos/LICENSE; upstream: https://github.com/michalsnik/aos/tree/v2.3.4
* Swiper 11.2.10, MIT License, https://swiperjs.com/ (lib/swiper/, only loaded when a slider widget is used). Copyright 2014-2025 Vladimir Kharlampidi. The unminified files sit next to the minified ones (lib/swiper/swiper-bundle.js, lib/swiper/swiper-bundle.css); upstream: https://github.com/nolimits4web/swiper/tree/v11.2.10
* Open Sans, Lora, Poppins, Playfair Display and Roboto, SIL Open Font License 1.1, https://fonts.google.com/ (self-hosted Latin-subset .woff2 files in fonts/webfonts/, declared in fonts/text-fonts/fonts.css). Copyright holders: Open Sans, The Open Sans Project Authors; Lora, The Lora Project Authors; Poppins, Indian Type Foundry; Playfair Display, The Playfair Display Project Authors; Roboto, The Roboto Project Authors.

== Changelog ==

= 1.0.7 =
* Single product and cart pages: the quantity field now has - and + buttons.
* Cart drawer redesigned: each product shows its picture, name, quantity and price with a remove button; the subtotal and the View Cart / Checkout buttons stay at the bottom. For logged-in users it now opens below the WordPress toolbar.
* My Account: the dashboard shows two cards, account details with addresses and the most recent orders.
* Classic checkout (shortcode) styled to match the rest of the shop: form cards, two-column fields and an order summary that stays in view on large screens.
* Notices with a button (for example "View cart" or "Browse products") no longer let the button cover the text.
* Product pictures that are not square now fill their card instead of leaving an empty strip.
* The top bar is only shown when it has text, a phone number or a social link.
* "No products found" page with a clear message and a link back to all products.
* Hero and page banner text is visible from the first paint; sliders keep their card width while they load; the page behind the open cart drawer no longer scrolls.
* Shop with 5 or 6 columns now uses them on wide screens; small fixes for small phones, keyboard focus and footer widget headings.
* Theme files are now loaded with the current version number, so browsers fetch the new styles after an update.

= 1.0.6 =
* The page no longer shifts sideways when the scrollbar appears while it loads, and the page behind the open mobile menu and cart drawer no longer scrolls.
* Sections on the first screen are shown at once instead of waiting for the scroll animation script.
* Variation dropdowns on product pages (for example Color and Size) now show a down arrow, so they are clearly recognisable as dropdowns.
* Printing leaves out the mobile menu, cart drawer, mobile shop bar, skip link and back-to-top button.
* Until a menu is assigned, the menu lists Home and the site's own pages instead of fixed links that could lead to missing pages.
* New theme description, screenshot and theme homepage (Theme URI) with a full guide.

= 1.0.5 =
* Keyboard: the open mobile menu and cart drawer keep focus inside them. Tab after the last item moves to the close button, Shift+Tab on the close button moves to the last item.
* Every bundled minified file now has its original next to it (Bootstrap, Font Awesome, Swiper), and the original AOS source is included in lib/aos/src/.
* Third-party scripts and styles are registered under their plain names (bootstrap, font-awesome, bootstrap-icons, aos, swiper) instead of a theme prefix.

= 1.0.4 =
* Simplified the theme so it only covers design and presentation. Removed features that belong in plugins: starter content, SEO/schema/sitemap output, maintenance mode, author box and custom user fields, menu-item and category custom fields, popup/announcement bar, admin and dashboard changes, WooCommerce extras (quick view, wishlist/compare, sale countdown, sticky add to cart, custom checkout fields, Buy Now/WhatsApp/Messenger buttons, AJAX search and filters).
* Unified every PHP function, hook, global, handle and image-size name, and every CSS class, id and custom property, under the "alqamara" prefix.
* Removed the optional Google Fonts URL setting: no request is ever made to another site.
* The theme no longer changes core behaviour (emoji, jQuery Migrate, generator headers, sidebars_widgets, WooCommerce options).
* Default footer credit is now the site name instead of the theme author.
* Replaced the bundled Poppins, Playfair Display and Roboto font files with proper Latin subsets (the previous files were missing basic Latin letters, so browsers fell back to system fonts).
* The front page now follows Settings > Reading (static page or latest posts).
* Added custom-background support, clearing of floated images and wrapping of long titles.
* Added a LICENSE file, Privacy and Resources documentation, and corrected the bundled library versions.
* Reduced the Customizer to three textarea controls.
* Included the unminified sources of the bundled minified libraries (lib/src/).
* Closed off-canvas menus and the cart drawer are no longer reachable with the Tab key; more visible keyboard focus; long words wrap inside content.

= 1.0.3 =
* Fix: a "Sale" string was translated under the wrong text domain.

= 1.0.2 =
* Removed a forced DISALLOW_FILE_EDIT define.

= 1.0.1 =
* Added the Contact Info widget, hero overlay option, header and top bar colour options and a square product image option.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.7 =
Shop design update: quantity buttons, a redesigned cart drawer, My Account dashboard cards and a styled classic checkout.

= 1.0.6 =
Steadier page while loading, a working scroll lock behind the mobile menu and cart, and a menu fallback that only links to existing pages.

= 1.0.5 = focus stays inside the open mobile menu and cart drawer; original files included for every minified library.

= 1.0.4 =
Large clean-up release. Features that belong in plugins (quick view, wishlist, SEO output, maintenance mode and similar) were removed, and PHP names were renamed to the "alqamara" prefix. Check your site after updating; Customizer settings that still exist are kept.
