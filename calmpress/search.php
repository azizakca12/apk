<?php
/**
 * Search results template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<header class="archive-header">
		<h1><?php printf( esc_html__( 'Results for: %s', 'calmpress' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>
		<?php get_search_form(); ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="card-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'post' ); ?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => esc_html__( 'Previous', 'calmpress' ), 'next_text' => esc_html__( 'Next', 'calmpress' ) ) ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
