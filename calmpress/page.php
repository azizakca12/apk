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
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
