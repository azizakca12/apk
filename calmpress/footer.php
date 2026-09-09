<?php
/**
 * Footer template.
 *
 * @package CalmPress
 */
?>
<footer class="site-footer">
	<div class="site-footer__inner">
		<div>
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
			<?php if ( calmpress_get_option( 'calmpress_footer_text' ) ) : ?><p><?php echo esc_html( calmpress_get_option( 'calmpress_footer_text' ) ); ?></p><?php endif; ?>
			<?php if ( calmpress_get_option( 'calmpress_show_footer_widgets' ) ) : ?>
				<?php calmpress_render_widget_area( 'footer-1' ); ?>
			<?php endif; ?>
			<?php
			$socials = array(
				'facebook'  => __( 'Facebook', 'calmpress' ),
				'instagram' => __( 'Instagram', 'calmpress' ),
				'x'         => __( 'X', 'calmpress' ),
				'youtube'   => __( 'YouTube', 'calmpress' ),
				'github'    => __( 'GitHub', 'calmpress' ),
			);
			$active_socials = array();
			foreach ( $socials as $key => $label ) {
				$url = calmpress_get_option( 'calmpress_social_' . $key );
				if ( $url ) {
					$active_socials[ $label ] = $url;
				}
			}
			?>
			<?php if ( $active_socials ) : ?>
				<nav class="social-navigation" aria-label="<?php esc_attr_e( 'Sosyal bağlantılar', 'calmpress' ); ?>">
					<?php foreach ( $active_socials as $label => $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" rel="me"><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
		<?php if ( calmpress_get_option( 'calmpress_show_footer_menu' ) ) : ?>
		<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Alt bilgi gezinmesi', 'calmpress' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
		<?php endif; ?>
	</div>
</footer>
<?php calmpress_render_ad( 'footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
