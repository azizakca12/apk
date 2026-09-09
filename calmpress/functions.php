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
			'primary' => esc_html__( 'Primary menu', 'calmpress' ),
			'footer'  => esc_html__( 'Footer menu', 'calmpress' ),
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
		'header'         => array( __( 'Header', 'calmpress' ), __( 'Widgets shown in the site header.', 'calmpress' ) ),
		'after-content'  => array( __( 'After content', 'calmpress' ), __( 'Widgets shown after the main content.', 'calmpress' ) ),
		'sidebar-primary' => array( __( 'Sidebar', 'calmpress' ), __( 'Widgets shown beside archive and article content.', 'calmpress' ) ),
		'home-before'    => array( __( 'Home before content', 'calmpress' ), __( 'Widgets shown before homepage sections.', 'calmpress' ) ),
		'home-after'     => array( __( 'Home after content', 'calmpress' ), __( 'Widgets shown after homepage sections.', 'calmpress' ) ),
		'footer-1'       => array( __( 'Footer', 'calmpress' ), __( 'Optional footer widgets.', 'calmpress' ) ),
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
				'system' => __( 'System', 'calmpress' ),
				'light'  => __( 'Light', 'calmpress' ),
				'dark'   => __( 'Dark', 'calmpress' ),
			),
			'activate' => __( 'Activate to change.', 'calmpress' ),
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
				'name'               => esc_html__( 'Apps', 'calmpress' ),
				'singular_name'      => esc_html__( 'App', 'calmpress' ),
				'add_new'            => esc_html__( 'Add app', 'calmpress' ),
				'add_new_item'       => esc_html__( 'Add new app', 'calmpress' ),
				'edit_item'          => esc_html__( 'Edit app', 'calmpress' ),
				'new_item'           => esc_html__( 'New app', 'calmpress' ),
				'view_item'          => esc_html__( 'View app', 'calmpress' ),
				'search_items'       => esc_html__( 'Search apps', 'calmpress' ),
				'not_found'          => esc_html__( 'No apps found', 'calmpress' ),
				'menu_name'          => esc_html__( 'Apps', 'calmpress' ),
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
				'name'          => esc_html__( 'App categories', 'calmpress' ),
				'singular_name' => esc_html__( 'App category', 'calmpress' ),
				'menu_name'     => esc_html__( 'Categories', 'calmpress' ),
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
		esc_html__( 'App details', 'calmpress' ),
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
		'download_url' => array( 'label' => __( 'Download URL', 'calmpress' ), 'type' => 'url' ),
		'version'      => array( 'label' => __( 'Version', 'calmpress' ), 'type' => 'text' ),
		'file_size'    => array( 'label' => __( 'File size', 'calmpress' ), 'type' => 'text' ),
		'platform'     => array( 'label' => __( 'Platform', 'calmpress' ), 'type' => 'text' ),
		'developer'    => array( 'label' => __( 'Developer', 'calmpress' ), 'type' => 'text' ),
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
	if ( ! isset( $attr['loading'] ) ) {
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
 * Return the default for a CalmPress setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function calmpress_get_option( $key ) {
	$defaults = array(
		'calmpress_accent_color'       => '#356ae6',
		'calmpress_container_width'    => 1120,
		'calmpress_cards_per_row'      => 3,
		'calmpress_announcement_text'  => '',
		'calmpress_announcement_link'  => '',
		'calmpress_show_footer_widgets' => 1,
		'calmpress_seo_description'    => '',
		'calmpress_organization_name'  => '',
		'calmpress_organization_logo'  => '',
		'calmpress_schema_enabled'     => 1,
		'calmpress_disable_emoji'      => 0,
		'calmpress_social_facebook'    => '',
		'calmpress_social_instagram'   => '',
		'calmpress_social_x'           => '',
		'calmpress_social_youtube'     => '',
		'calmpress_social_github'      => '',
	);
	if ( 0 === strpos( $key, 'calmpress_ad_' ) ) {
		$defaults[ $key ] = false;
	}
	return get_option( $key, array_key_exists( $key, $defaults ) ? $defaults[ $key ] : '' );
}

/**
 * Sanitize a theme accent color.
 *
 * @param string $value Submitted color.
 * @return string
 */
function calmpress_sanitize_accent_color( $value ) {
	$value = sanitize_hex_color_no_hash( (string) $value );
	return preg_match( '/^[0-9a-f]{6}$/i', $value ) ? '#' . strtolower( $value ) : '#356ae6';
}

/**
 * Sanitize a bounded integer setting.
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
 * Sanitize a checkbox.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function calmpress_sanitize_checkbox( $value ) {
	return empty( $value ) ? 0 : 1;
}

/**
 * Register settings used by the CalmPress admin panel.
 *
 * @return void
 */
function calmpress_register_settings() {
	$settings = array(
		'calmpress_accent_color'        => 'calmpress_sanitize_accent_color',
		'calmpress_container_width'     => 'calmpress_sanitize_container_width',
		'calmpress_cards_per_row'       => 'calmpress_sanitize_cards_per_row',
		'calmpress_announcement_text'   => 'sanitize_text_field',
		'calmpress_announcement_link'   => 'esc_url_raw',
		'calmpress_show_footer_widgets' => 'calmpress_sanitize_checkbox',
		'calmpress_seo_description'     => 'sanitize_textarea_field',
		'calmpress_organization_name'   => 'sanitize_text_field',
		'calmpress_organization_logo'   => 'esc_url_raw',
		'calmpress_schema_enabled'      => 'calmpress_sanitize_checkbox',
		'calmpress_disable_emoji'       => 'calmpress_sanitize_checkbox',
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
 * Add the settings page and its Settings API sections.
 *
 * @return void
 */
function calmpress_settings_menu() {
	add_theme_page(
		__( 'CalmPress Settings', 'calmpress' ),
		__( 'CalmPress Settings', 'calmpress' ),
		'manage_options',
		'calmpress-settings',
		'calmpress_render_settings_page'
	);
}
add_action( 'admin_menu', 'calmpress_settings_menu' );

/**
 * Add fields to the CalmPress settings screen.
 *
 * @return void
 */
function calmpress_settings_sections() {
	add_settings_section( 'calmpress_general', __( 'General design', 'calmpress' ), '__return_false', 'calmpress-settings' );
	calmpress_add_settings_field( 'calmpress_accent_color', 'color', __( 'Accent color', 'calmpress' ), __( 'Used for links, buttons, and focus states.', 'calmpress' ), 'calmpress_general' );
	calmpress_add_settings_field( 'calmpress_container_width', 'number', __( 'Container width (px)', 'calmpress' ), __( 'Choose a value between 960 and 1600 pixels.', 'calmpress' ), 'calmpress_general' );
	calmpress_add_settings_field( 'calmpress_cards_per_row', 'number', __( 'Cards per row', 'calmpress' ), __( 'Choose 1 to 4 cards on larger screens.', 'calmpress' ), 'calmpress_general' );

	add_settings_section( 'calmpress_header_footer', __( 'Header and footer', 'calmpress' ), '__return_false', 'calmpress-settings' );
	calmpress_add_settings_field( 'calmpress_announcement_text', 'text', __( 'Announcement text', 'calmpress' ), __( 'An optional accessible notice displayed above the navigation.', 'calmpress' ), 'calmpress_header_footer' );
	calmpress_add_settings_field( 'calmpress_announcement_link', 'url', __( 'Announcement link', 'calmpress' ), __( 'Optional destination for the announcement.', 'calmpress' ), 'calmpress_header_footer' );
	calmpress_add_settings_field( 'calmpress_show_footer_widgets', 'checkbox', __( 'Show footer widgets', 'calmpress' ), __( 'Display the Footer widget area in the site footer.', 'calmpress' ), 'calmpress_header_footer' );

	add_settings_section( 'calmpress_seo', __( 'SEO and structured data', 'calmpress' ), '__return_false', 'calmpress-settings' );
	calmpress_add_settings_field( 'calmpress_seo_description', 'textarea', __( 'Default meta description', 'calmpress' ), __( 'Used when a page or post does not have an excerpt.', 'calmpress' ), 'calmpress_seo' );
	calmpress_add_settings_field( 'calmpress_organization_name', 'text', __( 'Organization name', 'calmpress' ), __( 'Optional publisher name for structured data.', 'calmpress' ), 'calmpress_seo' );
	calmpress_add_settings_field( 'calmpress_organization_logo', 'url', __( 'Organization logo URL', 'calmpress' ), __( 'Use a trusted image URL from your media library.', 'calmpress' ), 'calmpress_seo' );
	calmpress_add_settings_field( 'calmpress_schema_enabled', 'checkbox', __( 'Enable schema.org JSON-LD', 'calmpress' ), __( 'Adds Article or SoftwareApplication metadata to singular pages.', 'calmpress' ), 'calmpress_seo' );

	add_settings_section( 'calmpress_performance', __( 'Performance', 'calmpress' ), '__return_false', 'calmpress-settings' );
	calmpress_add_settings_field( 'calmpress_disable_emoji', 'checkbox', __( 'Disable WordPress emoji assets', 'calmpress' ), __( 'Avoids loading emoji detection scripts when your site does not need them. Images remain lazy-loaded.', 'calmpress' ), 'calmpress_performance' );

	add_settings_section( 'calmpress_social', __( 'Social links', 'calmpress' ), '__return_false', 'calmpress-settings' );
	foreach ( array( 'facebook' => __( 'Facebook', 'calmpress' ), 'instagram' => __( 'Instagram', 'calmpress' ), 'x' => __( 'X', 'calmpress' ), 'youtube' => __( 'YouTube', 'calmpress' ), 'github' => __( 'GitHub', 'calmpress' ) ) as $key => $label ) {
		calmpress_add_settings_field( 'calmpress_social_' . $key, 'url', $label, __( 'Optional profile URL shown in the footer.', 'calmpress' ), 'calmpress_social' );
	}

	add_settings_section( 'calmpress_ads', __( 'Advertising module', 'calmpress' ), 'calmpress_ads_section_description', 'calmpress-settings' );
	foreach ( array( 'header' => __( 'Header', 'calmpress' ), 'before_content' => __( 'Before content', 'calmpress' ), 'after_content' => __( 'After content', 'calmpress' ), 'sidebar' => __( 'Sidebar', 'calmpress' ), 'footer' => __( 'Footer', 'calmpress' ) ) as $placement => $label ) {
		calmpress_add_settings_field( 'calmpress_ad_' . $placement . '_enabled', 'checkbox', sprintf( __( 'Enable %s ad', 'calmpress' ), $label ), __( 'Show this placement on the front end.', 'calmpress' ), 'calmpress_ads' );
		calmpress_add_settings_field( 'calmpress_ad_' . $placement . '_html', 'textarea', sprintf( __( '%s ad HTML', 'calmpress' ), $label ), __( 'Safe HTML only; script tags and unsafe attributes are stripped automatically.', 'calmpress' ), 'calmpress_ads' );
	}
}
add_action( 'admin_init', 'calmpress_settings_sections' );

/**
 * Register one settings field.
 *
 * @param string $key Setting key.
 * @param string $type Field type.
 * @param string $title Field title.
 * @param string $description Description.
 * @param string $section Section ID.
 * @return void
 */
function calmpress_add_settings_field( $key, $type, $title, $description, $section ) {
	add_settings_field(
		$key,
		$title,
		'calmpress_render_setting_field',
		'calmpress-settings',
		$section,
		array(
			'key'         => $key,
			'type'        => $type,
			'description' => $description,
		)
	);
}

/**
 * Render a Settings API field without unsafe direct HTML.
 *
 * @param array $args Field arguments.
 * @return void
 */
function calmpress_render_setting_field( $args ) {
	$key   = $args['key'];
	$type  = $args['type'];
	$value = calmpress_get_option( $key );
	$id    = sanitize_key( $key );
	$attrs = '';
	if ( 'number' === $type ) {
		$attrs = false !== strpos( $key, 'cards' ) ? ' min="1" max="4"' : ' min="960" max="1600"';
	}
	if ( 'checkbox' === $type ) {
		printf( '<label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s> %3$s</label>', esc_attr( $id ), checked( $value, 1, false ), esc_html__( 'Enabled', 'calmpress' ) );
	} elseif ( 'textarea' === $type ) {
		printf( '<textarea class="large-text" rows="5" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $id ), esc_textarea( (string) $value ) );
	} else {
		printf( '<input class="regular-text" type="%1$s" id="%2$s" name="%2$s" value="%3$s"%4$s>', esc_attr( $type ), esc_attr( $id ), esc_attr( (string) $value ), $attrs );
	}
	if ( ! empty( $args['description'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
	}
}

/**
 * Explain ad sanitization in the settings screen.
 *
 * @return void
 */
function calmpress_ads_section_description() {
	echo '<p>' . esc_html__( 'Ads are disabled by default. Add markup such as links, images, and text. For safety, script tags, event handlers, and unsafe attributes are stripped by WordPress.', 'calmpress' ) . '</p>';
}

/**
 * Render the CalmPress settings screen.
 *
 * @return void
 */
function calmpress_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'calmpress' ) );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'CalmPress Settings', 'calmpress' ); ?></h1>
		<p><?php esc_html_e( 'Configure the theme, accessibility-friendly advertising placements, widgets, and social profiles from one place.', 'calmpress' ); ?></p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'calmpress_settings' );
			do_settings_sections( 'calmpress-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Output front-end design variables from the settings panel.
 *
 * @return void
 */
function calmpress_print_design_variables() {
	$accent = calmpress_sanitize_accent_color( calmpress_get_option( 'calmpress_accent_color' ) );
	$width  = calmpress_sanitize_container_width( calmpress_get_option( 'calmpress_container_width' ) );
	$cards  = calmpress_sanitize_cards_per_row( calmpress_get_option( 'calmpress_cards_per_row' ) );
	printf( '<style id="calmpress-settings-vars">:root{--cp-accent:%1$s;--cp-content-width:%2$spx;--cp-card-columns:%3$d;}</style>', esc_attr( $accent ), esc_attr( $width ), absint( $cards ) );
}
add_action( 'wp_head', 'calmpress_print_design_variables', 5 );

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
 * Render one sanitized advertising placement.
 *
 * @param string $placement Placement key.
 * @return void
 */
function calmpress_render_ad( $placement ) {
	$allowed = array( 'header', 'before_content', 'after_content', 'sidebar', 'footer' );
	if ( ! in_array( $placement, $allowed, true ) || ! calmpress_get_option( 'calmpress_ad_' . $placement . '_enabled' ) ) {
		return;
	}
	$html = calmpress_get_option( 'calmpress_ad_' . $placement . '_html' );
	if ( '' === trim( (string) $html ) ) {
		return;
	}
	printf( '<aside class="calmpress-ad calmpress-ad--%1$s" aria-label="%2$s">%3$s</aside>', esc_attr( $placement ), esc_attr__( 'Advertisement', 'calmpress' ), wp_kses_post( $html ) );
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
		parent::__construct( 'calmpress_recent_apps', __( 'CalmPress: Recent Apps', 'calmpress' ), array( 'description' => __( 'Shows the latest apps from the CalmPress app directory.', 'calmpress' ) ) );
	}

	/**
	 * Display the widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent apps', 'calmpress' );
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
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Recent apps', 'calmpress' );
		printf( '<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p>', esc_attr( $this->get_field_id( 'title' ) ), esc_html__( 'Title:', 'calmpress' ), esc_attr( $this->get_field_name( 'title' ) ), esc_attr( $title ) );
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
