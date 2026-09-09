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
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer', 'calmpress' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Optional footer widgets.', 'calmpress' ),
			'before_widget' => '<section class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
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
	if ( is_singular() ) {
		$description = get_the_excerpt();
	} elseif ( is_home() ) {
		$description = get_bloginfo( 'description' );
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
	if ( is_admin() || ! is_singular() ) {
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
