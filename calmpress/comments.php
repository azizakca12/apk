<?php
/**
 * Comments template.
 *
 * @package CalmPress
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2><?php comments_number( esc_html__( 'Henüz yorum yok', 'calmpress' ), esc_html__( 'Bir yorum', 'calmpress' ), esc_html__( '% yorum', 'calmpress' ) ); ?></h2>
		<ol class="comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'avatar_size' => 44, 'short_ping' => true ) ); ?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>
	<?php
	comment_form(
		array(
			'title_reply'        => esc_html__( 'Yorum bırakın', 'calmpress' ),
			'class_submit'       => 'button',
			'comment_notes_after' => '',
		)
	);
	?>
</section>
