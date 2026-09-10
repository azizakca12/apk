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
	<?php calmpress_render_breadcrumbs(); ?>
	<header class="archive-header">
		<h1><?php echo esc_html( calmpress_get_option( 'calmpress_apps_archive_title' ) ? calmpress_get_option( 'calmpress_apps_archive_title' ) : post_type_archive_title( '', false ) ); ?></h1>
		<p><?php echo esc_html( calmpress_get_option( 'calmpress_apps_archive_description' ) ? calmpress_get_option( 'calmpress_apps_archive_description' ) : __( 'Yararlı Android uygulamalarını açık bilgiler ve doğrudan indirme bağlantılarıyla keşfedin.', 'calmpress' ) ); ?></p>
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
