<?php
/**
 * CalmPress settings and administration UI.
 *
 * @package CalmPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a stored CalmPress setting or its documented default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function calmpress_get_option( $key ) {
	$defaults = array(
		'calmpress_accent_color'         => '#356ae6',
		'calmpress_container_width'      => 1120,
		'calmpress_cards_per_row'        => 3,
		'calmpress_border_radius'        => 16,
		'calmpress_density'              => 'comfortable',
		'calmpress_announcement_text'   => '',
		'calmpress_announcement_link'   => '',
		'calmpress_show_logo'            => 1,
		'calmpress_show_footer_widgets'  => 1,
		'calmpress_show_footer_menu'     => 1,
		'calmpress_footer_text'          => '',
		'calmpress_seo_title'            => '',
		'calmpress_seo_description'      => '',
		'calmpress_organization_name'    => '',
		'calmpress_organization_logo'    => '',
		'calmpress_schema_enabled'       => 1,
		'calmpress_disable_emoji'        => 0,
		'calmpress_disable_dashicons'   => 0,
		'calmpress_disable_embeds'      => 0,
		'calmpress_lazy_images'          => 1,
		'calmpress_ads_enabled'          => 0,
		'calmpress_social_facebook'      => '',
		'calmpress_social_instagram'     => '',
		'calmpress_social_x'             => '',
		'calmpress_social_youtube'       => '',
		'calmpress_social_github'        => '',
	);
	if ( 0 === strpos( $key, 'calmpress_ad_' ) ) {
		$defaults[ $key ] = false;
	}

	return get_option( $key, array_key_exists( $key, $defaults ) ? $defaults[ $key ] : '' );
}

/**
 * Sanitize the accent color.
 *
 * @param string $value Submitted color.
 * @return string
 */
function calmpress_sanitize_accent_color( $value ) {
	$value = sanitize_hex_color_no_hash( (string) $value );
	return preg_match( '/^[0-9a-f]{6}$/i', $value ) ? '#' . strtolower( $value ) : '#356ae6';
}

/**
 * Sanitize the bounded container width.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function calmpress_sanitize_container_width( $value ) {
	return min( 1600, max( 960, absint( $value ) ) );
}

/**
 * Sanitize the number of cards per row.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function calmpress_sanitize_cards_per_row( $value ) {
	return min( 4, max( 1, absint( $value ) ) );
}

/**
 * Sanitize the border radius.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function calmpress_sanitize_border_radius( $value ) {
	return min( 32, max( 4, absint( $value ) ) );
}

/**
 * Sanitize the visual density choice.
 *
 * @param mixed $value Submitted value.
 * @return string
 */
function calmpress_sanitize_density( $value ) {
	$allowed = array( 'compact', 'comfortable', 'spacious' );
	$value   = sanitize_key( $value );
	return in_array( $value, $allowed, true ) ? $value : 'comfortable';
}

/**
 * Sanitize a checkbox value.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function calmpress_sanitize_checkbox( $value ) {
	return empty( $value ) ? 0 : 1;
}

/**
 * Register all public CalmPress options.
 *
 * Individual options are intentionally retained for backwards compatibility.
 *
 * @return void
 */
function calmpress_register_settings() {
	$settings = array(
		'calmpress_accent_color'        => 'calmpress_sanitize_accent_color',
		'calmpress_container_width'     => 'calmpress_sanitize_container_width',
		'calmpress_cards_per_row'       => 'calmpress_sanitize_cards_per_row',
		'calmpress_border_radius'       => 'calmpress_sanitize_border_radius',
		'calmpress_density'             => 'calmpress_sanitize_density',
		'calmpress_announcement_text'   => 'sanitize_text_field',
		'calmpress_announcement_link'   => 'esc_url_raw',
		'calmpress_show_logo'           => 'calmpress_sanitize_checkbox',
		'calmpress_show_footer_widgets' => 'calmpress_sanitize_checkbox',
		'calmpress_show_footer_menu'    => 'calmpress_sanitize_checkbox',
		'calmpress_footer_text'         => 'sanitize_text_field',
		'calmpress_seo_title'           => 'sanitize_text_field',
		'calmpress_seo_description'     => 'sanitize_textarea_field',
		'calmpress_organization_name'   => 'sanitize_text_field',
		'calmpress_organization_logo'   => 'esc_url_raw',
		'calmpress_schema_enabled'      => 'calmpress_sanitize_checkbox',
		'calmpress_disable_emoji'       => 'calmpress_sanitize_checkbox',
		'calmpress_disable_dashicons'   => 'calmpress_sanitize_checkbox',
		'calmpress_disable_embeds'      => 'calmpress_sanitize_checkbox',
		'calmpress_lazy_images'         => 'calmpress_sanitize_checkbox',
		'calmpress_ads_enabled'         => 'calmpress_sanitize_checkbox',
		'calmpress_social_facebook'     => 'esc_url_raw',
		'calmpress_social_instagram'    => 'esc_url_raw',
		'calmpress_social_x'            => 'esc_url_raw',
		'calmpress_social_youtube'      => 'esc_url_raw',
		'calmpress_social_github'       => 'esc_url_raw',
	);
	$placements = array( 'header', 'before_content', 'after_content', 'sidebar', 'footer' );
	foreach ( $placements as $placement ) {
		$settings[ 'calmpress_ad_' . $placement . '_enabled' ] = 'calmpress_sanitize_checkbox';
		$settings[ 'calmpress_ad_' . $placement . '_html' ]    = 'wp_kses_post';
	}

	foreach ( $settings as $key => $callback ) {
		register_setting(
			'calmpress_settings',
			$key,
			array(
				'type'              => 'string',
				'sanitize_callback' => $callback,
				'default'           => calmpress_get_option( $key ),
			)
		);
	}
}
add_action( 'admin_init', 'calmpress_register_settings' );

/**
 * Add the settings page under Appearance.
 *
 * @return void
 */
function calmpress_settings_menu() {
	add_theme_page(
		'CalmPress Ayarları',
		'CalmPress Ayarları',
		'manage_options',
		'calmpress-settings',
		'calmpress_render_settings_page'
	);
}
add_action( 'admin_menu', 'calmpress_settings_menu' );

/**
 * Enqueue assets only on the CalmPress settings screen.
 *
 * @param string $hook_suffix Current admin screen hook.
 * @return void
 */
function calmpress_admin_assets( $hook_suffix ) {
	if ( 'appearance_page_calmpress-settings' !== $hook_suffix ) {
		return;
	}
	$css = get_template_directory() . '/assets/css/admin.css';
	$js  = get_template_directory() . '/assets/js/admin.js';
	wp_enqueue_style( 'calmpress-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), file_exists( $css ) ? (string) filemtime( $css ) : CALMPRESS_VERSION );
	wp_enqueue_script( 'calmpress-admin', get_template_directory_uri() . '/assets/js/admin.js', array(), file_exists( $js ) ? (string) filemtime( $js ) : CALMPRESS_VERSION, true );
	wp_localize_script(
		'calmpress-admin',
		'calmpressAdmin',
		array(
			'confirmLeave' => 'Kaydedilmemiş değişiklikler var. Bu sayfadan ayrılmak istediğinize emin misiniz?',
		)
	);
}
add_action( 'admin_enqueue_scripts', 'calmpress_admin_assets' );

/**
 * Render one accessible field.
 *
 * @param string $key Setting key.
 * @param string $type Input type.
 * @param string $label Label.
 * @param string $description Help text.
 * @param array  $args Optional arguments.
 * @return void
 */
function calmpress_admin_field( $key, $type, $label, $description = '', $args = array() ) {
	$value = calmpress_get_option( $key );
	$id    = sanitize_key( $key );
	$class = ! empty( $args['class'] ) ? $args['class'] : 'regular-text';
	?>
	<div class="calmpress-admin__field">
		<label for="<?php echo esc_attr( $id ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		<?php if ( 'checkbox' === $type ) : ?>
			<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="0">
			<label class="calmpress-admin__switch">
				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $value, 1 ); ?>>
				<span aria-hidden="true"></span>
				<em><?php echo esc_html( $args['checked_label'] ?? 'Etkin' ); ?></em>
			</label>
		<?php elseif ( 'textarea' === $type ) : ?>
			<textarea class="<?php echo esc_attr( $class ); ?>" rows="<?php echo esc_attr( $args['rows'] ?? 4 ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo ! empty( $args['count'] ) ? 'data-cp-count="1"' : ''; ?>><?php echo esc_textarea( (string) $value ); ?></textarea>
		<?php elseif ( 'select' === $type ) : ?>
			<select class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>">
				<?php foreach ( $args['options'] as $option_key => $option_label ) : ?>
					<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $value, $option_key ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php else : ?>
			<input class="<?php echo esc_attr( $class ); ?>" type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" <?php echo $args['attrs'] ?? ''; ?>>
			<?php if ( 'color' === $type ) : ?><span class="calmpress-admin__swatch" data-cp-swatch="<?php echo esc_attr( $id ); ?>" aria-hidden="true"></span><?php endif; ?>
		<?php endif; ?>
		<?php if ( $description ) : ?><p class="description"><?php echo esc_html( $description ); ?></p><?php endif; ?>
		<?php if ( ! empty( $args['count'] ) ) : ?><small class="calmpress-admin__count" data-cp-count-for="<?php echo esc_attr( $id ); ?>">0 karakter</small><?php endif; ?>
	</div>
	<?php
}

/**
 * Render the custom CalmPress dashboard.
 *
 * @return void
 */
function calmpress_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim izniniz yok.', 'calmpress' ) );
	}
	$tabs = array(
		'dashboard' => array( 'Kontrol paneli', 'dashicons-dashboard' ),
		'appearance' => array( 'Görünüm', 'dashicons-admin-customizer' ),
		'header'    => array( 'Üst bilgi', 'dashicons-align-wide' ),
		'seo'       => array( 'SEO', 'dashicons-search' ),
		'performance' => array( 'Performans', 'dashicons-performance' ),
		'social'    => array( 'Sosyal', 'dashicons-share' ),
		'ads'       => array( 'Reklamlar', 'dashicons-megaphone' ),
		'widgets'   => array( "Widget'lar / yardım", 'dashicons-editor-help' ),
	);
	$tab = isset( $_GET['cp_tab'] ) ? sanitize_key( wp_unslash( $_GET['cp_tab'] ) ) : 'dashboard';
	$tab = array_key_exists( $tab, $tabs ) ? $tab : 'dashboard';
	$base_url = admin_url( 'themes.php?page=calmpress-settings' );
	?>
	<div class="wrap calmpress-admin">
		<div class="calmpress-admin__hero">
			<div>
				<p class="calmpress-admin__eyebrow">CALMPRESS <?php echo esc_html( CALMPRESS_VERSION ); ?></p>
				<h1>CalmPress kontrol paneli</h1>
				<p class="calmpress-admin__lead">Sitenizin görünümünü, hızını ve güvenli yayın ayarlarını tek bir sakin çalışma alanından yönetin.</p>
			</div>
			<div class="calmpress-admin__hero-actions">
				<span class="calmpress-admin__status"><span></span> Tema etkin</span>
				<a class="button button-primary button-hero" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">Siteyi görüntüle</a>
			</div>
		</div>
		<div class="calmpress-admin__layout">
			<aside class="calmpress-admin__sidebar" aria-label="CalmPress bölümleri">
				<nav>
					<?php foreach ( $tabs as $key => $tab_data ) : ?>
						<a class="<?php echo $key === $tab ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'cp_tab', $key, $base_url ) ); ?>" <?php echo $key === $tab ? 'aria-current="page"' : ''; ?>>
							<span class="dashicons <?php echo esc_attr( $tab_data[1] ); ?>" aria-hidden="true"></span><?php echo esc_html( $tab_data[0] ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
			</aside>
			<main class="calmpress-admin__main">
				<form class="calmpress-admin__form" action="options.php" method="post">
					<?php settings_fields( 'calmpress_settings' ); ?>
				<?php if ( 'dashboard' === $tab ) : ?>
					<section class="calmpress-admin__card calmpress-admin__welcome">
						<div><span class="calmpress-admin__kicker">Hazır mısınız?</span><h2>Sitenizi kendi ritminize göre tasarlayın.</h2><p>Soldaki bölümlerden birini seçerek başlayın. Ayarlar WordPress’in güvenli Settings API’si ile kaydedilir.</p></div>
						<div class="calmpress-admin__quick-links"><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">Özelleştiriciye git <span>↗</span></a><a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>">Widget'ları düzenle <span>↗</span></a><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">Menüleri yönet <span>↗</span></a></div>
					</section>
					<div class="calmpress-admin__stats">
						<div><span>Vurgu rengi</span><strong class="calmpress-admin__stat-color" style="--cp-stat-color: <?php echo esc_attr( calmpress_get_option( 'calmpress_accent_color' ) ); ?>"></strong><b><?php echo esc_html( calmpress_get_option( 'calmpress_accent_color' ) ); ?></b></div>
						<div><span>Kart düzeni</span><strong><?php echo esc_html( calmpress_get_option( 'calmpress_cards_per_row' ) ); ?> sütun</strong><b><?php echo esc_html( calmpress_get_option( 'calmpress_container_width' ) ); ?> px genişlik</b></div>
						<div><span>Reklam modülü</span><strong><?php echo calmpress_get_option( 'calmpress_ads_enabled' ) ? 'Etkin' : 'Kapalı'; ?></strong><b>Güvenli HTML filtreli</b></div>
					</div>
				<?php elseif ( 'appearance' === $tab ) : ?>
					<section class="calmpress-admin__card"><div class="calmpress-admin__card-head"><div><span class="calmpress-admin__kicker">Marka ve düzen</span><h2>Görünüm ayarları</h2></div><p>Ön yüz CSS değişkenleri bu değerlerle anında uyumlanır.</p></div>
						<div class="calmpress-admin__grid calmpress-admin__grid--2">
							<?php calmpress_admin_field( 'calmpress_accent_color', 'color', 'Vurgu rengi', 'Bağlantılar, düğmeler ve klavye odağı için kullanılır.', array( 'class' => 'calmpress-admin__color' ) ); ?>
							<?php calmpress_admin_field( 'calmpress_container_width', 'number', 'İçerik genişliği (px)', '960–1600 px arasında bir değer seçin.', array( 'attrs' => 'min="960" max="1600"' ) ); ?>
							<?php calmpress_admin_field( 'calmpress_cards_per_row', 'number', 'Kart sütunları', 'Geniş ekranlarda 1–4 kart.', array( 'attrs' => 'min="1" max="4"' ) ); ?>
							<?php calmpress_admin_field( 'calmpress_border_radius', 'number', 'Köşe yuvarlaklığı (px)', 'Kartların ve yüzeylerin yumuşaklık düzeyi.', array( 'attrs' => 'min="4" max="32"' ) ); ?>
							<?php calmpress_admin_field( 'calmpress_density', 'select', 'Arayüz yoğunluğu', 'İç boşlukları sitenizin içeriğine göre ayarlayın.', array( 'options' => array( 'compact' => 'Sıkı', 'comfortable' => 'Rahat', 'spacious' => 'Geniş' ) ) ); ?>
							<?php calmpress_admin_field( 'calmpress_show_logo', 'checkbox', 'Logo göster', 'Özel logo tanımlıysa üst bilgide görüntüler.' ); ?>
						</div>
					</section>
				<?php elseif ( 'header' === $tab ) : ?>
					<section class="calmpress-admin__card"><div class="calmpress-admin__card-head"><div><span class="calmpress-admin__kicker">İlk izlenim</span><h2>Üst bilgi ve alt bilgi</h2></div><p>Ziyaretçilerin sitenizle kurduğu ilk ve son temas.</p></div>
						<div class="calmpress-admin__grid calmpress-admin__grid--2">
							<?php calmpress_admin_field( 'calmpress_announcement_text', 'text', 'Duyuru metni', 'Menünün üzerinde erişilebilir bir duyuru görüntülenir.', array( 'class' => 'widefat' ) ); ?>
							<?php calmpress_admin_field( 'calmpress_announcement_link', 'url', 'Duyuru bağlantısı', 'Duyuruya tıklanınca gidilecek adres.', array( 'class' => 'widefat' ) ); ?>
							<?php calmpress_admin_field( 'calmpress_show_footer_widgets', 'checkbox', 'Alt bilgi widget alanı', 'Footer widget alanını sitenin altında göster.' ); ?>
							<?php calmpress_admin_field( 'calmpress_show_footer_menu', 'checkbox', 'Alt bilgi menüsü', 'Footer menüsü konumunu göster.' ); ?>
							<?php calmpress_admin_field( 'calmpress_footer_text', 'text', 'Alt bilgi metni', 'Telif satırının altında kısa bir metin.', array( 'class' => 'widefat' ) ); ?>
						</div>
					</section>
				<?php elseif ( 'seo' === $tab ) : ?>
					<section class="calmpress-admin__card"><div class="calmpress-admin__card-head"><div><span class="calmpress-admin__kicker">Bulunabilirlik</span><h2>SEO ve yapılandırılmış veri</h2></div><p>Arama motorlarına açık, temiz ve güvenilir bilgiler gönderin.</p></div>
						<?php calmpress_admin_field( 'calmpress_seo_title', 'text', 'Varsayılan SEO başlığı', 'Özel başlığı olmayan sayfalar için kullanılır.', array( 'class' => 'widefat', 'count' => true ) ); ?>
						<?php calmpress_admin_field( 'calmpress_seo_description', 'textarea', 'Varsayılan meta açıklaması', 'Özel özet bulunmayan içeriklerde kullanılır.', array( 'class' => 'widefat', 'rows' => 5, 'count' => true ) ); ?>
						<div class="calmpress-admin__grid calmpress-admin__grid--2">
							<?php calmpress_admin_field( 'calmpress_organization_name', 'text', 'Kuruluş adı', 'Yapılandırılmış veride yayıncı adı.', array( 'class' => 'widefat' ) ); ?>
							<?php calmpress_admin_field( 'calmpress_organization_logo', 'url', 'Kuruluş logosu URL’si', 'Medya kütüphanesindeki güvenilir bir görsel URL’si.', array( 'class' => 'widefat' ) ); ?>
						</div>
						<?php calmpress_admin_field( 'calmpress_schema_enabled', 'checkbox', 'schema.org JSON-LD etkin', 'Yazı ve uygulama sayfalarına yapılandırılmış veri ekler.' ); ?>
					</section>
				<?php elseif ( 'performance' === $tab ) : ?>
					<section class="calmpress-admin__card"><div class="calmpress-admin__card-head"><div><span class="calmpress-admin__kicker">Hız ve odak</span><h2>Performans</h2></div><p>Gereksiz varlıkları kapatın; değişiklikleri ölçümleyerek uygulayın.</p></div>
						<?php calmpress_admin_field( 'calmpress_disable_emoji', 'checkbox', 'WordPress emoji dosyalarını kapat', 'Emoji kullanmayan sitelerde algılama betiğini yüklemez.' ); ?>
						<?php calmpress_admin_field( 'calmpress_disable_dashicons', 'checkbox', 'Ön yüzde Dashicons’u kapat', 'Oturum açmamış ziyaretçiler için kullanılmıyorsa kapatın.' ); ?>
						<?php calmpress_admin_field( 'calmpress_disable_embeds', 'checkbox', 'Gömme (embed) desteğini kapat', 'WordPress embed betiğini ve keşif bağlantılarını kaldırır.' ); ?>
						<?php calmpress_admin_field( 'calmpress_lazy_images', 'checkbox', 'Görselleri tembel yükle', 'İlk görsel hariç görsellerin tarayıcı tarafından ertelenmesini sağlar.' ); ?>
						<div class="calmpress-admin__notice">Not: Önbellek eklentiniz veya CDN’niz varsa değişiklik sonrası önbelleği temizleyin.</div>
					</section>
				<?php elseif ( 'social' === $tab ) : ?>
					<section class="calmpress-admin__card"><div class="calmpress-admin__card-head"><div><span class="calmpress-admin__kicker">Topluluk</span><h2>Sosyal profiller</h2></div><p>Alt bilgide göstermek istediğiniz profil adreslerini ekleyin.</p></div>
						<div class="calmpress-admin__grid calmpress-admin__grid--2">
							<?php foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'x' => 'X', 'youtube' => 'YouTube', 'github' => 'GitHub' ) as $network => $label ) { calmpress_admin_field( 'calmpress_social_' . $network, 'url', $label, 'Alt bilgide gösterilecek profil URL’si.', array( 'class' => 'widefat' ) ); } ?>
						</div>
					</section>
				<?php elseif ( 'ads' === $tab ) : ?>
					<section class="calmpress-admin__card"><div class="calmpress-admin__card-head"><div><span class="calmpress-admin__kicker">Gelir alanları</span><h2>Reklamlar</h2></div><p>Reklamlar varsayılan olarak kapalıdır. Yalnızca güvenli HTML kabul edilir; script etiketleri kaydedilmez.</p></div>
						<?php calmpress_admin_field( 'calmpress_ads_enabled', 'checkbox', 'Reklam modülünü etkinleştir', 'Aşağıdaki alanlardan etkin olanları ön yüzde göster.' ); ?>
						<div class="calmpress-admin__ad-grid">
							<?php foreach ( array( 'header' => 'Üst bilgi', 'before_content' => 'İçerik öncesi', 'after_content' => 'İçerik sonrası', 'sidebar' => 'Kenar çubuğu', 'footer' => 'Alt bilgi' ) as $placement => $label ) : ?>
								<div class="calmpress-admin__ad-card">
									<div class="calmpress-admin__ad-head"><h3><?php echo esc_html( $label ); ?></h3><button type="button" class="button-link calmpress-admin__preview-toggle" data-cp-preview="<?php echo esc_attr( $placement ); ?>">Önizlemeyi aç</button></div>
									<?php calmpress_admin_field( 'calmpress_ad_' . $placement . '_enabled', 'checkbox', 'Bu alanı göster', 'Yerleşimi ön yüzde göster.' ); ?>
									<?php calmpress_admin_field( 'calmpress_ad_' . $placement . '_html', 'textarea', 'Güvenli HTML', 'Bağlantı, görsel ve metin ekleyin.', array( 'class' => 'widefat', 'rows' => 5, 'count' => true ) ); ?>
									<pre class="calmpress-admin__preview" data-cp-preview-content="<?php echo esc_attr( $placement ); ?>" hidden></pre>
								</div>
							<?php endforeach; ?>
						</div>
					</section>
				<?php else : ?>
					<section class="calmpress-admin__card"><div class="calmpress-admin__card-head"><div><span class="calmpress-admin__kicker">Yardım</span><h2>Widget’lar ve hızlı kurulum</h2></div><p>CalmPress’in temel yapı taşlarını birkaç dakikada tamamlayın.</p></div>
						<ol class="calmpress-admin__steps"><li><strong>Görünümü seçin.</strong> Vurgu rengini, genişliği ve kart düzenini belirleyin.</li><li><strong>Menüleri bağlayın.</strong> <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">Menüler</a> ekranında Birincil ve Alt bilgi konumlarını atayın.</li><li><strong>Widget ekleyin.</strong> <a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>">Widget’lar</a> ekranında başlık, içerik sonrası, kenar çubuğu ve alt bilgi alanlarını kullanın.</li><li><strong>Güvenli kalın.</strong> Reklam alanlarına yalnızca HTML girin. WordPress, script ve olay özniteliklerini otomatik olarak temizler.</li></ol>
						<div class="calmpress-admin__notice">Türkçe metinler varsayılan olarak temaya dahildir. WordPress dil paketleri ve child theme çevirileri bunların üzerine yazabilir.</div>
					</section>
				<?php endif; ?>
					<?php if ( 'dashboard' !== $tab && 'widgets' !== $tab ) : ?><button type="submit" class="button button-primary button-hero">Değişiklikleri kaydet</button><?php endif; ?>
				</form>
			</main>
		</div>
	</div>
	<?php
}
