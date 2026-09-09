<?php
/**
 * App archive template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<header class="archive-header">
		<h1><?php post_type_archive_title(); ?></h1>
		<p><?php esc_html_e( 'Discover useful Android applications with clear details and direct downloads.', 'calmpress' ); ?></p>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="card-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'app' ); ?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => esc_html__( 'Previous', 'calmpress' ), 'next_text' => esc_html__( 'Next', 'calmpress' ) ) ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
