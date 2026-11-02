# Al Qamara Lite

A clean, fast, free WordPress theme for blogs, business sites and WooCommerce stores, built on Bootstrap 5.

- **WordPress.org:** https://wordpress.org/themes/al-qamara-lite/ (official download and updates)
- **Live demo:** https://tech.alqamara.com/themes/alqamara-lite/
- **License:** GNU General Public License v2 or later (see [LICENSE](LICENSE))

This repository holds the same source as the version published on WordPress.org.

## Features

- A header with an optional top bar and a primary menu: dropdowns on desktop, a slide-in menu on mobile. The homepage header can be transparent over the hero.
- A homepage built from sections: Hero, Stats, About, Features, Blog, Newsletter, Call to Action and three generic rows. Some sections are filled from widget areas, others from the Customizer.
- Customizer options for the logo, colours, fonts (bundled, no outside requests), header, footer, blog, sidebar position and page banners. With WooCommerce active, there are also options for the shop and single product pages.
- Three custom widgets: Posts (grid or slider), Category Showcase and Contact Info.
- Page templates: Full Width, Narrow, Blog and Blank Canvas.
- Full WooCommerce styling (shop, product, cart, checkout and account pages), loaded only when WooCommerce is active.
- Block editor support: responsive embeds, block styles and editor styles.
- Translation ready, with RTL stylesheets.
- Keyboard friendly: skip link, visible focus outlines, and focus kept inside the open mobile menu and cart drawer.
- No requests to other websites: all CSS, JavaScript, icons and fonts are bundled.

## Requirements

- WordPress 6.4 or newer
- PHP 7.4 or newer
- WooCommerce is optional

## Installation

**From WordPress (recommended):** in your admin go to *Appearance → Themes → Add New*, search for "Al Qamara Lite", then click Install and Activate.

**From this repository:** download the code as a ZIP, or clone it into `wp-content/themes/al-qamara-lite`:

```bash
git clone https://github.com/techalqamara/al-qamara-lite.git wp-content/themes/al-qamara-lite
```

Then activate it under *Appearance → Themes*.

## Getting started

1. Go to *Appearance → Customize* to set your logo, colours and homepage content.
2. Go to *Appearance → Menus* and assign a menu to the **Primary Menu** location.
3. Go to *Appearance → Widgets* to fill the homepage section areas, the footer columns and the sidebars.
4. To show the homepage sections, choose a static front page under *Settings → Reading*.

## Bundled resources

The theme bundles Bootstrap 5.0.2, Font Awesome Free 7.1.0, Bootstrap Icons 1.13.1, AOS 2.3.4 and Swiper 11.2.10. It also bundles the Open Sans, Lora, Poppins, Playfair Display and Roboto fonts. All of them use GPL-compatible licenses. Each minified file has its original next to it; the AOS source is in `lib/aos/src/`. The full list with licenses and copyright holders is in [readme.txt](readme.txt) under "Resources".

## Changelog

See the "Changelog" section of [readme.txt](readme.txt).

## Support and contributing

- Questions and help: the [WordPress.org support forum](https://wordpress.org/support/theme/al-qamara-lite/).
- Bugs and suggestions: open an issue here. See [CONTRIBUTING.md](CONTRIBUTING.md).
