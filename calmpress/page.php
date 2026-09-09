<?php
/**
 * Page template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-card' ); ?>>
			<header><h1 class="entry-title"><?php the_title(); ?></h1></header>
			<?php if ( calmpress_get_option( 'calmpress_share_buttons' ) ) : calmpress_render_share_tools( 'top' ); endif; ?>
			<div class="entry-content"><?php the_content(); ?></div>
			<?php if ( calmpress_get_option( 'calmpress_share_buttons' ) ) : calmpress_render_share_tools( 'bottom' ); endif; ?>
		</article>
		<?php calmpress_render_author_box(); ?>
		<?php calmpress_render_related_posts(); ?>
	<?php endwhile; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
