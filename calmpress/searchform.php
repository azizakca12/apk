<?php
/**
 * Search form.
 *
 * @package CalmPress
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Şunu ara:', 'calmpress' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Ara…', 'placeholder', 'calmpress' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<button type="submit"><?php esc_html_e( 'Ara', 'calmpress' ); ?></button>
</form>
