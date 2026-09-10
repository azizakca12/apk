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
require_once get_template_directory() . '/inc/customizer.php';

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
	register_widget( 'CalmPress_Popular_Posts_Widget' );
	register_widget( 'CalmPress_Categories_Widget' );
}
add_action( 'widgets_init', 'calmpress_widgets_init' );

/**
 * Hide the block Categories widget from singular content sidebars.
 *
 * @param WP_Widget|null $instance Widget instance.
 * @param array          $widget Widget arguments.
 * @param array          $args Sidebar arguments.
 * @return WP_Widget|null
 */
function calmpress_hide_content_categories_widget( $instance, $widget, $args ) {
	if ( ! is_singular() || empty( $args['id'] ) || 'sidebar-primary' !== $args['id'] || ! is_object( $widget ) || 'WP_Widget_Block' !== get_class( $widget ) ) {
		return $instance;
	}
	$content = isset( $instance['content'] ) ? (string) $instance['content'] : '';
	if ( false !== strpos( $content, '"core/categories"' ) || false !== strpos( $content, 'wp:categories' ) ) {
		return false;
	}
	return $instance;
}
add_filter( 'widget_display_callback', 'calmpress_hide_content_categories_widget', 10, 3 );

/**
 * Return whether enhanced singular content can be processed.
 *
 * @return bool
 */
function calmpress_is_enhanced_content() {
	return ! is_admin() && ! is_feed() && ! is_embed() && in_the_loop() && is_main_query() && is_singular( array( 'post', 'page' ) ) && 'app' !== get_post_type();
}

/**
 * Build a stable, unique heading id.
 *
 * @param string   $text Heading text.
 * @param string[] $used IDs already used in the document.
 * @param int      $index Heading index.
 * @return string
 */
function calmpress_heading_id( $text, &$used, $index ) {
	$id = sanitize_title( wp_strip_all_tags( $text ) );
	if ( '' === $id ) {
		$id = 'baslik-' . absint( $index );
	}
	$base = $id;
	$suffix = 2;
	while ( in_array( $id, $used, true ) ) {
		$id = $base . '-' . $suffix;
		++$suffix;
	}
	$used[] = $id;
	return $id;
}

/**
 * Render a compact accessible table of contents.
 *
 * @param array $items Heading data.
 * @return string
 */
function calmpress_render_toc( $items ) {
	if ( empty( $items ) ) {
		return '';
	}
	$output = '<nav class="calmpress-toc" aria-labelledby="calmpress-toc-title" data-toc><details open><summary id="calmpress-toc-title">' . esc_html__( 'İçindekiler', 'calmpress' ) . '</summary><ol>';
	foreach ( $items as $item ) {
		$output .= '<li class="calmpress-toc__level-' . absint( $item['level'] ) . '"><a href="#' . esc_attr( $item['id'] ) . '">' . esc_html( $item['text'] ) . '</a></li>';
	}
	return $output . '</ol></details></nav>';
}

/**
 * Add ids to H2/H3 elements and prepend a table of contents.
 *
 * DOMDocument is used where available so links, code and nested markup are
 * preserved. The small fallback only replaces heading tags and never parses
 * arbitrary HTML.
 *
 * @param string $content Post content.
 * @return string
 */
function calmpress_add_table_of_contents( $content ) {
	static $processed = array();
	if ( ! calmpress_is_enhanced_content() || ! calmpress_get_option( 'calmpress_toc_enabled' ) || false !== strpos( $content, 'data-toc' ) ) {
		return $content;
	}
	$post_id = get_the_ID();
	if ( isset( $processed[ $post_id ] ) ) {
		return $content;
	}
	$processed[ $post_id ] = true;
	$items = array();
	$used  = array();
	if ( class_exists( 'DOMDocument' ) ) {
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$flags = defined( 'LIBXML_HTML_NOIMPLIED' ) ? LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING : LIBXML_NOERROR | LIBXML_NOWARNING;
		if ( @$dom->loadHTML( '<?xml encoding="UTF-8"><div id="calmpress-content-root">' . $content . '</div>', $flags ) ) {
			$root = $dom->getElementById( 'calmpress-content-root' );
			if ( ! $root ) {
				foreach ( $dom->getElementsByTagName( 'div' ) as $candidate ) {
					if ( 'calmpress-content-root' === $candidate->getAttribute( 'id' ) ) {
						$root = $candidate;
						break;
					}
				}
			}
			if ( $root ) {
				$index = 0;
				$xpath = new DOMXPath( $dom );
				foreach ( $xpath->query( './/h2 | .//h3', $root ) as $heading ) {
						$tag = strtolower( $heading->tagName );
						$text = trim( preg_replace( '/\s+/u', ' ', $heading->textContent ) );
						if ( '' === $text ) {
							continue;
						}
						$id = sanitize_title( $heading->getAttribute( 'id' ) );
						if ( '' === $id || in_array( $id, $used, true ) ) {
							$id = calmpress_heading_id( $text, $used, ++$index );
							$heading->setAttribute( 'id', $id );
						} else {
							$heading->setAttribute( 'id', $id );
							$used[] = $id;
							++$index;
						}
						$items[] = array( 'id' => $id, 'text' => $text, 'level' => 2 === (int) substr( $tag, 1 ) ? 2 : 3 );
				}
				if ( ! empty( $items ) ) {
					$updated = '';
					foreach ( $root->childNodes as $child ) {
						$updated .= $dom->saveHTML( $child );
					}
					return calmpress_render_toc( $items ) . $updated;
				}
			}
		}
	}
	$index = 0;
	$fallback = preg_replace_callback(
		'/<h([23])(\s[^>]*)?>(.*?)<\/h\1>/is',
		function ( $match ) use ( &$items, &$used, &$index ) {
			$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $match[3] ) ) );
			if ( '' === $text ) {
				return $match[0];
			}
			$attributes = (string) $match[2];
			if ( preg_match( '/\bid\s*=\s*["\']([^"\']+)["\']/i', $attributes, $id_match ) && '' !== sanitize_title( $id_match[1] ) && ! in_array( $id_match[1], $used, true ) ) {
				$id = sanitize_title( $id_match[1] );
				$attributes = preg_replace( '/\bid\s*=\s*["\'][^"\']+["\']/i', 'id="' . esc_attr( $id ) . '"', $attributes, 1 );
				$used[] = $id;
			} else {
				$id = calmpress_heading_id( $text, $used, ++$index );
				$attributes .= ' id="' . esc_attr( $id ) . '"';
			}
			$items[] = array( 'id' => $id, 'text' => $text, 'level' => absint( $match[1] ) );
			return '<h' . $match[1] . $attributes . '>' . $match[3] . '</h' . $match[1] . '>';
		},
		$content
	);
	return ! empty( $items ) ? calmpress_render_toc( $items ) . $fallback : $content;
}
add_filter( 'the_content', 'calmpress_add_table_of_contents', 20 );

/**
 * Find a related post using shared categories and tags.
 *
 * @param int $post_id Current post.
 * @param int $limit Number of posts.
 * @return WP_Query
 */
function calmpress_related_query( $post_id, $limit = 3 ) {
	$tax_query = array( 'relation' => 'OR' );
	$categories = wp_get_post_categories( $post_id );
	$tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
	if ( $categories ) {
		$tax_query[] = array( 'taxonomy' => 'category', 'field' => 'term_id', 'terms' => $categories );
	}
	if ( $tags ) {
		$tax_query[] = array( 'taxonomy' => 'post_tag', 'field' => 'term_id', 'terms' => $tags );
	}
	if ( count( $tax_query ) < 2 ) {
		return new WP_Query( array( 'post__in' => array( 0 ), 'no_found_rows' => true ) );
	}
	return new WP_Query(
		array(
			'post_type' => 'post',
			'post__not_in' => array( $post_id ),
			'posts_per_page' => min( 6, max( 1, absint( $limit ) ) ),
			'tax_query' => $tax_query,
			'orderby' => 'date',
			'no_found_rows' => true,
			'cache_results' => true,
			'ignore_sticky_posts' => true,
		)
	);
}

/**
 * Add one related-post card after the third paragraph.
 *
 * @param string $content Post content.
 * @return string
 */
function calmpress_insert_in_content_related( $content ) {
	static $processed = array();
	if ( ! calmpress_is_enhanced_content() || ! calmpress_get_option( 'calmpress_related_enabled' ) || false !== strpos( $content, 'data-related-inline' ) ) {
		return $content;
	}
	$post_id = get_the_ID();
	if ( isset( $processed[ $post_id ] ) ) {
		return $content;
	}
	$processed[ $post_id ] = true;
	$query = calmpress_related_query( $post_id, 1 );
	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return $content;
	}
	$query->the_post();
	$card = '<aside class="related-inline" data-related-inline><strong>' . esc_html__( 'Ayrıca okuyun', 'calmpress' ) . '</strong><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></aside>';
	wp_reset_postdata();
	if ( class_exists( 'DOMDocument' ) ) {
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$flags = defined( 'LIBXML_HTML_NOIMPLIED' ) ? LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING : LIBXML_NOERROR | LIBXML_NOWARNING;
		if ( @$dom->loadHTML( '<?xml encoding="UTF-8"><div id="cp-related-root">' . $content . '</div>', $flags ) ) {
			$root = $dom->getElementById( 'cp-related-root' );
			if ( ! $root ) {
				foreach ( $dom->getElementsByTagName( 'div' ) as $candidate ) {
					if ( 'cp-related-root' === $candidate->getAttribute( 'id' ) ) {
						$root = $candidate;
						break;
					}
				}
			}
			if ( $root ) {
				$paragraphs = $root->getElementsByTagName( 'p' );
				if ( $paragraphs->length >= 3 ) {
					$fragment_dom = new DOMDocument( '1.0', 'UTF-8' );
					@$fragment_dom->loadHTML( '<?xml encoding="UTF-8">' . $card, $flags );
					$node = $fragment_dom->getElementsByTagName( 'aside' )->item( 0 );
					if ( $node ) {
						$imported = $dom->importNode( $node, true );
						$target = $paragraphs->item( 2 );
						$target->parentNode->insertBefore( $imported, $target->nextSibling );
						$updated = '';
						foreach ( $root->childNodes as $child ) {
							$updated .= $dom->saveHTML( $child );
						}
						return $updated;
					}
				}
			}
		}
	}
	$count = 0;
	return preg_replace_callback( '/(<p\b[^>]*>.*?<\/p>)/is', function ( $match ) use ( &$count, $card ) {
		++$count;
		return 3 === $count ? $match[1] . $card : $match[1];
	}, $content );
}
add_filter( 'the_content', 'calmpress_insert_in_content_related', 21 );

/**
 * Render reusable share controls.
 *
 * @param string $position Placement label.
 * @return void
 */
function calmpress_render_share_tools( $position = '' ) {
	$label = __( 'Bu yazıyı paylaş', 'calmpress' );
	$id = 'calmpress-share-' . sanitize_html_class( $position ? $position : 'content' );
	printf( '<div id="%1$s" class="share-tools share-tools--%2$s" data-share-title="%3$s" data-share-url="%4$s"><strong>%5$s</strong><button type="button" class="button button--secondary" data-share-native aria-label="%6$s">%7$s</button><button type="button" class="button button--secondary" data-share-copy aria-label="%8$s">%9$s</button><span class="screen-reader-text" data-share-status aria-live="polite"></span></div>', esc_attr( $id ), esc_attr( $position ? $position : 'content' ), esc_attr( get_the_title() ), esc_url( get_permalink() ), esc_html( $label ), esc_attr__( 'Sistem paylaşımını aç', 'calmpress' ), esc_html__( 'Paylaş', 'calmpress' ), esc_attr__( 'Bağlantıyı kopyala', 'calmpress' ), esc_html__( 'Bağlantıyı kopyala', 'calmpress' ) );
}

/**
 * Render tags for the current post.
 *
 * @return void
 */
function calmpress_render_post_tags() {
	if ( ! has_tag() ) {
		return;
	}
	echo '<div class="post-tags"><strong>' . esc_html__( 'Etiketler', 'calmpress' ) . '</strong><ul>';
	foreach ( get_the_tags() as $tag ) {
		printf( '<li><a href="%1$s">#%2$s</a></li>', esc_url( get_tag_link( $tag ) ), esc_html( $tag->name ) );
	}
	echo '</ul></div>';
}

/**
 * Render the author information card.
 *
 * @return void
 */
function calmpress_render_author_box() {
	if ( ! calmpress_get_option( 'calmpress_author_box' ) ) {
		return;
	}
	$author_id = get_the_author_meta( 'ID' );
	printf( '<section class="author-box" aria-labelledby="author-box-title">%1$s<div><h2 id="author-box-title">%2$s</h2><p class="author-box__name"><a href="%3$s">%4$s</a></p>%5$s</div></section>', get_avatar( $author_id, 80, '', get_the_author(), array( 'class' => array( 'author-box__avatar' ) ) ), esc_html__( 'Yazar', 'calmpress' ), esc_url( get_author_posts_url( $author_id ) ), esc_html( get_the_author() ), wpautop( esc_html( get_the_author_meta( 'description', $author_id ) ) ) );
}

/**
 * Render related cards below a singular article.
 *
 * @return void
 */
function calmpress_render_related_posts() {
	if ( ! calmpress_is_enhanced_content() || ! calmpress_get_option( 'calmpress_related_enabled' ) ) {
		return;
	}
	$query = calmpress_related_query( get_the_ID(), min( 6, max( 1, absint( calmpress_get_option( 'calmpress_related_count' ) ) ) ) );
	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return;
	}
	echo '<section class="related-posts" aria-labelledby="related-posts-title"><h2 id="related-posts-title">' . esc_html__( 'Bunlar da ilginizi çekebilir', 'calmpress' ) . '</h2><div class="related-posts__grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/content', 'post' );
	}
	echo '</div></section>';
	wp_reset_postdata();
}

/**
 * Render the lightweight built-in sidebar sections.
 *
 * @return void
 */
function calmpress_render_builtin_sidebar() {
	if ( calmpress_get_option( 'calmpress_popular_widget' ) ) {
		$query = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => min( 10, max( 1, absint( calmpress_get_option( 'calmpress_popular_count' ) ) ) ), 'orderby' => 'comment_count', 'order' => 'DESC', 'no_found_rows' => true, 'cache_results' => true, 'ignore_sticky_posts' => true ) );
		if ( $query->have_posts() ) {
			echo '<section class="widget calmpress-popular-widget"><h2 class="widget-title">' . esc_html__( 'Çok okunanlar', 'calmpress' ) . '</h2><ol>';
			while ( $query->have_posts() ) {
				$query->the_post();
				printf( '<li><a href="%1$s">%2$s</a><span>%3$s</span></li>', esc_url( get_permalink() ), esc_html( get_the_title() ), esc_html( sprintf( _n( '%s yorum', '%s yorum', get_comments_number(), 'calmpress' ), number_format_i18n( get_comments_number() ) ) ) );
			}
			echo '</ol></section>';
		}
		wp_reset_postdata();
	}
	if ( calmpress_get_option( 'calmpress_categories_widget' ) && ! is_singular( array( 'post', 'page' ) ) ) {
		$categories = get_categories( array( 'hide_empty' => true, 'number' => 12 ) );
		if ( $categories ) {
			echo '<section class="widget calmpress-categories-widget"><h2 class="widget-title">' . esc_html__( 'Kategoriler', 'calmpress' ) . '</h2><ul>';
			foreach ( $categories as $category ) {
				printf( '<li><a href="%1$s">%2$s</a><span>%3$s</span></li>', esc_url( get_category_link( $category ) ), esc_html( $category->name ), absint( $category->count ) );
			}
			echo '</ul></section>';
		}
	}
}

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
			'defaultTheme' => calmpress_get_option( 'calmpress_theme_default' ),
			'shareCopied' => __( 'Bağlantı kopyalandı.', 'calmpress' ),
			'sharePrompt' => __( 'Bağlantıyı kopyalayın:', 'calmpress' ),
			'shareOpened' => __( 'Paylaşım penceresi açıldı.', 'calmpress' ),
			'searchClose' => __( 'Aramayı kapat', 'calmpress' ),
			'searchStatus' => __( 'Arama penceresi açıldı.', 'calmpress' ),
		)
	);
	wp_script_add_data( 'calmpress-theme', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'calmpress_enqueue_assets' );

/**
 * Add design preference classes to the document body.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function calmpress_body_classes( $classes ) {
	if ( calmpress_get_option( 'calmpress_high_contrast' ) ) {
		$classes[] = 'calmpress-high-contrast';
	}
	if ( calmpress_get_option( 'calmpress_reduced_motion' ) ) {
		$classes[] = 'calmpress-reduced-motion';
	}
	return $classes;
}
add_filter( 'body_class', 'calmpress_body_classes' );

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
		'min_android'  => array( 'label' => __( 'Minimum Android sürümü', 'calmpress' ), 'type' => 'text' ),
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

	$changelog = get_post_meta( $post->ID, '_calmpress_changelog', true );
	printf(
		'<p><label for="calmpress_changelog"><strong>%1$s</strong></label><br><textarea class="widefat" rows="5" id="calmpress_changelog" name="calmpress_changelog">%2$s</textarea><br><span class="description">%3$s</span></p>',
		esc_html__( 'Sürüm notları / değişiklik günlüğü', 'calmpress' ),
		esc_textarea( $changelog ),
		esc_html__( 'Her satır ayrı bir madde olarak görüntülenir.', 'calmpress' )
	);

	$screenshot_ids = calmpress_app_screenshots( $post->ID );
	echo '<div class="calmpress-app-screenshots">';
	echo '<p><strong>' . esc_html__( 'Ekran görüntüleri (en fazla 4)', 'calmpress' ) . '</strong></p>';
	echo '<ul class="calmpress-app-screenshots__list" id="calmpress-app-screenshots-list" data-max="4">';
	foreach ( $screenshot_ids as $attachment_id ) {
		$thumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
		if ( ! $thumb ) {
			continue;
		}
		printf(
			'<li data-id="%1$d"><img src="%2$s" alt=""><button type="button" class="button-link calmpress-app-screenshots__remove" aria-label="%3$s">&times;</button></li>',
			absint( $attachment_id ),
			esc_url( $thumb[0] ),
			esc_attr__( 'Kaldır', 'calmpress' )
		);
	}
	echo '</ul>';
	printf( '<input type="hidden" id="calmpress_screenshots" name="calmpress_screenshots" value="%s">', esc_attr( implode( ',', $screenshot_ids ) ) );
	echo '<p><button type="button" class="button" id="calmpress-app-screenshots-button">' . esc_html__( 'Ekran görüntüsü ekle', 'calmpress' ) . '</button></p>';
	echo '<p class="description">' . esc_html__( 'Medya kütüphanesinden en fazla dört görsel seçin.', 'calmpress' ) . '</p>';
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
		'min_android'  => 'sanitize_text_field',
		'changelog'    => 'sanitize_textarea_field',
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

	if ( isset( $_POST['calmpress_screenshots'] ) ) {
		$ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['calmpress_screenshots'] ) ) ) ) );
		$ids = array_values( array_unique( $ids ) );
		$ids = array_values( array_filter( $ids, static function ( $attachment_id ) {
			return 'attachment' === get_post_type( $attachment_id );
		} ) );
		$ids = array_slice( $ids, 0, 4 );
		if ( empty( $ids ) ) {
			delete_post_meta( $post_id, '_calmpress_screenshots' );
		} else {
			update_post_meta( $post_id, '_calmpress_screenshots', implode( ',', $ids ) );
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
	$allowed = array( 'download_url', 'version', 'file_size', 'platform', 'developer', 'min_android', 'changelog' );
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
	$accent_hover = calmpress_sanitize_accent_color( calmpress_get_option( 'calmpress_accent_hover' ) );
	$accent_contrast = calmpress_sanitize_accent_color( calmpress_get_option( 'calmpress_accent_contrast' ) );
	$focus = calmpress_sanitize_accent_color( calmpress_get_option( 'calmpress_focus_color' ) );
	$width  = calmpress_sanitize_container_width( calmpress_get_option( 'calmpress_container_width' ) );
	$cards  = calmpress_sanitize_cards_per_row( calmpress_get_option( 'calmpress_cards_per_row' ) );
	$radius = calmpress_sanitize_border_radius( calmpress_get_option( 'calmpress_border_radius' ) );
	$density = calmpress_sanitize_density( calmpress_get_option( 'calmpress_density' ) );
	$density_space = array( 'compact' => '.85rem', 'comfortable' => '1rem', 'spacious' => '1.2rem' );
	$body_font = 'readable' === calmpress_get_option( 'calmpress_body_font' ) ? 'Atkinson Hyperlegible, "Segoe UI", Helvetica, Arial, sans-serif' : '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif';
	$heading_font = 'readable' === calmpress_get_option( 'calmpress_heading_font' ) ? 'Atkinson Hyperlegible, "Segoe UI", Helvetica, Arial, sans-serif' : '-apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", sans-serif';
	printf( '<style id="calmpress-settings-vars">:root{--cp-accent:%1$s;--cp-accent-hover:%2$s;--cp-accent-contrast:%3$s;--cp-focus:%4$s;--cp-content-width:%5$spx;--cp-card-columns:%6$d;--cp-radius:%7$spx;--cp-density:%8$s;--cp-density-space:%9$s;--cp-font-body:%10$s;--cp-font-heading:%11$s;}</style>', esc_attr( $accent ), esc_attr( $accent_hover ), esc_attr( $accent_contrast ), esc_attr( $focus ), esc_attr( $width ), absint( $cards ), absint( $radius ), esc_attr( $density ), esc_attr( $density_space[ $density ] ), esc_attr( $body_font ), esc_attr( $heading_font ) );
}
add_action( 'wp_head', 'calmpress_print_design_variables', 20 );

/**
 * Print the restricted custom CSS entered in CalmPress Studio.
 *
 * @return void
 */
function calmpress_print_custom_css() {
	$css = function_exists( 'calmpress_customize_css' ) ? calmpress_customize_css( calmpress_get_option( 'calmpress_custom_css' ) ) : '';
	if ( '' === $css ) {
		return;
	}
	$css = preg_replace( '/<\/style/i', '', wp_strip_all_tags( $css ) );
	printf( '<style id="calmpress-custom-css">%s</style>', $css );
}
add_action( 'wp_head', 'calmpress_print_custom_css', 21 );

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

/**
 * A comment-count based popular posts widget with no plugin dependency.
 */
class CalmPress_Popular_Posts_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct( 'calmpress_popular_posts', __( 'CalmPress: Çok okunanlar', 'calmpress' ), array( 'description' => __( 'Yazıları yorum sayısına göre listeler.', 'calmpress' ) ) );
	}

	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Çok okunanlar', 'calmpress' );
		$count = ! empty( $instance['count'] ) ? min( 10, max( 1, absint( $instance['count'] ) ) ) : 5;
		$query = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => $count, 'orderby' => 'comment_count', 'order' => 'DESC', 'no_found_rows' => true, 'cache_results' => true, 'ignore_sticky_posts' => true ) );
		if ( ! $query->have_posts() ) {
			return;
		}
		echo $args['before_widget'] . $args['before_title'] . esc_html( $title ) . $args['after_title'] . '<ol>';
		while ( $query->have_posts() ) {
			$query->the_post();
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( get_permalink() ), esc_html( get_the_title() ) );
		}
		echo '</ol>' . $args['after_widget'];
		wp_reset_postdata();
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Çok okunanlar', 'calmpress' );
		$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 5;
		printf( '<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p><p><label for="%5$s">%6$s</label><input class="small-text" id="%5$s" name="%7$s" type="number" min="1" max="10" value="%8$d"></p>', esc_attr( $this->get_field_id( 'title' ) ), esc_html__( 'Başlık:', 'calmpress' ), esc_attr( $this->get_field_name( 'title' ) ), esc_attr( $title ), esc_attr( $this->get_field_id( 'count' ) ), esc_html__( 'Sayı:', 'calmpress' ), esc_attr( $this->get_field_name( 'count' ) ), $count );
	}

	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( $new_instance['title'] ?? '' ), 'count' => min( 10, max( 1, absint( $new_instance['count'] ?? 5 ) ) ) );
	}
}

/**
 * A dedicated category widget, separate from the generic core block.
 */
class CalmPress_Categories_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct( 'calmpress_categories', __( 'CalmPress: Kategoriler', 'calmpress' ), array( 'description' => __( 'Yazı kategorilerini CalmPress görünümüyle gösterir.', 'calmpress' ) ) );
	}

	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Kategoriler', 'calmpress' );
		$categories = get_categories( array( 'hide_empty' => true, 'number' => 12 ) );
		if ( ! $categories ) {
			return;
		}
		echo $args['before_widget'] . $args['before_title'] . esc_html( $title ) . $args['after_title'] . '<ul>';
		foreach ( $categories as $category ) {
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( get_category_link( $category ) ), esc_html( $category->name ) );
		}
		echo '</ul>' . $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Kategoriler', 'calmpress' );
		printf( '<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p>', esc_attr( $this->get_field_id( 'title' ) ), esc_html__( 'Başlık:', 'calmpress' ), esc_attr( $this->get_field_name( 'title' ) ), esc_attr( $title ) );
	}

	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( $new_instance['title'] ?? '' ) );
	}
}
/**
 * Return the estimated reading time in whole minutes for a post.
 *
 * Uses a simple word-count heuristic (content words divided by a configured
 * words-per-minute rate). Splitting on whitespace works well for Turkish
 * since it does not depend on locale-specific word boundary rules.
 *
 * @param int|WP_Post|null $post Post ID or object. Defaults to the current post.
 * @return int
 */
function calmpress_reading_time_minutes( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return 0;
	}
	$text  = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	$words = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
	$count = is_array( $words ) ? count( $words ) : 0;
	if ( 0 === $count ) {
		return 0;
	}
	$wpm = min( 320, max( 120, absint( calmpress_get_option( 'calmpress_reading_time_wpm' ) ) ) );
	return max( 1, (int) ceil( $count / $wpm ) );
}

/**
 * Build the localized "X dk okuma" reading-time label.
 *
 * @param int|WP_Post|null $post Post ID or object.
 * @return string Empty string when the feature is disabled or there is no content.
 */
function calmpress_reading_time_label( $post = null ) {
	if ( ! calmpress_get_option( 'calmpress_reading_time' ) ) {
		return '';
	}
	$minutes = calmpress_reading_time_minutes( $post );
	if ( $minutes < 1 ) {
		return '';
	}
	$suffix = trim( (string) calmpress_get_option( 'calmpress_reading_time_label' ) );
	if ( '' === $suffix ) {
		$suffix = __( 'dk okuma', 'calmpress' );
	}
	return $minutes . ' ' . $suffix;
}

/**
 * Return the post types the lightweight view counter tracks and displays.
 *
 * @return string[]
 */
function calmpress_view_counter_post_types() {
	$types = apply_filters( 'calmpress_view_counter_post_types', array( 'post', 'app' ) );
	return array_values( array_filter( array_map( 'sanitize_key', (array) $types ) ) );
}

/**
 * Return the stored, sanitized view count for a post.
 *
 * @param int|null $post_id Post ID. Defaults to the current post in the loop.
 * @return int
 */
function calmpress_get_view_count( $post_id = null ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_the_ID() );
	if ( ! $post_id ) {
		return 0;
	}
	return absint( get_post_meta( $post_id, '_calmpress_views', true ) );
}

/**
 * A dependency-free, best-effort check for common crawler user agents.
 *
 * This intentionally avoids third-party bot-detection services; it only
 * skips counting a handful of well-known non-human clients.
 *
 * @return bool
 */
function calmpress_is_probable_bot() {
	$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	if ( '' === $agent ) {
		return true;
	}
	return (bool) preg_match( '/bot|crawl|spider|slurp|facebookexternalhit|whatsapp|telegrambot|pingdom|uptimerobot|ahrefs|semrush|mj12bot|lighthouse|headless|bingpreview/i', $agent );
}

/**
 * Increment a post's view counter at most once per visitor per day.
 *
 * No IP addresses or other identifying data are stored; a single, small
 * first-party cookie remembers which posts a browser already counted so a
 * refresh or repeat visit within the window does not inflate the number.
 * Logged-in users who can edit posts, feeds, previews, and likely bots are
 * excluded so editorial traffic never pollutes the count.
 *
 * @return void
 */
function calmpress_track_view() {
	if ( is_admin() || ! calmpress_get_option( 'calmpress_view_counter' ) ) {
		return;
	}
	$post_types = calmpress_view_counter_post_types();
	if ( empty( $post_types ) || ! is_singular( $post_types ) || is_preview() || is_feed() ) {
		return;
	}
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return;
	}
	if ( calmpress_is_probable_bot() || headers_sent() ) {
		return;
	}
	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}
	$viewed = array();
	if ( isset( $_COOKIE['cp_viewed'] ) ) {
		$viewed = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['cp_viewed'] ) ) ) ) );
	}
	if ( in_array( $post_id, $viewed, true ) ) {
		return;
	}
	update_post_meta( $post_id, '_calmpress_views', calmpress_get_view_count( $post_id ) + 1 );
	$viewed[] = $post_id;
	if ( count( $viewed ) > 200 ) {
		$viewed = array_slice( $viewed, -200 );
	}
	$cookie_path   = ( defined( 'COOKIEPATH' ) && COOKIEPATH ) ? COOKIEPATH : '/';
	$cookie_domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';
	setcookie( 'cp_viewed', implode( ',', $viewed ), time() + DAY_IN_SECONDS, $cookie_path, $cookie_domain, is_ssl(), true );
}
add_action( 'template_redirect', 'calmpress_track_view' );

/**
 * Render an accessible, muted view-count badge with an eye icon.
 *
 * @param int|null $post_id Post ID. Defaults to the current post.
 * @return void
 */
function calmpress_render_view_count( $post_id = null ) {
	if ( ! calmpress_get_option( 'calmpress_show_views' ) ) {
		return;
	}
	$post_id = $post_id ? absint( $post_id ) : absint( get_the_ID() );
	if ( ! $post_id || ! in_array( get_post_type( $post_id ), calmpress_view_counter_post_types(), true ) ) {
		return;
	}
	$count = calmpress_get_view_count( $post_id );
	printf(
		'<span class="view-count"><svg class="view-count__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 5C5 5 2 12 2 12s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-2.2a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6Z"/></svg><span class="screen-reader-text">%1\$s</span><span aria-hidden="true">%2\$s</span></span>',
		esc_html( sprintf( _n( '%s görüntülenme', '%s görüntülenme', $count, 'calmpress' ), number_format_i18n( $count ) ) ),
		esc_html( number_format_i18n( $count ) )
	);
}

/**
 * Render the reading-time label and view-count badge together.
 *
 * Reading time only applies to standard posts; the view badge honors the
 * configured, filterable post types.
 *
 * @param int|null $post_id Post ID. Defaults to the current post.
 * @return void
 */
function calmpress_render_entry_extra_meta( $post_id = null ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_the_ID() );
	if ( ! $post_id ) {
		return;
	}
	$reading = 'post' === get_post_type( $post_id ) ? calmpress_reading_time_label( $post_id ) : '';
	ob_start();
	if ( $reading ) {
		printf( '<span class="reading-time"><span aria-hidden="true">⏱</span>%s</span>', esc_html( $reading ) );
	}
	calmpress_render_view_count( $post_id );
	$markup = ob_get_clean();
	if ( '' === $markup ) {
		return;
	}
	echo '<span class="entry-meta__extra">' . $markup . '</span>';
}

/**
 * Return the list of allowed screenshot attachment IDs for an app.
 *
 * @param int $post_id App post ID.
 * @return int[]
 */
function calmpress_app_screenshots( $post_id ) {
	$raw = get_post_meta( absint( $post_id ), '_calmpress_screenshots', true );
	if ( '' === trim( (string) $raw ) ) {
		return array();
	}
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) );
	$ids = array_values( array_unique( $ids ) );
	$ids = array_filter( $ids, static function ( $attachment_id ) {
		return 'attachment' === get_post_type( $attachment_id );
	} );
	return array_slice( array_values( $ids ), 0, 4 );
}

/**
 * Enqueue the media uploader and screenshot picker script on the app editor.
 *
 * @param string $hook_suffix Current admin screen hook.
 * @return void
 */
function calmpress_app_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'app' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
	$style = get_template_directory() . '/assets/css/admin.css';
	wp_enqueue_style( 'calmpress-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), file_exists( $style ) ? (string) filemtime( $style ) : CALMPRESS_VERSION );
	$script = get_template_directory() . '/assets/js/app-admin.js';
	wp_enqueue_script( 'calmpress-app-admin', get_template_directory_uri() . '/assets/js/app-admin.js', array(), file_exists( $script ) ? (string) filemtime( $script ) : CALMPRESS_VERSION, true );
	wp_localize_script(
		'calmpress-app-admin',
		'calmpressAppAdmin',
		array(
			'title'        => __( 'Ekran görüntüsü seç', 'calmpress' ),
			'buttonText'   => __( 'Kullan', 'calmpress' ),
			'removeLabel'  => __( 'Kaldır', 'calmpress' ),
			'limitReached' => __( 'En fazla dört ekran görüntüsü ekleyebilirsiniz.', 'calmpress' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'calmpress_app_admin_assets' );

/**
 * Render a semantic, accessible breadcrumb trail.
 *
 * Skipped on the front page since it has no ancestors. Intentionally plain
 * HTML with no JSON-LD so it never conflicts with the schema.org markup
 * already printed for singular content.
 *
 * @return void
 */
function calmpress_render_breadcrumbs() {
	if ( is_front_page() || ! calmpress_get_option( 'calmpress_breadcrumbs' ) ) {
		return;
	}
	$items   = array();
	$items[] = array(
		'label' => __( 'Anasayfa', 'calmpress' ),
		'url'   => home_url( '/' ),
	);

	if ( is_singular( 'app' ) ) {
		$archive_link = get_post_type_archive_link( 'app' );
		if ( $archive_link ) {
			$items[] = array( 'label' => __( 'Uygulamalar', 'calmpress' ), 'url' => $archive_link );
		}
		$terms = get_the_terms( get_the_ID(), 'app_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term    = reset( $terms );
			$items[] = array( 'label' => $term->name, 'url' => get_term_link( $term ) );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_singular( 'post' ) ) {
		$blog_page_id = (int) get_option( 'page_for_posts' );
		if ( $blog_page_id ) {
			$items[] = array( 'label' => get_the_title( $blog_page_id ), 'url' => get_permalink( $blog_page_id ) );
		}
		$categories = get_the_category();
		if ( $categories ) {
			$items[] = array( 'label' => $categories[0]->name, 'url' => get_category_link( $categories[0] ) );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $ancestor_id ) {
			$items[] = array( 'label' => get_the_title( $ancestor_id ), 'url' => get_permalink( $ancestor_id ) );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_post_type_archive( 'app' ) ) {
		$items[] = array( 'label' => post_type_archive_title( '', false ), 'url' => '' );
	} elseif ( is_tax( 'app_category' ) ) {
		$archive_link = get_post_type_archive_link( 'app' );
		if ( $archive_link ) {
			$items[] = array( 'label' => __( 'Uygulamalar', 'calmpress' ), 'url' => $archive_link );
		}
		$items[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
	} elseif ( is_category() || is_tag() ) {
		$items[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
	} elseif ( is_author() ) {
		$items[] = array( 'label' => sprintf( __( 'Yazar: %s', 'calmpress' ), get_the_author() ), 'url' => '' );
	} elseif ( is_date() ) {
		$items[] = array( 'label' => __( 'Tarihe göre arşiv', 'calmpress' ), 'url' => '' );
	} elseif ( is_search() ) {
		$items[] = array( 'label' => sprintf( __( 'Arama sonuçları: %s', 'calmpress' ), get_search_query() ), 'url' => '' );
	} elseif ( is_home() ) {
		$items[] = array( 'label' => __( 'Günlük', 'calmpress' ), 'url' => '' );
	} else {
		return;
	}

	if ( count( $items ) < 2 ) {
		return;
	}

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'İzlek', 'calmpress' ) . '"><ol>';
	$last = count( $items ) - 1;
	foreach ( $items as $index => $item ) {
		if ( $index === $last || '' === $item['url'] ) {
			printf( '<li aria-current="page">%s</li>', esc_html( $item['label'] ) );
		} else {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
		}
	}
	echo '</ol></nav>';
}

/**
 * Detect a handful of common SEO plugins so CalmPress never prints
 * duplicate Open Graph, Twitter Card, or PWA meta tags alongside them.
 *
 * @return bool
 */
function calmpress_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' )
		|| class_exists( 'WPSEO_Frontend' )
		|| defined( 'RANK_MATH_VERSION' )
		|| class_exists( 'RankMath' )
		|| defined( 'SEOPRESS_VERSION' )
		|| function_exists( 'seopress_init' )
		|| defined( 'AIOSEO_VERSION' )
		|| class_exists( 'All_in_One_SEO_Pack' );
}

/**
 * Print native Open Graph, Twitter Card, and PWA meta tags.
 *
 * Only runs when no known SEO plugin is active, so CalmPress never produces
 * duplicate tags. The document `<link rel="canonical">` is intentionally
 * left to WordPress core's own `rel_canonical()`, which already prints it;
 * this only adds `og:url`, which mirrors it for social crawlers.
 *
 * @return void
 */
function calmpress_social_meta() {
	if ( is_admin() || is_feed() || calmpress_seo_plugin_active() ) {
		return;
	}

	$title       = '';
	$description = '';
	$url         = '';
	$type        = 'website';
	$image       = '';

	if ( is_singular() ) {
		$url   = (string) get_permalink();
		$title = wp_strip_all_tags( get_the_title() );
		$type  = 'app' === get_post_type() ? 'website' : 'article';
		if ( has_excerpt() ) {
			$description = get_the_excerpt();
		} else {
			$description = wp_trim_words( wp_strip_all_tags( get_the_content() ), 45, '…' );
		}
		if ( has_post_thumbnail() ) {
			$image = (string) get_the_post_thumbnail_url( get_the_ID(), 'large' );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term        = get_queried_object();
		$url         = (string) get_term_link( $term );
		$title       = single_term_title( '', false );
		$description = wp_strip_all_tags( term_description() );
	} elseif ( is_author() ) {
		$url   = (string) get_author_posts_url( get_queried_object_id() );
		$title = get_the_author();
	} elseif ( is_home() || is_front_page() ) {
		$url         = home_url( '/' );
		$title       = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
	} else {
		global $wp;
		$url   = home_url( add_query_arg( array(), $wp->request ) );
		$title = wp_strip_all_tags( wp_get_document_title() );
	}

	if ( '' === trim( (string) $description ) ) {
		$description = calmpress_get_option( 'calmpress_seo_description' );
	}
	$description = wp_strip_all_tags( (string) $description );
	if ( '' === $image ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo_src = wp_get_attachment_image_src( $custom_logo_id, 'large' );
			if ( $logo_src ) {
				$image = $logo_src[0];
			}
		}
	}

	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	if ( $title ) {
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	}
	if ( $description ) {
		$trimmed = wp_trim_words( $description, 55, '…' );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $trimmed ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $trimmed ) );
	}
	if ( $url ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	}
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta name="twitter:card" content="%s">' . "\n", esc_attr( $image ? 'summary_large_image' : 'summary' ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
	$accent = calmpress_sanitize_accent_color( calmpress_get_option( 'calmpress_accent_color' ) );
	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $accent ) );
	echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
	echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
	echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
	printf( '<meta name="apple-mobile-web-app-title" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	echo '<link rel="manifest" href="' . esc_url( home_url( '/site.webmanifest' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'calmpress_social_meta', 3 );

/**
 * Serve a small, dynamic web app manifest without adding rewrite rules.
 *
 * Intercepts `/site.webmanifest` (and the equally common `/manifest.json`)
 * at `template_redirect`, before WordPress renders its 404 template, so no
 * permalink flush is required after activation.
 *
 * @return void
 */
function calmpress_serve_manifest() {
	if ( is_admin() || calmpress_seo_plugin_active() ) {
		return;
	}
	$request_path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
	$request_path = rtrim( $request_path, '/' );
	if ( ! in_array( $request_path, array( '/site.webmanifest', '/manifest.json' ), true ) ) {
		return;
	}
	$icons        = array();
	$site_icon_id = get_option( 'site_icon' );
	if ( $site_icon_id ) {
		foreach ( array( 192, 512 ) as $size ) {
			$src = wp_get_attachment_image_src( (int) $site_icon_id, array( $size, $size ) );
			if ( $src ) {
				$icons[] = array(
					'src'   => $src[0],
					'sizes' => $size . 'x' . $size,
					'type'  => get_post_mime_type( (int) $site_icon_id ) ?: 'image/png',
				);
			}
		}
	}
	$manifest = array(
		'name'             => get_bloginfo( 'name' ),
		'short_name'       => wp_trim_words( get_bloginfo( 'name' ), 2, '' ),
		'description'      => wp_strip_all_tags( get_bloginfo( 'description' ) ),
		'start_url'        => home_url( '/' ),
		'display'          => 'standalone',
		'background_color' => '#ffffff',
		'theme_color'      => calmpress_sanitize_accent_color( calmpress_get_option( 'calmpress_accent_color' ) ),
		'icons'            => $icons,
	);
	header( 'Content-Type: application/manifest+json; charset=utf-8' );
	status_header( 200 );
	echo wp_json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}
add_action( 'template_redirect', 'calmpress_serve_manifest', 0 );
