<?php
/**
 * App category archive.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<?php calmpress_render_breadcrumbs(); ?>
	<header class="archive-header">
		<h1><?php single_term_title(); ?></h1>
		<?php the_archive_description( '<p>', '</p>' ); ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="card-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'app' ); ?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => esc_html__( 'Önceki', 'calmpress' ), 'next_text' => esc_html__( 'Sonraki', 'calmpress' ) ) ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
