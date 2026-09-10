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
	<?php calmpress_render_breadcrumbs(); ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="single-header">
				<?php if ( calmpress_get_option( 'calmpress_show_metadata' ) || calmpress_get_option( 'calmpress_reading_time' ) || calmpress_get_option( 'calmpress_show_views' ) ) : ?><div class="entry-meta"><?php if ( calmpress_get_option( 'calmpress_show_metadata' ) ) : ?><span><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( get_the_author() ); ?></span><?php endif; ?><?php calmpress_render_entry_extra_meta(); ?></div><?php endif; ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?><p class="entry-meta"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			</header>
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'class' => 'featured-image', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); endif; ?>
			<div class="content-card">
				<?php if ( calmpress_get_option( 'calmpress_share_buttons' ) ) : calmpress_render_share_tools( 'top' ); endif; ?>
				<div class="entry-content"><?php the_content(); ?></div>
				<?php wp_link_pages( array( 'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Sayfalar', 'calmpress' ) . '">', 'after' => '</nav>' ) ); ?>
				<?php if ( calmpress_get_option( 'calmpress_share_buttons' ) ) : ?>
					<?php calmpress_render_share_tools( 'bottom' ); ?>
				<?php endif; ?>
				<?php calmpress_render_post_tags(); ?>
			</div>
			<?php calmpress_render_author_box(); ?>
			<nav class="post-navigation" aria-label="<?php esc_attr_e( 'Yazı gezinmesi', 'calmpress' ); ?>">
				<div class="nav-previous"><?php previous_post_link( '<span class="post-navigation__label">%link</span>', '← Önceki yazı: %title' ); ?></div>
				<div class="nav-next"><?php next_post_link( '<span class="post-navigation__label">%link</span>', 'Sonraki yazı: %title →' ); ?></div>
			</nav>
		</article>
		<?php calmpress_render_related_posts(); ?>
		<?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
	<?php endwhile; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
