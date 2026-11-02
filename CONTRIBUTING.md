# Contributing to Al Qamara Lite

Thank you for helping to improve Al Qamara Lite. Bug reports, fixes and suggestions are welcome.

## Reporting a bug

1. Check the open issues first, so the same problem is not reported twice.
2. Include your WordPress version, PHP version, browser, and (if used) WooCommerce version.
3. Write clear steps to reproduce the problem. A screenshot or short screen recording helps a lot for visual issues.

For security problems, please do not open a public issue. Report them privately through the contact details on the author's WordPress.org profile: https://profiles.wordpress.org/alqamara/

## Pull requests

The theme is published on WordPress.org, so every change must follow the [WordPress.org theme requirements](https://make.wordpress.org/themes/handbook/review/required/):

- **Presentation only.** No custom post types, shortcodes, SEO output or other "plugin territory" features.
- **Prefix everything.** Use the `alqamara_` prefix for PHP functions, hooks and globals, and the `alqamara-` prefix for the theme's own handles, CSS classes and ids. Third-party libraries keep their plain handle names (`bootstrap`, `swiper` and so on).
- **Escape and sanitize.** Escape all output, sanitize all input, and give every Customizer setting a sanitize callback.
- **Translatable text.** Use the `al-qamara-lite` text domain for all text.
- **Accessible.** Keyboard navigation and visible focus must keep working.
- **No outside requests.** Bundle assets locally. If you add a minified file, include its original next to it.
- **Coding standards.** Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).

Please keep each pull request to one change, and describe how you tested it.
