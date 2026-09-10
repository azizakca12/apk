<?php
/**
 * CalmPress live theme customizer.
 *
 * @package CalmPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize a bounded customizer text value.
 *
 * @param mixed $value Value.
 * @return string
 */
function calmpress_customize_text( $value ) {
	return sanitize_text_field( $value );
}

/**
 * Sanitize a bounded multiline value.
 *
 * @param mixed $value Value.
 * @return string
 */
function calmpress_customize_textarea( $value ) {
	return sanitize_textarea_field( $value );
}

/**
 * Sanitize a custom CSS snippet without allowing markup or executable CSS.
 *
 * This is intentionally a small, theme-scoped CSS editor, not a general code
 * editor. It is still restricted to users who can edit theme options.
 *
 * @param mixed $value CSS value.
 * @return string
 */
function calmpress_customize_css( $value ) {
	$value = wp_strip_all_tags( (string) $value );
	$value = preg_replace( '/<\/?style[^>]*>/i', '', $value );
	$value = preg_replace( '/<\?(?:php|=)?|\?>/i', '', $value );
	$value = preg_replace( '/(?:expression|javascript|vbscript|behavior|-moz-binding)\s*:/i', '', $value );
	$value = preg_replace( '/@import\s+/i', '', $value );
	return substr( trim( (string) $value ), 0, 12000 );
}

/**
 * Sanitize a selected option.
 *
 * @param mixed  $value Submitted value.
 * @param string[] $allowed Allowed values.
 * @param string $fallback Fallback.
 * @return string
 */
function calmpress_customize_choice( $value, $allowed, $fallback ) {
	$value = sanitize_key( $value );
	return in_array( $value, $allowed, true ) ? $value : $fallback;
}

/**
 * Return the settings exposed to the Customizer.
 *
 * @return array
 */
function calmpress_customize_settings() {
	return array(
		'calmpress_accent_color'             => array( 'default' => '#356ae6', 'sanitize' => 'calmpress_sanitize_accent_color', 'transport' => 'postMessage' ),
		'calmpress_accent_hover'             => array( 'default' => '#234fb8', 'sanitize' => 'calmpress_sanitize_accent_color', 'transport' => 'postMessage' ),
		'calmpress_accent_contrast'          => array( 'default' => '#ffffff', 'sanitize' => 'calmpress_sanitize_accent_color', 'transport' => 'postMessage' ),
		'calmpress_container_width'          => array( 'default' => 1120, 'sanitize' => 'calmpress_sanitize_container_width', 'transport' => 'postMessage' ),
		'calmpress_cards_per_row'            => array( 'default' => 3, 'sanitize' => 'calmpress_sanitize_cards_per_row', 'transport' => 'postMessage' ),
		'calmpress_border_radius'            => array( 'default' => 16, 'sanitize' => 'calmpress_sanitize_border_radius', 'transport' => 'postMessage' ),
		'calmpress_density'                  => array( 'default' => 'comfortable', 'sanitize' => 'calmpress_sanitize_density', 'transport' => 'postMessage' ),
		'calmpress_body_font'                => array( 'default' => 'system', 'sanitize' => 'calmpress_customize_font', 'transport' => 'postMessage' ),
		'calmpress_heading_font'             => array( 'default' => 'system', 'sanitize' => 'calmpress_customize_font', 'transport' => 'postMessage' ),
		'calmpress_theme_default'            => array( 'default' => 'system', 'sanitize' => 'calmpress_customize_theme', 'transport' => 'refresh' ),
		'calmpress_reduced_motion'           => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_custom_css'               => array( 'default' => '', 'sanitize' => 'calmpress_customize_css', 'transport' => 'refresh' ),
		'calmpress_show_logo'                => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_sticky_header'            => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_announcement_text'        => array( 'default' => '', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_announcement_link'        => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_announcement_style'       => array( 'default' => 'soft', 'sanitize' => 'calmpress_customize_announcement_style', 'transport' => 'refresh' ),
		'calmpress_show_header_widgets'      => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_primary_nav_style'        => array( 'default' => 'minimal', 'sanitize' => 'calmpress_customize_nav_style', 'transport' => 'refresh' ),
		'calmpress_hero_eyebrow'             => array( 'default' => 'Özenli yayıncılık ve uygulama keşfi', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_hero_title'               => array( 'default' => '', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_hero_description'         => array( 'default' => '', 'sanitize' => 'sanitize_textarea_field', 'transport' => 'refresh' ),
		'calmpress_hero_cta_text'            => array( 'default' => '', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_hero_cta_link'            => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_show_latest_journal'      => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_show_featured_apps'      => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_section_spacing'          => array( 'default' => 'comfortable', 'sanitize' => 'calmpress_customize_spacing', 'transport' => 'postMessage' ),
		'calmpress_cards_per_section'        => array( 'default' => 3, 'sanitize' => 'calmpress_sanitize_cards_per_row', 'transport' => 'refresh' ),
		'calmpress_gradient_background'      => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_blog_archive_title'       => array( 'default' => '', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_blog_archive_description' => array( 'default' => '', 'sanitize' => 'sanitize_textarea_field', 'transport' => 'refresh' ),
		'calmpress_apps_archive_title'       => array( 'default' => '', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_apps_archive_description' => array( 'default' => '', 'sanitize' => 'sanitize_textarea_field', 'transport' => 'refresh' ),
		'calmpress_excerpt_length'            => array( 'default' => 24, 'sanitize' => 'calmpress_customize_excerpt_length', 'transport' => 'refresh' ),
		'calmpress_show_metadata'            => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_app_download_label'       => array( 'default' => 'APK indir', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_show_app_facts'           => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_footer_text'              => array( 'default' => '', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_show_footer_widgets'      => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_show_footer_menu'         => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_footer_columns'           => array( 'default' => 2, 'sanitize' => 'calmpress_customize_footer_columns', 'transport' => 'refresh' ),
		'calmpress_back_to_top'              => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_share_buttons'            => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_toc_enabled'              => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_related_enabled'          => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_related_count'            => array( 'default' => 3, 'sanitize' => 'absint', 'transport' => 'refresh' ),
		'calmpress_author_box'               => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_popular_widget'           => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_popular_count'            => array( 'default' => 5, 'sanitize' => 'absint', 'transport' => 'refresh' ),
		'calmpress_categories_widget'        => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_modal_search'             => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_mobile_nav'              => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_campaign_enabled'         => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_campaign_text'            => array( 'default' => 'Yeni yazıları keşfedin.', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_campaign_link'            => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_ads_enabled'              => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_high_contrast'            => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_focus_color'              => array( 'default' => '#356ae6', 'sanitize' => 'calmpress_sanitize_accent_color', 'transport' => 'postMessage' ),
		'calmpress_lazy_images'              => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_disable_emoji'            => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_disable_embeds'           => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_disable_dashicons'        => array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_social_facebook'           => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_social_instagram'          => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_social_x'                  => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_social_youtube'            => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_social_github'             => array( 'default' => '', 'sanitize' => 'esc_url_raw', 'transport' => 'refresh' ),
		'calmpress_reading_time'              => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_reading_time_wpm'          => array( 'default' => 200, 'sanitize' => 'calmpress_sanitize_reading_wpm', 'transport' => 'refresh' ),
		'calmpress_reading_time_label'        => array( 'default' => 'dk okuma', 'sanitize' => 'sanitize_text_field', 'transport' => 'refresh' ),
		'calmpress_view_counter'              => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_show_views'                => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_breadcrumbs'               => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_show_app_screenshots'      => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
		'calmpress_show_app_changelog'        => array( 'default' => 1, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' ),
	);
	foreach ( array( 'header', 'before_content', 'after_content', 'sidebar', 'footer' ) as $placement ) {
		$settings[ 'calmpress_ad_' . $placement . '_enabled' ] = array( 'default' => 0, 'sanitize' => 'calmpress_sanitize_checkbox', 'transport' => 'refresh' );
	}
	return $settings;
}

function calmpress_customize_font( $value ) {
	return calmpress_customize_choice( $value, array( 'system', 'readable' ), 'system' );
}

function calmpress_customize_theme( $value ) {
	return calmpress_customize_choice( $value, array( 'system', 'light', 'dark' ), 'system' );
}

function calmpress_customize_announcement_style( $value ) {
	return calmpress_customize_choice( $value, array( 'soft', 'accent', 'minimal' ), 'soft' );
}

function calmpress_customize_nav_style( $value ) {
	return calmpress_customize_choice( $value, array( 'minimal', 'pill', 'underline' ), 'minimal' );
}

function calmpress_customize_spacing( $value ) {
	return calmpress_customize_choice( $value, array( 'compact', 'comfortable', 'spacious' ), 'comfortable' );
}

function calmpress_customize_excerpt_length( $value ) {
	return min( 60, max( 8, absint( $value ) ) );
}

function calmpress_customize_footer_columns( $value ) {
	return min( 4, max( 1, absint( $value ) ) );
}

/**
 * Register the CalmPress Customizer sections and settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function calmpress_customize_register( $wp_customize ) {
	$settings = calmpress_customize_settings();
	$capability = 'edit_theme_options';

	$wp_customize->add_panel( 'calmpress_studio', array(
		'title'       => __( 'CalmPress Studio', 'calmpress' ),
		'description' => __( 'Apple esintili, sakin ve erişilebilir tasarım sisteminizi tek yerden yönetin.', 'calmpress' ),
		'priority'    => 10,
	) );
	$sections = array(
		'calmpress_global'  => array( 'panel' => 'calmpress_studio', 'title' => __( 'Stüdyo ve marka', 'calmpress' ), 'description' => __( 'Sitenin ritmini, renklerini ve tipografisini ayarlayın.', 'calmpress' ) ),
		'calmpress_header'  => array( 'panel' => 'calmpress_studio', 'title' => __( 'Üst bilgi ve duyuru', 'calmpress' ) ),
		'calmpress_home'    => array( 'panel' => 'calmpress_studio', 'title' => __( 'Ana sayfa', 'calmpress' ) ),
		'calmpress_content' => array( 'panel' => 'calmpress_studio', 'title' => __( 'Blog ve uygulamalar', 'calmpress' ) ),
		'calmpress_footer'  => array( 'panel' => 'calmpress_studio', 'title' => __( 'Alt bilgi ve sosyal', 'calmpress' ) ),
		'calmpress_ads'     => array( 'panel' => 'calmpress_studio', 'title' => __( 'Reklam görünürlüğü', 'calmpress' ), 'description' => __( 'Reklam HTML’sini CalmPress Ayarları ekranından düzenleyin; burada yalnızca güvenli görünürlük kontrolleri bulunur.', 'calmpress' ) ),
		'calmpress_access'  => array( 'panel' => 'calmpress_studio', 'title' => __( 'Erişilebilirlik ve performans', 'calmpress' ) ),
	);
	foreach ( $sections as $id => $section ) {
		$args = array(
			'title'       => $section['title'],
			'panel'       => $section['panel'],
			'priority'    => 10,
			'description' => isset( $section['description'] ) ? $section['description'] : '',
		);
		$wp_customize->add_section( $id, $args );
	}

	foreach ( $settings as $key => $args ) {
		$wp_customize->add_setting( $key, array(
			'default'           => get_option( $key, $args['default'] ),
			'type'              => 'option',
			'capability'        => $capability,
			'sanitize_callback' => $args['sanitize'],
			'transport'         => $args['transport'],
		) );
	}

	$control = function ( $id, $section, $label, $description, $type = 'text', $control_args = array() ) use ( $wp_customize ) {
		$args = array_merge( array(
			'label'       => $label,
			'description' => $description,
			'section'     => $section,
			'settings'    => $id,
			'active_callback' => '__return_true',
		), $control_args );
		if ( 'color' === $type ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id . '_control', $args ) );
		} else {
			$args['type'] = $type;
			$wp_customize->add_control( $id . '_control', $args );
		}
	};
	$checkbox = function ( $id, $section, $label, $description = '' ) use ( $control ) {
		$control( $id, $section, $label, $description, 'checkbox' );
	};
	$select = function ( $id, $section, $label, $description, $choices ) use ( $control ) {
		$control( $id, $section, $label, $description, 'select', array( 'choices' => $choices ) );
	};

	$control( 'calmpress_accent_color', 'calmpress_global', __( 'Vurgu rengi', 'calmpress' ), __( 'Bağlantılar, düğmeler ve aktif durumlar için canlı renk.', 'calmpress' ), 'color' );
	$control( 'calmpress_accent_hover', 'calmpress_global', __( 'Vurgu üzerine gelme rengi', 'calmpress' ), __( 'Fareyle üzerine gelindiğinde kullanılacak renk.', 'calmpress' ), 'color' );
	$control( 'calmpress_accent_contrast', 'calmpress_global', __( 'Vurgu üzeri metin rengi', 'calmpress' ), __( 'Düğme metni için açık veya koyu renk seçin.', 'calmpress' ), 'color' );
	$control( 'calmpress_container_width', 'calmpress_global', __( 'Maksimum içerik genişliği', 'calmpress' ), __( '960–1600 piksel arasında sakin bir okuma genişliği.', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 960, 'max' => 1600, 'step' => 8 ) ) );
	$control( 'calmpress_cards_per_row', 'calmpress_global', __( 'Kart sütunları', 'calmpress' ), __( 'Geniş ekranlarda bir satırdaki kart sayısı.', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 1, 'max' => 4 ) ) );
	$control( 'calmpress_border_radius', 'calmpress_global', __( 'Köşe yuvarlaklığı', 'calmpress' ), __( 'Kart ve yüzeylerin yumuşaklığı (4–32 piksel).', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 4, 'max' => 32 ) ) );
	$select( 'calmpress_density', 'calmpress_global', __( 'Arayüz yoğunluğu', 'calmpress' ), __( 'Kart iç boşluklarını içerik ritminize uydurun.', 'calmpress' ), array( 'compact' => __( 'Sıkı', 'calmpress' ), 'comfortable' => __( 'Rahat', 'calmpress' ), 'spacious' => __( 'Geniş', 'calmpress' ) ) );
	$select( 'calmpress_body_font', 'calmpress_global', __( 'Gövde yazı karakteri', 'calmpress' ), __( 'Harici yazı tipi yüklemeden okunaklı bir sistem seçimi.', 'calmpress' ), array( 'system' => __( 'Sistem / SF Pro', 'calmpress' ), 'readable' => __( 'Okunaklı / Atkinson benzeri', 'calmpress' ) ) );
	$select( 'calmpress_heading_font', 'calmpress_global', __( 'Başlık yazı karakteri', 'calmpress' ), __( 'Başlıkların karakterini belirleyin.', 'calmpress' ), array( 'system' => __( 'Sistem / SF Display', 'calmpress' ), 'readable' => __( 'Okunaklı / insanist', 'calmpress' ) ) );
	$select( 'calmpress_theme_default', 'calmpress_global', __( 'Varsayılan renk teması', 'calmpress' ), __( 'Ziyaretçinin yerel tercihi yine önceliklidir.', 'calmpress' ), array( 'system' => __( 'Sistem', 'calmpress' ), 'light' => __( 'Açık', 'calmpress' ), 'dark' => __( 'Koyu', 'calmpress' ) ) );
	$checkbox( 'calmpress_reduced_motion', 'calmpress_global', __( 'Hareketi azalt', 'calmpress' ), __( 'Animasyonları herkes için varsayılan olarak sakinleştirir.', 'calmpress' ) );
	$control( 'calmpress_custom_css', 'calmpress_global', __( 'Özel CSS', 'calmpress' ), __( 'Yalnızca CSS yazın. PHP, HTML, JavaScript ve script etiketleri desteklenmez; değişiklikleri yayınlamadan önce kontrol edin.', 'calmpress' ), 'textarea', array( 'input_attrs' => array( 'rows' => 8, 'placeholder' => '.hero { /* küçük dokunuşlar */ }' ) ) );

	$checkbox( 'calmpress_show_logo', 'calmpress_header', __( 'Logoyu göster', 'calmpress' ), __( 'Site logosu ayarlıysa üst bilgide görünür.', 'calmpress' ) );
	$checkbox( 'calmpress_sticky_header', 'calmpress_header', __( 'Yapışkan üst bilgi', 'calmpress' ), __( 'Sayfa kaydırılırken üst bilgiyi görünür tutar.', 'calmpress' ) );
	$control( 'calmpress_announcement_text', 'calmpress_header', __( 'Duyuru metni', 'calmpress' ), __( 'Üst menünün üzerinde kısa, erişilebilir bir duyuru.', 'calmpress' ) );
	$control( 'calmpress_announcement_link', 'calmpress_header', __( 'Duyuru bağlantısı', 'calmpress' ), __( 'Duyuruya tıklanınca açılacak güvenli adres.', 'calmpress' ), 'url' );
	$select( 'calmpress_announcement_style', 'calmpress_header', __( 'Duyuru stili', 'calmpress' ), __( 'Duyurunun görsel vurgusunu seçin.', 'calmpress' ), array( 'soft' => __( 'Yumuşak yüzey', 'calmpress' ), 'accent' => __( 'Vurgu bandı', 'calmpress' ), 'minimal' => __( 'Minimal', 'calmpress' ) ) );
	$checkbox( 'calmpress_show_header_widgets', 'calmpress_header', __( 'Üst bilgi bileşenlerini göster', 'calmpress' ), __( 'Widget alanını görünür tutar.', 'calmpress' ) );
	$select( 'calmpress_primary_nav_style', 'calmpress_header', __( 'Birincil menü stili', 'calmpress' ), __( 'Menü bağlantılarının sakin görünümünü belirleyin.', 'calmpress' ), array( 'minimal' => __( 'Minimal', 'calmpress' ), 'pill' => __( 'Hap düğmeler', 'calmpress' ), 'underline' => __( 'Alt çizgi', 'calmpress' ) ) );

	$control( 'calmpress_hero_eyebrow', 'calmpress_home', __( 'Hero üst etiketi', 'calmpress' ), __( 'Başlığın üstünde görünen kısa ifade.', 'calmpress' ) );
	$control( 'calmpress_hero_title', 'calmpress_home', __( 'Hero başlığı', 'calmpress' ), __( 'Boş bırakılırsa site adı kullanılır.', 'calmpress' ) );
	$control( 'calmpress_hero_description', 'calmpress_home', __( 'Hero açıklaması', 'calmpress' ), __( 'Boş bırakılırsa site açıklaması kullanılır.', 'calmpress' ), 'textarea' );
	$control( 'calmpress_hero_cta_text', 'calmpress_home', __( 'Hero düğme metni', 'calmpress' ), __( 'İsteğe bağlı eylem düğmesi.', 'calmpress' ) );
	$control( 'calmpress_hero_cta_link', 'calmpress_home', __( 'Hero düğme bağlantısı', 'calmpress' ), __( 'Düğmenin gideceği adres.', 'calmpress' ), 'url' );
	$checkbox( 'calmpress_show_latest_journal', 'calmpress_home', __( 'Son günlük bölümünü göster', 'calmpress' ) );
	$checkbox( 'calmpress_show_featured_apps', 'calmpress_home', __( 'Öne çıkan uygulamalar bölümünü göster', 'calmpress' ) );
	$select( 'calmpress_section_spacing', 'calmpress_home', __( 'Bölüm aralığı', 'calmpress' ), __( 'Ana sayfa bölümleri arasındaki nefes alanı.', 'calmpress' ), array( 'compact' => __( 'Sıkı', 'calmpress' ), 'comfortable' => __( 'Rahat', 'calmpress' ), 'spacious' => __( 'Geniş', 'calmpress' ) ) );
	$control( 'calmpress_cards_per_section', 'calmpress_home', __( 'Bölüm başına kart', 'calmpress' ), __( 'Ana sayfada her bölümde 1–4 içerik.', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 1, 'max' => 4 ) ) );
	$checkbox( 'calmpress_gradient_background', 'calmpress_home', __( 'Hero gradyan arka planı', 'calmpress' ), __( 'Hero yüzeyine çok hafif vurgu gradyanı ekler.', 'calmpress' ) );

	$control( 'calmpress_blog_archive_title', 'calmpress_content', __( 'Blog arşiv başlığı', 'calmpress' ), __( 'Boş bırakılırsa WordPress arşiv başlığı kullanılır.', 'calmpress' ) );
	$control( 'calmpress_blog_archive_description', 'calmpress_content', __( 'Blog arşiv açıklaması', 'calmpress' ), __( 'Günlük sayfasının üstünde gösterilir.', 'calmpress' ), 'textarea' );
	$control( 'calmpress_apps_archive_title', 'calmpress_content', __( 'Uygulama arşiv başlığı', 'calmpress' ), __( 'Uygulamalar sayfasının başlığı.', 'calmpress' ) );
	$control( 'calmpress_apps_archive_description', 'calmpress_content', __( 'Uygulama arşiv açıklaması', 'calmpress' ), __( 'Uygulamalar sayfasının üst açıklaması.', 'calmpress' ), 'textarea' );
	$control( 'calmpress_excerpt_length', 'calmpress_content', __( 'Özet kelime sayısı', 'calmpress' ), __( 'Kartlarda 8–60 kelime arasında gösterilir.', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 8, 'max' => 60 ) ) );
	$checkbox( 'calmpress_show_metadata', 'calmpress_content', __( 'Yayın bilgilerini göster', 'calmpress' ), __( 'Yazı tarihini ve yazarını kartlarda/sayfalarda gösterir.', 'calmpress' ) );
	$control( 'calmpress_app_download_label', 'calmpress_content', __( 'Uygulama indirme düğmesi', 'calmpress' ), __( 'Uygulama detaylarındaki düğmenin metni.', 'calmpress' ) );
	$checkbox( 'calmpress_show_app_facts', 'calmpress_content', __( 'Uygulama bilgilerini göster', 'calmpress' ), __( 'Sürüm, platform ve geliştirici gibi bilgileri gösterir.', 'calmpress' ) );
	$checkbox( 'calmpress_show_app_screenshots', 'calmpress_content', __( 'Uygulama ekran görüntülerini göster', 'calmpress' ), __( 'Uygulama sayfasında yüklenen ekran görüntüsü galerisini gösterir.', 'calmpress' ) );
	$checkbox( 'calmpress_show_app_changelog', 'calmpress_content', __( 'Uygulama sürüm notlarını göster', 'calmpress' ), __( 'Değişiklik günlüğü girildiyse uygulama sayfasında listeler.', 'calmpress' ) );
	$checkbox( 'calmpress_reading_time', 'calmpress_content', __( 'Okuma süresini göster', 'calmpress' ), __( 'Yazılarda ve kartlarda tahmini "X dk okuma" bilgisini gösterir.', 'calmpress' ) );
	$control( 'calmpress_reading_time_wpm', 'calmpress_content', __( 'Dakikadaki kelime sayısı', 'calmpress' ), __( 'Okuma hızı tahmini için 120-320 arasında bir değer.', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 120, 'max' => 320 ) ) );
	$control( 'calmpress_reading_time_label', 'calmpress_content', __( 'Okuma süresi etiketi', 'calmpress' ), __( 'Sayının sonrasında görünecek kısa Türkçe ifade.', 'calmpress' ) );
	$checkbox( 'calmpress_view_counter', 'calmpress_content', __( 'Görüntülenme sayacını etkinleştir', 'calmpress' ), __( 'Ziyaretçi başına günde bir kez, çerez tabanlı ve gizlilik dostu sayım yapar.', 'calmpress' ) );
	$checkbox( 'calmpress_show_views', 'calmpress_content', __( 'Görüntülenme rozetini göster', 'calmpress' ), __( 'Sayaç etkinse göz simgeli rozeti gösterir.', 'calmpress' ) );
	$checkbox( 'calmpress_breadcrumbs', 'calmpress_content', __( 'İzlek (breadcrumb) gezinmesini göster', 'calmpress' ), __( 'Yazı, sayfa ve arşivlerin üstünde erişilebilir bir gezinme yolu gösterir.', 'calmpress' ) );

	$control( 'calmpress_footer_text', 'calmpress_footer', __( 'Alt bilgi metni', 'calmpress' ), __( 'Telif satırının altında kısa bir marka notu.', 'calmpress' ) );
	$checkbox( 'calmpress_show_footer_menu', 'calmpress_footer', __( 'Alt bilgi menüsünü göster', 'calmpress' ) );
	$checkbox( 'calmpress_show_footer_widgets', 'calmpress_footer', __( 'Alt bilgi bileşenlerini göster', 'calmpress' ) );
	$control( 'calmpress_footer_columns', 'calmpress_footer', __( 'Alt bilgi sütunları', 'calmpress' ), __( '1–4 sütunluk düzen tercihi.', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 1, 'max' => 4 ) ) );
	$checkbox( 'calmpress_back_to_top', 'calmpress_footer', __( 'Başa dön düğmesi', 'calmpress' ), __( 'Uzun sayfalarda erişilebilir bir hızlı dönüş düğmesi gösterir.', 'calmpress' ) );
	$checkbox( 'calmpress_share_buttons', 'calmpress_footer', __( 'Paylaş düğmelerini göster', 'calmpress' ), __( 'Yazılarda Web Share ve kopyalama yedeği sunar.', 'calmpress' ) );
	$checkbox( 'calmpress_toc_enabled', 'calmpress_content', __( 'İçindekiler tablosunu göster', 'calmpress' ), __( 'Yazı ve sayfalardaki H2/H3 başlıklarından erişilebilir bir liste oluşturur.', 'calmpress' ) );
	$checkbox( 'calmpress_related_enabled', 'calmpress_content', __( 'İlgili yazıları göster', 'calmpress' ), __( 'İçerik içinde ve yazının sonunda benzer kategori/etiketleri gösterir.', 'calmpress' ) );
	$control( 'calmpress_related_count', 'calmpress_content', __( 'İlgili yazı sayısı', 'calmpress' ), __( 'Yazı sonunda gösterilecek kart sayısı (1–6).', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 1, 'max' => 6 ) ) );
	$checkbox( 'calmpress_author_box', 'calmpress_content', __( 'Yazar kutusunu göster', 'calmpress' ), __( 'Yazının altında avatar, biyografi ve yazar bağlantısı gösterir.', 'calmpress' ) );
	$checkbox( 'calmpress_popular_widget', 'calmpress_content', __( 'Çok okunanlar bölümünü göster', 'calmpress' ), __( 'Kenar çubuğunda yorum sayısına göre popüler yazıları listeler.', 'calmpress' ) );
	$control( 'calmpress_popular_count', 'calmpress_content', __( 'Çok okunanlar sayısı', 'calmpress' ), __( 'Kenar çubuğunda gösterilecek yazı sayısı (1–10).', 'calmpress' ), 'number', array( 'input_attrs' => array( 'min' => 1, 'max' => 10 ) ) );
	$checkbox( 'calmpress_categories_widget', 'calmpress_content', __( 'CalmPress kategorilerini göster', 'calmpress' ), __( 'Kenar çubuğunda özel kategori bölümünü gösterir.', 'calmpress' ) );
	$checkbox( 'calmpress_modal_search', 'calmpress_header', __( 'Açılır aramayı göster', 'calmpress' ), __( 'Başlıkta klavye odaklı arama penceresi açan düğmeyi gösterir.', 'calmpress' ) );
	$checkbox( 'calmpress_mobile_nav', 'calmpress_footer', __( 'Mobil alt gezinmeyi göster', 'calmpress' ), __( 'Küçük ekranlarda menü, ana sayfa ve arama kısayolları sunar.', 'calmpress' ) );
	$checkbox( 'calmpress_campaign_enabled', 'calmpress_footer', __( 'Alt kampanya bildirimini göster', 'calmpress' ), __( 'Kapatma tercihini cihazda saklayan sakin bir bildirim bandı.', 'calmpress' ) );
	$control( 'calmpress_campaign_text', 'calmpress_footer', __( 'Kampanya metni', 'calmpress' ), __( 'Bildirimde görünecek kısa Türkçe metin.', 'calmpress' ) );
	$control( 'calmpress_campaign_link', 'calmpress_footer', __( 'Kampanya bağlantısı', 'calmpress' ), __( 'İsteğe bağlı güvenli bağlantı.', 'calmpress' ), 'url' );
	foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'x' => 'X', 'youtube' => 'YouTube', 'github' => 'GitHub' ) as $network => $label ) {
		$control( 'calmpress_social_' . $network, 'calmpress_footer', $label, __( 'Alt bilgide gösterilecek profil URL’si.', 'calmpress' ), 'url' );
	}

	$checkbox( 'calmpress_ads_enabled', 'calmpress_ads', __( 'Reklam modülünü etkinleştir', 'calmpress' ), __( 'HTML içeriklerini CalmPress Ayarları ekranından düzenleyin.', 'calmpress' ) );
	foreach ( array( 'header' => __( 'Üst bilgi', 'calmpress' ), 'before_content' => __( 'İçerik öncesi', 'calmpress' ), 'after_content' => __( 'İçerik sonrası', 'calmpress' ), 'sidebar' => __( 'Kenar çubuğu', 'calmpress' ), 'footer' => __( 'Alt bilgi', 'calmpress' ) ) as $placement => $label ) {
		$checkbox( 'calmpress_ad_' . $placement . '_enabled', 'calmpress_ads', sprintf( __( '%s reklamını göster', 'calmpress' ), $label ), __( 'Bu alanı ön yüzde görünür yapar.', 'calmpress' ) );
	}

	$checkbox( 'calmpress_high_contrast', 'calmpress_access', __( 'Yüksek kontrast modu', 'calmpress' ), __( 'Yüzey ve metin kontrastını belirginleştirir.', 'calmpress' ) );
	$control( 'calmpress_focus_color', 'calmpress_access', __( 'Odak halkası rengi', 'calmpress' ), __( 'Klavye kullanıcıları için görünür odak rengi.', 'calmpress' ), 'color' );
	$checkbox( 'calmpress_lazy_images', 'calmpress_access', __( 'Görselleri tembel yükle', 'calmpress' ) );
	$checkbox( 'calmpress_disable_emoji', 'calmpress_access', __( 'Emoji varlıklarını kapat', 'calmpress' ) );
	$checkbox( 'calmpress_disable_embeds', 'calmpress_access', __( 'Gömme (embed) varlıklarını kapat', 'calmpress' ) );
	$checkbox( 'calmpress_disable_dashicons', 'calmpress_access', __( 'Dashicons’u ön yüzde kapat', 'calmpress' ) );
}
add_action( 'customize_register', 'calmpress_customize_register' );

/**
 * Enqueue the tiny postMessage preview bridge.
 *
 * @return void
 */
function calmpress_customize_preview() {
	wp_enqueue_script( 'calmpress-customizer-preview', get_template_directory_uri() . '/assets/js/customizer-preview.js', array( 'customize-preview' ), CALMPRESS_VERSION, true );
}
add_action( 'customize_preview_init', 'calmpress_customize_preview' );
