<?php
/**
 * Front page template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<?php calmpress_render_widget_area( 'home-before' ); ?>
	<section class="hero <?php echo calmpress_get_option( 'calmpress_gradient_background' ) ? 'hero--gradient' : ''; ?>" aria-labelledby="hero-title">
		<?php $hero_eyebrow = calmpress_get_option( 'calmpress_hero_eyebrow' ); ?>
		<?php $hero_title = calmpress_get_option( 'calmpress_hero_title' ) ? calmpress_get_option( 'calmpress_hero_title' ) : get_bloginfo( 'name' ); ?>
		<?php $hero_description = calmpress_get_option( 'calmpress_hero_description' ) ? calmpress_get_option( 'calmpress_hero_description' ) : get_bloginfo( 'description' ); ?>
		<?php if ( $hero_eyebrow ) : ?><p class="badge"><?php echo esc_html( $hero_eyebrow ); ?></p><?php endif; ?>
		<h1 id="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
		<?php if ( $hero_description ) : ?><p><?php echo esc_html( $hero_description ); ?></p><?php endif; ?>
		<?php if ( calmpress_get_option( 'calmpress_hero_cta_text' ) && calmpress_get_option( 'calmpress_hero_cta_link' ) ) : ?>
			<a class="button hero__cta" href="<?php echo esc_url( calmpress_get_option( 'calmpress_hero_cta_link' ) ); ?>"><?php echo esc_html( calmpress_get_option( 'calmpress_hero_cta_text' ) ); ?></a>
		<?php endif; ?>
	</section>

	<?php
	$section_cards = min( 4, max( 1, absint( calmpress_get_option( 'calmpress_cards_per_section' ) ) ) );
	$latest_posts = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => $section_cards,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	?>
	<?php if ( calmpress_get_option( 'calmpress_show_latest_journal' ) && $latest_posts->have_posts() ) : ?>
		<section aria-labelledby="latest-title">
			<div class="section-heading">
				<h2 id="latest-title"><?php esc_html_e( 'Günlükten son yazılar', 'calmpress' ); ?></h2>
				<?php $blog_url = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' ); ?>
				<a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Tümünü gör', 'calmpress' ); ?></a>
			</div>
			<div class="card-grid">
				<?php while ( $latest_posts->have_posts() ) : $latest_posts->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'post' ); ?>
				<?php endwhile; ?>
			</div>
		</section>
	<?php endif; wp_reset_postdata(); ?>

	<?php
	$latest_apps = new WP_Query(
		array(
			'post_type'      => 'app',
			'posts_per_page' => $section_cards,
			'no_found_rows'  => true,
		)
	);
	?>
	<?php if ( calmpress_get_option( 'calmpress_show_featured_apps' ) && $latest_apps->have_posts() ) : ?>
		<section class="section-spaced section-spaced--<?php echo esc_attr( calmpress_get_option( 'calmpress_section_spacing' ) ); ?>" aria-labelledby="apps-title">
			<div class="section-heading">
				<h2 id="apps-title"><?php esc_html_e( 'Öne çıkan uygulamalar', 'calmpress' ); ?></h2>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"><?php esc_html_e( 'Uygulamalara göz at', 'calmpress' ); ?></a>
			</div>
			<div class="card-grid">
				<?php while ( $latest_apps->have_posts() ) : $latest_apps->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'app' ); ?>
				<?php endwhile; ?>
			</div>
		</section>
	<?php endif; wp_reset_postdata(); ?>
	<?php calmpress_render_widget_area( 'home-after' ); ?>
</main>
<?php
get_footer();
