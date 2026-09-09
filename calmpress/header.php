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
<a class="screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'calmpress' ); ?></a>
<header class="site-header">
	<div class="site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<span class="site-branding__name"><?php bloginfo( 'name' ); ?></span>
			</a>
		</div>
		<nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'calmpress' ); ?>">
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
		<button type="button" class="theme-toggle" aria-label="<?php esc_attr_e( 'Change color theme', 'calmpress' ); ?>" data-theme-toggle>
			<span aria-hidden="true">◐</span>
			<span class="theme-toggle__label"><?php esc_html_e( 'Theme', 'calmpress' ); ?></span>
		</button>
	</div>
</header>
