# CalmPress

CalmPress is a lightweight, classic WordPress theme for a blog and APK/app
showcase. It uses semantic PHP templates, a small responsive stylesheet, and
one dependency-free script for the system/light/dark theme switcher.

## Requirements

- WordPress 6.1 or newer
- PHP 8.1 or newer

## Installation

1. Copy the `calmpress` directory to `wp-content/themes/`.
2. In **Appearance → Themes**, activate **CalmPress**.
3. Set a site title and tagline in **Settings → General**.
4. Optionally assign menus under **Appearance → Menus** to the Primary and
   Footer locations.
5. Set a static front page and a Posts page under **Settings → Reading** if
   you want separate homepage and journal URLs.
6. Add apps under **Apps**. Set a featured image for the app icon and fill in
   the App details box. The **Download URL** is escaped and stored only after
   capability and nonce checks.

The theme registers the `app` custom post type and `app_category` taxonomy.
Permalinks are flushed on activation; if an existing site still shows a 404
for `/apps/`, visit **Settings → Permalinks** and press **Save Changes**.

## Included features

- Responsive layouts from 320px through wide displays
- Light, dark, and system color modes with a reduced-motion-friendly toggle
- WCAG AA-focused contrast, keyboard focus states, skip link, labels, and
  semantic landmarks
- Responsive WordPress image markup, lazy loading, async decoding, and eager
  loading for the first visual on singular pages
- `theme.json`, editor styles, title tags, feeds, core sitemap/robots support,
  breadcrumbs-ready semantic structure, and Article/SoftwareApplication JSON-LD
- Secure app metadata editing with capability checks, sanitization, and a
  nonce
- No external fonts, trackers, build step, or heavy JavaScript dependencies

## Customization

Use the Site Identity, Menus, Reading, and Widgets screens to customize the
theme. Child themes can override any template or enqueue additional styles.
