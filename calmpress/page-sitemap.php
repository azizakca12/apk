<?php
/**
 * Template Name: Site Haritası
 * Template Post Type: page
 *
 * A lightweight HTML sitemap: categories, an app archive link with app
 * categories, and a capped, paginated list of recent posts. Assign this
 * template to any page (or name the page "sitemap") to use it; CalmPress
 * never creates the page automatically.
 *
 * @package CalmPress
 */

get_header();

$calmpress_sitemap_page    = isset( $_GET['sayfa'] ) ? max( 1, absint( $_GET['sayfa'] ) ) : 1;
$calmpress_sitemap_per_page = 30;
$calmpress_sitemap_posts   = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => $calmpress_sitemap_per_page,
		'paged'               => $calmpress_sitemap_page,
		'ignore_sticky_posts' => true,
	)
);
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<?php calmpress_render_breadcrumbs(); ?>
	<?php while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-card sitemap-page' ); ?>>
		<header><h1 class="entry-title"><?php the_title(); ?></h1></header>
		<?php if ( get_the_content() ) : ?><div class="entry-content"><?php the_content(); ?></div><?php endif; ?>

		<section class="sitemap-section" aria-labelledby="sitemap-categories-title">
			<h2 id="sitemap-categories-title"><?php esc_html_e( 'Kategoriler', 'calmpress' ); ?></h2>
			<?php $calmpress_sitemap_categories = get_categories( array( 'hide_empty' => true ) ); ?>
			<?php if ( $calmpress_sitemap_categories ) : ?>
				<ul class="sitemap-list">
					<?php foreach ( $calmpress_sitemap_categories as $calmpress_sitemap_category ) : ?>
						<li><a href="<?php echo esc_url( get_category_link( $calmpress_sitemap_category ) ); ?>"><?php echo esc_html( $calmpress_sitemap_category->name ); ?></a><span><?php echo esc_html( sprintf( _n( '%s yazı', '%s yazı', $calmpress_sitemap_category->count, 'calmpress' ), number_format_i18n( $calmpress_sitemap_category->count ) ) ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p><?php esc_html_e( 'Henüz kategori bulunmuyor.', 'calmpress' ); ?></p>
			<?php endif; ?>
		</section>

		<?php if ( post_type_exists( 'app' ) ) : ?>
		<section class="sitemap-section" aria-labelledby="sitemap-apps-title">
			<h2 id="sitemap-apps-title"><?php esc_html_e( 'Uygulamalar', 'calmpress' ); ?></h2>
			<p><a class="button button--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"><?php esc_html_e( 'Tüm uygulama arşivini gör', 'calmpress' ); ?></a></p>
			<?php $calmpress_sitemap_app_categories = get_terms( array( 'taxonomy' => 'app_category', 'hide_empty' => true ) ); ?>
			<?php if ( ! is_wp_error( $calmpress_sitemap_app_categories ) && $calmpress_sitemap_app_categories ) : ?>
				<ul class="sitemap-list">
					<?php foreach ( $calmpress_sitemap_app_categories as $calmpress_sitemap_app_category ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $calmpress_sitemap_app_category ) ); ?>"><?php echo esc_html( $calmpress_sitemap_app_category->name ); ?></a><span><?php echo absint( $calmpress_sitemap_app_category->count ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>
		<?php endif; ?>

		<section class="sitemap-section" aria-labelledby="sitemap-posts-title">
			<h2 id="sitemap-posts-title"><?php esc_html_e( 'Son yazılar', 'calmpress' ); ?></h2>
			<?php if ( $calmpress_sitemap_posts->have_posts() ) : ?>
				<ul class="sitemap-list">
					<?php while ( $calmpress_sitemap_posts->have_posts() ) : $calmpress_sitemap_posts->the_post(); ?>
						<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><span><?php echo esc_html( get_the_date() ); ?></span></li>
					<?php endwhile; ?>
				</ul>
				<?php if ( $calmpress_sitemap_posts->max_num_pages > 1 ) : ?>
					<nav class="sitemap-pagination" aria-label="<?php esc_attr_e( 'Yazı sayfaları', 'calmpress' ); ?>">
						<ul>
							<?php for ( $calmpress_sitemap_page_number = 1; $calmpress_sitemap_page_number <= $calmpress_sitemap_posts->max_num_pages; $calmpress_sitemap_page_number++ ) : ?>
								<?php if ( $calmpress_sitemap_page_number === $calmpress_sitemap_page ) : ?>
									<li aria-current="page"><?php echo absint( $calmpress_sitemap_page_number ); ?></li>
								<?php else : ?>
									<?php $calmpress_sitemap_link = 1 === $calmpress_sitemap_page_number ? remove_query_arg( 'sayfa' ) : add_query_arg( 'sayfa', $calmpress_sitemap_page_number ); ?>
									<li><a href="<?php echo esc_url( $calmpress_sitemap_link ); ?>"><?php echo absint( $calmpress_sitemap_page_number ); ?></a></li>
								<?php endif; ?>
							<?php endfor; ?>
						</ul>
					</nav>
				<?php endif; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Henüz yazı bulunmuyor.', 'calmpress' ); ?></p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</section>
	</article>
	<?php endwhile; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();