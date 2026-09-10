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
- Turkish "X dk okuma" reading-time labels, a privacy-conscious cookie-based
  view counter, and semantic accessible breadcrumbs on singular content,
  pages, and archives
- Native Open Graph, Twitter Card, and PWA meta (only printed when no known
  SEO plugin such as Yoast, Rank Math, SEOPress, or All in One SEO is
  active), plus a dynamic `/site.webmanifest`
- An optional `page-sitemap.php` "Site Haritası" page template, and an
  extended app details box with a minimum Android version, changelog, and
  up to four screenshots

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

### Okuma süresi, görüntülenme sayacı ve izlek (breadcrumb)

**Görünüm → Özelleştir → CalmPress Studio → Blog ve uygulamalar** bölümünde üç yeni denetim grubu bulunur:

- **Okuma süresini göster**: içerik kelime sayısını dakikadaki kelime sayısına (varsayılan 200, 120–320 arasında ayarlanabilir) bölerek "X dk okuma" etiketini yazılarda ve kartlarda gösterir. Etiketin sonundaki metin özelleştirilebilir.
- **Görüntülenme sayacını etkinleştir / Görüntülenme rozetini göster**: yazı ve uygulama sayfalarında, ziyaretçi başına günde bir kez artan, göz simgeli bir sayaç gösterir. Sayaç hiçbir IP adresi veya kimlik bilgisi saklamaz; yalnızca tarayıcıda küçük bir birinci taraf çerezi (`cp_viewed`) hangi yazıların zaten sayıldığını hatırlar. Oturum açmış yazarlar/yöneticiler ve bilinen tarayıcı botları sayıma dahil edilmez.
- **İzlek (breadcrumb) gezinmesini göster**: yazı, sayfa, arşiv ve taksonomi şablonlarının üstünde erişilebilir bir `<nav aria-label>` gezinme yolu ekler; ana sayfada gösterilmez ve şema.org JSON-LD ile çakışmaması için JSON-LD üretmez.

### Yerel Open Graph, Twitter Card ve PWA meta etiketleri

Yoast SEO, Rank Math, SEOPress veya All in One SEO gibi bilinen bir SEO eklentisi etkin değilse CalmPress; `og:title`, `og:description`, `og:url`, `og:image`, `og:site_name`, Twitter Card, `theme-color` ve PWA meta etiketlerini otomatik olarak ekler. Standart `<link rel="canonical">` etiketi kasıtlı olarak WordPress çekirdeğinin kendi `rel_canonical()` çıktısına bırakılır; böylece iki kez yazılmaz. Aynı zamanda `/site.webmanifest` adresi, siteyi ekle simgesi, tema rengi ve site simgesinden üretilen ikonlarla anlık olarak sunulur; bunun için herhangi bir kalıcı bağlantı (permalink) ayarı veya ek dosya gerekmez.

### HTML site haritası sayfa şablonu

Tema, `page-sitemap.php` adlı bir sayfa şablonu içerir (Site Haritası). Bu şablon otomatik olarak hiçbir sayfaya uygulanmaz. Kullanmak için **Sayfalar → Yeni ekle** ile bir sayfa oluşturun (isterseniz slug'ı `sitemap` yapın, ya da herhangi bir slug seçip sağ taraftaki Sayfa Şablonu açılır menüsünden **Site Haritası**'nı seçin). Şablon; kategorileri, uygulama kategorilerini, uygulama arşivi bağlantısını ve sayfalanmış/sınırlandırılmış (30'ar) son yazılar listesini erişilebilir biçimde gösterir.

### Genişletilmiş uygulama ayrıntıları

Uygulama düzenleme ekranındaki **Uygulama ayrıntıları** kutusu artık minimum Android sürümü, sürüm notları/değişiklik günlüğü ve medya kütüphanesinden seçilen en fazla dört ekran görüntüsü alanı içerir. Ekran görüntüleri, WordPress'in yerleşik medya yükleyicisiyle eklenir; kimlik doğrulaması aynı nonce ve `edit_post` yetenek kontrolüyle korunur ve kaydedilen kimlikler yalnızca gerçek görsel eklerine göre doğrulanır. **CalmPress Studio → Blog ve uygulamalar** bölümündeki anahtarlarla galeri ve sürüm notları bölümlerinin `single-app.php` üzerinde gösterilip gösterilmeyeceği ayrı ayrı denetlenir.
