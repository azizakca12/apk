<?php
/**
 * Generic content card fallback.
 *
 * @package CalmPress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<div class="post-card__body">
		<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
	</div>
</article>
