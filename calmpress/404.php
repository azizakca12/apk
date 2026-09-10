<?php
/**
 * Not found template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<div class="content-card empty-state">
		<h1><?php esc_html_e( 'Bu sayfa taşınmış olabilir.', 'calmpress' ); ?></h1>
		<p><?php esc_html_e( 'Arama yapmayı veya ana sayfaya dönmeyi deneyin.', 'calmpress' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana sayfaya dön', 'calmpress' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
