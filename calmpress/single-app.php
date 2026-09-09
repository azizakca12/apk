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
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="app-hero">
				<div>
					<div class="entry-meta"><?php esc_html_e( 'Android uygulaması', 'calmpress' ); ?></div>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?><p class="entry-meta"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
					<?php $download_url = calmpress_app_detail( get_the_ID(), 'download_url' ); ?>
					<?php if ( $download_url ) : ?>
						<a class="button app-download" href="<?php echo esc_url( $download_url ); ?>" rel="nofollow sponsored"><?php esc_html_e( 'APK indir', 'calmpress' ); ?></a>
					<?php endif; ?>
				</div>
				<div>
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'calmpress-app-icon', array( 'class' => 'app-hero__icon', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); endif; ?>
				</div>
			</div>
			<div class="content-card">
				<dl class="app-details">
					<?php
					$app_fields = array(
						'version'   => __( 'Sürüm', 'calmpress' ),
						'file_size' => __( 'Dosya boyutu', 'calmpress' ),
						'platform'  => __( 'Platform', 'calmpress' ),
						'developer' => __( 'Geliştirici', 'calmpress' ),
					);
					foreach ( $app_fields as $key => $label ) :
						$value = calmpress_app_detail( get_the_ID(), $key );
						if ( ! $value ) {
							continue;
						}
						?>
						<div class="app-detail"><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
				<div class="entry-content"><?php the_content(); ?></div>
			</div>
		</article>
	<?php endwhile; ?>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
