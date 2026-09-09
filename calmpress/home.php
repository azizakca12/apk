<?php
/**
 * Blog index template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<header class="archive-header">
		<h1><?php echo esc_html( calmpress_get_option( 'calmpress_blog_archive_title' ) ? calmpress_get_option( 'calmpress_blog_archive_title' ) : ( get_the_title( (int) get_option( 'page_for_posts' ) ) ?: __( 'Günlük', 'calmpress' ) ) ); ?></h1>
		<?php $blog_description = calmpress_get_option( 'calmpress_blog_archive_description' ) ? calmpress_get_option( 'calmpress_blog_archive_description' ) : get_bloginfo( 'description' ); ?>
		<?php if ( $blog_description ) : ?>
			<p><?php echo esc_html( $blog_description ); ?></p>
		<?php endif; ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="card-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'post' ); ?>
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
