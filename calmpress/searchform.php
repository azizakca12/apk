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
	<label class="search-category">
		<span class="screen-reader-text"><?php esc_html_e( 'Kategori seçin:', 'calmpress' ); ?></span>
		<select name="cat">
			<option value=""><?php esc_html_e( 'Tüm kategoriler', 'calmpress' ); ?></option>
			<?php foreach ( get_categories( array( 'hide_empty' => true ) ) as $category ) : ?>
				<option value="<?php echo absint( $category->term_id ); ?>" <?php selected( get_query_var( 'cat' ), $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<button type="submit"><?php esc_html_e( 'Ara', 'calmpress' ); ?></button>
</form>
