<?php
/**
 * CalmPress theme functions.
 *
 * @package CalmPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CALMPRESS_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/admin.php';

/**
 * Set up theme defaults and supported features.
 *
 * @return void
 */
function calmpress_setup() {
	load_theme_textdomain( 'calmpress', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'gallery', 'caption', 'search-form', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );

	add_image_size( 'calmpress-card', 720, 405, true );
	add_image_size( 'calmpress-app-icon', 320, 320, true );

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Birincil menü', 'calmpress' ),
			'footer'  => esc_html__( 'Alt bilgi menüsü', 'calmpress' ),
		)
	);
}
add_action( 'after_setup_theme', 'calmpress_setup' );

/**
 * Register widget areas.
 *
 * @return void
 */
function calmpress_widgets_init() {
	$areas = array(
		'header'         => array( __( 'Üst bilgi', 'calmpress' ), __( 'Site üst bilgisinde gösterilen bileşenler.', 'calmpress' ) ),
		'after-content'  => array( __( 'İçerik sonrası', 'calmpress' ), __( 'Ana içerikten sonra gösterilen bileşenler.', 'calmpress' ) ),
		'sidebar-primary' => array( __( 'Kenar çubuğu', 'calmpress' ), __( 'Arşiv ve yazı içeriğinin yanında gösterilen bileşenler.', 'calmpress' ) ),
		'home-before'    => array( __( 'Ana sayfa içerik öncesi', 'calmpress' ), __( 'Ana sayfa bölümlerinden önce gösterilen bileşenler.', 'calmpress' ) ),
		'home-after'     => array( __( 'Ana sayfa içerik sonrası', 'calmpress' ), __( 'Ana sayfa bölümlerinden sonra gösterilen bileşenler.', 'calmpress' ) ),
		'footer-1'       => array( __( 'Alt bilgi', 'calmpress' ), __( 'İsteğe bağlı alt bilgi bileşenleri.', 'calmpress' ) ),
	);
	foreach ( $areas as $id => $area ) {
		register_sidebar(
			array(
				'name'          => esc_html( $area[0] ),
				'id'            => $id,
				'description'   => esc_html( $area[1] ),
				'before_widget' => '<section class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
	register_widget( 'CalmPress_Recent_Apps_Widget' );
}
add_action( 'widgets_init', 'calmpress_widgets_init' );

/**
 * Enqueue the small, dependency-free front-end assets.
 *
 * @return void
 */
function calmpress_enqueue_assets() {
	$stylesheet_path = get_stylesheet_directory() . '/style.css';
	$script_path     = get_template_directory() . '/assets/js/theme.js';

	wp_enqueue_style( 'calmpress-style', get_stylesheet_uri(), array(), file_exists( $stylesheet_path ) ? (string) filemtime( $stylesheet_path ) : CALMPRESS_VERSION );
	wp_enqueue_script( 'calmpress-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), file_exists( $script_path ) ? (string) filemtime( $script_path ) : CALMPRESS_VERSION, true );
	wp_localize_script(
		'calmpress-theme',
		'calmpressTheme',
		array(
			'labels' => array(
				'system' => __( 'Sistem', 'calmpress' ),
				'light'  => __( 'Açık', 'calmpress' ),
				'dark'   => __( 'Koyu', 'calmpress' ),
			),
			'activate' => __( 'Değiştirmek için etkinleştirin.', 'calmpress' ),
		)
	);
	wp_script_add_data( 'calmpress-theme', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'calmpress_enqueue_assets' );

/**
 * Register the APK app post type and its category taxonomy.
 *
 * @return void
 */
function calmpress_register_app_content() {
	register_post_type(
		'app',
		array(
			'labels' => array(
				'name'               => esc_html__( 'Uygulamalar', 'calmpress' ),
				'singular_name'      => esc_html__( 'Uygulama', 'calmpress' ),
				'add_new'            => esc_html__( 'Uygulama ekle', 'calmpress' ),
				'add_new_item'       => esc_html__( 'Yeni uygulama ekle', 'calmpress' ),
				'edit_item'          => esc_html__( 'Uygulamayı düzenle', 'calmpress' ),
				'new_item'           => esc_html__( 'Yeni uygulama', 'calmpress' ),
				'view_item'          => esc_html__( 'Uygulamayı görüntüle', 'calmpress' ),
				'search_items'       => esc_html__( 'Uygulama ara', 'calmpress' ),
				'not_found'          => esc_html__( 'Uygulama bulunamadı', 'calmpress' ),
				'menu_name'          => esc_html__( 'Uygulamalar', 'calmpress' ),
			),
			'public'              => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-smartphone',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'apps', 'with_front' => false ),
			'taxonomies'          => array( 'app_category' ),
			'publicly_queryable'  => true,
			'show_in_nav_menus'   => true,
		)
	);

	register_taxonomy(
		'app_category',
		array( 'app' ),
		array(
			'labels'            => array(
				'name'          => esc_html__( 'Uygulama kategorileri', 'calmpress' ),
				'singular_name' => esc_html__( 'Uygulama kategorisi', 'calmpress' ),
				'menu_name'     => esc_html__( 'Kategoriler', 'calmpress' ),
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'app-category', 'with_front' => false ),
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'calmpress_register_app_content' );

/**
 * Add fields used by the app showcase.
 *
 * @return void
 */
function calmpress_add_app_meta_box() {
	add_meta_box(
		'calmpress-app-details',
		esc_html__( 'Uygulama ayrıntıları', 'calmpress' ),
		'calmpress_render_app_meta_box',
		'app',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_app', 'calmpress_add_app_meta_box' );

/**
 * Render app metadata fields.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function calmpress_render_app_meta_box( $post ) {
	wp_nonce_field( 'calmpress_save_app_details', 'calmpress_app_nonce' );
	$fields = array(
		'download_url' => array( 'label' => __( 'İndirme URL’si', 'calmpress' ), 'type' => 'url' ),
		'version'      => array( 'label' => __( 'Sürüm', 'calmpress' ), 'type' => 'text' ),
		'file_size'    => array( 'label' => __( 'Dosya boyutu', 'calmpress' ), 'type' => 'text' ),
		'platform'     => array( 'label' => __( 'Platform', 'calmpress' ), 'type' => 'text' ),
		'developer'    => array( 'label' => __( 'Geliştirici', 'calmpress' ), 'type' => 'text' ),
	);
	echo '<div class="calmpress-app-fields">';
	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, '_calmpress_' . $key, true );
		printf(
			'<p><label for="calmpress_%1$s"><strong>%2$s</strong></label><br><input class="widefat" type="%3$s" id="calmpress_%1$s" name="calmpress_%1$s" value="%4$s"></p>',
			esc_attr( $key ),
			esc_html( $field['label'] ),
			esc_attr( $field['type'] ),
			esc_attr( $value )
		);
	}
	echo '</div>';
}

/**
 * Save app metadata securely.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function calmpress_save_app_details( $post_id ) {
	if ( ! isset( $_POST['calmpress_app_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['calmpress_app_nonce'] ) ), 'calmpress_save_app_details' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) || 'app' !== get_post_type( $post_id ) ) {
		return;
	}

	$fields = array(
		'download_url' => 'esc_url_raw',
		'version'      => 'sanitize_text_field',
		'file_size'    => 'sanitize_text_field',
		'platform'     => 'sanitize_text_field',
		'developer'    => 'sanitize_text_field',
	);
	foreach ( $fields as $key => $sanitizer ) {
		$field_name = 'calmpress_' . $key;
		if ( ! isset( $_POST[ $field_name ] ) ) {
			continue;
		}
		$value = call_user_func( $sanitizer, wp_unslash( $_POST[ $field_name ] ) );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_calmpress_' . $key );
		} else {
			update_post_meta( $post_id, '_calmpress_' . $key, $value );
		}
	}
}
add_action( 'save_post_app', 'calmpress_save_app_details' );

/**
 * Return a stored app detail.
 *
 * @param int    $post_id App ID.
 * @param string $key     Detail key.
 * @return string
 */
function calmpress_app_detail( $post_id, $key ) {
	$allowed = array( 'download_url', 'version', 'file_size', 'platform', 'developer' );
	if ( ! in_array( $key, $allowed, true ) ) {
		return '';
	}
	return (string) get_post_meta( $post_id, '_calmpress_' . $key, true );
}

/**
 * Print a concise SEO description for singular content.
 *
 * @return void
 */
function calmpress_meta_description() {
	if ( is_admin() || ! ( is_singular() || is_home() || is_front_page() ) ) {
		return;
	}
	$description = '';
	if ( is_singular() && has_excerpt() ) {
		$description = get_the_excerpt();
	} elseif ( is_home() ) {
		$description = get_bloginfo( 'description' );
	}
	if ( ! $description ) {
		$description = calmpress_get_option( 'calmpress_seo_description' );
	}
	if ( ! $description ) {
		return;
	}
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
}
add_action( 'wp_head', 'calmpress_meta_description', 1 );

/**
 * Use the configured SEO title when a document has no more specific title.
 *
 * @param array $parts Document title parts.
 * @return array
 */
function calmpress_document_title_parts( $parts ) {
	$title = calmpress_get_option( 'calmpress_seo_title' );
	if ( $title && ( is_front_page() || is_home() ) ) {
		$parts['title'] = $title;
	}
	return $parts;
}
add_filter( 'document_title_parts', 'calmpress_document_title_parts' );

/**
 * Add schema.org JSON-LD for posts and apps.
 *
 * @return void
 */
function calmpress_schema_markup() {
	if ( is_admin() || ! is_singular() || ! calmpress_get_option( 'calmpress_schema_enabled' ) ) {
		return;
	}
	$post_type = get_post_type();
	$schema    = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'app' === $post_type ? 'SoftwareApplication' : 'Article',
		'name'        => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ),
		'url'         => get_permalink(),
		'datePublished' => get_the_date( DATE_W3C ),
		'dateModified'  => get_the_modified_date( DATE_W3C ),
		'author'      => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
		),
	);
	$organization_name = calmpress_get_option( 'calmpress_organization_name' );
	$organization_logo = calmpress_get_option( 'calmpress_organization_logo' );
	if ( $organization_name ) {
		$schema['publisher'] = array(
			'@type' => 'Organization',
			'name'  => $organization_name,
		);
		if ( $organization_logo ) {
			$schema['publisher']['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => esc_url_raw( $organization_logo ),
			);
		}
	}
	if ( has_post_thumbnail() ) {
		$schema['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}
	if ( 'app' === $post_type ) {
		$schema['operatingSystem'] = calmpress_app_detail( get_the_ID(), 'platform' ) ?: 'Android';
		$schema['applicationCategory'] = 'MobileApplication';
		$download_url = calmpress_app_detail( get_the_ID(), 'download_url' );
		if ( $download_url ) {
			$schema['downloadUrl'] = $download_url;
		}
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'calmpress_schema_markup', 20 );

/**
 * Use accessible image attributes for theme thumbnails.
 *
 * @param array $attr       Image attributes.
 * @param WP_Post $attachment Attachment object.
 * @param string|array $size Requested size.
 * @return array
 */
function calmpress_image_attributes( $attr, $attachment, $size ) {
	$attr['decoding'] = 'async';
	if ( calmpress_get_option( 'calmpress_lazy_images' ) && ! isset( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'calmpress_image_attributes', 10, 3 );

/**
 * Flush rewrites after theme activation so /apps/ works immediately.
 *
 * @return void
 */
function calmpress_activate() {
	calmpress_register_app_content();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'calmpress_activate' );

/**
 * Output front-end design variables from the settings panel.
 *
 * @return void
 */
function calmpress_print_design_variables() {
	$accent = calmpress_sanitize_accent_color( calmpress_get_option( 'calmpress_accent_color' ) );
	$width  = calmpress_sanitize_container_width( calmpress_get_option( 'calmpress_container_width' ) );
	$cards  = calmpress_sanitize_cards_per_row( calmpress_get_option( 'calmpress_cards_per_row' ) );
	$radius = calmpress_sanitize_border_radius( calmpress_get_option( 'calmpress_border_radius' ) );
	$density = calmpress_sanitize_density( calmpress_get_option( 'calmpress_density' ) );
	$density_space = array( 'compact' => '.85rem', 'comfortable' => '1rem', 'spacious' => '1.2rem' );
	printf( '<style id="calmpress-settings-vars">:root{--cp-accent:%1$s;--cp-content-width:%2$spx;--cp-card-columns:%3$d;--cp-radius:%4$spx;--cp-density:%5$s;--cp-density-space:%6$s;}</style>', esc_attr( $accent ), esc_attr( $width ), absint( $cards ), absint( $radius ), esc_attr( $density ), esc_attr( $density_space[ $density ] ) );
}
add_action( 'wp_head', 'calmpress_print_design_variables', 20 );

/**
 * Disable emoji assets when requested.
 *
 * @return void
 */
function calmpress_disable_emojis() {
	if ( ! calmpress_get_option( 'calmpress_disable_emoji' ) ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'calmpress_disable_emojis', 9 );

/**
 * Remove optional front-end assets when the performance controls request it.
 *
 * @return void
 */
function calmpress_optimize_frontend_assets() {
	if ( calmpress_get_option( 'calmpress_disable_dashicons' ) && ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
		wp_deregister_style( 'dashicons' );
	}
	if ( calmpress_get_option( 'calmpress_disable_embeds' ) ) {
		wp_dequeue_script( 'wp-embed' );
		wp_deregister_script( 'wp-embed' );
	}
}
add_action( 'wp_enqueue_scripts', 'calmpress_optimize_frontend_assets', 100 );

/**
 * Remove oEmbed discovery hooks when embeds are disabled.
 *
 * @return void
 */
function calmpress_disable_embeds() {
	if ( ! calmpress_get_option( 'calmpress_disable_embeds' ) ) {
		return;
	}
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
}
add_action( 'init', 'calmpress_disable_embeds', 9 );

/**
 * Disable embed discovery when requested.
 *
 * @param array $links Discovery links.
 * @return array
 */
function calmpress_filter_embed_links( $links ) {
	return calmpress_get_option( 'calmpress_disable_embeds' ) ? array() : $links;
}
function calmpress_filter_embed_discover( $discover ) {
	return calmpress_get_option( 'calmpress_disable_embeds' ) ? false : $discover;
}
add_filter( 'embed_oembed_discover', 'calmpress_filter_embed_discover' );
add_filter( 'oembed_links', 'calmpress_filter_embed_links' );

/**
 * Render one sanitized advertising placement.
 *
 * @param string $placement Placement key.
 * @return void
 */
function calmpress_render_ad( $placement ) {
	$allowed = array( 'header', 'before_content', 'after_content', 'sidebar', 'footer' );
	$global_ads = get_option( 'calmpress_ads_enabled', null );
	if ( ! in_array( $placement, $allowed, true ) || ( null !== $global_ads && ! $global_ads ) || ! calmpress_get_option( 'calmpress_ad_' . $placement . '_enabled' ) ) {
		return;
	}
	$html = calmpress_get_option( 'calmpress_ad_' . $placement . '_html' );
	if ( '' === trim( (string) $html ) ) {
		return;
	}
	printf( '<aside class="calmpress-ad calmpress-ad--%1$s" aria-label="%2$s">%3$s</aside>', esc_attr( $placement ), esc_attr__( 'Reklam', 'calmpress' ), wp_kses_post( $html ) );
}

/**
 * Render the before-content placement.
 *
 * @return void
 */
function calmpress_render_before_content() {
	calmpress_render_ad( 'before_content' );
}

/**
 * Render an optional widget area.
 *
 * @param string $id Sidebar ID.
 * @return void
 */
function calmpress_render_widget_area( $id ) {
	if ( is_active_sidebar( $id ) ) {
		dynamic_sidebar( $id );
	}
}

/**
 * Add before-content, after-content, and after-content widgets consistently.
 *
 * @return void
 */
function calmpress_render_after_content_extensions() {
	if ( is_admin() ) {
		return;
	}
	calmpress_render_ad( 'after_content' );
	calmpress_render_widget_area( 'after-content' );
}
add_action( 'get_footer', 'calmpress_render_after_content_extensions', 5 );

/**
 * A small Widget API widget for recent apps.
 */
class CalmPress_Recent_Apps_Widget extends WP_Widget {
	/**
	 * Construct the widget.
	 */
	public function __construct() {
		parent::__construct( 'calmpress_recent_apps', __( 'CalmPress: Son uygulamalar', 'calmpress' ), array( 'description' => __( 'CalmPress uygulama dizinindeki en yeni uygulamaları gösterir.', 'calmpress' ) ) );
	}

	/**
	 * Display the widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Son uygulamalar', 'calmpress' );
		$query = new WP_Query( array( 'post_type' => 'app', 'posts_per_page' => 5, 'no_found_rows' => true ) );
		if ( ! $query->have_posts() ) {
			return;
		}
		echo $args['before_widget'];
		echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		echo '<ul class="calmpress-recent-apps">';
		while ( $query->have_posts() ) {
			$query->the_post();
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( get_permalink() ), esc_html( get_the_title() ) );
		}
		echo '</ul>';
		echo $args['after_widget'];
		wp_reset_postdata();
	}

	/**
	 * Render widget settings.
	 *
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Son uygulamalar', 'calmpress' );
		printf( '<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p>', esc_attr( $this->get_field_id( 'title' ) ), esc_html__( 'Başlık:', 'calmpress' ), esc_attr( $this->get_field_name( 'title' ) ), esc_attr( $title ) );
	}

	/**
	 * Save widget settings.
	 *
	 * @param array $new_instance New settings.
	 * @param array $old_instance Previous settings.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array( 'title' => isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '' );
	}
}
