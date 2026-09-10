<?php
/**
 * Generic content card fallback.
 *
 * @package CalmPress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<div class="post-card__body">
		<?php if ( calmpress_get_option( 'calmpress_show_metadata' ) ) : ?><div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div><?php endif; ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="link-arrow" aria-hidden="true">↗</span></a></h2>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), min( 60, max( 8, absint( calmpress_get_option( 'calmpress_excerpt_length' ) ) ) ) ) ); ?></p>
	</div>
</article>
