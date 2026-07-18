<?php defined( 'ABSPATH' ) || exit; ?>

<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<header class="site-header" role="banner">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => 'nav',
					'container_aria_label' => __( 'Primary', 'dimu' ),
					'fallback_cb'    => false,
				)
			);
			if ( function_exists( 'DIMU\\Boilerplate\\dimu_breadcrumbs' ) ) {
				\DIMU\Boilerplate\dimu_breadcrumbs();
			}
			?>
		</header>
