<?php
/**
 * Single app template.
 *
 * @package CalmPress
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php calmpress_render_before_content(); ?>
	<?php calmpress_render_breadcrumbs(); ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="app-hero">
				<div>
					<div class="entry-meta"><span><?php esc_html_e( 'Android uygulaması', 'calmpress' ); ?></span><?php calmpress_render_entry_extra_meta(); ?></div>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?><p class="entry-meta"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
					<?php $download_url = calmpress_app_detail( get_the_ID(), 'download_url' ); ?>
					<?php if ( $download_url ) : ?>
						<a class="button app-download" href="<?php echo esc_url( $download_url ); ?>" rel="nofollow sponsored"><?php echo esc_html( calmpress_get_option( 'calmpress_app_download_label' ) ? calmpress_get_option( 'calmpress_app_download_label' ) : __( 'APK indir', 'calmpress' ) ); ?></a>
					<?php endif; ?>
				</div>
				<div>
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'calmpress-app-icon', array( 'class' => 'app-hero__icon', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); endif; ?>
				</div>
			</div>
			<div class="content-card">
				<?php if ( calmpress_get_option( 'calmpress_show_app_facts' ) ) : ?><dl class="app-details">
					<?php
					$app_fields = array(
						'version'     => __( 'Sürüm', 'calmpress' ),
						'file_size'   => __( 'Dosya boyutu', 'calmpress' ),
						'platform'    => __( 'Platform', 'calmpress' ),
						'developer'   => __( 'Geliştirici', 'calmpress' ),
						'min_android' => __( 'Minimum Android sürümü', 'calmpress' ),
					);
					foreach ( $app_fields as $key => $label ) :
						$value = calmpress_app_detail( get_the_ID(), $key );
						if ( ! $value ) {
							continue;
						}
						?>
						<div class="app-detail"><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
					<?php endforeach; ?>
				</dl><?php endif; ?>
				<div class="entry-content"><?php the_content(); ?></div>
			</div>
			<?php $calmpress_screenshots = calmpress_app_screenshots( get_the_ID() ); ?>
			<?php if ( calmpress_get_option( 'calmpress_show_app_screenshots' ) && $calmpress_screenshots ) : ?>
			<section class="app-gallery" aria-labelledby="app-gallery-title">
				<h2 id="app-gallery-title"><?php esc_html_e( 'Ekran görüntüleri', 'calmpress' ); ?></h2>
				<div class="app-gallery__grid">
					<?php foreach ( $calmpress_screenshots as $calmpress_screenshot_id ) : ?>
						<?php echo wp_get_attachment_image( $calmpress_screenshot_id, 'large', false, array( 'class' => 'app-gallery__image', 'loading' => 'lazy', 'decoding' => 'async', 'alt' => get_the_title() ) ); ?>
					<?php endforeach; ?>
				</div>
			</section>
			<?php endif; ?>
			<?php $calmpress_changelog = calmpress_app_detail( get_the_ID(), 'changelog' ); ?>
			<?php if ( calmpress_get_option( 'calmpress_show_app_changelog' ) && $calmpress_changelog ) : ?>
			<section class="app-changelog" aria-labelledby="app-changelog-title">
				<h2 id="app-changelog-title"><?php esc_html_e( 'Sürüm notları', 'calmpress' ); ?></h2>
				<div class="app-changelog__content"><ul>
					<?php foreach ( preg_split( '/\r\n|\r|\n/', trim( $calmpress_changelog ) ) as $calmpress_changelog_line ) : ?>
						<?php $calmpress_changelog_line = trim( $calmpress_changelog_line ); ?>
						<?php if ( '' === $calmpress_changelog_line ) { continue; } ?>
						<li><?php echo esc_html( $calmpress_changelog_line ); ?></li>
					<?php endforeach; ?>
				</ul></div>
			</section>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
