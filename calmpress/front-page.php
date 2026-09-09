<?php
/**
 * Front page template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<section class="hero" aria-labelledby="hero-title">
		<p class="badge"><?php esc_html_e( 'Thoughtful publishing & app discovery', 'calmpress' ); ?></p>
		<h1 id="hero-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
		<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
	</section>

	<?php
	$latest_posts = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	?>
	<?php if ( $latest_posts->have_posts() ) : ?>
		<section aria-labelledby="latest-title">
			<div class="section-heading">
				<h2 id="latest-title"><?php esc_html_e( 'Latest from the journal', 'calmpress' ); ?></h2>
				<?php $blog_url = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' ); ?>
				<a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'View all', 'calmpress' ); ?></a>
			</div>
			<div class="card-grid">
				<?php while ( $latest_posts->have_posts() ) : $latest_posts->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'post' ); ?>
				<?php endwhile; ?>
			</div>
		</section>
	<?php endif; wp_reset_postdata(); ?>

	<?php
	$latest_apps = new WP_Query(
		array(
			'post_type'      => 'app',
			'posts_per_page' => 3,
			'no_found_rows'  => true,
		)
	);
	?>
	<?php if ( $latest_apps->have_posts() ) : ?>
		<section class="section-spaced" aria-labelledby="apps-title">
			<div class="section-heading">
				<h2 id="apps-title"><?php esc_html_e( 'Featured apps', 'calmpress' ); ?></h2>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"><?php esc_html_e( 'Browse apps', 'calmpress' ); ?></a>
			</div>
			<div class="card-grid">
				<?php while ( $latest_apps->have_posts() ) : $latest_apps->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'app' ); ?>
				<?php endwhile; ?>
			</div>
		</section>
	<?php endif; wp_reset_postdata(); ?>
</main>
<?php
get_footer();
