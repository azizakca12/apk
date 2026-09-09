<?php
/**
 * App card.
 *
 * @package CalmPress
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'app-card' ); ?>>
	<a class="post-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Uygulama ayrıntılarını gör: %s', 'calmpress' ), get_the_title() ) ); ?>">
		<div class="app-card__body">
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'calmpress-app-icon', array( 'class' => 'app-card__icon' ) ); endif; ?>
			<h2 class="entry-title"><?php the_title(); ?></h2>
			<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), min( 60, max( 8, absint( calmpress_get_option( 'calmpress_excerpt_length' ) ) ) ) ) ); ?></p><?php endif; ?>
			<?php if ( calmpress_get_option( 'calmpress_show_app_facts' ) ) : ?><div class="app-card__facts">
				<?php $version = calmpress_app_detail( get_the_ID(), 'version' ); ?>
				<?php $platform = calmpress_app_detail( get_the_ID(), 'platform' ); ?>
				<?php if ( $version ) : ?><span><?php echo esc_html( $version ); ?></span><?php endif; ?>
				<?php if ( $platform ) : ?><span><?php echo esc_html( $platform ); ?></span><?php endif; ?>
			</div><?php endif; ?>
			<span class="card-link"><?php esc_html_e( 'Ayrıntıları gör', 'calmpress' ); ?><span aria-hidden="true">↗</span></span>
		</div>
	</a>
</article>
