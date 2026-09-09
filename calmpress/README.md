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
- Accessible H2/H3 table of contents with stable heading links, in-content
  “Ayrıca okuyun” cards, tag links, author cards, and related-post cards
- Reusable share controls (Web Share with clipboard fallback) above and below
  singular content, plus a comment-count popular-posts sidebar
- Focus-trapped category search modal, responsive mobile navigation, and an
  optional dismissible campaign notification stored in local storage
- No external fonts, trackers, build step, or heavy JavaScript dependencies

## Customization

Use the Site Identity, Menus, Reading, and Widgets screens to customize the
theme. The **Appearance → Customize → CalmPress Studio** workspace adds live
preview controls for the complete visual system: accent and focus colors,
content width, card density, corner radius, system/readable font stacks,
default color mode, reduced motion, sticky header, announcement styles,
navigation treatments, homepage hero and section visibility, archive copy,
excerpt length, app facts and download labels, footer layout, social links,
share buttons, table of contents, related posts, author cards, popular posts,
categories, modal search, mobile navigation, campaign text/link, back-to-top
navigation, contrast, image loading, embeds, emoji, Dashicons, and ad
placement visibility. Child themes can override any template or enqueue
additional styles. The generic core Categories block remains hidden on
singular content; use the dedicated CalmPress categories widget or the
CalmPress Studio option instead.

The optional Custom CSS field accepts a bounded, CSS-only snippet. It strips
HTML, script-like CSS constructs, and style tags before output. Do not paste
PHP, JavaScript, or HTML into it; the warning in the Customizer is deliberate.

### CalmPress Settings panel

After activation, open **Appearance → CalmPress Settings**. The panel uses the
WordPress Settings API and is available to administrators with the
`manage_options` capability. It includes design controls (accent color,
container width, and cards per row), announcement/header and footer controls,
SEO/schema defaults, emoji performance settings, social profile links, and
five advertising placements. Ad fields accept safe HTML through
`wp_kses_post`; script tags, event handlers, and unsafe attributes are removed
automatically. Ads are disabled until each placement is explicitly enabled.

The Widgets screen includes Header, After content, Sidebar, Home before
content, Home after content, and Footer areas. The bundled **CalmPress: Recent
Apps** widget lists the latest `app` entries.

### Türkçe kullanım ve çeviri

CalmPress’in varsayılan kullanıcı arayüzü Türkçedir; İngilizce veya başka bir
dil paketi etkin olduğunda WordPress çevirileri bu metinlerin üzerine yazabilir.
WordPress’in **Ayarlar → Genel → Site Dili** bölümünden **Türkçe** seçildiğinde
tema `languages/calmpress-tr_TR.po` çeviri kataloğunu kullanır. WordPress.org
dil paketleri veya Loco Translate gibi bir araç `.mo` dosyasını oluşturup
yükleyebilir; temanın `.po` dosyası kaynak çevirileri ve yeni yönetim paneli
ifadelerini içerir. Yönetim panelini **Görünüm → CalmPress Settings** üzerinden
yapılandırın. Reklam kodu eklerken yalnızca güvenli HTML kullanın; JavaScript
etiketleri bilerek kaldırılır.
