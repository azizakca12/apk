<?php
/**
 * App card.
 *
 * @package CalmPress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'app-card' ); ?>>
	<a class="post-card__link" href="<?php the_permalink(); ?>">
		<div class="app-card__body">
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'calmpress-app-icon', array( 'class' => 'app-card__icon' ) ); endif; ?>
			<h2 class="entry-title"><?php the_title(); ?></h2>
			<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p><?php endif; ?>
			<div class="app-card__facts">
				<?php $version = calmpress_app_detail( get_the_ID(), 'version' ); ?>
				<?php $platform = calmpress_app_detail( get_the_ID(), 'platform' ); ?>
				<?php if ( $version ) : ?><span><?php echo esc_html( $version ); ?></span><?php endif; ?>
				<?php if ( $platform ) : ?><span><?php echo esc_html( $platform ); ?></span><?php endif; ?>
			</div>
		</div>
	</a>
</article>
