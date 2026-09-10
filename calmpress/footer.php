<?php
/**
 * Footer template.
 *
 * @package CalmPress
 */
?>
<footer class="site-footer site-footer--columns-<?php echo esc_attr( min( 4, max( 1, absint( calmpress_get_option( 'calmpress_footer_columns' ) ) ) ) ); ?>">
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
<?php if ( calmpress_get_option( 'calmpress_mobile_nav' ) ) : ?>
	<nav class="mobile-bottom-nav" aria-label="<?php esc_attr_e( 'Mobil gezinme', 'calmpress' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><span aria-hidden="true">⌂</span><?php esc_html_e( 'Ana sayfa', 'calmpress' ); ?></a>
		<?php if ( has_nav_menu( 'primary' ) ) : ?><button type="button" data-mobile-menu aria-expanded="false"><span aria-hidden="true">☰</span><?php esc_html_e( 'Menü', 'calmpress' ); ?></button><?php endif; ?>
		<?php if ( calmpress_get_option( 'calmpress_modal_search' ) ) : ?><button type="button" data-search-open aria-controls="calmpress-search-modal"><span aria-hidden="true">⌕</span><?php esc_html_e( 'Ara', 'calmpress' ); ?></button><?php endif; ?>
	</nav>
<?php endif; ?>
<?php if ( calmpress_get_option( 'calmpress_campaign_enabled' ) && calmpress_get_option( 'calmpress_campaign_text' ) ) : ?>
	<div class="campaign-notice" data-campaign data-campaign-key="<?php echo esc_attr( md5( calmpress_get_option( 'calmpress_campaign_text' ) . calmpress_get_option( 'calmpress_campaign_link' ) ) ); ?>">
		<?php if ( calmpress_get_option( 'calmpress_campaign_link' ) ) : ?><a href="<?php echo esc_url( calmpress_get_option( 'calmpress_campaign_link' ) ); ?>"><?php echo esc_html( calmpress_get_option( 'calmpress_campaign_text' ) ); ?></a><?php else : ?><span><?php echo esc_html( calmpress_get_option( 'calmpress_campaign_text' ) ); ?></span><?php endif; ?>
		<button type="button" data-campaign-dismiss aria-label="<?php esc_attr_e( 'Bildirimi kapat', 'calmpress' ); ?>">×</button>
	</div>
<?php endif; ?>
<?php if ( calmpress_get_option( 'calmpress_back_to_top' ) ) : ?>
	<button type="button" class="back-to-top" data-back-to-top aria-label="<?php esc_attr_e( 'Sayfanın başına dön', 'calmpress' ); ?>"><span aria-hidden="true">↑</span><span class="screen-reader-text"><?php esc_html_e( 'Başa dön', 'calmpress' ); ?></span></button>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
