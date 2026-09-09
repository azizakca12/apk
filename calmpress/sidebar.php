<?php
/**
 * Sidebar template.
 *
 * @package CalmPress
 */

if ( ! is_active_sidebar( 'sidebar-primary' ) && ! calmpress_get_option( 'calmpress_ad_sidebar_enabled' ) ) {
	return;
}
?>
<aside class="site-sidebar" aria-label="<?php esc_attr_e( 'Kenar çubuğu', 'calmpress' ); ?>">
	<?php calmpress_render_widget_area( 'sidebar-primary' ); ?>
	<?php calmpress_render_ad( 'sidebar' ); ?>
</aside>
