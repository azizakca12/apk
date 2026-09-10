<?php
/**
 * Generic archive template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<?php calmpress_render_breadcrumbs(); ?>
	<header class="archive-header">
		<?php $archive_title = is_home() && calmpress_get_option( 'calmpress_blog_archive_title' ) ? calmpress_get_option( 'calmpress_blog_archive_title' ) : ''; ?>
		<?php $archive_description = is_home() && calmpress_get_option( 'calmpress_blog_archive_description' ) ? calmpress_get_option( 'calmpress_blog_archive_description' ) : ''; ?>
		<?php if ( $archive_title ) : ?><h1><?php echo esc_html( $archive_title ); ?></h1><?php else : ?><h1><?php the_archive_title(); ?></h1><?php endif; ?>
		<?php if ( $archive_description ) : ?><p><?php echo esc_html( $archive_description ); ?></p><?php else : ?><?php the_archive_description( '<p>', '</p>' ); ?><?php endif; ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="card-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', is_post_type_archive( 'app' ) ? 'app' : 'post' ); ?>
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
