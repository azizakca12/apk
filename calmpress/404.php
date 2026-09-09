<?php
/**
 * Not found template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<div class="content-card empty-state">
		<h1><?php esc_html_e( 'That page has moved.', 'calmpress' ); ?></h1>
		<p><?php esc_html_e( 'Try a search or return to the homepage.', 'calmpress' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go home', 'calmpress' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
