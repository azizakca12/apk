<?php
/**
 * Header template.
 *
 * @package CalmPress
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="reading-progress" aria-hidden="true"><span class="reading-progress__bar"></span></div>
<a class="screen-reader-text" href="#primary"><?php esc_html_e( 'İçeriğe geç', 'calmpress' ); ?></a>
<?php $announcement_style = function_exists( 'calmpress_customize_announcement_style' ) ? calmpress_customize_announcement_style( calmpress_get_option( 'calmpress_announcement_style' ) ) : 'soft'; ?>
<?php $nav_style = function_exists( 'calmpress_customize_nav_style' ) ? calmpress_customize_nav_style( calmpress_get_option( 'calmpress_primary_nav_style' ) ) : 'minimal'; ?>
<header class="site-header <?php echo calmpress_get_option( 'calmpress_sticky_header' ) ? 'is-sticky' : 'is-static'; ?>">
	<?php if ( calmpress_get_option( 'calmpress_announcement_text' ) ) : ?>
		<div class="site-announcement site-announcement--<?php echo esc_attr( $announcement_style ); ?>">
			<?php if ( calmpress_get_option( 'calmpress_announcement_link' ) ) : ?>
				<a href="<?php echo esc_url( calmpress_get_option( 'calmpress_announcement_link' ) ); ?>"><?php echo esc_html( calmpress_get_option( 'calmpress_announcement_text' ) ); ?></a>
			<?php else : ?>
				<?php echo esc_html( calmpress_get_option( 'calmpress_announcement_text' ) ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() && calmpress_get_option( 'calmpress_show_logo' ) ) : ?>
				<?php the_custom_logo(); ?>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<span class="site-branding__name"><?php bloginfo( 'name' ); ?></span>
			</a>
		</div>
		<nav class="primary-navigation primary-navigation--<?php echo esc_attr( $nav_style ); ?>" aria-label="<?php esc_attr_e( 'Birincil gezinme', 'calmpress' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
		<?php if ( calmpress_get_option( 'calmpress_modal_search' ) ) : ?>
			<button type="button" class="search-toggle button--secondary" data-search-open aria-controls="calmpress-search-modal" aria-haspopup="dialog"><?php esc_html_e( 'Ara', 'calmpress' ); ?></button>
		<?php endif; ?>
		<button type="button" class="theme-toggle" aria-label="<?php esc_attr_e( 'Renk temasını değiştir', 'calmpress' ); ?>" data-theme-toggle>
			<span aria-hidden="true">◐</span>
			<span class="theme-toggle__label"><?php esc_html_e( 'Tema', 'calmpress' ); ?></span>
		</button>
	</div>
	<?php if ( calmpress_get_option( 'calmpress_show_header_widgets' ) ) : ?>
		<?php calmpress_render_widget_area( 'header' ); ?>
	<?php endif; ?>
</header>
<?php calmpress_render_ad( 'header' ); ?>
<?php if ( calmpress_get_option( 'calmpress_modal_search' ) ) : ?>
	<div class="search-modal" id="calmpress-search-modal" data-search-modal hidden>
		<div class="search-modal__overlay" data-search-close></div>
		<section class="search-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="calmpress-search-title">
			<button type="button" class="search-modal__close button--secondary" data-search-close aria-label="<?php esc_attr_e( 'Aramayı kapat', 'calmpress' ); ?>">×</button>
			<h2 id="calmpress-search-title"><?php esc_html_e( 'Sitede ara', 'calmpress' ); ?></h2>
			<?php get_search_form(); ?>
		</section>
	</div>
<?php endif; ?>
