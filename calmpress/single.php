<?php
/**
 * Single post template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="single-header">
				<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( get_the_author() ); ?></div>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?><p class="entry-meta"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			</header>
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'class' => 'featured-image', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); endif; ?>
			<div class="content-card">
				<div class="entry-content"><?php the_content(); ?></div>
				<?php wp_link_pages( array( 'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Sayfalar', 'calmpress' ) . '">', 'after' => '</nav>' ) ); ?>
			</div>
			<nav class="post-navigation" aria-label="<?php esc_attr_e( 'Yazı gezinmesi', 'calmpress' ); ?>">
				<div class="nav-previous"><?php previous_post_link( '%link', '← %title' ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title →' ); ?></div>
			</nav>
		</article>
		<?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
	<?php endwhile; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
