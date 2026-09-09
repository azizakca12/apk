<?php
/**
 * Post card.
 *
 * @package CalmPress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Yazıyı oku: %s', 'calmpress' ), get_the_title() ) ); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'calmpress-card', array( 'class' => 'post-card__image' ) ); ?>
		<?php endif; ?>
		<div class="post-card__body">
			<?php if ( calmpress_get_option( 'calmpress_show_metadata' ) ) : ?><div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div><?php endif; ?>
			<h2 class="entry-title"><?php the_title(); ?></h2>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), min( 60, max( 8, absint( calmpress_get_option( 'calmpress_excerpt_length' ) ) ) ) ) ); ?></p>
			<span class="card-link"><?php esc_html_e( 'Yazıyı oku', 'calmpress' ); ?><span aria-hidden="true">↗</span></span>
		</div>
	</a>
</article>
