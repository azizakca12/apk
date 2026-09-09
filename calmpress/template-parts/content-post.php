<?php
/**
 * Post card.
 *
 * @package CalmPress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'calmpress-card', array( 'class' => 'post-card__image' ) ); ?>
		<?php endif; ?>
		<div class="post-card__body">
			<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
			<h2 class="entry-title"><?php the_title(); ?></h2>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		</div>
	</a>
</article>
